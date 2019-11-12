<?php

/**
 * DAO for pages management.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Lib
 */
class PageDao extends \Temma\Dao {
	/** Disable cache. */
	protected $_disableCache = true;
	/** Table Definition. */
	protected $_tableName = 'Page';

	/* ****************** READ **************** */
	/**
	 * Returns a page.
	 * @param	int	$id		Page's identifier.
	 * @param	int	$versionId	(optional) Identifier of the version to fetch.
	 * @param	int	$userId		(optional) If given, fetch the user's subscription on the page.
	 * @return	array	Hash.
	 */
	public function get($id, $versionId=null, $userId=0) {
		$sql = "SELECT	Page.*,
				PageVersion.skriv,
				(SELECT COUNT(*) FROM PageVersion WHERE pageId = Page.id) AS nbrVersions,
				(SELECT COUNT(*) FROM Page sub WHERE sub.parentPageId = Page.id) AS nbrChildren,
				creator.name AS creatorName,
				modifier.name AS modifierName ";
		if ($userId)
			$sql .= ", IF(Subscription.id, 1, 0) AS subscribed ";
		$sql .= "FROM Page
				INNER JOIN User creator ON (creator.id = Page.creatorId)
				INNER JOIN PageVersion ON (PageVersion.pageId = Page.id)
				INNER JOIN User modifier ON (modifier.id = PageVersion.creatorId) ";
		if ($userId)
			$sql .= "LEFT OUTER JOIN Subscription ON (Subscription.pageId = Page.id AND Subscription.userId = '" . $this->_db->quote($userId) . "') ";
		$sql .= "WHERE Page.id = '" . $this->_db->quote($id) . "' ";
		if (isset($versionId))
			$sql .= "AND PageVersion.id = '" . $this->_db->quote($versionId) . "' ";
		else
			$sql .= "AND PageVersion.id = Page.currentVersionId ";
		$page = $this->_db->queryOne($sql);
		if ($page)
			$page['toc'] = !empty($page['toc']) ? json_decode($page['toc'], true) : '';
		return ($page);
	}
	/**
	 * Returns sub-pages of a given page.
	 * @param	int	$id	(optional) Parent page identifier.
	 * @return	array	List of hashes.
	 */
	public function getSubPages($id=0) {
		$criteria = $this->criteria()->equal('parentPageId', $id);
		$pages = $this->search($criteria, 'priority', null, null);
		$pageIds = [];
		$pagesList = [];
		foreach ($pages as $page) {
			$page['nbrChildren'] = 0;
			$pagesList[$page['id']] = $page;
			$pagesIds[] = $page['id'];
		}
		if (count($pagesIds)) {
			$sql = "SELECT	parentPageId,
					COUNT(*) AS n
				FROM Page
				WHERE parentPageId IN (" . implode(',', $pagesIds) . ")
				GROUP BY parentPageId";
			$result = $this->_db->queryAll($sql);
			foreach ($result as $res) {
				$pagesList[$res['parentPageId']]['nbrChildren'] = $res['n'];
			}
		}
		return ($pagesList);
	}
	/**
	 * Returns page's breadcrumb.
	 * @param	array	$page	Hash containing page's data.
	 * @return	array	List of id and titles.
	 */
	public function getBreadcrumb($page) {
		$breadcrumb = array();
		$parentId = $page['parentPageId'];
		while ($parentId > 0) {
			$parent = $this->get($parentId);
			$breadcrumb[] = array(
				'id'	=> $parentId,
				'title'	=> $parent['title']
			);
			$parentId = $parent['parentPageId'];
		}
		$breadcrumb = array_reverse($breadcrumb);
		return ($breadcrumb);
	}
	/**
	 * Get the list of versions of a page.
	 * @param	int	$id	Page's identifier.
	 * @return	array	List of hashes.
	 */
	public function getVersions($id) {
		$sql = "SELECT	PageVersion.id,
				PageVersion.title,
				PageVersion.creationDate,
				User.name
			FROM PageVersion
				INNER JOIN User On (User.id = PageVersion.creatorId)
			WHERE pageId = '" . $this->_db->quote($id) . "'
			ORDER BY id DESC";
		$data = $this->_db->queryAll($sql);
		$versions = array();
		foreach ($data as $line)
			$versions[$line['id']] = $line;
		return ($versions);
	}
	/**
	 * Returns the list of a page's subscribers.
	 * @param	int		$id		Page's identifier.
	 * @param	int|array	$exclude	(optional) Identifier (or a list of identifiers) to exclude.
	 * @return	array	List of users.
	 */
	public function getSubscribers($id, $exclude=null) {
		if (!is_array($exclude))
			$exclude = array($exclude);
		$sql = "SELECT User.*
			FROM Subscription
				INNER JOIN User ON (Subscription.userId = User.id)
			WHERE Subscription.pageId = '" . $this->_db->quote($id) . "' ";
		if ($exclude)
			$sql .= "AND Subscription.userId NOT IN ('" . implode(', ', $exclude) . "')";
		$result = $this->_db->queryAll($sql);
		return ($result);
	}

