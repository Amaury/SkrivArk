<?php

/**
 * Installation controller.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class InstallController extends \Temma\Controller {
	/** Init. */
	public function init() {
		if ($this->get('URL') == '/') {
			$this->redirect('/install');
			return (self::EXEC_STOP);
		}
		// check temma.json
		$path = realpath(__DIR__ . '/../etc/temma.json');
		$temma = json_decode(file_get_contents($path), true);
		$rootController = $temma['application']['rootController'] ?? null;
		if ($rootController == 'PageController') {
			$this->redirect('/');
			return (self::EXEC_STOP);
		}
		// check files access
		$writableTemma = is_writable(__DIR__ . '/../etc/temma.json');
		$writableLog = is_writable(__DIR__ . '/../log');
		$writableTmp = is_writable(__DIR__ . '/../tmp');
		$writableSplash = is_writable(__DIR__ . '/../var/splashscreen.html');
		$this->set('writableTemma', $writableTemma);
		$this->set('writableLog', $writableLog);
		$this->set('writableTmp', $writableTmp);
		$this->set('writableSplash', $writableSplash);
		if (!$writableTemma || !$writableLog || !$writableTmp || !$writableSplash) {
			$action = $this->get('ACTION');
			if (!empty($action)) {
				$this->redirect('/install');
				return (self::EXEC_STOP);
			}
		}
	}
	/** Main page. */
	public function execIndex() {
	}
	/** Step 1. */
	public function execStep1() {
		// set template variables
		$this->set('dberror', $_GET['dberror']) ?? null;
		$this->set('dbhostname', $_GET['dbhostname'] ?? null);
		$this->set('dbname', $_GET['dbname'] ?? null);
		$this->set('dbuser', $_GET['dbuser'] ?? null);
		$this->set('dbpassword', $_GET['dbpassword'] ?? null);
	}
	/** Store step 1 data. */
	public function execProceedStep1() {
		$dbhostname = trim($_POST['dbhostname'] ?? null);
		$dbname = trim($_POST['dbname'] ?? null);
		$dbuser = trim($_POST['dbuser'] ?? null);
		$dbpassword = trim($_POST['dbpassword'] ?? null);
		// try to connect
		$dsn = "mysqli://$dbuser:$dbpassword@$dbhostname/$dbname";
		try {
			// connexion
			$db = FineDatabase::factory($dsn);
			$db->charset('utf8');
			$db->exec("DO 1");
		} catch (Exception $e) {
			$this->_session->set('dberror', true);
			$this->redirect('/install/step1?dberror=1&dbhostname=' . urlencode($dbhostname) .
			                '&dbname=' . urlencode($dbname) . '&dbuser=' . urlencode($dbuser) .
			                '&dbpassword=' . urlencode($dbpassword));
			return;
		}
		// create database
		$sql = file_get_contents(__DIR__ . '/../etc/database.sql');
		$sql = explode(';', $sql);
		foreach ($sql as $request) {
			$request = trim($request);
			if (!empty($request))
				$db->exec($request);
		}
		// update temma.json
		$path = realpath(__DIR__ . '/../etc/temma.json');
		$temma = json_decode(file_get_contents($path), true);
		$temma['application']['dataSources']['_db'] = $dsn;
		file_put_contents($path, json_encode($temma, JSON_PRETTY_PRINT));
		$this->redirect('/install/step2');
	}
	/** Step 2. */
	public function execStep2() {
		// set template variables
		$this->set('cacheerror', $_GET['cacheerror'] ?? null);
		$this->set('cacheserver', $_GET['cacheserver'] ?? null);
		$this->set('cachehost', $_GET['cachehost'] ?? null);
		$this->set('cacheport', $_GET['cacheport'] ?? null);
	}
	/** Store step 2 data. */
	public function execProceedStep2() {
		$cacheserver = trim($_POST['cacheserver'] ?? null);
		$cachehost = trim($_POST['cachehost'] ?? null);
		$cacheport = trim($_POST['cacheport'] ?? null);
		// read temma.json
		$path = realpath(__DIR__ . '/../etc/temma.json');
		$temma = json_decode(file_get_contents($path), true);
		// process
		$cacheerror = false;
		if ($cacheserver == 'nocache') {
			// no cache server
			unset($temma['application']['dataSources']['_cache']);
			unset($temma['application']['dataSources']['_ndb']);
			unset($temma['application']['sessionSource']);
		} else if ($cacheserver == 'memcache') {
			// memcache server
			$dsn = "memcache://$cachehost:$cacheport";
			$enabled = false;
			try {
				$cache = FineCache::factory($dsn);
				if (!$cache->isEnabled())
					throw new Exception();
				$val = mt_rand();
				$id = 'test-skrivark-' . uniqid();
				$cache->set($id, $val);
				if ($cache->get($id) != $val)
					throw new \Exception();
				unset($temma['application']['dataSources']['_ndb']);
				$temma['application']['dataSources']['_cache'] = $dsn;
				$temma['application']['sessionSource'] = '_cache';
			} catch (\Exception $e) {
				$cacheerror = true;
			}
		} else if ($cacheserver == 'redis') {
			// redis server
			$dsn = "redis://$cachehost:$cacheport";
			try {
				$ndb = FineNDB::factory($dsn);
				$val = mt_rand();
				$id = 'test-skrivark-' . uniqid();
				$ndb->set($id, $val);
				if ($ndb->get($id) != $val)
					throw new \Exception();
				unset($temma['application']['dataSources']['_cache']);
				$temma['application']['dataSources']['_ndb'] = $dsn;
				$temma['application']['sessionSource'] = '_ndb';
			} catch (\Exception $e) {
				$cacheerror = true;
			}
		}
		if ($cacheerror) {
			$this->redirect('/install/step2?cacheerror=1&cacheserver=' . urlencode($cacheserver) .
			                '&cachehost=' . urlencode($cachehost) . '&cacheport=' . urlencode($cacheport));
			return;
		}
		file_put_contents($path, json_encode($temma, JSON_PRETTY_PRINT));
		$this->redirect('/install/step3');
	}
	/** Step 3. */
	public function execStep3() {
		// set template variables
		$this->set('paramerror', $_GET['paramerror'] ?? null);
		$this->set('sitename', $_GET['sitename'] ?? null);
		$this->set('baseurl', $_GET['baseurl'] ?? null);
		$this->set('emailsender', $_GET['emailsender'] ?? null);
		$this->set('demomode', $_GET['demomode'] ?? null);
		$this->set('titledurl', $_GET['titledurl'] ?? null);
		$this->set('allowreadonly', $_GET['allowreadonly'] ?? null);
		$this->set('disqus', $_GET['disqus'] ?? null);
		$this->set('googleanalytics', $_GET['googleanalytics'] ?? null);
		$this->set('loglevel', $_GET['loglevel'] ?? null);
	}
	/** Store step 3 data. */
	public function execProceedStep3() {
		$sitename = trim($_POST['sitename'] ?? null);
		$baseurl = trim($_POST['baseurl'] ?? null);
		$emailsender = trim($_POST['emailsender'] ?? null);
		$demomode = (($_POST['demomode'] ?? 0) == 1) ? true : false;
		$titledurl = (($_POST['titledurl'] ?? 0) == 1) ? true : false;
		$allowreadonly = (($_POST['allowreadonly'] ?? 0) == 1) ? true : false;
		$disqus = trim($_POST['disqus'] ?? null);
		$googleanalytics = trim($_POST['googleanalytics'] ?? null);
		$loglevel = trim($_POST['loglevel'] ?? null);
		// check params
		if (empty($sitename) || empty($baseurl) || empty($emailsender)) {
			$this->redirect('/install/step2?paramerror=1&sitename=' . urlencode($sitename) .
			                '&baseurl=' . urlencode($baseurl) . '&emailsender=' . urlencode($emailsender) .
			                '&demomode=' . urlencode($demomode) . '&titledurl=' . urlencode($titledurl) .
			                '&allowreadonly=' . urlencode($allowreadonly) . '&disqus=' . urlencode($disqus) .
			                '&googleanalytics=' . urlencode($googleanalytics) . '&loglevel=' . urlencode($loglevel));
			return;
		}
		// read temma.json
		$path = realpath(__DIR__ . '/../etc/temma.json');
		$temma = json_decode(file_get_contents($path), true);
		// update temma.json
		$temma['autoimport'] = [
			'sitename'		=> $sitename,
			'baseURL'		=> $baseurl,
			'emailSender'		=> $emailsender,
			'demoMode'		=> $demomode,
			'titledURL'		=> $titledurl,
			'allowReadOnly'		=> $allowreadonly,
			'disqus'		=> $disqus,
			'googleAnalytics'	=> $googleanalytics,
		];
		file_put_contents($path, json_encode($temma, JSON_PRETTY_PRINT));
		// management of the demo mode
		if ($demomode) {
			// create the demo user
			// fill the database with demo data
			$sql = file_get_contents(__DIR__ . '/../etc/demo-data.sql');
			$sql = explode(';', $sql);
			foreach ($sql as $request) {
				$request = trim($request);
				if (!empty($request))
					$this->_db->exec($request);
			}
		}
		$this->redirect('/install/step4');
	}
	/** Step 4. */
	public function execStep4() {
		// set template variables
		$this->set('adminerror', $_GET['adminerror'] ?? null);
		$this->set('adminname', $_GET['adminname'] ?? null);
		$this->set('adminemail', $_GET['adminemail'] ?? null);
	}
	/** Create configuration. */
	public function execProceedStep4() {
		$adminname = trim($_POST['adminname'] ?? '');
		$adminemail = trim($_POST['adminemail'] ?? '');
		$adminpassword = trim($_POST['adminpassword'] ?? '');
		// check params
		if (empty($adminname) || empty($adminemail) || empty($adminpassword) || strlen($adminpassword) < 6) {
			$this->redirect('/install/step4?adminerror=1&adminname=' . urlencode($adminname) .
			                '&adminemail=' . urlencode($adminemail));
			return;
		}
		// create admin user
		$sql = "INSERT INTO User
			SET admin = TRUE,
			    name = '" . $this->_db->quote($_POST['adminname']) . "',
			    email = '" . $this->_db->quote($_POST['adminemail']) . "',
			    password = '" . $this->_db->quote(md5($_POST['adminpassword'])) . "',
			    creationDate = NOW(),
			    modifDate = NOW()";
		$this->_db->exec($sql);
		// update temma.json
		$path = realpath(__DIR__ . '/../etc/temma.json');
		$temma = json_decode(file_get_contents($path), true);
		$temma['application']['rootController'] = 'PageController';
		$temma['plugins'] = [
			'_pre' => ['IdentificationController'],
		];
		file_put_contents($path, json_encode($temma, JSON_PRETTY_PRINT));
		// redirect
		$this->redirect('/install/done');
	}
	/** "Thank you" page. */
	public function execDone() {
	}
}

