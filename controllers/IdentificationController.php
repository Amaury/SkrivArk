<?php

use \Temma\Bas\Log as TµLog;

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
		TµLog::log('ark', 'DEBUG', 'preplugin');
		$user = $this->_session->get('user');
		if (isset($user['id'])) {
			$this['user'] = $user;
			return (self::EXEC_FORWARD);
		}
		if ($this['conf']['allowReadOnly'] === true || $this['conf']['URL'] == '/install/done')
			return (self::EXEC_FORWARD);
		if ($this['CONTROLLER'] !== 'identification') {
			$this->redirect('/identification/login');
			return (self::EXEC_HALT);
		}
	}
	/** Login form. */
	public function execLogin() {
		TµLog::log('ark', 'DEBUG', 'Login action');
		$this['emailLogin'] = $this->_session->get('emailLogin');
		$this->_session->set('emailLogin', null);
		$this->template('identification/login.tpl');
	}
	/** Authentification. */
	public function execAuth() {
		$email = $_POST['email'];
		$pwd = $_POST['password'];
		TµLog::log('skriv', 'DEBUG', "Auth user '$email'.");
		$user = $this->_loader->userDao->getFromCredentials($email, $pwd);
		TµLog::log('skriv', 'DEBUG', "Fetched user:" . print_r($user, true));
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
		TµLog::log('skriv', 'DEBUG', "Logout action.");
		$this->_session->set('user', null);
		$this->redirect('/');
	}
	/** Account page. */
	public function execAccount() {
		TµLog::log('skriv', 'DEBUG', "Account action.");
	}
	/** Modify an account. */
	public function execUpdateAccount() {
		TµLog::log('skriv', 'DEBUG', "UpdateAccount action.");
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);
		$password2 = trim($_POST['password2']);
		$this->redirect('/identification/account');
		// check parameters
		if (empty($name) || filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
		    (!empty($password) && !empty($password2) && $password !== $password2))
			return (self::EXEC_HALT);
		// update
		$data = ['name' => $name];
		if (!($this['conf']['demoMode'] ?? false)) {
			$data['email'] = $email;
			if (!empty($password) && $password === $password2)
				$data['password'] = md5($password);
		}
		try {
			$this->_loader->userDao->update($this['user']['id'], $data);
			$currentUser = $this->_loader->userDao->get($this['user']['id']);
			$this->_session->set('user', $currentUser);
		} catch (Exception $e) { }
	}
}

