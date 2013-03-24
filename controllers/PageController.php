<?php

/**
 * Page controller.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class PageController extends \Temma\Controller {
	/** User DAO. */
	private $_userDao = null;
	/** Page DAO. */
	private $_pageDao = null;

	/** Init. */
	public function init() {
		FineLog::log('skriv', 'DEBUG', "Init.");
		$this->_userDao = $this->loadDao('UserDao');
		$this->_pageDao = $this->loadDao('PageDao');
		FineLog::log('skriv', 'DEBUG', "End of init.");
	}
	/** Root page. */
	public function execIndex() {
		$this->execShow(0);
		$splashscreen = file_get_contents(__DIR__ . '/../var/splashscreen.html');
		$this->set('splashscreen', $splashscreen);
		$this->template('page/show.tpl');
	}
	/**
	 * Displays a page.
	 * @param	int	$id	Page's identifier.
	 */
	public function execShow($id=0) {
		FineLog::log('skriv', 'DEBUG', "Show action.");
		if ($this->get('CONTROLLER') == 'page' && (!$id || !is_numeric($id))) {
			$this->redirect('/');
			return (self::EXEC_HALT);
		}
		// get page's data
		$page = $this->_pageDao->get($id);
		$breadcrumb = $this->_pageDao->getBreadcrumb($page);
		$this->set('breadcrumb', $breadcrumb);
		// get subpages
		$subPages = $this->_pageDao->getSubPages($id);
		$this->set('page', $page);
		$this->set('subPages', $subPages);
		// get level 0 subpages (for page move)
		$level0 = $this->_pageDao->getSubPages(0);
		$this->set('subLevelPages', $level0);
	}
	/**
	 * Returns a chunk of HTML, to select a subpage for moving a page.
	 * @param	int	$pageId		Current page idnetifier.
	 * @param	int	$parentId	Parent level identifier.
	 */
	public function execGetSubLevels($pageId, $parentLevelId=0) {
		FineLog::log('skriv', 'INFO', "GetSubLevels action.");
		$this->set('page', array('id' => $pageId));
		$this->set('parentSubLevelId', $parentLevelId);
		$sub = $this->_pageDao->getSubPages($parentLevelId);
		$this->set('subLevelPages', $sub);
	}
	/**
	 * Move a page.
	 * @param	int	$id		Page identifier.
	 * @param	int	$destinationId	Destination page identifier.
	 */
	public function execMove($id, $destinationId) {
		$this->_pageDao->move($id, $destinationId);
		$this->redirect("/page/show/$id");
	}
	/**
	 * Show the edition form.
	 * @param	int	$id		Identifier of the page to modify.
	 * @param	int	$versionId	(optional) Identifier of the version to edit.
	 */
	public function execEdit($id, $versionId=null) {
		FineLog::log('skriv', 'DEBUG', "Edit action ($id).");
		// get page's data
		$page = $this->_pageDao->get($id, $versionId);
		$breadcrumb = $this->_pageDao->getBreadcrumb($page);
		$this->set('editContent', $this->_session->get('editContent'));
		$this->_session->set('editContent', null);
		$this->set('page', $page);
		$this->set('breadcrumb', $breadcrumb);
	}
	/**
	 * Update a page.
	 * @param	int	$id	Page's identifier.
	 */
	public function execStoreEdit($id) {
		FineLog::log('skriv', 'DEBUG', "StoreEdit($id) action.");
		$title = trim($_POST['title']);
		if (empty($title)) {
			$this->_session->set('editContent', $_POST['content']);
			$this->redirect("/page/edit/$id");
			return (self::EXEC_HALT);
		}
		$html = $this->_render($_POST['content']);
		$user = $this->get('user');
		$this->_pageDao->addVersion($id, $user['id'], $title, $_POST['content'], $html);
		$this->redirect("/page/show/$id");
	}
	/**
	 * Show the creation form.
	 * @param	int	$parentId	Identifier of the parent page.
	 */
	public function execCreate($parentId) {
		$parentPage = $this->_pageDao->get($parentId);
		$breadcrumb = $this->_pageDao->getBreadcrumb($parentPage);
		$breadcrumb = is_array($breadcrumb) ? $breadcrumb : array();
		if (isset($parentPage))
			$breadcrumb[] = $parentPage;
		$this->set('editContent', $this->_session->get('editContent'));
		$this->_session->set('editContent', null);
		$this->set('page', $page);
		$this->set('breadcrumb', $breadcrumb);
		$this->set('parentId', $parentId);
		$this->template('page/edit.tpl');
	}
	/**
	 * Store a new page.
	 * @param	int	$parentId	Identifier of the parent page.
	 */
	public function execStoreCreate($parentId) {
		FineLog::log('skriv', 'DEBUG', "StoreEdit($parentId) action.");
		$title = trim($_POST['title']);
		if (empty($title)) {
			$this->_session->set('editContent', $_POST['content']);
			$this->redirect("/page/create/$id");
			return (self::EXEC_HALT);
		}
		$html = $this->_render($_POST['content']);
		$user = $this->get('user');

		$id = $this->_pageDao->add($parentId, $user['id'], $title, $_POST['content'], $html);
		$this->redirect("/page/show/$id");
	}
	/** Convert a SkrivML text into HTML (used in edit page). */
	public function execConvert() {
		FineLog::log('skriv', 'INFO', "Convert action.");
		$html = $this->_render($_POST['text']);
		print($html);
		return (self::EXEC_QUIT);
	}
	/**
	 * Remove a page.
	 * @param	int	$id	Page's identifier.
	 */
	public function execRemove($id) {
		$page = $this->_pageDao->get($id);
		$subPages = $this->_pageDao->getSubPages($id);
		if (isset($subPages) && !empty($subPages)) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_HALT);
		}
		$this->_pageDao->remove($id);
		$this->redirect("/page/show/" . $page['parentPageId']);
	}
	/**
	 * Define subpages' priorities.
	 * @param	int	$id	Page's identifier.
	 */
	public function execSetPriorities($id) {
		$this->_pageDao->setPriorities($id, $_POST['prio']);
		$this->view('\Temma\Views\JsonView');
		$this->set('json', 1);
	}
	/**
	 * Shows the list of versions of a page.
	 * @param	int	$id		Page's identifier.
	 * @param	int	$versionFrom	(optional) Identifier of the first version to compare.
	 * @param	int	$versionTo	(optional) Identifier of the second version to compare.
	 */
	public function execVersions($id, $versionFrom=null, $versionTo=null) {
		FineLog::log('skriv', 'DEBUG', "Versions action.");
		if ((isset($versionFrom) && !isset($versionTo)) ||
		    (!isset($versionFrom) && isset($versionTo)) ||
		    (isset($versionFrom) && !is_numeric($versionTo)) ||
		    (isset($versionTo) && !is_numeric($versionTo))) {
			$this->redirect("/page/versions/$id");
			return (self::EXEC_HALT);
		}
		if (isset($versionFrom) && isset($versionTo) && $versionFrom < $versionTo) {
			$this->redirect("/page/versions/$id/$versionTo/$versionFrom");
			return (self::EXEC_HALT);
		}
		// fetch data, from the 'show' action
		$res = $this->execShow($id);
		if ($res != self::EXEC_FORWARD)
			return ($res);
		// get the list of versions
		$versions = $this->_pageDao->getVersions($id);
		$this->set('versions', $versions);
		if ((isset($versionFrom) && !isset($versions[$versionFrom])) ||
		    (isset($versionTo) && !isset($versions[$versionTo]))) {
			$this->redirect("/page/versions/$id");
			return (self::EXEC_HALT);
		}
		if (!isset($versionFrom)) {
			foreach ($versions as $subId => $subVal) {
				if (!isset($versionFrom)) {
					$versionFrom = $subId;
					continue;
				}
				if (!isset($versionTo))
					$versionTo = $subId;
				break;
			}
			$this->redirect("/page/versions/$id/$versionFrom/$versionTo");
			return (self::EXEC_HALT);
		}
		// version numbers
		$this->set('versionFrom', $versionFrom);
		$this->set('versionTo', $versionTo);
		// get versions for diff
		$sql = "SELECT	pageFrom.title AS fromTitle,
				pageFrom.skriv AS fromSkriv,
				pageTo.title AS toTitle,
				pageTo.skriv AS toSkriv
			FROM PageVersion pageFrom
				INNER JOIN PageVersion pageTo ON (pageFrom.pageId = pageTo.pageId)
			WHERE pageFrom.id = '" . $this->_db->quote($versionFrom) . "'
			  AND pageTo.id = '" . $this->_db->quote($versionTo) . "'";
		$versions = $this->_db->queryOne($sql);
		require_once('finediff.php');
		// compare titles
		$fromTitle = mb_convert_encoding($versions['toTitle'], 'HTML-ENTITIES', 'UTF-8');
		$toTitle = mb_convert_encoding($versions['fromTitle'], 'HTML-ENTITIES', 'UTF-8');
		$finediff = new FineDiff($fromTitle, $toTitle, FineDiff::$wordGranularity);
		$diffResult = $finediff->renderDiffToHTML();
		$diffResult = htmlspecialchars_decode(htmlspecialchars_decode($diffResult));
		$this->set('titleDiff', $diffResult);
		// compare texts
		$fromText = mb_convert_encoding($versions['toSkriv'], 'HTML-ENTITIES', 'UTF-8');
		$toText = mb_convert_encoding($versions['fromSkriv'], 'HTML-ENTITIES', 'UTF-8');
		$finediff = new FineDiff($fromText, $toText, FineDiff::$wordGranularity);
		$diffResult = $finediff->renderDiffToHTML();
		$diffResult = htmlspecialchars_decode(htmlspecialchars_decode($diffResult));
		$this->set('skrivDiff', $diffResult);
	}

	/* *********** PRIVATE METHODS ************* */
	/**
	 * Render a SkrivML text in HTML.
	 * @param	string	$text	SkrivML text.
	 * @return	string	HTML result.
	 */
	private function _render($text) {
		$params = array(
			'firstTitleLevel'	=> 2
		);
		$skrivRenderer = \Skriv\Markup\Renderer::factory('html', $params);
		$html = $skrivRenderer->render($text);
		return ($html);
	}
}

