<?php

/**
 * Administration controller.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class AdminController extends \Temma\Web\Controller {
	/** Init. */
	public function __wakeup() {
		$isAdmin = $this['user']['admin'] ?? false;
		if (!$isAdmin) {
			$this->httpError(410);
			return (self::EXEC_HALT);
		}
	}
	/** Main page. */
	public function execIndex() {
		// get users
		$this['users'] = $this->_loader->userDao->getUsers();
		// read configuration file
		$appPath = $this->_loader->config->appPath;
		if (is_writable("$appPath/etc/temma.json")) {
			$this['editableConfig'] = true;
			$json = json_decode(file_get_contents("$appPath/etc/temma.json"), true);
			$this['dbDSN'] = $json['application']['dataSources']['_db'];
			$this['cacheDSN'] = $json['application']['dataSources']['_cache'];
			$this['logLevel'] = $json['loglevels']['Temma/Base'];
		}
		// read splashscreen
		if (is_writable("$appPath/var/splashscreen.html")) {
			$this['editableSplashscreen'] = true;
			$this['splashscreen'] = file_get_contents("$appPath/var/splashscreen.html");
		}
	}
	/** Check if an HTML content is valid. */
	public function execCheckHtml() {
		$this->view('\Temma\Views\JsonView');
		$this['json'] = \Temma\Utils\Text::isValidHtmlSyntax($_POST['html']);
	}
	/** Save a new splashscreen. */
	public function execSplash() {
		$appPath = $this->_loader->config->appPath;
		file_put_contents("$appPath/var/splashscreen.html", $_POST['html']);
		$this->redirect('/admin');
	}
	/** Store new configuration. */
	public function execConfig() {
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
	public function execAddUser() {
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);
		$admin = ($_POST['admin'] == '1') ? 1 : 0;
		$generate = ($_POST['generate'] == '1') ? true : false;
		$this->redirect('/admin');
		// data verification
		$conf = $this['conf'];
		if (empty($name) || filter_var($email, FILTER_VALIDATE_EMAIL) === false || (!$generate && empty($password)) || (isset($conf['demoMode']) && $conf['demoMode']))
			return (self::EXEC_HALT);
		// creation
		if ($generate)
			$password = substr(md5(time() . mt_rand()), 0, 6);
		try {
			$this->_userDao->create(array(
				'admin'		=> $admin,
				'name'		=> $name,
				'email'		=> $email,
				'password'	=> md5($password),
				'creationDate'	=> substr(date('c'), 0, 19),
			));
		} catch (Exception $e) { }
		if (!$generate)
			return;
		// send an email
		$currentUser = $this['user'];
		$conf = $this['conf'];
		$headers = "MIME-Version: 1.0\r\n" .
			   "Content-type: text/html; charset=utf8\r\n" .
			   "From: " . $conf['emailSender'];
		$msg = "<html><body>
				<h1>" . htmlspecialchars($conf['sitename']) . "</h1>
				<p>Hi " . htmlspecialchars($name) . ",</p>
				<p>
					" . htmlspecialchars($currentUser['name']) . " has created an account for you on the
					<a href=\"" . htmlspecialchars($conf['baseURL']) . "\">" . htmlspecialchars($conf['sitename']) . "</a> site.
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
		mail($email, '[' . $conf['sitename'] . '] Account Creation', $msg, $headers);
	}
	/**
	 * Change user admin status.
	 * @param	int	$userId	User's identifier.
	 * @param	int	$admin	1 if admin, 0 else.
	 */
	public function execSetAdmin($userId, $admin) {
		$this->view('\Temma\Views\JsonView');
		$user = $this['user'];
		$conf = $this['conf'];
		if ($user['id'] == $userId || (isset($conf['demoMode']) && $conf['demoMode'])) {
			$this['json'] = 0;
			return (self::EXEC_HALT);
		}
		$admin = ($admin == 1) ? 1 : 0;
		$this->_userDao->update($userId, array('admin' => $admin));
		$this['json'] = 1;
	}
	/**
	 * Remove a user.
	 * @param	int	$userId	User's identifier.
	 */
	public function execRemoveUser($userId) {
		$this->view('\Temma\Views\JsonView');
		$user = $this['user'];
		$conf = $this['conf'];
		if ($user['id'] == $userId || (isset($conf['demoMode']) && $conf['demoMode'])) {
			$this['json'] = 0;
			return (self::EXEC_HALT);
		}
		$this->_userDao->remove($userId);
		$this['json'] = 1;
	}
	private function _exportZip($zip, $id, $path='') {
		$sql = "SELECT *
			FROM Page
			WHERE parentPageId = '$id'";
		$pages = $this->_db->queryAll($sql);
		foreach ($pages as $page)
			$zip->addFromString($path . $page['id'] . '-' . $page['title'] . '.html', $page['html']);
		foreach ($pages as $page)
			$this->_exportZip($zip, $page['id'], $path . $page['id'] . '-' . $page['title'] . '/');
	}
	/** Database export. */
	public function execExport() {
		FineLog::log('skriv', 'INFO', "Export action.");
		$format = $_GET['format'];
		if ($format == 'html') {
			$tmpfile = tempnam('/tmp', 'skriv-export-');
			$zip = new ZipArchive();
			if ($zip->open($tmpfile, ZIPARCHIVE::CREATE) !== true) {
				$this->httpError(500);
				return (self::EXEC_HALT);
			}
			$conf = $this['conf'];
			$sitename = (isset($conf['sitename']) && !empty($conf['sitename'])) ? $conf['sitename'] : 'SkrivArk';
			$this->_exportZip($zip, 0, "$sitename/");
			$zip->close();
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="dump-' . date('Ymd-Hi') . '.zip"');
			readfile($tmpfile);
			unlink($tmpfile);
		} else {
			header('Content-Type: text/plain');
			header('Content-Disposition: attachment; filename="dump-' . date('Ymd-Hi') . '.sql"');
			// model
			readfile(__DIR__ . '/../etc/database.sql');
			// users
			print("\n-- User\n");
			$sql = "SELECT * FROM User ORDER BY id";
			$users = $this->_db->queryAll($sql);
			$nbrUsers = count($users);
			print("INSERT INTO User (id, admin, name, email, password, creationDate, modifDate) VALUES\n");
			for ($j = 0; $j < $nbrUsers; $j++) {
				$user = $users[$j];
				print("('" . $this->_db->quote($user['id']) . "', " .
				      "'" . $this->_db->quote($user['admin']) . "', " .
				      "'" . $this->_db->quote($user['name']) . "', " .
				      "'" . $this->_db->quote($user['email']) . "', " .
				      "'" . $this->_db->quote($user['password']) . "', " .
				      "'" . $this->_db->quote($user['creationDate']) . "', " .
				      "'" . $this->_db->quote($user['modifDate']) . "')");
				if ($j < ($nbrUsers - 1))
					print(",\n");
			}
			print(";\n");
			// pages
			print("\n-- Page\n");
			for ($i = 0; ; $i += 50) {
				$sql = "SELECT * FROM Page ORDER BY id LIMIT $i, 50";
				$pages = $this->_db->queryAll($sql);
				$nbrPages = count($pages);
				if (!$nbrPages)
					break;
				print("INSERT INTO Page (id, title, html, toc, creationDate, modifDate, creatorId, priority, parentPageId, currentVersionId) VALUES\n");
				for ($j = 0; $j < $nbrPages; $j++) {
					$page = $pages[$j];
					print("('" . $this->_db->quote($page['id']) . "', " .
					      "'" . $this->_db->quote($page['title']) . "', " .
					      "'" . $this->_db->quote($page['html']) . "', " .
					      "'" . $this->_db->quote($page['toc']) . "', " .
					      "'" . $this->_db->quote($page['creationDate']) . "', " .
					      "'" . $this->_db->quote($page['modifDate']) . "', " .
					      "'" . $this->_db->quote($page['creatorId']) . "', " .
					      "'" . $this->_db->quote($page['priority']) . "', " .
					      "'" . $this->_db->quote($page['parentPageId']) . "', " .
					      "'" . $this->_db->quote($page['currentVersionId']) . "')");
					if ($j < ($nbrPages - 1))
						print(",\n");
				}
				print(";\n");
			}
			// versions
			print("\n-- PageVersion\n");
			for ($i = 0; ; $i += 50) {
				$sql = "SELECT * FROM PageVersion ORDER BY id LIMIT $i, 50";
				$versions = $this->_db->queryAll($sql);
				$nbrVersions = count($versions);
				if (!$nbrVersions)
					break;
				print("INSERT INTO PageVersion (id, title, skriv, creationDate, creatorId, pageId) VALUES\n");
				for ($j = 0; $j < $nbrVersions; $j++) {
					$version = $versions[$j];
					print("('" . $this->_db->quote($version['id']) . "', " .
					      "'" . $this->_db->quote($version['title']) . "', " .
					      "'" . $this->_db->quote($version['skriv']) . "', " .
					      "'" . $this->_db->quote($version['creationDate']) . "', " .
					      "'" . $this->_db->quote($version['creatorId']) . "', " .
					      "'" . $this->_db->quote($version['pageId']) . "')");
					if ($j < ($nbrVersions - 1))
						print(",\n");
				}
				print(";\n");
			}
			// subscriptions
			print("\n-- Subscription\n");
			for ($i = 0; ; $i += 50) {
				$sql = "SELECT * FROM Subscription ORDER BY id LIMIT $i, 50";
				$subscriptions = $this->_db->queryAll($sql);
				$nbrSubscriptions = count($subscriptions);
				if (!$nbrSubscriptions)
					break;
				print("INSERT INTO Subscription (id, userId, pageId, createDate) VALUES\n");
				for ($j = 0; $j < $nbrSubscriptions; $j++) {
					$subscription = $subscriptions[$j];
					print("('" . $this->_db->quote($subscription['id']) . "', " .
					      "'" . $this->_db->quote($subscription['userId']) . "', " .
					      "'" . $this->_db->quote($subscription['pageId']) . "', " .
					      "'" . $this->_db->quote($subscription['createDate']) . "')");
					if ($j < ($nbrSubscriptions - 1))
						print(",\n");
				}
				print(";\n");
			}
		}
		return (self::EXEC_QUIT);
	}
}

