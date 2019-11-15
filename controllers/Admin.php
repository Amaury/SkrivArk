<?php

/**
 * Administration controller.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class Admin extends \Temma\Web\Controller {
	/** Init. */
	public function __wakeup() {
		$isAdmin = $this['user']['admin'] ?? false;
		if (!$isAdmin) {
			$this->httpError(410);
			return (self::EXEC_HALT);
		}
	}
	/** Main page. */
	public function index() {
		// get users
		$this['users'] = $this->_loader->userDao->getUsers();
		// read configuration file
		$appPath = $this->_loader->config->appPath;
		if (is_writable("$appPath/etc/temma.json")) {
			$this['editableConfig'] = true;
			$json = json_decode(file_get_contents("$appPath/etc/temma.json"), true);
			$this['dbDSN'] = $json['application']['dataSources']['db'];
			$this['cacheDSN'] = $json['application']['dataSources']['cache'];
			$this['logLevel'] = $json['loglevels']['Temma/Base'];
		}
		// read splashscreen
		if (is_writable("$appPath/var/splashscreen.html")) {
			$this['editableSplashscreen'] = true;
			$this['splashscreen'] = file_get_contents("$appPath/var/splashscreen.html");
		}
	}
	/** Check if an HTML content is valid. */
	public function checkHtml() {
		$this->view('\Temma\Views\JsonView');
		$this['json'] = \Temma\Utils\Text::isValidHtmlSyntax($_POST['html']);
	}
	/** Save a new splashscreen. */
	public function splash() {
		$appPath = $this->_loader->config->appPath;
		file_put_contents("$appPath/var/splashscreen.html", $_POST['html']);
		$this->redirect('/admin');
	}
	/** Store new configuration. */
	public function config() {
		$this->subProcess('Install', 'proceedStep3');
		$this->redirect('/admin');
		return;

		$config = json_decode(file_get_contents(__DIR__ . '/../etc/temma.json'), true);
		$config['autoimport']['sitename'] = $_POST['sitename'];
		$config['autoimport']['baseURL'] = $_POST['baseurl'];
		$config['autoimport']['emailSender'] = $_POST['emailsender'];
		$config['autoimport']['demoMode'] = (isset($_POST['demomode']) && $_POST['demomode'] === '1') ? true : false;
		$config['autoimport']['titledURL'] = (isset($_POST['titledurl']) && $_POST['titledurl'] === '1') ? true : false;
		$config['autoimport']['allowReadOnly'] = (isset($_POST['allowreadonly']) && $_POST['allowreadonly'] === '1') ? true : false;
		$config['autoimport']['disqus'] = $_POST['disqus'];
		$config['autoimport']['googleAnalytics'] = $_POST['googleanalytics'];
		$loglevel = $_POST['loglevel'];
		if ($loglevel == 'DEBUG' || $loglevel == 'NOTE' || $loglevel == 'INFO' || $loglevel == 'WARN' || $loglevel == 'ERROR')
			$config['loglevels']['finebase'] = $config['loglevels']['temma'] = $config['loglevels']['skriv'] = $loglevel;
		$json = json_encode($config, JSON_PRETTY_PRINT);
		file_put_contents(__DIR__ . '/../etc/temma.json', $json);
		$this->redirect('/admin');
	}
	/** Ad a new user. */
	public function addUser() {
		$this->redirect('/admin');
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);
		$admin = ($_POST['admin'] == '1') ? 1 : 0;
		$generate = ($_POST['generate'] == '1') ? true : false;
		// data verification
		$conf = $this['conf'];
		if (empty($name) || filter_var($email, FILTER_VALIDATE_EMAIL) === false || (!$generate && empty($password)) || $conf['demoMode'])
			return (self::EXEC_HALT);
		// creation
		if ($generate)
			$password = substr(md5(time() . mt_rand()), 0, 8);
		$this->_loader->userDao->addUser($name, $email, $password, $admin);
		if (!$generate)
			return;
		// send an email
		$headers = "MIME-Version: 1.0\r\n" .
			   "Content-type: text/html; charset=utf8\r\n" .
			   "From: " . $this['conf']['emailSender'];
		$msg = "<html><body>
				<h1>" . htmlspecialchars($this['conf']['sitename']) . "</h1>
				<p>Hi " . htmlspecialchars($name) . ",</p>
				<p>
					" . htmlspecialchars($this['user']['name']) . " has created an account for you on the
					<a href=\"" . htmlspecialchars($this['conf']['baseURL']) . "\">" . htmlspecialchars($this['conf']['sitename']) . "</a> site.
				</p>
				<p>
					Your temporary password is <strong>" . htmlspecialchars($password) . "</strong>
				</p>
				<p>
					Best regards,<br />
					The Skriv Team
				</p>
			</body></html>";
		// send email
		mail($email, '[' . $this['conf']['sitename'] . '] Account Creation', $msg, $headers);
	}
	/**
	 * Change user admin status.
	 * @param	int	$userId	User's identifier.
	 * @param	int	$admin	1 if admin, 0 else.
	 */
	public function setAdmin($userId, $admin) {
		$this->view('\Temma\Views\JsonView');
		if ($this['user']['id'] == $userId || $this['conf']['demoMode']) {
			$this['json'] = 0;
			return (self::EXEC_HALT);
		}
		$admin = ($admin == 1) ? 1 : 0;
		$this->_loader->userDao->setAdminRights($userId, $admin);
		$this['json'] = 1;
	}
	/**
	 * Remove a user.
	 * @param	int	$userId	User's identifier.
	 */
	public function removeUser($userId) {
		$this->view('\Temma\Views\JsonView');
		$conf = $this['conf'];
		if ($this['user']['id'] == $userId || $this['conf']['demoMode']) {
			$this['json'] = 0;
			return (self::EXEC_HALT);
		}
		$this->_loader->userDao->removeUser($userId);
		$this['json'] = 1;
	}
	/** Database export. */
	public function export() {
		$format = $_GET['format'];
		if ($format == 'html') {
			try {
				$zipPath = $this->_loader->exportBo->exportHtml();
			} catch (\Exception $e) {
				throw new \Temma\Exceptions\HttpException($e->getMessage(), 500);
			}
		} else if ($format == 'sql') {
			$this->_loader->exportBo->exportSql();
		} else {
			throw new \Temma\Exceptions\HttpExcpetion("Unknown export format '$format'.", 410);
		}
		return (self::EXEC_QUIT);
	}
}

