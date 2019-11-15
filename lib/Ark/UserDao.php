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
	 * @param	\Temma\Base\Database	$db	Database object.
	 */
	public function __construct(\Temma\Base\Database $db) {
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
	public function addUser($name, $email, $password, $isAdmin=false) : int {
		$sql = "INSERT INTO User
			SET admin = " . ($isAdmin ? 'TRUE' : 'FALSE') . ",
			    name = " . $this->_db->quote($name) . ",
			    email = " . $this->_db->quote($email) . ",
			    password = " . $this->_db->quote(md5($password)) . ",
			    creationDate = NOW(),
			    modifDate = NOW()";
		$this->_db->exec($sql);
		$userId = $this->_db->lastInsertId();
		return ($userId);
	}
	/**
	 * Fetch a user from its credentials.
	 * @param	string	$email		User's email.
	 * @param	string	$password	User's password.
	 * @return	array	Associative array.
	 */
	public function getFromCredentials($email, $password) : array {
		$sql = "SELECT *
			FROM User
			WHERE email = " . $this->_db->quote($email) . "
			  AND password = " . $this->_db->quote(md5($password)) . "
			LIMIT 1";
		$user = $this->_db->queryOne($sql);
		return ($user);
	}
	/**
	 * Get user.
	 * @param	int	$userId	User ID.
	 * @return	array	Associative array.
	 */
	public function get(int $userId) : array {
		$sql = "SELECT *
			FROM User
			WHERE id = " . $this->_db->quote($userId);
		$user = $this->_db->queryOne($sql);
		return ($user);
	}
	/**
	 * Get users.
	 * @param	?bool	$isAdmin	(optional) True to fetch only administrators. Null by default.
	 * @param	string	$order		(optional) 'name' to order by user names; 'id' to order by ID. 'name' by default.
	 * @return	array	List of associative arrays.
	 */
	public function getUsers(?bool $isAdmin=null, string $order='name') : array {
		$order = ($order == 'id') ? 'id' : 'name';
		$sql = "SELECT *
			FROM User ";
		if ($isAdmin === true)
			$sql .= "WHERE admin = TRUE ";
		else if ($isAdmin === false)
			$sql .= "WHERE admin = FALSE ";
		$sql .= "ORDER BY $order";
		$users = $this->_db->queryAll($sql);
		return ($users);
	}
	/**
	 * Get subscriptions, paginated and ordered by ID.
	 * @param	int	$pageOffset	(optional) Number of the page, starting at zero. 0 by default.
	 * @param	int	$nbrPerPage	(optional) Number of subscriptions by pagination. 50 by default.
	 * @return	array	List of associative arrays.
	 */
	public function getSubscriptions(int $pageOffset=0, int $nbrPerPage=50) : array {
		$offset = $pageOffset * $nbrPerPage;
		$sql = "SELECT * 
			FROM Subscription
			ORDER BY id
			LIMIT $offset, $nbrPerPage";
		$subscriptions = $this->_db->queryAll($sql);
		return ($subscriptions);
	}
	/**
	 * Update a user.
	 * @param	int	$userId		User ID.
	 * @param	string	$name		User name.
	 * @param	string	$email		User email.
	 * @param	?string	$password	User password.
	 */
	public function update(int $userId, string $name, string $email, ?string $password) /* : void */ {
		$sql = "UPDATE User
			SET name = " . $this->_db->quote($name) . ",
			    email = " . $this->_db->quote($email) . " ";
		if ($password)
			$sql .= ", password = " . $this->_db->quote(md5($password)) . " ";
		$sql .= "WHERE id = " . $this->_db->quote($userId);
		$this->_db->exec($sql);
	}
	/**
	 * Set the admin rights of an user.
	 * @param	int	$userId		User ID.
	 * @param	bool	$isAdmin	True if the user is admin.
	 */
	public function setAdminRights(int $userId, bool $isAdmin) {
		$sql = "UPDATE User
			SET admin = " . ($isAdmin ? 'TRUE' : 'FALSE') . "
			WHERE id = " . $this->_db->quote($userId);
		$this->_db->exec($sql);
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
	/**
	 * Remove a user.
	 * @param	int	$userId	User ID.
	 */
	public function removeUser(int $userId) {
		$sql = "DELETE FROM User
			WHERE id = " . $this->_db->quote($userId);
		$this->_db->exec($sql);
	}
}

