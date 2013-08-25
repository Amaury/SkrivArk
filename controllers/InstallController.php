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
		if (!is_a($this->_db, 'FineDatasource')) {
			$this->redirect('/');
			return (self::EXEC_STOP);
		}
		if (is_writable(__DIR__ . '/../etc/temma.json'))
			$this->set('editableConfig', true);
		else {
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
		$this->set('dberror', $this->_session->get('dberror'));
		$this->set('dbhostname', $this->_session->get('dbhostname'));
		$this->set('dbname', $this->_session->get('dbname'));
		$this->set('dbuser', $this->_session->get('dbuser'));
		$this->set('dbpassword', $this->_session->get('dbpassword'));
		// reset session variables
		$this->_session->set('dberror', null);
		$this->_session->set('dbhostname', null);
		$this->_session->set('dbname', null);
		$this->_session->set('dbuser', null);
		$this->_session->set('dbpassword', null);
	}
	/** Store step 1 data. */
	public function execProceedStep1() {
		$this->_session->set('dbhostname', trim($_POST['dbhostname']));
		$this->_session->set('dbname', trim($_POST['dbname']));
		$this->_session->set('dbuser', trim($_POST['dbuser']));
		$this->_session->set('dbpassword', trim($_POST['dbpassword']));
		// try to connect
		$dsn = 'mysqli://' . $_POST['dbuser'] . ':' . $_POST['dbpassword'] . '@' . $_POST['dbhostname'] . '/' . $_POST['dbname'];
		try {
			$db = FineDatabase::factory($dsn);
			$db->charset('utf8');
			$this->redirect('/install/step2');
		} catch (Exception $e) {
			$this->_session->set('dberror', true);
			$this->redirect('/install/step1');
		}
	}
	/** Step 2. */
	public function execStep2() {
		// set template variables
		$this->set('cacheerror', $this->_session->get('cacheerror'));
		$this->set('cachehost', $this->_session->get('cachehost'));
		$this->set('cacheport', $this->_session->get('cacheport'));
		// reset session variables
		$this->_session->set('cacheerror', null);
		$this->_session->set('cachehost', null);
		$this->_session->set('cacheport', null);
	}
	/** Store step 2 data. */
	public function execProceedStep2() {
		$this->_session->set('cachehost', trim($_POST['cachehost']));
		$this->_session->set('cacheport', trim($_POST['cacheport']));
		// try to connect
		$dsn = 'memcache://' . $_POST['cachehost'] . ':' . $_POST['cacheport'];
		$enabled = false;
		try {
			$cache = FineCache::factory($dsn);
			if (!$cache->isEnabled())
				throw new Exception();
			$val = mt_rand();
			$id = 'test-skrivark-' . uniqid();
			$cache->set($id, $val);
			if ($cache->get($id) != $val)
				throw new Exception();
			$this->redirect('/install/step3');
		} catch (Exception $e) {
			$this->_session->set('cacheerror', true);
			$this->redirect('/install/step2');
		}
	}
	/** Step 3. */
	public function execStep3() {
		// set template variables
		$this->set('paramerror', $this->_session->get('paramerror'));
		$this->set('sitename', $this->_session->get('sitename'));
		$this->set('baseurl', $this->_session->get('baseurl'));
		$this->set('emailsender', $this->_session->get('emailsender'));
		$this->set('demomode', $this->_session->get('demomode'));
		$this->set('titledurl', $this->_session->get('titledurl'));
		$this->set('allowreadonly', $this->_session->get('allowreadonly'));
		$this->set('disqus', $this->_session->get('disqus'));
		$this->set('googleanalytics', $this->_session->get('googleanalytics'));
		$this->set('loglevel', $this->_session->get('loglevel'));
		// reset session variables
		$this->_session->set('paramerror', null);
		$this->_session->set('sitename', null);
		$this->_session->set('baseurl', null);
		$this->_session->set('emailsender', null);
		$this->_session->set('demomode', null);
		$this->_session->set('titledurl', null);
		$this->_session->set('allowreadonly', null);
		$this->_session->set('disqus', null);
		$this->_session->set('googleanalytics', null);
		$this->_session->set('loglevel', null);
	}
	/** Store step 3 data. */
	public function execProceedStep3() {
		$this->_session->set('sitename', trim($_POST['sitename']));
		$this->_session->set('baseurl', trim($_POST['baseurl']));
		$this->_session->set('emailsender', trim($_POST['emailsender']));
		$this->_session->set('demomode', (isset($_POST['demomode']) && $_POST['demomode'] == 1) ? true : false);
		$this->_session->set('titledurl', (isset($_POST['titledurl']) && $_POST['titledurl'] == 1) ? true : false);
		$this->_session->set('allowreadonly', (isset($_POST['allowreadonly']) && $_POST['allowreadonly'] == 1) ? true : false);
		$this->_session->set('disqus', trim($_POST['disqus']));
		$this->_session->set('googleanalytics', trim($_POST['googleanalytics']));
		$this->_session->set('loglevel', trim($_POST['loglevel']));
		// check params
		if (!isset($_POST['sitename']) || !isset($_POST['baseurl']) || !isset($_POST['emailsender'])) {
			$this->_session->set('paramerror', true);
			$this->redirect('/install/step2');
			return;
		}
		$this->redirect('/install/step4');
	}
	/** Step 4. */
	public function execStep4() {
		// set template variables
		$this->set('adminerror', $this->_session->get('adminerror'));
		$this->set('adminname', $this->_session->get('adminname'));
		$this->set('adminemail', $this->_session->get('adminemail'));
		// reset session variables
		$this->_session->set('adminerror', null);
		$this->_session->set('adminname', null);
		$this->_session->set('adminemail', null);
	}
	/** Create configuration. */
	public function execProceedStep4() {
		// check params
		if (!isset($_POST['adminname']) || !isset($_POST['adminemail']) || !isset($_POST['adminpassword']) || strlen($_POST['adminpassword']) < 6) {
			$this->_session->set('adminname', trim($_POST['adminname']));
			$this->_session->set('adminemail', trim($_POST['adminemail']));
			$this->_session->set('adminerror', true);
			$this->redirect('/install/step4');
			return;
		}
		// create config file
		$loglevel = $this->_session->get('loglevel');
		$dbDsn = 'mysqli://' . $this->_session->get('dbuser') . ':' . $this->_session->get('dbpassword') .
			 '@' . $this->_session->get('dbhostname') . '/' . $this->_session->get('dbname');
		$cacheDsn = 'memcache://' . $this->_session->get('cachehost') . ':' . $this->_session->get('cacheport');
		$config = array(
			'application' => array(
				'dataSources' => array(
					'_db'	 => $dbDsn,
					'_cache' => $cacheDsn
				),
				'sessionName'	 => 'ArkSession',
				'sessionSource'	 => '_cache',
				'rootController' => 'PageController'
			),
			'loglevels' => array(
				'finebase'	=> $loglevel,
				'temma'		=> $loglevel,
				'skriv'		=> $loglevel
			),
			'plugins' => array(
				'_pre' => array(
					'IdentificationController'
				)
			),
			'autoimport' => array(
				'sitename'		=> $this->_session->get('sitename'),
				'baseURL'		=> $this->_session->get('baseurl'),
				'emailSender'		=> $this->_session->get('emailsender'),
				'demoMode'		=> $this->_session->get('demomode'),
				'titledURL'		=> $this->_session->get('titledurl'),
				'allowReadOnly'		=> $this->_session->get('allowreadonly'),
				'disqus'		=> $this->_session->get('disqus'),
				'googleAnalytics'	=> $this->_session->get('googleanalytics')
			)
		);
		$json = TextUtil::JsonEncode($config);
		file_put_contents(__DIR__ . '/../etc/temma.json', $json);
		// create database
		$db = FineDatabase::factory($dbDsn);
		$sql = file_get_contents(__DIR__ . '/../etc/database.sql');
		$sql = explode(';', $sql);
		foreach ($sql as $request) {
			$request = trim($request);
			if (!empty($request))
				$db->exec($request);
		}
		if ($this->_session->get('demomode')) {
			$sql = file_get_contents(__DIR__ . '/../etc/demo-data.sql');
			$sql = explode(';', $sql);
			foreach ($sql as $request) {
				$request = trim($request);
				if (!empty($request))
					$db->exec($request);
			}
		}
		// create admin user
		$sql = "INSERT INTO User
			SET admin = TRUE,
			    name = '" . $db->quote($_POST['adminname']) . "',
			    email = '" . $db->quote($_POST['$adminemail']) . "',
			    password = '" . $db->quote(md5($_POST['adminpassword'])) . "',
			    creationDate = NOW(),
			    modifDate = NOW()";
		$db->exec($sql);
		$this->redirect('/install/done');
	}
	/** "Thank you" page. */
	public function execDone() {
	}
}

