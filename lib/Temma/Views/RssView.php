<?php

namespace Temma\Views;

/**
 * Vue traitant les flux RSS.
 *
 * Cette vue nécessite plusieurs variables :
 * <ul>
 * <li>domain : Nom de domaine du site</li>
 * <li>title : Titre du site.</li>
 * <li>description : Description du site.</li>
 * <li>language : Langue du site (fr, en, ...).</li>
 * <li>Contact : Adresse email de contact</li>
 * <li>articles : Liste de hash contenant toutes les données sur tous les articles.</li>
 * </ul>
 *
 * @author	Amaury Bouchard <amaury.bouchard@finemedia.fr>
 * @copyright	© 2007-2011, Fine Media
 * @package	Temma
 * @subpackage	Views
 * @version	$Id$
 */
class RssView extends \Temma\View {
	/** Titre du site. */
	private $_title = null;
	/** Adresse du site. */
	private $_domain = null;
	/** Adresse du flux RSS. */
	private $_feedLink = null;
	/** Description du site. */
	private $_description = null;
	/** Langue du site. */
	private $_language = null;
	/** Adresse de contact. */
	private $_contact = null;
	/** Liste des articles. */
	private $_articles = null;

	/**
	 * Fonction d'initialisation.
	 * @param	\Temma\Response	$response	Réponse de l'exécution du contrôleur.
	 * @param	string		$templatePath	Chemin vers le template à traiter.
	 */
	public function init(\Temma\Response $response) {
		$this->_domain = $response->getData('domain');
		$this->_feedLink = $response->getData('feedLink');
		$this->_title = $response->getData('title');
		$this->_description = $response->getData('description');
		$this->_language = $response->getData('language');
		$this->_contact = $response->getData('contact');
		$this->_articles = $response->getData('articles');
	}
	/** Ecrit les headers HTTP sur la sortie standard si nécessaire. */
	public function sendHeaders() {
		header('Content-Type: application/rss+xml; charset=UTF-8');
	}
	/** Ecrit le corps du document sur la sortie standard. */
	public function sendBody() {
		print('<' . '?xml version="1.0" encoding="UTF-8"?' . ">\n");
		print("<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n");
		print("<channel>\n");
		print("\t<link>http://" . $this->_domain . "/</link>\n");
		if (!empty($this->_title))
			print("\t<title>" . $this->_title . "</title>\n");
		if (!empty($this->_feedLink))
			print("\t<atom:link href=\"" . $this->_feedLink . "\" rel=\"self\" type=\"application/rss+xml\" />\n");
		if (!empty($this->_description))
			print("\t<description>" . $this->_description . "</description>\n");
		if (!empty($this->_language))
			print("\t<language>" . $this->_language . "</language>\n");
		if (!empty($this->_contact)) {
			print("\t<managingEditor>" . $this->_contact . "</managingEditor>\n");
			print("\t<webMaster>" . $this->_contact . "</webMaster>\n");
		}
		print("\t<generator>FineMedia RSS generator 0.3.0</generator>\n");
		print("\t<copyright>Copyright, Fine Media</copyright>\n");
		print("\t<category>Blog</category>\n");
		print("\t<docs>http://blogs.law.harvard.edu/tech/rss</docs>\n");
		print("\t<ttl>1440</ttl>\n");
		foreach ($this->_articles as $article) {
			if (isset($article['creationDate']))
				$date = $article['creationDate'];
			if (isset($date)) {
				$year = (int)substr($date, 0, 4);
				$month = (int)substr($date, 5, 2);
				$day = (int)substr($date, 8, 2);
				$hour = (int)substr($date, 11, 2);
				$min = (int)substr($date, 14, 2);
				$sec = (int)substr($date, 17, 2);
			}
			$time = mktime($hour, $min, $sec, $month, $day, $year);
			$pubDate = date('r', $time);
			$content = (!isset($article['abstract']) || empty($article['abstract'])) ? '' : ($article['abstract'] . ' (...)');
			$content = str_replace('href="/', 'href="http://' . $this->_domain . '/', $content);
			$content = str_replace('src="/', 'src="http://' . $this->_domain . '/', $content);
			$content = trim($content);
			if (isset($article['url']) && !empty($article['url']))
				$url = $article['url'];
			else
				$url = 'http://' . $this->_domain . '/' . $article['folderName'] .
					(($article['folderName'] == $article['name']) ? "" : ('/' . $article['name']));
			print("\t<item>\n");
			print("\t\t<title>" . str_replace('&', '&amp;', $article['title']) . "</title>\n");
			print("\t\t<pubDate>$pubDate</pubDate>\n");
			print("\t\t<link>" . $url . "</link>\n");
			print("\t\t<guid isPermaLink=\"true\">" . $url . "</guid>\n");
			if (!empty($content))
				print("\t\t<description>" . htmlspecialchars($content, ENT_COMPAT, 'UTF-8') . "</description>\n");
			print("\t</item>\n");
		}
		print("</channel>\n</rss>");
	}
}

?>
