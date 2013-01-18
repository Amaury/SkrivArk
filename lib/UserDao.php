<?php

/**
 * DAO for users management.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Lib
 */
class UserDao extends \Temma\Dao {
	/** Disable cache. */
	protected $_disableCache = true;
	/** Table definition. */
	protected $_tableName = 'User';

	/* ****************** READ **************** */
	/**
	 * Fetch a user from its credentials.
	 * @param	string	$email		User's email.
	 * @param	string	$password	User's password.
	 * @return	array	Hash.
	 */
	public function getFromCredentials($email, $password) {
		FineLog::log('skriv', 'DEBUG', "Get from credentials '$email' - '$password'.");
		$criteria = $this->criteria()->equal('email', $email)
					     ->equal('password', $password);
		$user = $this->search($criteria, null, null, 1);
		if (isset($user[0]))
			return ($user[0]);
		return (null);
	}
}
