<?php

use \Temma\Base\Log as TµLog;

/**
 * Page controller.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Controllers
 */
class Page extends \Temma\Web\Controller {
	/** Root page. */
	public function index() {
		if ($this['URL'] != '/') {
			$this->redirect('/');
			return (self::EXEC_HALT);
		}
		$this->show(0);
		$splashscreen = file_get_contents(__DIR__ . '/../var/splashscreen.html');
		$this['splashscreen'] = $splashscreen;
		$this->template('page/show.tpl');
	}
	/**
	 * Displays a page.
	 * @param	int	$id	Page's identifier.
	 * @param	string	$title	(optinal) Page's title. Optional only when the $id parameter is set to zero.
	 */
	public function show(int $id=0, string $title=null) {
		TµLog::log('ark', 'DEBUG', "Show action.");
		// check root page
		if ($this['CONTROLLER'] == 'page' && (!$id || !is_numeric($id))) {
			$this->redirect('/');
			return (self::EXEC_HALT);
		}
		// get page's data
		$userId = $this['user']['id'] ?? 0;
		$page = $this->_loader->pageDao->get($id, null, $userId);
		if ($id != 0) {
			// check page's title
			$pageTitle = \Temma\Utils\Text::urlize($page['title']);
			if ($pageTitle != $title) {
				$this->redirect("/page/show/$id/$pageTitle");
				return (self::EXEC_HALT);
			}
		}
		// get breadcrumb
		$breadcrumb = $this->_loader->pageDao->getBreadcrumb($page);
		$this['breadcrumb'] = $breadcrumb;
		// manage sub-page
		$showAsSubPage = (!$this['user'] && $id && !$page['nbrChildren']) ? true : false;
		// get subpages
		if ($showAsSubPage)
			$subPages = $this->_loader->pageDao->getSubPages($page['parentPageId']);
		else
			$subPages = $this->_loader->pageDao->getSubPages($id);
		foreach ($subPages as &$subPage) {
			$subPage['url'] = \Temma\Utils\Text::urlize($subPage['title']);
		}
		$this['page'] = $page;
		$this['subPages'] = $subPages;
		$this['showAsSubPage'] = $showAsSubPage;
		// get level 0 subpages (for page move)
		$level0 = $this->_loader->pageDao->getSubPages(0);
		$this['subLevelPages'] = $level0;
	}
	/**
	 * Returns a chunk of HTML, to select a subpage for moving a page.
	 * @param	int	$pageId		Current page idnetifier.
	 * @param	int	$parentId	Parent level identifier.
	 */
	public function getSubLevels(int $pageId, int $parentLevelId=0) {
		TµLog::log('ark', 'INFO', "GetSubLevels action.");
		$this['page'] = ['id' => $pageId];
		$this['parentSubLevelId'] = $parentLevelId;
		$sub = $this->_loader->pageDao->getSubPages($parentLevelId);
		$this['subLevelPages'] = $sub;
	}
	/**
	 * Move a page.
	 * @param	int	$id		Page identifier.
	 * @param	int	$destinationId	Destination page identifier.
	 */
	public function move(int $id, int $destinationId) {
		if (isset($this['user']))
			$this->_loader->pageDao->move($id, $destinationId);
		$this->redirect("/page/show/$id");
	}
	/**
	 * Show the edition form.
	 * @param	int	$id		Identifier of the page to modify.
	 * @param	int	$versionId	(optional) Identifier of the version to edit.
	 */
	public function edit(int $id, int $versionId=null) {
		TµLog::log('ark', 'DEBUG', "Edit action ($id).");
		// check user
		if (!isset($this['user'])) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
		// get page's data
		$page = $this->_loader->pageDao->get($id, $versionId);
		[$html, $toc] = $this->_render($page['skriv']);
		$page['html'] = $html;
		$this['page'] = $page;
		$this['editContent'] = $this->_loader->session['editContent'];
		unset($this->_loader->session['editContent']);
	}
	/**
	 * Update a page.
	 * @param	int	$id	Page's identifier.
	 */
	public function storeEdit(int $id) {
		TµLog::log('ark', 'DEBUG', "StoreEdit($id) action.");
		// check user
		if (!isset($this['user'])) {
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
		[$html, $toc] = $this->_render($_POST['content']);
		// update the page
		$this->_loader->pageDao->addVersion($id, $this['user']['id'], $title, $_POST['content'], $html, $toc);
		// warn all subscribers
		$subscribers = $this->_loader->pageDao->getSubscribers($id, $this['user']['id']);
		if (!empty($subscribers)) {
			$recipients = [];
			foreach ($subscribers as $subscriber)
				$recipients[] = $subscriber['email'];
			$headers = "MIME-Version: 1.0\r\n" .
				   "Content-type: text/html; charset=utf8\r\n" .
				   "From: " . $this['conf']['emailSender'] . "\r\n" .
				   "Bcc: " . implode(',', $recipients);
			$msg = "<html><body>
					<h1>" . htmlspecialchars($this['conf']['sitename']) . "</h1>
					<p>Hi,</p>
					<p>
						" . htmlspecialchars($this['user']['name']) . " has modified the page
						«&nbsp;<em><a href=\"" . htmlspecialchars($this['conf']['baseURL']) . "/page/show/$id\">". htmlspecialchars($title) . "</a></em>&nbsp;».
					</p>
					<p>
						Best regards,<br />
						The Skriv Team
					</p>
				</body></html>";
			mail(null, '[' . $this['conf']['sitename'] . '] Page Modification', $msg, $headers);
		}
		// redirection
		$this->redirect("/page/show/$id");
	}
	/**
	 * Show the creation form.
	 * @param	int	$parentId	Identifier of the parent page.
	 */
	public function create(int $parentId) {
		// check user
		if (!isset($this['user'])) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
		// get data
		$parentPage = $this->_loader->pageDao->get($parentId);
		$breadcrumb = $this->_loader->pageDao->getBreadcrumb($parentPage);
		$breadcrumb = is_array($breadcrumb) ? $breadcrumb : [];
		if (isset($parentPage))
			$breadcrumb[] = $parentPage;
		$this['editContent'] = $this->_loader->session->get('editContent');
		$this->_loader->session->set('editContent', null);
		$this['page'] = $page;
		$this['breadcrumb'] = $breadcrumb;
		$this['parentId'] = $parentId;
		$this->template('page/edit.tpl');
	}
	/**
	 * Store a new page.
	 * @param	int	$parentId	Identifier of the parent page.
	 */
	public function storeCreate(int $parentId) {
		TµLog::log('ark', 'DEBUG', "StoreEdit($parentId) action.");
		// check user
		if (!isset($this['user'])) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
		$title = trim($_POST['title']);
		// get data
		if (empty($title)) {
			$this->_loader->session->set('editContent', $_POST['content']);
			$this->redirect("/page/create/$id");
			return (self::EXEC_HALT);
		}
		[$html, $toc] = $this->_render($_POST['content']);
		// create the new page
		$id = $this->_loader->pageDao->add($parentId, $this['user']['id'], $title, $_POST['content'], $html, $toc);
		// is there subscribers to the parent page?
		if ($parentId) {
			$subscribers = $this->_loader->pageDao->getSubscribers($parentId, $this['user']['id']);
			if (!empty($subscribers)) {
				$recipients = [];
				foreach ($subscribers as $subscriber)
					$recipients[] = $subscriber['email'];
				$headers = "MIME-Version: 1.0\r\n" .
					   "Content-type: text/html; charset=utf8\r\n" .
					   "From: " . $this['conf']['emailSender'] . "\r\n" .
					   "Bcc: " . implode(',', $recipients);
				$msg = "<html><body>
						<h1>" . htmlspecialchars($this['conf']['sitename']) . "</h1>
						<p>Hi,</p>
						<p>
							" . htmlspecialchars($this['user']['name']) . " has created the page
							«&nbsp;<em><a href=\"" . htmlspecialchars($this['conf']['baseURL']) . "/page/show/$id\">". htmlspecialchars($title) . "</a></em>&nbsp;».
						</p>
						<p>
							Best regards,<br />
							The Skriv Team
						</p>
					</body></html>";
				mail(null, '[' . $this['conf']['sitename'] . '] Page Creation', $msg, $headers);
			}
		}
		// redirection
		$this->redirect("/page/show/$id");
	}
	/** Convert a SkrivML text into HTML (used in edit page). */
	public function convert() {
		[$html, $toc] = $this->_render($_POST['text']);
		print($html);
		return (self::EXEC_QUIT);
	}
	/**
	 * Remove a page.
	 * @param	int	$id	Page's identifier.
	 */
	public function remove(int $id) {
		// check user
		if (!isset($this['user'])) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_FORWARD);
		}
		// get data
		$page = $this->_loader->pageDao->get($id);
		$subPages = $this->_loader->pageDao->getSubPages($id);
		if (isset($subPages) && !empty($subPages)) {
			$this->redirect("/page/show/$id");
			return (self::EXEC_HALT);
		}
		// warn all subscribers
		$subscribers = $this->_loader->pageDao->getSubscribers($id, $this['user']['id']);
		if (!empty($subscribers)) {
			$recipients = [];
			foreach ($subscribers as $subscriber)
				$recipients[] = $subscriber['email'];
			$headers = "MIME-Version: 1.0\r\n" .
				   "Content-type: text/html; charset=utf8\r\n" .
				   "From: " . $this['conf']['emailSender'] . "\r\n" .
				   "Bcc: " . implode(',', $recipients);
			$msg = "<html><body>
					<h1>" . htmlspecialchars($this['conf']['sitename']) . "</h1>
					<p>Hi,</p>
					<p>
						" . htmlspecialchars($this['user']['name']) . " has removed the page
						«&nbsp;<em>". htmlspecialchars($page['title']) . "</em>&nbsp;».
					</p>
					<p>
						Best regards,<br />
						The Skriv Team
					</p>
				</body></html>";
			mail(null, '[' . $this['conf']['sitename'] . '] Page Deleted', $msg, $headers);
		}
		// remove the page
		$this->_loader->pageDao->remove($id);
		// redirection
		$this->redirect("/page/show/" . $page['parentPageId']);
	}
	/**
	 * Define subpages' priorities.
	 * @param	int	$id	Page's identifier.
	 */
	public function setPriorities(int $id) {
		$this->view('\Temma\Views\JsonView');
		// check user
		if (!isset($this['user'])) {
			$this['json'] = 0;
			return (self::EXEC_FORWARD);
		}
		// set priorities
		$this->_loader->pageDao->setPriorities($id, $_POST['prio']);
		$this['json'] = 1;
	}
	/**
	 * Manage page subscription.
	 * @param	int	$pageId		Page's identifier.
	 * @param	int	$subscribed	1 if the user subscribed to the page.
	 */
	public function subscription(int $pageId, int $subscribed) {
		// check user
		if (!isset($this['user'])) {
			return (self::EXEC_QUIT);
		}
		// set subscription
		if (isset($this['conf']['demoMode']) && $this['conf']['demoMode'])
			return (self::EXEC_QUIT);
		$this->_loader->pageDao->subscription($this['user']['id'], $pageId, ($subscribed ? true : false));
		$this->view('\Temma\View\JsonView');
		$this['json'] = 1;
	}
	/**
	 * Shows the list of versions of a page.
	 * @param	int	$id		Page's identifier.
	 * @param	?int	$versionFrom	(optional) Identifier of the first version to compare.
	 * @param	?int	$versionTo	(optional) Identifier of the second version to compare.
	 */
	public function versions(int $id, ?int $versionFrom=null, ?int $versionTo=null) {
		TµLog::log('ark', 'DEBUG', "Versions action.");
		if ((isset($versionFrom) && !isset($versionTo)) ||
		    (!isset($versionFrom) && isset($versionTo))) {
			$this->redirect("/page/versions/$id");
			return (self::EXEC_HALT);
		}
		if (isset($versionFrom) && isset($versionTo) && $versionFrom < $versionTo) {
			$this->redirect("/page/versions/$id/$versionTo/$versionFrom");
			return (self::EXEC_HALT);
		}
		// fetch data, from the 'show' action
		$page = $this->_loader->pageDao->get($id);
		if (!$page)
			throw new \Temma\Exceptions\HttpException("No page with ID '$id'.", 404);
		$this['page'] = $page;
		// get the list of versions
		$versions = $this->_loader->pageDao->getVersions($id);
		$this['versions'] = $versions;
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
		$this['versionFrom'] = $versionFrom;
		$this['versionTo'] = $versionTo;
		// get versions for diff
		[$titleDiff, $skrivDiff] = $this->_loader->pageDao->compareVersions($versionFrom, $versionTo);
		$this['titleDiff'] = $titleDiff;
		$this['skrivDiff'] = $skrivDiff;
	}

	/* *********** PRIVATE METHODS ************* */
	/**
	 * Render a SkrivML text in HTML.
	 * @param	string	$text	SkrivML text.
	 * @return	array	Array with the HTML result, and the raw Table Of Contents.
	 */
	private function _render(string $text) {
		$params = [
			'firstTitleLevel'	=> 2,
			'addFootnotes'		=> true,
			'codeInlineStyles'	=> true,
		];
		$skrivRenderer = \Skriv\Markup\Renderer::factory('html', $params);
		$html = $skrivRenderer->render($text);
		$toc = $skrivRenderer->getToc(true);
		return ([$html, $toc]);
	}
}

