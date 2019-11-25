<?php

namespace Ark;

/**
 * DAO for pages management.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Lib
 */
class PageDao {
	/** Database object. */
	private $_db = null;

	/**
	 * Constructor.
	 * @param	\Temma\Base\Database	$db	Database connection object.
	 */
	public function __construct(\Temma\Base\Database $db) {
		$this->_db = $db;
	}

	/* ****************** READ **************** */
	/**
	 * Return the number of pages.
	 * @return	int	The number of pages.
	 */
	public function nbrPages() : int {
		$sql = "SELECT COUNT(*) AS n
			FROM Page";
		$n = $this->_db->queryOne($sql);
		return ($n['n']);
	}
	/**
	 * Search pages.
	 * @param	string	$s		Search string.
	 * @param	?bool	$private	True to search only private pages, false to search only non-private pages, null to search all pages. Null by default.
	 * @return	?array	List of associative arrays.
	 */
	public function search(string $s, ?bool $private) : ?array {
		$sql = "SELECT id, title, html
			FROM Page
			WHERE MATCH(title, html) AGAINST (" . $this->_db->quote($s) . " IN NATURAL LANGUAGE MODE) ";
		if ($private === true)
			$sql .= "AND isPrivate = TRUE ";
		else if ($private === false)
			$sql .= "AND isPrivate = FALSE ";
		$result = $this->_db->queryAll($sql);
		return ($result);
	}
	/**
	 * Returns a page.
	 * @param	int	$id		Page's identifier.
	 * @param	?int	$versionId	(optional) Identifier of the version to fetch.
	 * @param	?int	$userId		(optional) If given, fetch the user's subscription on the page.
	 * @return	array	Associative array.
	 */
	public function get(int $id, ?int $versionId=null, ?int $userId=null) : array {
		$sql = "SELECT	Page.*,
				PageVersion.html AS sourceHtml,
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
			$sql .= "LEFT OUTER JOIN Subscription ON (Subscription.pageId = Page.id AND Subscription.userId = " . $this->_db->quote($userId) . ") ";
		$sql .= "WHERE Page.id = " . $this->_db->quote($id) . " ";
		if (isset($versionId))
			$sql .= "AND PageVersion.id = " . $this->_db->quote($versionId) . " ";
		else
			$sql .= "AND PageVersion.id = Page.currentVersionId ";
		$page = $this->_db->queryOne($sql);
		if ($page && !empty($page['toc']))
			$page['toc'] = json_decode($page['toc'], true);
		return ($page);
	}
	/**
	 * Returns all pages, paginated and ordered by ID.
	 * @param	int	$pageOffset	(optional) Number of the page, starting at zero. 0 by default.
	 * @param	int	$nbrPerPage	(optional) Number of pages by pagination. 50 by default.
	 * @return	array	List of associative arrays.
	 */
	public function getPages(int $pageOffset=0, int $nbrPerPage=50) : array {
		$offset = $pageOffset * $nbrPerPage;
		$sql = "SELECT *
			FROM Page
			ORDER BY id
			LIMIT $offset, $nbrPerPage";
		$pages = $this->_db->queryAll($sql);
		return ($pages);
	}
	/**
	 * Returns all page versions, paginated and ordered by ID.
	 * @param	int	$pageOffset	(optional) Number of the page, starting at zero. 0 by default.
	 * @param	int	$nbrPerPage	(optional) Number of versions by pagination. 50 by default.
	 * @return	array	List of associative arrays.
	 */
	public function getPageVersions(int $pageOffset=0, int $nbrPerPage=50) : array {
		$offset = $pageOffset * $nbrPerPage;
		$sql = "SELECT *
			FROM PageVersion
			ORDER BY id
			LIMIT $offset, $nbrPerPage";
		$versions = $this->_db->queryAll($sql);
		return ($versions);
	}
	/**
	 * Returns sub-pages of a given page.
	 * @param	int	$id		(optional) Parent page identifier.
	 * @param	int	$excludeId	(optional) ID of a page to exclude from the list.
	 * @param	bool	$private	(optional) True to get only private pages, false to get only non-private pages, null to get all pages. Null by default.
	 * @return	array	List of associative arrays.
	 */
	public function getSubPages(int $id=0, ?int $excludeId=null, ?bool $private) : array {
		$sql = "SELECT *
			FROM Page
			WHERE parentPageId = " . $this->_db->quote($id) . " ";
		if ($excludeId)
			$sql .= "AND id != " . $this->_db->quote($excludeId) . " ";
		if (is_bool($private))
			$sql .= "AND isPrivate = " . ($private ? 'TRUE' : 'FALSE') . " ";
		$sql .= "ORDER BY `priority`";
		$pages = $this->_db->queryAll($sql);
		$pagesIds = [];
		$pagesList = [];
		foreach ($pages as $page) {
			$page['nbrChildren'] = 0;
			$pagesList[$page['id']] = $page;
			$pagesIds[] = $page['id'];
		}
		if ($pagesIds) {
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
	 * @return	array	List of associative arrays with id and title keys.
	 */
	public function getBreadcrumb(array $page) : array {
		$breadcrumb = [];
		$parentId = $page['parentPageId'] ?? 0;
		while ($parentId > 0) {
			$parent = $this->get($parentId);
			$breadcrumb[] = [
				'id'	=> $parentId,
				'title'	=> $parent['title']
			];
			$parentId = $parent['parentPageId'];
		}
		$breadcrumb = array_reverse($breadcrumb);
		return ($breadcrumb);
	}
	/**
	 * Get the list of versions of a page.
	 * @param	int	$id	Page's identifier.
	 * @return	array	List of associative arrays.
	 */
	public function getVersions(int $id) : array {
		$sql = "SELECT	PageVersion.id,
				PageVersion.title,
				PageVersion.creationDate,
				User.name
			FROM PageVersion
				INNER JOIN User On (User.id = PageVersion.creatorId)
			WHERE pageId = " . $this->_db->quote($id) . "
			ORDER BY id DESC";
		$data = $this->_db->queryAll($sql);
		$versions = [];
		foreach ($data as $line)
			$versions[$line['id']] = $line;
		return ($versions);
	}
	/**
	 * Return the diff between two versions.
	 * @param	int	$fromId	ID of the first version to compare.
	 * @param	int	$toId	ID of the second version to compare.
	 * @return	array	Array with titleDiff and skrivDiff.
	 */
	public function compareVersions(int $fromId, int $toId) {
		$sql = "SELECT  pageFrom.title AS fromTitle,
				pageFrom.html AS fromHtml,
				pageTo.title AS toTitle,
				pageTo.html AS toHtml
			FROM PageVersion pageFrom
				INNER JOIN PageVersion pageTo ON (pageFrom.pageId = pageTo.pageId)
			WHERE pageFrom.id = " . $this->_db->quote($fromId) . "
			  AND pageTo.id = " . $this->_db->quote($toId);
		$versions = $this->_db->queryOne($sql);
		require_once('finediff.php');
		$result = [];
		// compare titles
		$fromTitle = mb_convert_encoding($versions['toTitle'], 'HTML-ENTITIES', 'UTF-8');
		$toTitle = mb_convert_encoding($versions['fromTitle'], 'HTML-ENTITIES', 'UTF-8');
		$finediff = new \FineDiff($fromTitle, $toTitle, \FineDiff::$wordGranularity);
		$diffResult = $finediff->renderDiffToHTML();
		$diffResult = htmlspecialchars_decode(htmlspecialchars_decode($diffResult));
		$result[] = $diffResult;
		// compare texts
		$fromText = $versions['toHtml'];
		$toText = $versions['fromHtml'];
		$finediff = new \HtmlDiff\HtmlDiff($fromText, $toText);
		$finediff->build();
		$diffResult = $finediff->getDifference();
		$result[] = $diffResult;
		return ($result);
	}
	/**
	 * Returns the list of a page's subscribers.
	 * @param	int		$id		Page's identifier.
	 * @param	int|array	$exclude	(optional) Identifier (or a list of identifiers) to exclude.
	 * @return	array	List of users.
	 */
	public function getSubscribers(int $id, /* int|array */ $exclude=null) : array {
		if (!is_array($exclude))
			$exclude = [$exclude];
		$sql = "SELECT User.*
			FROM Subscription
				INNER JOIN User ON (Subscription.userId = User.id)
			WHERE Subscription.pageId = " . $this->_db->quote($id) . " ";
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
	 * @param	string	$html		HTML text.
	 * @param	?array	$toc		Table Of Contents.
	 * @param	bool	$isPrivate	True if the page is private. False otherwise.
	 * @return	int	Page's identifier.
	 */
	public function add(int $parentId, int $creatorId, string $title, string $html, ?array $toc, bool $isPrivate) : int {
		// add entry in PageVersion
		$sql = "INSERT INTO PageVersion
			SET title = " . $this->_db->quote($title) . ",
			    html = " . $this->_db->quote($html) . ",
			    isPrivate = " . ($isPrivate ? 'TRUE' : 'FALSE') . ",
			    creationDate = NOW(),
			    creatorId = " . $this->_db->quote($creatorId);
		$this->_db->exec($sql);
		$versionId = $this->_db->lastInsertId();
		// get priority
		$sql = "SELECT MAX(`priority`) AS prio
			FROM Page
			WHERE parentPageId = " . $this->_db->quote($parentId);
		$prio = $this->_db->queryOne($sql);
		// add entry in Page
		$sql = "INSERT INTO Page
			SET title = " . $this->_db->quote($title) . ",
			    html = " . $this->_db->quote($html) . ",
			    toc = " . $this->_db->quote(json_encode($toc)) . ",
			    creatorId = " . $this->_db->quote($creatorId) . ",
			    creationDate = NOW(),
			    modifDate = NOW(),
			    priority = " . ($prio['prio'] + 1) . ",
			    isPrivate = " . ($isPrivate ? 'TRUE' : 'FALSE') . ",
			    parentPageId = " . $this->_db->quote($parentId) . ",
			    currentVersionId = " . $this->_db->quote($versionId);
		$this->_db->exec($sql);
		$id = $this->_db->lastInsertId();
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
	 * @param	int	$id		Page's identifier.
	 * @param	int	$userId		Modifier's identifier.
	 * @param	string	$title		Page's title.
	 * @param	string	$html		HTML text.
	 * @param	array	$toc		Table Of Content.
	 * @param	bool	$isPrivate	True if the page is private. False otherwise.
	 */
	public function addVersion(int $id, int $userId, string $title, string $html, ?array $toc, bool $isPrivate) /* : void */ {
		$sql = "INSERT INTO PageVersion
			SET title = " . $this->_db->quote($title) . ",
			    html = " . $this->_db->quote($html) . ",
			    isPrivate = " . ($isPrivate ? 'TRUE' : 'FALSE') . ",
			    creationDate = NOW(),
			    creatorId = " . $this->_db->quote($userId) . ",
			    pageId = " . $this->_db->quote($id);
		$this->_db->exec($sql);
		$versionId = $this->_db->lastInsertId();
		$sql = "UPDATE Page
			SET title = " . $this->_db->quote($title) . ",
			    html = " . $this->_db->quote($html) . ",
			    toc = " . $this->_db->quote(json_encode($toc)) . ",
			    isPrivate = " . ($isPrivate ? 'TRUE' : 'FALSE') . ",
			    modifDate = NOW(),
			    currentVersionId = '$versionId'
			WHERE id = " . $this->_db->quote($id);
		$this->_db->exec($sql);
	}
	/**
	 * Remove a page.
	 * @param	int	$id	Page's identifier.
	 */
	public function remove(int $id) /* : void */ {
		$sql = "DELETE FROM PageVersion
			WHERE pageId = " . $this->_db->quote($id);
		$this->_db->exec($sql);
		$sql = "DELETE FROM Page
			WHERE id = " . $this->_db->quote($id);
		$this->_db->exec($sql);
	}
	/**
	 * Set subpages' priorities.
	 * @param	int	$parentPageId	Parent page's identifier.
	 * @param	array	$idList		Sub-pages' identifiers.
	 */
	public function setPriorities(int $parentPageId, array $idList) /* : void */ {
		// reset
		$sql = "UPDATE Page
			SET `priority` = 0
			WHERE parentPageId = " . $this->_db->quote($parentPageId);
		$this->_db->exec($sql);
		// new priorities
		$matches = null;
		$prio = 0;
		foreach ($idList as $id) {
			if (!preg_match('/\D*(\d+)$/', $id, $matches))
				continue;
			$sql = "UPDATE Page
				SET priority = $prio
				WHERE parentPageId = " . $this->_db->quote($parentPageId) . "
				  AND id = " . $this->_db->quote($matches[1]);
			$this->_db->exec($sql);
			$prio++;
		}
	}
	/**
	 * Move a page.
	 * @param	int	$pageId		Page identifier.
	 * @param	int	$destinationId	Destination page identifier.
	 */
	public function move(int $pageId, int $destinationId) /* : void */ {
		$destinationId = $this->_db->quote($destinationId);
		$sql = "SELECT IFNULL(MAX(priority), -1) AS prio
			FROM Page
			WHERE parentPageId = $destinationId";
		$result = $this->_db->queryOne($sql);
		$prio = $result['prio'] + 1;
		$sql = "UPDATE Page
			SET parentPageId = $destinationId,
			    priority = $prio
			WHERE id = " . $this->_db->quote($pageId);
		$this->_db->exec($sql);
	}
	/**
	 * Manage page subscription.
	 * @param	int	$userId		User's identifier.
	 * @param	int	$pageId		Page's identifier.
	 * @param	bool	$subscribed	Page subscription.
	 */
	public function subscription(int $userId, int $pageId, int $subscribed) /* : void */ {
		if (!$subscribed)
			$sql = "DELETE FROM Subscription
				WHERE userId = " . $this->_db->quote($userId) . "
				  AND pageId = " . $this->_db->quote($pageId);
		else
			$sql = "INSERT INTO Subscription
				SET userId = " . $this->_db->quote($userId). ",
				    pageId = " . $this->_db->quote($pageId) . ",
				    createDate = NOW()
				ON DUPLICATE KEY UPDATE id = id";
		$this->_db->exec($sql);
	}
}

