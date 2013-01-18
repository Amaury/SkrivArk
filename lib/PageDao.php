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
	 * @param	int	$id	Page's identifier.
	 * @return	array	Hash.
	 */
	public function get($id) {
		$sql = "SELECT	Page.*,
				creator.name AS creatorName,
				modifier.name AS modifierName
			FROM Page
				INNER JOIN User creator ON (creator.id = Page.creatorId)
				LEFT OUTER JOIN User modifier ON (modifier.id = Page.modifierId)
			WHERE Page.id = '" . $this->_db->quote($id) . "'";
		return ($this->_db->queryOne($sql));
	}
	/**
	 * Returns sub-pages of a given page.
	 * @param	int	$id	(optional) Parent page identifier.
	 * @return	array	List of hashes.
	 */
	public function getSubPages($id=0) {
		$criteria = $this->criteria()->equal('parentPageId', $id);
		return ($this->search($criteria, 'priority', null, null));
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

	/* ***************** WRITE ************** */
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
}

