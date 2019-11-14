<?php

use \Temma\Base\Log as TµLog;

/**
 * Installation controller.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013-2019, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class Install extends \Temma\Web\Controller {
	/** Init. */
	public function __wakeup() {
		if ($this['URL'] == '/') {
			$this->redirect('/install');
			return (self::EXEC_STOP);
		}
		// check temma.json
		if ($this->_loader->installBo->isInstalled()) {
			TµLog::log('ark', 'INFO', "Try to install an already installed site.");
			$this->redirect('/');
			return (self::EXEC_HALT);
		}
		// check files access
		$this['filesAccessRights'] = $this->_loader->installBo->checkFilesAccessRights();
		if ($this['filesAccessRights'] !== true && !empty($this['ACTION'])) {
			$this->redirect('/install');
			return (self::EXEC_HALT);
		}
	}
	/** Main page. */
	public function index() {
	}
	/** Step 1. */
	public function step1() {
		// set template variables
		$this['dberror'] = $_GET['dberror'] ?? null;
		$this['dbhostname'] = $_GET['dbhostname'] ?? null;
		$this['dbname'] = $_GET['dbname'] ?? null;
		$this['dbuser'] = $_GET['dbuser'] ?? null;
		$this['dbpassword'] = $_GET['dbpassword'] ?? null;
	}
	/** Store step 1 data. */
	public function proceedStep1() {
		$dbhostname = trim($_POST['dbhostname'] ?? null);
		$dbname = trim($_POST['dbname'] ?? null);
		$dbuser = trim($_POST['dbuser'] ?? null);
		$dbpassword = trim($_POST['dbpassword'] ?? null);
		// try to connect
		$db = $this->_loader->installBo->checkDatabaseParameters($dbhostname, $dbname, $dbuser, $dbpassword);
		if (!$db) {
			$this->redirect('/install/step1?dberror=1&dbhostname=' . urlencode($dbhostname) .
			                '&dbname=' . urlencode($dbname) . '&dbuser=' . urlencode($dbuser) .
			                '&dbpassword=' . urlencode($dbpassword));
			return (self::EXEC_HALT);
		}
		// create database
		$this->_loader->installBo->createDatabase($db);
		// update temma.json
		$this->_loader->installBo->updateConfigDatabase($dbhostname, $dbname, $dbuser, $dbpassword);
		$this->redirect('/install/step2');
	}
	/** Step 2. */
	public function step2() {
		// set template variables
		$this['cacheerror'] = $_GET['cacheerror'] ?? null;
		$this['cacheserver'] = $_GET['cacheserver'] ?? null;
		$this['cachehost'] = $_GET['cachehost'] ?? null;
		$this['cacheport'] = $_GET['cacheport'] ?? null;
	}
	/** Store step 2 data. */
	public function proceedStep2() {
		$cacheserver = trim($_POST['cacheserver'] ?? null);
		$cachehost = trim($_POST['cachehost'] ?? null);
		$cacheport = trim($_POST['cacheport'] ?? null);
		try {
			// update temma.json
			$this->_loader->installBo->updateConfigSession($cacheserver, $cachehost, $cacheport);
		} catch (\Exception $e) {
			$this->redirect('/install/step2?cacheerror=1&cacheserver=' . urlencode($cacheserver) .
			                '&cachehost=' . urlencode($cachehost) . '&cacheport=' . urlencode($cacheport));
			return (self::EXEC_HALT);
		}
		$this->redirect('/install/step3');
	}
	/** Step 3. */
	public function step3() {
		// set template variables
		$this['paramerror'] = $_GET['paramerror'] ?? null;
		$this['sitename'] = $_GET['sitename'] ?? null;
		$this['baseurl'] = $_GET['baseurl'] ?? null;
		$this['emailsender'] = $_GET['emailsender'] ?? null;
		$this['demomode'] = $_GET['demomode'] ?? null;
		$this['titledurl'] = $_GET['titledurl'] ?? null;
		$this['allowreadonly'] = $_GET['allowreadonly'] ?? null;
		$this['disqus'] = $_GET['disqus'] ?? null;
		$this['googleanalytics'] = $_GET['googleanalytics'] ?? null;
		$this['loglevel'] = $_GET['loglevel'] ?? null;
	}
	/** Store step 3 data. */
	public function proceedStep3() {
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
		if (empty($sitename) || empty($baseurl) || empty($emailsender) || !in_array($loglevel, ['DEBUG', 'INFO', 'NOTE', 'WARN', 'ERROR'])) {
			$this->redirect('/install/step2?paramerror=1&sitename=' . urlencode($sitename) .
			                '&baseurl=' . urlencode($baseurl) . '&emailsender=' . urlencode($emailsender) .
			                '&demomode=' . urlencode($demomode) . '&titledurl=' . urlencode($titledurl) .
			                '&allowreadonly=' . urlencode($allowreadonly) . '&disqus=' . urlencode($disqus) .
			                '&googleanalytics=' . urlencode($googleanalytics) . '&loglevel=' . urlencode($loglevel));
			return (self::EXEC_HALT);
		}
		// update temma.json and manage demo mode
		$this->_loader->installBo->updateConfigParameters($sitename, $baseurl, $emailsender, $demomode, $titledurl,
		                                                  $allowreadonly, $disqus, $googleanalytics, $loglevel);
		$this->redirect('/install/step4');
	}
	/** Step 4. */
	public function step4() {
		// set template variables
		$this['adminerror'] = $_GET['adminerror'] ?? null;
		$this['adminname'] = $_GET['adminname'] ?? null;
		$this['adminemail'] = $_GET['adminemail'] ?? null;
	}
	/** Create configuration. */
	public function proceedStep4() {
		$adminname = trim($_POST['adminname'] ?? '');
		$adminemail = trim($_POST['adminemail'] ?? '');
		$adminpassword = trim($_POST['adminpassword'] ?? '');
		// check params
		if (empty($adminname) || empty($adminemail) || empty($adminpassword) || strlen($adminpassword) < 6) {
			$this->redirect('/install/step4?adminerror=1&adminname=' . urlencode($adminname) .
			                '&adminemail=' . urlencode($adminemail));
			return (self::EXEC_HALT);
		}
		// create admin user
		$this->_loader->userDao->addUser($adminname, $adminemail, $adminpassword, true);
		// update temma.json
		$this->_loader->installBo->updateConfigFinish();
		// redirect
		$this->redirect('/install/done');
	}
	/** "Thank you" page. */
	public function done() {
	}
}

