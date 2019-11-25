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
	/** Search pages. */
	public function search() {
		TµLog::log('ark', 'DEBUG', "Search action.");
		$s = $_GET['s'] ?? '';
		$currentUser = $this['user'] ?? null;
		$allowReadOnly = $this['conf']['allowReadOnly'] ?? false;
		$allowPrivatePages = $this['conf']['allowPrivatePages'] ?? false;
		$private = ($currentUser && $allowReadOnly && $allowPrivatePages) ? null : false;
		$result = $this->_loader->pageDao->search($s, $private);
		foreach ($result as &$link) {
			$link['url'] = \Temma\Utils\Text::urlize($link['title']);
			$excerpt = $this->_excerpt(strip_tags($link['html']), $s);
			$excerpt = trim($excerpt);
			$link['excerpt'] = $excerpt ?: (substr(strip_tags($link['html']), 0, 80) . ' (...)');
		}
		$this['s'] = $s;
		$this['result'] = $result;
	}
	/**
	 * Displays a page.
	 * @param	int	$id	Page's identifier.
	 * @param	string	$title	(optinal) Page's title. Optional only when the $id parameter is set to zero.
	 */
	public function show(int $id=0, string $title=null) {
		TµLog::log('ark', 'DEBUG', "Show action.");
		// check root page
		if ($this['CONTROLLER'] == 'page' && !$id) {
			$this->redirect('/');
			return (self::EXEC_HALT);
		}
		// get page's data
		$allowReadOnly = $this['conf']['allowReadOnly'] ?? false;
		$allowPrivatePages = $this['conf']['allowPrivatePages'] ?? false;
		$userId = $this['user']['id'] ?? 0;
		if ($id != 0) {
			$page = $this->_loader->pageDao->get($id, null, $userId);
			// check page
			if (!isset($page['id'])) {
				$this->httpError(404);
				return (self::EXEC_HALT);
			}
			// check URL
			$pageTitle = \Temma\Utils\Text::urlize($page['title']);
			if ($pageTitle != $title) {
				$this->redirect("/page/show/$id/$pageTitle");
				return (self::EXEC_HALT);
			}
			// check if private
			if (!$userId && $page['isPrivate'] && $allowReadOnly && $allowPrivatePages) {
				$this->redirect('/authentication/login');
				return (self::EXEC_HALT);
			}
			// get breadcrumb
			$breadcrumb = $this->_loader->pageDao->getBreadcrumb($page);
			$this['breadcrumb'] = $breadcrumb;
		}
		// manage sub-page
		$showAsSubPage = (!$this['user'] && $id && !$page['nbrChildren']) ? true : false;
		$privateStatus = (!$userId && $allowReadOnly && $allowPrivatePages) ? false : null;
		// get subpages
		if ($showAsSubPage)
			$subPages = $this->_loader->pageDao->getSubPages($page['parentPageId'], null, $privateStatus);
		else
			$subPages = $this->_loader->pageDao->getSubPages($id, null, $privateStatus);
		foreach ($subPages as &$subPage) {
			$subPage['url'] = \Temma\Utils\Text::urlize($subPage['title']);
		}
		$this['page'] = $page;
		$this['subPages'] = $subPages;
		$this['showAsSubPage'] = $showAsSubPage;
		// get level 0 subpages (for page move)
		$level0 = $this->_loader->pageDao->getSubPages(0, null, $privateStatus);
		$this['subLevelPages'] = $level0;
	}
	/**
	 * Returns a chunk of HTML, to select a subpage for moving a page.
	 * @param	int	$pageId		Current page idnetifier.
	 * @param	int	$parentId	Parent level identifier.
	 */
	public function getSubLevels(int $pageId, int $parentLevelId=0) {
		TµLog::log('ark', 'INFO', "GetSubLevels action.");
		$userId = $this['user']['id'] ?? 0;
		$allowReadOnly = $this['conf']['allowReadOnly'] ?? false;
		$allowPrivatePages = $this['conf']['allowPrivatePages'] ?? false;
		$privateStatus = (!$userId && $allowReadOnly && $allowPrivatePages) ? false : null;
		$this['page'] = ['id' => $pageId];
		$this['parentSubLevelId'] = $parentLevelId;
		$sub = $this->_loader->pageDao->getSubPages($parentLevelId, $pageId, $privateStatus);
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
		$page['html'] = $page['sourceHtml'];
		$page['url'] = \Temma\Utils\Text::urlize($page['title']);
		$this['page'] = $page;
		$this['editContent'] = $this->_loader->session['editContent'];
		unset($this->_loader->session['editContent']);
		$this['isPrivate'] = $this->_loader->session['isPrivate'];
		unset($this->_loader->session['isPrivate']);
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
		$title = trim($_POST['title'] ?? '');
		$html = trim($_POST['content'] ?? '');
		$isPrivate = (isset($_POST['private']) && $_POST['private'] == '1') ? true : false;
		if (empty($title)) {
			$this->_loader->session['editContent'] = $html;
			$this->_loader->session['isPrivate'] = $isPrivate;
			$this->redirect("/page/edit/$id");
			return (self::EXEC_HALT);
		}
		// cleanup content
		$html = $this->_cleanupHtml($html);
		$toc = $this->_generateToc($html);
		// update the page
		$this->_loader->pageDao->addVersion($id, $this['user']['id'], $title, $html, $toc, $isPrivate);
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
		$parentPage['url'] = \Temma\Utils\Text::urlize($parentPage['title'] ?? '');
		$breadcrumb = $this->_loader->pageDao->getBreadcrumb($parentPage);
		$breadcrumb = is_array($breadcrumb) ? $breadcrumb : [];
		$this['editContent'] = $this->_loader->session['editContent'];
		unset($this->_loader->session['editContent']);
		$this['page'] = $parentPage;
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
		// get data
		$title = trim($_POST['title'] ?? '');
		$html = trim($_POST['content'] ?? '');
		$isPrivate = (isset($_POST['private']) && $_POST['private'] == '1') ? true : false;
		if (empty($title)) {
			$this->_loader->session['editContent'] = $html;
			$this->redirect("/page/create/$id");
			return (self::EXEC_HALT);
		}
		// cleanup content
		$html = $this->_cleanupHtml($html);
		$toc = $this->_generateToc($html);
		// create the new page
		$id = $this->_loader->pageDao->add($parentId, $this['user']['id'], $title, $html, $toc, $isPrivate);
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
		[$titleDiff, $htmlDiff] = $this->_loader->pageDao->compareVersions($versionFrom, $versionTo);
		$this['titleDiff'] = $titleDiff;
		$this['htmlDiff'] = $htmlDiff;
	}

	/* *********** PRIVATE METHODS ************* */
	/**
	 * Cleanup an HTML string.
	 * @param	string	$html	Input HTML.
	 * @return	string	The cleaned HTML.
	 */
	private function _cleanupHtml(string $html) : string {
		$html = isset($_POST['content']) ? \Temma\Utils\HTMLCleaner::clean($html) : '';
		$h1Found = (strpos($html, '<h1>') === false) ? false : true;
		foreach ([5, 4, 3, 2, 1] as $level) {
			$sublevel = $level + 1;
			if ($h1Found) {
				$html = str_replace(["<h$level>", "</h$level>"], ["<h$sublevel>", "</h$sublevel>"], $html);
			}
			$regex = "/<h$sublevel>([^<]*)<\\/h$sublevel>/i";
			$html = preg_replace_callback($regex, function($matches) use ($sublevel) {
				if (!isset($matches[1]))
					return ($matches[0]);
				return ("<h$sublevel id=\"h$sublevel-" . \Temma\Utils\Text::urlize($matches[1]) . '">' . $matches[1] . "</h$sublevel>");
			}, $html);
		}
		return ($html);
	}
	/**
	 * Generate the table of contents.
	 * @param	string	$html	Input HTML.
	 * @return	array	List of elements.
	 */
	private function _generateToc(string $html) : array {
		$tags = new SimpleXMLElement('<html>' . $html . '</html>');
		$toc = [];
		$lastIndex = -1;
		foreach ($tags as $tag) {
			$tagName = $tag->getName();
			if (!in_array($tagName, ['h2', 'h3']))
				continue;
			$toc[] = [
				'type'  => $tagName,
				'value' => (string)$tag,
				'id'    => $tagName . '-' . \Temma\Utils\Text::urlize((string)$tag),
			];
		}
		return ($toc);
	}
	/**
	 * Generate the excerpt of a text.
	 * @param	string	$text	Input text.
	 * @param	string	$query	The query.
	 * @return	string	Output text.
	 * @link	https://stackoverflow.com/questions/1292121/how-to-generate-the-snippet-like-those-generated-by-google-with-php-and-mysql
	 */
	private function _excerpt(string $text, string $query) : string {
		$text = "\n {$text}\n";
		// words
		$words = join('|', explode(' ', preg_quote($query)));

		// lookahead/behind assertions ensures cut between words
		$s = '\s\x00-/:-@\[-`{-~'; //character set for start/end of words
		preg_match_all('#(?<=[' . $s . ']).{1,60}((' . $words . ').{1,60})+(?=[' . $s . '])#uis', $text, $matches, PREG_SET_ORDER);

		// delimiter between occurences
		$results = [];
		foreach ($matches as $line) {
			$results[] = htmlspecialchars($line[0], 0, 'UTF-8');
		}
		if (count($results) == 1)
			$result = $results[0] . ' (...)';
		else
			$result = join(' (...) ', $results);
		// highlight
		$result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $result);

		return ($result);
	}
}

