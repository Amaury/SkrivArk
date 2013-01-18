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
	}
	/**
	 * Show the edition form.
	 * @param	int	$id	Identifier of the page to modify.
	 */
	public function execEdit($id) {
		FineLog::log('skriv', 'DEBUG', "Edit action ($id).");
		// get page's data
		$page = $this->_pageDao->get($id);
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
		$this->_pageDao->update($id, array(
			'title'		=> $title,
			'skriv'		=> $_POST['content'],
			'html'		=> $html,
			'modifierId'	=> $user['id'],
			'modifDate'	=> date('c')
		));
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
		$id = $this->_pageDao->create(array(
			'title'		=> $title,
			'skriv'		=> $_POST['content'],
			'html'		=> $html,
			'creatorId'	=> $user['id'],
			'creationDate'	=> date('c'),
			'parentPageId'	=> $parentId
		));
		$this->redirect("/page/show/$id");
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