	/* ***************** WRITE ************** */
	/**
	 * Create a new page.
	 * @param	int	$parentId	Parent page identifier.
	 * @param	int	$creatorId	Creator identifier.
	 * @param	string	$title		Page's title.
	 * @param	string	$skriv		SkrivML text.
	 * @param	string	$html		HTML text.
	 * @param	array	$toc		Table Of Contents.
	 * @return	int	Page's identifier.
	 */
	public function add($parentId, $creatorId, $title, $skriv, $html, $toc) {
		// add entry in PageVersion
		$sql = "INSERT INTO PageVersion
			SET title = '" . $this->_db->quote($title) . "',
			    skriv = '" . $this->_db->quote($skriv) . "',
			    creationDate = NOW(),
			    creatorId = '" . $this->_db->quote($creatorId) . "'";
		$this->_db->exec($sql);
		$versionId = $this->_db->lastInsertId();
		// get priority
		$sql = "SELECT MAX(`priority`) AS prio
			FROM Page
			WHERE parentPageId = '" . $this->_db->quote($parentId) . "'";
		$prio = $this->_db->queryOne($sql);
		// add entry in Page
		$id = $this->create(array(
			'title'			=> $title,
			'html'			=> $html,
			'toc'			=> json_encode($toc),
			'creatorId'		=> $creatorId,
			'creationDate'		=> substr(date('c'), 0, 19),
			'priority'		=> ($prio['prio'] + 1),
			'parentPageId'		=> $parentId,
			'currentVersionId'	=> $versionId
		));
		// update PageVersion
		$sql = "UPDATE PageVersion
			SET pageId = '$id'
			WHERE id = '$versionId'";
		$this->_db->exec($sql);
		// add a subscription
		$this->subscription($creatorId, $id, true);
		return ($id);
	}
	/**
	 * Update a page by adding a new version.
	 * @param	int	$id	Page's identifier.
	 * @param	int	$userId	Modifier's identifier.
	 * @param	string	$title	Page's title.
	 * @param	string	$skriv	SkrivML text.
	 * @param	string	$html	HTML text.
	 * @param	array	$toc	Table Of Content.
	 */
	public function addVersion($id, $userId, $title, $skriv, $html, $toc) {
		$sql = "INSERT INTO PageVersion
			SET title = '" . $this->_db->quote($title) . "',
			    skriv = '" . $this->_db->quote($skriv) . "',
			    creationDate = NOW(),
			    creatorId = '" . $this->_db->quote($userId) . "',
			    pageId = '" . $this->_db->quote($id) . "'";
		$this->_db->exec($sql);
		$versionId = $this->_db->lastInsertId();
		$this->update($id, array(
			'title'			=> $title,
			'html'			=> $html,
			'toc'			=> json_encode($toc),
			'modifDate'		=> substr(date('c'), 0, 19),
			'currentVersionId'	=> $versionId
		));
	}
	/**
	 * Remove a page.
	 * @param	int	$id	Page's identifier.
	 */
	public function remove($id) {
		$sql = "DELETE FROM PageVersion
			WHERE pageId = '" . $this->_db->quote($id) . "'";
		$this->_db->exec($sql);
		$sql = "DELETE FROM Page
			WHERE id = '" . $this->_db->quote($id) . "'";
		$this->_db->exec($sql);
	}
	/**
	 * Set subpages' priorities.
	 * @param	int	$parentPageId	Parent page's identifier.
	 * @param	array	$idList		Sub-pages' identifiers.
	 */
	public function setPriorities($parentPageId, $idList) {
		// reset
		$this->update($this->criteria()->equal('parentPageId', $parentPageId), array('priority' => 0));
		// new priorities
		$matches = null;
		$prio = 0;
		foreach ($idList as $id) {
			if (!preg_match('/\D*(\d+)$/', $id, $matches))
				continue;
			$sql = "UPDATE Page
				SET priority = $prio
				WHERE parentPageId = '" . $this->_db->quote($parentPageId) . "'
				  AND id = '" . $this->_db->quote($matches[1]) . "'";
			$this->_db->exec($sql);
			$prio++;
		}
	}
	/**
	 * Move a page.
	 * @param	int	$pageId		Page identifier.
	 * @param	int	$destinationId	Destination page identifier.
	 */
	public function move($pageId, $destinationId) {
		$destinationId = $this->_db->quote($destinationId);
		$sql = "SELECT IFNULL(MAX(priority), -1) AS prio
			FROM Page
			WHERE parentPageId = '$destinationId'";
		$result = $this->_db->queryOne($sql);
		$prio = $result['prio'] + 1;
		$sql = "UPDATE Page
			SET parentPageId = '$destinationId',
			    priority = $prio
			WHERE id = '" . $this->_db->quote($pageId) . "'";
		$this->_db->exec($sql);
	}
	/**
	 * Manage page subscription.
	 * @param	int	$userId		User's identifier.
	 * @param	int	$pageId		Page's identifier.
	 * @param	bool	$subscribed	Page subscription.
	 */
	public function subscription($userId, $pageId, $subscribed) {
		if (!$subscribed)
			$sql = "DELETE FROM Subscription
				WHERE userId = '" . $this->_db->quote($userId) . "'
				  AND pageId = '" . $this->_db->quote($pageId) . "'";
		else
			$sql = "INSERT INTO Subscription
				SET userId = '" . $this->_db->quote($userId) . "',
				    pageId = '" . $this->_db->quote($pageId) . "',
				    createDate = NOW()
				ON DUPLICATE KEY UPDATE id = id";
		$this->_db->exec($sql);
	}
}

