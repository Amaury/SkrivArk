<?php

use \Temma\Base\Log as TµLog;

/**
 * Authentication controller and plugin.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class Authentication extends \Temma\Web\Plugin {
	/** Pre-plugin. */
	public function preplugin() {
		TµLog::log('ark', 'DEBUG', 'preplugin');
		$user = $this->_loader->session['user'];
		if (isset($user['id'])) {
			$this['user'] = $user;
			return (self::EXEC_FORWARD);
		}
		if ($this['conf']['allowReadOnly'] === true || $this['conf']['URL'] == '/install/done')
			return (self::EXEC_FORWARD);
		if ($this['CONTROLLER'] !== 'authentication') {
			$this->redirect('/authentication/login');
			return (self::EXEC_HALT);
		}
	}
	/** Login form. */
	public function login() {
		TµLog::log('ark', 'DEBUG', 'Login action');
		$this['emailLogin'] = $this->_loader->session['emailLogin'];
		unset($this->_loader->session['emailLogin']);
	}
	/** Authentication. */
	public function auth() {
		$email = $_POST['email'];
		$pwd = $_POST['password'];
		TµLog::log('ark', 'DEBUG', "Auth user '$email'.");
		$user = $this->_loader->userDao->getFromCredentials($email, $pwd);
		TµLog::log('ark', 'DEBUG', "Fetched user:" . print_r($user, true));
		if (!isset($user['id'])) {
			// unknown user
			$this->_loader->session['emailLogin'] = $email;
			$this->redirect('/identification/login');
			return (self::EXEC_HALT);
		}
		$this->_loader->session['user'] = $user;
		$this->redirect('/');
	}
	/** Logout. */
	public function logout() {
		TµLog::log('ark', 'DEBUG', "Logout action.");
		unset($this->_loader->session['user']);
		$this->redirect('/');
	}
	/** Account page. */
	public function account() {
		TµLog::log('ark', 'DEBUG', "Account action.");
		$this['editable'] = $this->_editableAccount();
		$this['paramsError'] = $this->_loader->session['paramsError'];
		unset($this->_loader->session['paramsError']);
		$this['updated'] = $this->_loader->session['updated'];
		unset($this->_loader->session['updated']);
	}
	/** Modify an account. */
	public function updateAccount() {
		TµLog::log('ark', 'DEBUG', "UpdateAccount action.");
		$this->redirect('/authentication/account');
		if (!$this->_editableAccount())
			return (self::EXEC_HALT);
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);
		$password2 = trim($_POST['password2']);
		// check parameters
		if (empty($name) || empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
		    (!empty($password) && $password !== $password2)) {
			$this->_loader->session['paramsError'] = true;
			return (self::EXEC_HALT);
		}
		// update
		$this->_loader->userDao->update($this['user']['id'], $name, $email, $password);
		$currentUser = $this->_loader->userDao->get($this['user']['id']);
		$this->_loader->session['user'] = $currentUser;
		$this->_loader->session['updated'] = true;
	}

	/* ********** PRIVATE METHODS ********** */
	/**
	 * Tell if the current user's account could be modified or not.
	 * @return	bool	True if the current user's account could be modified.
	 */
	private function _editableAccount() {
		if ($this['conf']['demoMode'] && $this['user']['email'] == 'demo@demo.com')
			return (false);
		return (true);
	}
}

