<?php

namespace Ark;

use \Temma\Base\Log as TµLog;

/**
 * Object used to do SQL Exports of the database.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	©2019, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Ark
 */
class ExportBo {
	/** Loader. */
	private $_loader = null;

	/**
	 * Constructor.
	 * @param	\Ark\Loader	$loader	Loader object.
	 */
	public function __construct(\Ark\Loader $loader) {
		$this->_loader = $loader;
	}
	/**
	 * Export the database as HTML archive.
	 * @return	string	Path to the ZIP file.
	 * @throws	\Temma\Exceptions\IOException	If the ZIP file can't be created.
	 */
	public function exportHtml() /* : void */ {
		$tmpfile = tempnam('/tmp', 'ark-export-');
		$zip = new \ZipArchive();
		if ($zip->open($tmpfile, \ZipArchive::CREATE) !== true)
			throw new \Temma\Exceptions\IOException("Unable to create ZIP archive '$tmpfile'.", \Temma\Exceptions\IOException::UNWRITABLE);
		$this->_zipSubpages($zip, 0);
		$zip->close();
		$zipSize = filesize($tmpfile);
		if (!$zipSize)
			throw new \Temma\Exceptions\IOException("Empty ZIP file.", \Temma\Exceptions\IOException::BAD_FORMAT);
		header('Content-Type: application/zip');
		header("Content-Length: $zipSize");
		header('Content-Disposition: attachment; filename="arkdump-' . date('Ymd-Hi') . '.zip"');
		readfile($tmpfile);
		unlink($tmpfile);
	}
	/**
	 * Export the database as SQL archive.
	 */
	public function exportSql() /* : void */ {
		$db = $this->_loader->dataSources['db'];
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="arkdump-' . date('Ymd-Hi') . '.sql"');
		// model
		readfile($this->_loader->config->appPath . '/etc/database.sql');
		// users
		print("\n\n-- User\n");
		$users = $this->_loader->userDao->getUsers(null, 'id');
		if ($users) {
			print("INSERT INTO User (id, admin, name, email, password, creationDate, modifDate) VALUES\n");
			foreach ($users as $index => $user) {
				print("(" . $db->quote($user['id']) . ", " .
				      $db->quote($user['admin']) . ", " .
				      $db->quote($user['name']) . ", " .
				      $db->quote($user['email']) . ", " .
				      $db->quote($user['password']) . ", " .
				      $db->quote($user['creationDate']) . ", " .
				      $db->quote($user['modifDate']) . ")");
				if ($index != array_key_last($users))
					print(",\n");
			}
			print(";\n");
		}
		// pages
		print("\n\n-- Page\n");
		for ($offset = 0; ; $offset += 50) {
			$pages = $this->_loader->pageDao->getPages($offset, 50);
			if (!$pages)
				break;
			print("INSERT INTO Page (id, title, html, toc, creationDate, modifDate, creatorId, priority, parentPageId, currentVersionId) VALUES\n");
			foreach ($pages as $index => $page) {
				print("(" . $db->quote($page['id']) . ", " .
				      $db->quote($page['title']) . ", " .
				      $db->quote($page['html']) . ", " .
				      $db->quote($page['toc']) . ", " .
				      $db->quote($page['creationDate']) . ", " .
				      $db->quote($page['modifDate']) . ", " .
				      $db->quote($page['creatorId']) . ", " .
				      $db->quote($page['priority']) . ", " .
				      $db->quote($page['parentPageId']) . ", " .
				      $db->quote($page['currentVersionId']) . ")");
				if ($index != array_key_last($pages))
					print(",\n");
			}
			print(";\n");
		}
		// versions
		print("\n\n-- PageVersion\n");
		for ($offset = 0; ; $offset += 50) {
			$versions = $this->_loader->pageDao->getVersions($offset, 50);
			if (!$versions)
				break;
			print("INSERT INTO PageVersion (id, title, skriv, creationDate, creatorId, pageId) VALUES\n");
			foreach ($versions as $index => $version) {
				print("(" . $db->quote($version['id']) . ", " .
				      $db->quote($version['title']) . ", " .
				      $db->quote($version['skriv']) . ", " .
				      $db->quote($version['creationDate']) . ", " .
				      $db->quote($version['creatorId']) . ", " .
				      $db->quote($version['pageId']) . ")");
				if ($index != array_key_last($versions))
					print(",\n");
			}
			print(";\n");
		}
		// subscriptions
		print("\n-- Subscription\n");
		for ($offset = 0; ; $offset += 50) {
			$subscriptions = $this->_loader->userDao->getSubscriptions($offset, 50);
			if (!$subscriptions)
				break;
			print("INSERT INTO Subscription (id, userId, pageId, createDate) VALUES\n");
			foreach ($subscriptions as $index => $subscription) {
				print("(" . $db->quote($subscription['id']) . ", " .
				      $db->quote($subscription['userId']) . ", " .
				      $db->quote($subscription['pageId']) . ", " .
				      $db->quote($subscription['createDate']) . ")");
				if ($index != array_key_last($subscriptions))
					print(",\n");
			}
			print(";\n");
		}

	}

	/* ********** PRIVATE METHODS ********** */
	/**
	 * Add all sub-pages of a page to a ZIP archive in HTML format.
	 * @param	\ZipArchive	$zip	The ZIP archive object.
	 * @param	int		$id	Parent page ID.
	 * @param	string		$path	(optional) The path from the archive root. If not empty, must have a trailing slash.
	 */
	private function _zipSubpages($zip, $id, $path='') {
		$pages = $this->_loader->pageDao->getSubPages($id);
		foreach ($pages as $page) {
			$filename = $path . $page['id'] . '-' . \Temma\Utils\Text::filenamize($page['title']);
			$zip->addFromString("$filename.html", $page['html']);
			$zip->addFromString("$filename.txt", $page['title']);
		}
		foreach ($pages as $page) {
			$this->_zipSubpages($zip, $page['id'], $path . $page['id'] . '-' . \Temma\Utils\Text::filenamize($page['title']) . '/');
		}
	}
}

