<?php

/**
 * Identification controller and plugin.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class IdentificationController extends \Temma\Controller {
	/** Pre-plugin. */
	public function preplugin() {
		FineLog::log('skriv', 'DEBUG', 'preplugin');
		$user = $this->_session->get('user');
		if (isset($user['id'])) {
			$this->set('user', $user);
			return (self::EXEC_FORWARD);
		}
		$conf = $this->get('conf');
		if ($conf['allowReadOnly'] === true || $this->get('URL') == '/install/done')
			return (self::EXEC_FORWARD);
		$controller = $this->get('CONTROLLER');
		if ($controller !== 'identification') {
			$this->redirect('/identification/login');
			return (self::EXEC_HALT);
		}
	}
	/** Login form. */
	public function execLogin() {
		FineLog::log('skriv', 'DEBUG', 'Login action');
		$this->set('emailLogin', $this->_session->get('emailLogin'));
		$this->_session->set('emailLogin', null);
		$this->template('identification/login.tpl');
	}
	/** Authentification. */
	public function execAuth() {
		$email = $_POST['email'];
		$pwd = $_POST['password'];
		FineLog::log('skriv', 'DEBUG', "Auth user '$email'.");
		$userDao = $this->loadDao('UserDao');
		$user = $userDao->getFromCredentials($email, $pwd);
		FineLog::log('skriv', 'DEBUG', "Fetched user:" . print_r($user, true));
		if (!isset($user['id'])) {
			// unknown user
			$this->_session->set('emailLogin', $email);
			$this->redirect('/identification/login');
			return (self::EXEC_HALT);
		}
		$this->_session->set('user', $user);
		$this->redirect('/');
	}
	/** Logout. */
	public function execLogout() {
		FineLog::log('skriv', 'DEBUG', "Logout action.");
		$this->_session->set('user', null);
		$this->redirect('/');
	}
	/** Account page. */
	public function execAccount() {
		FineLog::log('skriv', 'DEBUG', "Account action.");
	}
	/** Modify an account. */
	public function execUpdateAccount() {
		FineLog::log('skriv', 'DEBUG', "UpdateAccount action.");
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);
		$password2 = trim($_POST['password2']);
		$this->redirect('/identification/account');
		// date verification
		if (empty($name) || filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
		    (!empty($password) && !empty($password2) && $password !== $password2))
			return (self::EXEC_HALT);
		// update
		$conf = $this->get('conf');
		$data = array('name' => $name);
		if (!isset($conf['demoMode']) || !$conf['demoMode']) {
			$data['email'] = $email;
			if (!empty($password) && $password === $password2)
				$data['password'] = md5($password);
		}
		$currentUser = $this->get('user');
		try {
			$userDao = $this->loadDao('UserDao');
			$userDao->update($currentUser['id'], $data);
			$currentUser = $userDao->get($currentUser['id']);
			$this->_session->set('user', $currentUser);
		} catch (Exception $e) { }
	}
}

