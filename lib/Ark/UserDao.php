<?php

namespace Ark;

/**
 * DAO for users management.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013-2019, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Lib
 */
class UserDao {
	/** Database connection. */
	private $_db;

	/**
	 * Constructor.
	 * @param	\Temma\Web\Database	$db	Database object.
	 */
	public function __construct(\Temma\Web\Database $db) {
		$this->_db = $db;
	}

	/**
	 * Create a new user.
	 * @param	string	$name		User's name.
	 * @param	string	$email		User's email address.
	 * @param	string	$password	User's password.
	 * @param	bool	$isAdmin	(optional) True if the user has admin rights. False by default.
	 * @return	int	User id.
	 */
	public function addUser($name, $email, $password, $isAdmin=false) {
		$sql = "INSERT INTO User
			SET admin = " . ($isAdmin ? 'TRUE' : 'FALSE') . ",
			    name = " . $this->_db->quote($name) . ",
			    email = " . $this->_db->quote($email) . ",
			    password = " . $this->_db->quote(md5($password)) . ",
			    creationDate = NOW()";
		$this->_db->exec($sql);
		$userId = $this->_db->lastInsertId();
		return ($userId);
	}
	/**
	 * Fetch a user from its credentials.
	 * @param	string	$email		User's email.
	 * @param	string	$password	User's password.
	 * @return	array	Hash.
	 */
	public function getFromCredentials($email, $password) {
		$sql = "SELECT *
			FROM User
			WHERE email = " . $this->_db->quote($email) . "
			  AND password = " . $this->_db->quote(md5($password)) . "
			LIMIT 1";
		$user = $this->_db->queryOne($sql);
		return ($user);
	}
	/**
	 * Get users.
	 * @param	?bool	$isAdmin	(optional) True to fetch only administrators. Null by default.
	 * @return	array	List of associative arrays.
	 */
	public function getUsers(?bool $isAdmin=false) {
		$sql = "SELECT *
			FROM User ";
		if ($isAdmin === true)
			$sql .= "WHERE admin = TRUE ";
		else if ($isAdmin === false)
			$sql .= "WHERE admin = FALSE ";
		$sql .= "ORDER BY name";
		$users = $this->_db->queryAll($sql);
		return ($users);
	}
	/**
	 * Get admin users.
	 * @return	array	List of associative arrays.
	 */
	public function getAdministrators() : ?array {
		$sql = "SELECT id,
		               name,
		               email
			FROM User
			WHERE admin = TRUE
			ORDER BY id";
		$users = $this->_db->queryAll($sql);
		return ($users);
	}
}

