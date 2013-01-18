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
		$controller = $this->get('CONTROLLER');
		$action = $this->get('ACTION');
		$user = $this->_session->get('user');
		if (isset($user['id'])) {
			$this->set('user', $user);
			return (self::EXEC_FORWARD);
		}
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
}

