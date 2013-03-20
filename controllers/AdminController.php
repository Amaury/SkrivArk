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
}

