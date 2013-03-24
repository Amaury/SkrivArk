<?php

/**
 * Administration controller.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class AdminController extends \Temma\Controller {
	/** User DAO. */
	private $_userDao = null;

	/** Init. */
	public function init() {
		$user = $this->get('user');
		if ($user['admin'] != 1) {
			$this->httpError(410);
			return (self::EXEC_HALT);
		}
		$this->_userDao = $this->loadDao('UserDao');
	}
	/** Main page. */
	public function execIndex() {
		$users = $this->_userDao->search(null, 'name');
		$this->set('users', $users);
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
		if (empty($name) || filter_var($email, FILTER_VALIDATE_EMAIL) === false || (!$generate && empty($password)))
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
				'creationDate'	=> date('c')
			));
		} catch (Exception $e) { }
		if (!$generate)
			return;
		// send an email
		$currentUser = $this->get('user');
		$conf = $this->get('conf');
		$headers = "MIME-Version: 1.0\r\n" .
			   "Content-type: text/html; charset=utf8\r\n" .
			   "From: " . $currentUser['name'] . "<" . $currentUser['email'] . ">";
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
		$user = $this->get('user');
		if ($user['id'] == $userId) {
			$this->set('json', 0);
			return (self::EXEC_HALT);
		}
		$admin = ($admin == 1) ? 1 : 0;
		$this->_userDao->update($userId, array('admin' => $admin));
		$this->set('json', 1);
	}
	/**
	 * Remove a user.
	 * @param	int	$userId	User's identifier.
	 */
	public function execRemoveUser($userId) {
		$this->view('\Temma\Views\JsonView');
		$user = $this->get('user');
		if ($user['id'] == $userId) {
			$this->set('json', 0);
			return (self::EXEC_HALT);
		}
		$this->_userDao->remove($userId);
		$this->set('json', 1);
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
			$conf = $this->get('conf');
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
			print("\n--Page\n");
			for ($i = 0; ; $i += 50) {
				$sql = "SELECT * FROM Page ORDER BY id LIMIT $i, 50";
				$pages = $this->_db->queryAll($sql);
				$nbrPages = count($pages);
				if (!$nbrPages)
					break;
				print("INSERT INTO Page (id, title, html, creationDate, modifDate, creatorId, priority, parentPageId, currentVersionId) VALUES\n");
				for ($j = 0; $j < $nbrPages; $j++) {
					$page = $pages[$j];
					print("('" . $this->_db->quote($page['id']) . "', " .
					      "'" . $this->_db->quote($page['title']) . "', " .
					      "'" . $this->_db->quote($page['html']) . "', " .
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
			print("\n--PageVersion\n");
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
		}
		return (self::EXEC_QUIT);
	}
}

