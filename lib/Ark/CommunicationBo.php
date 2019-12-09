<?php

/**
 * CommunicationBo
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2019, Amaury Bouchard
 */

namespace Ark;

/**
 * Object used to send emails.
 */
class CommunicationBo {
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
	 * Send an email to a newly created user.
	 * @param	string	$adminName	Name of the administrator user.
	 * @param	string	$email		User's email address.
	 * @param	string	$name		User's name.
	 * @param	string	$password	User's password.
	 */
	public function emailCreatedUser(string $adminName, string $email, string $name, string $password) /* : void */ {
		$sitename = $this->_loader->config->autoimport['sitename'] ?? null;
		$emailSender = $this->_loader->config->autoimport['emailSender'] ?? null;
		$baseUrl = $this->_loader->config->autoimport['baseURL'] ?? null;
		// create message
		$headers = "MIME-Version: 1.0\r\n" .
			   "Content-type: text/html; charset=utf8\r\n" .
			   "From: $emailSender";
		$msg = "<html><body>
				<h1>" . htmlspecialchars($sitename) . "</h1>
				<p>Hi " . htmlspecialchars($name) . ",</p>
				<p>
					" . htmlspecialchars($adminName) . " has created an account for you on the
					<a href=\"" . htmlspecialchars($baseUrl) . "\">" . htmlspecialchars($sitename) . "</a> site.
				</p>
				<p>
					Your temporary password is <strong>" . htmlspecialchars($password) . "</strong>
				</p>
				<p>
					Best regards,<br />
					The Skriv Team
				</p>
			</body></html>";
		// send email
		mail($email, "[$sitename] Account Creation", $msg, $headers);
	}
	/**
	 * Send an email to subscribers when a page is created.
	 * @param	int	$parentPageId	ID of the parent page to the created page.
	 * @param	string	$pageTitle	Title of the created page.
	 * @param	int	$creatorId	ID of the user who created the page.
	 * @param	string	$creatorName	Name of the user who created the page.
	 */
	public function emailSubscribersCreation(int $parentPageId, string $pageTitle, int $creatorId, string $creatorName) /* : void */ {
		$sitename = $this->_loader->config->autoimport['sitename'] ?? null;
		$emailSender = $this->_loader->config->autoimport['emailSender'] ?? null;
		$baseUrl = $this->_loader->config->autoimport['baseURL'] ?? null;
		// get subscribers
		$subscribers = $this->_loader->pageDao->getSubscribers($parentPageId, $creatorId);
		if (empty($subscribers)) {
			return;
		}
		// send emails
		$recipients = [];
		foreach ($subscribers as $subscriber)
			$recipients[] = $subscriber['email'];
		$headers = "MIME-Version: 1.0\r\n" .
			   "Content-type: text/html; charset=utf8\r\n" .
			   "From: $emailSender\r\n" .
			   "Bcc: " . implode(',', $recipients);
		$msg = "<html><body>
				<h1>" . htmlspecialchars($sitename) . "</h1>
				<p>Hi,</p>
				<p>
					" . htmlspecialchars($creatorName) . " has created the page
					«&nbsp;<em><a href=\"" . htmlspecialchars($baseUrl) . "/page/show/$pageId\">". htmlspecialchars($pageTitle) . "</a></em>&nbsp;».
				</p>
				<p>
					Best regards,<br />
					The Skriv Team
				</p>
			</body></html>";
		mail(null, "[$sitename] Page Creation", $msg, $headers);
	}
	/**
	 * Send an email to subscribers when a page is modified.
	 * @param	int	$pageId		ID of the page.
	 * @param	string	$pageTitle	Title of the page.
	 * @param	int	$modifierId	ID of the user who modified the page.
	 * @param	string	$modifierName	Name of the user who modified the page.
	 */
	public function emailSubscribersModification(int $pageId, string $pageTitle, int $modifierId, string $modifierName) /* : void */ {
		$sitename = $this->_loader->config->autoimport['sitename'] ?? null;
		$emailSender = $this->_loader->config->autoimport['emailSender'] ?? null;
		$baseUrl = $this->_loader->config->autoimport['baseURL'] ?? null;
		// get subscribers
		$subscribers = $this->_loader->pageDao->getSubscribers($pageId, $modifierId);
		if (empty($subscribers)) {
			return;
		}
		// send emails
		$recipients = []; 
		foreach ($subscribers as $subscriber)
			$recipients[] = $subscriber['email'];
		$headers = "MIME-Version: 1.0\r\n" .
			   "Content-type: text/html; charset=utf8\r\n" .
			   "From: $emailSender\r\n" .
			   "Bcc: " . implode(',', $recipients);
		$msg = "<html><body>
				<h1>" . htmlspecialchars($sitename) . "</h1>
				<p>Hi,</p>
				<p>
					" . htmlspecialchars($modifierName) . " has modified the page
					«&nbsp;<em><a href=\"" . htmlspecialchars($baseUrl) . "/page/show/$pageId\">". htmlspecialchars($pageTitle) . "</a></em>&nbsp;».
				</p>
				<p>
					Best regards,<br />
					The Skriv Team
				</p>
			</body></html>";
		mail(null, "[$sitename] Page Modification", $msg, $headers);
	}
	/**
	 * Send an email to subscribers when a page is deleted.
	 * @param	int	$pageId		ID of the page.
	 * @param	string	$pageTitle	Title of the page.
	 * @param	int	$deleterId	ID of the user who deleted the page.
	 * @param	string	$deleterName	Name of the user who deleted the page.
	 */
	public function emailSubscribersDeletion(int $pageId, string $pageTitle, int $deleterId, string $deleterName) /* : void */ {
		$sitename = $this->_loader->config->autoimport['sitename'] ?? null;
		$emailSender = $this->_loader->config->autoimport['emailSender'] ?? null;
		$baseUrl = $this->_loader->config->autoimport['baseURL'] ?? null;
		// get subscribers
		$subscribers = $this->_loader->pageDao->getSubscribers($pageId, $deleterId);
                if (empty($subscribers)) {
			return;
		}
		// send messages
		$recipients = [];
		foreach ($subscribers as $subscriber)
			$recipients[] = $subscriber['email'];
		$headers = "MIME-Version: 1.0\r\n" .
			   "Content-type: text/html; charset=utf8\r\n" .
			   "From: $emailSender\r\n" .
			   "Bcc: " . implode(',', $recipients);
		$msg = "<html><body>
				<h1>" . htmlspecialchars($sitename) . "</h1>
				<p>Hi,</p>
				<p>
					" . htmlspecialchars($deleterName) . " has removed the page
					«&nbsp;<em>". htmlspecialchars($pageTitle) . "</em>&nbsp;».
				</p>
				<p>
					Best regards,<br />
					The Skriv Team
				</p>
			</body></html>";
		mail(null, "[$sitename] Page Deletion", $msg, $headers);
	}
}

