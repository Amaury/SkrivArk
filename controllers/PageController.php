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
	 * @param	string	$title	(optional) Page's title.
	 */
	public function execShow($id=0, $title=null) {
		FineLog::log('skriv', 'DEBUG', "Show action.");
		if ($this->get('CONTROLLER') == 'page' && (!$id || !is_numeric($id))) {
			$this->redirect('/');
			return (self::EXEC_HALT);
		}
		$conf = $this->get('conf');
		// get page's data
		$user = $this->get('user');
		$userId = isset($user) ? $user['id'] : 0;
		$page = $this->_pageDao->get($id, null, $userId);
		if ($id != 0 && isset($conf['titledUrl']) && $conf['titledUrl'] === true) {
			// check page's title
			$pageTitle = TextUtil::titleToUrl($page['title']);
			if ($pageTitle != $title) {
				$this->redirect("/page/show/$id/$pageTitle");
				return;
			}
		}
		// get breadcrumb
		$breadcrumb = $this->_pageDao->getBreadcrumb($page);
		$this->set('breadcrumb', $breadcrumb);
		// manage sub-page
		$showAsSubPage = (!$user && $id && !$page['nbrChildren']) ? true : false;
		// get subpages
		if ($showAsSubPage)
			$subPages = $this->_pageDao->getSubPages($page['parentPageId']);
		else
			$subPages = $this->_pageDao->getSubPages($id);
		foreach ($subPages as &$subPage) {
			$intro = substr(strip_tags($subPage['html']), 0, 120);
			$intro .= (strlen($intro) >= 120) ? ' (...)' : '';
			$subPage['intro'] = $intro;
			if (isset($conf['titledURL']) && $conf['titledURL'] === true)
				$subPage['titledUrl'] = TextUtil::titleToUrl($subPage['title']);
		}
		$this->set('page', $page);
		$this->set('subPages', $subPages);
		$this->set('showAsSubPage', $showAsSubPage);
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
		$user = $this->get('user');
		if (isset($user))
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
		// check user
		$user = $this->get('user');
		if (!isset($user)) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
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
		// check user
		$user = $this->get('user');
		if (!isset($user)) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
		// get data
		$title = trim($_POST['title']);
		if (empty($title)) {
			$this->_session->set('editContent', $_POST['content']);
			$this->redirect("/page/edit/$id");
			return (self::EXEC_HALT);
		}
		list($html, $toc) = $this->_render($_POST['content']);
		$currentUser = $this->get('user');
		// update the page
		$this->_pageDao->addVersion($id, $currentUser['id'], $title, $_POST['content'], $html, $toc);
		// warn all subscribers
		$subscribers = $this->_pageDao->getSubscribers($id, $currentUser['id']);
		if (!empty($subscribers)) {
			$conf = $this->get('conf');
			$recipients = array();
			foreach ($subscribers as $subscriber)
				$recipients[] = $subscriber['email'];
			$headers = "MIME-Version: 1.0\r\n" .
				   "Content-type: text/html; charset=utf8\r\n" .
				   "From: " . $conf['emailSender'] . "\r\n" .
				   "Bcc: " . implode(',', $recipients);
			$msg = "<html><body>
					<h1>" . htmlspecialchars($conf['sitename']) . "</h1>
					<p>Hi,</p>
					<p>
						" . htmlspecialchars($currentUser['name']) . " has modified the page
						«&nbsp;<em><a href=\"" . htmlspecialchars($conf['baseURL']) . "/page/show/$id\">". htmlspecialchars($title) . "</a></em>&nbsp;».
					</p>
					<p>
						Best regards,<br />
						The Skriv Team
					</p>
				</body></html>";
			mail(null, '[' . $conf['sitename'] . '] Page Modification', $msg, $headers);
		}
		// redirection
		$this->redirect("/page/show/$id");
	}
	/**
	 * Show the creation form.
	 * @param	int	$parentId	Identifier of the parent page.
	 */
	public function execCreate($parentId) {
		// check user
		$user = $this->get('user');
		if (!isset($user)) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
		// get data
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
		// check user
		$user = $this->get('user');
		if (!isset($user)) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
		$title = trim($_POST['title']);
		// get data
		if (empty($title)) {
			$this->_session->set('editContent', $_POST['content']);
			$this->redirect("/page/create/$id");
			return (self::EXEC_HALT);
		}
		list($html, $toc) = $this->_render($_POST['content']);
		$currentUser = $this->get('user');
		// create the new page
		$id = $this->_pageDao->add($parentId, $currentUser['id'], $title, $_POST['content'], $html, $toc);
		// is there subscribers to the parent page?
		if ($parentId) {
			$subscribers = $this->_pageDao->getSubscribers($parentId, $currentUser['id']);
			if (!empty($subscribers)) {
				$conf = $this->get('conf');
				$recipients = array();
				foreach ($subscribers as $subscriber)
					$recipients[] = $subscriber['email'];
				$headers = "MIME-Version: 1.0\r\n" .
					   "Content-type: text/html; charset=utf8\r\n" .
					   "From: " . $conf['emailSender'] . "\r\n" .
					   "Bcc: " . implode(',', $recipients);
				$msg = "<html><body>
						<h1>" . htmlspecialchars($conf['sitename']) . "</h1>
						<p>Hi,</p>
						<p>
							" . htmlspecialchars($currentUser['name']) . " has created the page
							«&nbsp;<em><a href=\"" . htmlspecialchars($conf['baseURL']) . "/page/show/$id\">". htmlspecialchars($title) . "</a></em>&nbsp;».
						</p>
						<p>
							Best regards,<br />
							The Skriv Team
						</p>
					</body></html>";
				mail(null, '[' . $conf['sitename'] . '] Page Creation', $msg, $headers);
			}
		}
		// redirection
		$this->redirect("/page/show/$id");
	}
	/** Convert a SkrivML text into HTML (used in edit page). */
	public function execConvert() {
		FineLog::log('skriv', 'INFO', "Convert action.");
		list($html, $toc) = $this->_render($_POST['text']);
		print($html);
		return (self::EXEC_QUIT);
	}
	/**
	 * Remove a page.
	 * @param	int	$id	Page's identifier.
	 */
	public function execRemove($id) {
		// check user
		$user = $this->get('user');
		if (!isset($user)) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
		// get data
		$page = $this->_pageDao->get($id);
		$subPages = $this->_pageDao->getSubPages($id);
		if (isset($subPages) && !empty($subPages)) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_HALT);
		}
		// warn all subscribers
		$currentUser = $this->get('user');
		$subscribers = $this->_pageDao->getSubscribers($id, $currentUser['id']);
		if (!empty($subscribers)) {
			$conf = $this->get('conf');
			$recipients = array();
			foreach ($subscribers as $subscriber)
				$recipients[] = $subscriber['email'];
			$headers = "MIME-Version: 1.0\r\n" .
				   "Content-type: text/html; charset=utf8\r\n" .
				   "From: " . $conf['emailSender'] . "\r\n" .
				   "Bcc: " . implode(',', $recipients);
			$msg = "<html><body>
					<h1>" . htmlspecialchars($conf['sitename']) . "</h1>
					<p>Hi,</p>
					<p>
						" . htmlspecialchars($currentUser['name']) . " has removed the page
						«&nbsp;<em>". htmlspecialchars($page['title']) . "</em>&nbsp;».
					</p>
					<p>
						Best regards,<br />
						The Skriv Team
					</p>
				</body></html>";
			mail(null, '[' . $conf['sitename'] . '] Page Deleted', $msg, $headers);
		}
		// remove the page
		$this->_pageDao->remove($id);
		// redirection
		$this->redirect("/page/show/" . $page['parentPageId']);
	}
	/**
	 * Define subpages' priorities.
	 * @param	int	$id	Page's identifier.
	 */
	public function execSetPriorities($id) {
		$this->view('\Temma\Views\JsonView');
		// check user
		$user = $this->get('user');
		if (!isset($user)) {
			$this->set('json', 0);
			return (self::EXEC_FORWARD);
		}
		// set priorities
		$this->_pageDao->setPriorities($id, $_POST['prio']);
		$this->set('json', 1);
	}
	/**
	 * Manage page subscription.
	 * @param	int	$pageId		Page's identifier.
	 * @param	int	$subscribed	1 if the user subscribed to the page.
	 */
	public function execSubscription($pageId, $subscribed) {
		// check user
		$user = $this->get('user');
		if (!isset($user)) {
			return (self::EXEC_QUIT);
		}
		// set subscription
		$conf = $this->get('conf');
		if (isset($conf['demoMode']) && $conf['demoMode'])
			return (self::EXEC_QUIT);
		$user = $this->get('user');
		$this->_pageDao->subscription($user['id'], $pageId, ($subscribed ? true : false));
		$this->view('\Temma\View\JsonView');
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
	 * @return	array	Array with the HTML result, and the raw Table Of Contents.
	 */
	private function _render($text) {
		$params = array(
			'firstTitleLevel'	=> 2,
			'addFootnotes'		=> true,
			'codeInlineStyles'	=> true,
		);
		$skrivRenderer = \Skriv\Markup\Renderer::factory('html', $params);
		$html = $skrivRenderer->render($text);
		$toc = $skrivRenderer->getToc(true);
		return (array($html, $toc));
	}
}

