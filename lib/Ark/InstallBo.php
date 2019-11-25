<?php

namespace Ark;

/**
 * Installation helper.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2019, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Ark
 */
class InstallBo {
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
	 * Tell if the site was already installed.
	 * @return	bool	True if the site is installed, false otherwise.
	 */
	public function isInstalled() : bool {
		$temma = $this->_readConf();
		$rootController = $temma['application']['rootController'] ?? null;
		if ($rootController == 'PageController')
			return (true);
		return (false);
	}
	/**
	 * Tell if file rights are fine.
	 * @return	mixed	True if the rights are good. Otherwise, a list of files with bad rights.
	 */
	public function checkFilesAccessRights() /* : mixed */ {
		$appPath = $this->_loader->config->appPath;
		$list = [
			"etc/temma.json",
			"log",
			"tmp",
			"var/splashscreen.html",
		];
		$rightsOk = true;
		$err = [];
		foreach ($list as $path) {
			if (!is_writable("$appPath/$path")) {
				$err[] = $path;
				$rightsOk = false;
			}
		}
		return ($rightsOk ?: $err);
	}
	/**
	 * Check database connection parameters.
	 * @param	string	$dbhostname	DB hostname.
	 * @param	string	$dbname		DB name.
	 * @param	string	$dbuser		DB user.
	 * @param	string	$dbpassword	DB password.
	 * @return	\Temma\Base\Database	A database object, or null if the connection parameters are wrong.
	 */
	public function checkDatabaseParameters(string $dbhostname, string $dbname, string $dbuser, string $dbpassword) : ?\Temma\Base\Database {
		$dsn = "mysql://$dbuser:$dbpassword@$dbhostname/$dbname";
		try {
			// connexion
			$db = \Temma\Base\Database::factory($dsn);
			$db->exec("DO 1");
		} catch (\Exception $e) {
			return (null);
		}
		return ($db);
	}
	/**
	 * Create the tables in the database.
	 * @param	\Temma\Base\Database	$db	Connection to the database.
	 */
	public function createDatabase(\Temma\Base\Database $db) /* : void */ {
		$sql = file_get_contents($this->_loader->config->appPath . '/etc/database.sql');
		$sql = explode(';', $sql);
		foreach ($sql as $request) {
			$request = trim($request);
			if (!empty($request))
				$db->exec($request);
		}
	}
	/**
	 * Update the database DSN in the 'temma.json' configuration file.
	 * @param	string	$dbhostname	DB hostname.
	 * @param	string	$dbname		DB name.
	 * @param	string	$dbuser		DB user.
	 * @param	string	$dbpassword	DB password.
	 */
	public function updateConfigDatabase(string $dbhostname, string $dbname, string $dbuser, string $dbpassword) /* : void */ {
		$temma = $this->_readConf();
		$dsn = "mysql://$dbuser:$dbpassword@$dbhostname/$dbname";
                $temma['application']['dataSources']['db'] = $dsn;
		$this->_writeConf($temma);
	}
	/**
	 * Update the session storage parameters in the 'temma.json' configuration file.
	 * @param	string	$server	'nocache', 'memcache' or 'redis'.
	 * @param	?string	$host	Server hostname. Could be null ('nocache' server).
	 * @param	?int	$port	Port number. Could be null ('nocache' server).
	 * @throw	\Exception	If the configured server is not available.
	 */
	public function updateConfigSession(string $server, ?string $host, ?int $port) /* : void */ {
		$temma = $this->_readConf();
		if ($server == 'nocache') {
			// no cache server
			unset($temma['application']['dataSources']['cache']);
			unset($temma['application']['sessionSource']);
		} else {
			$dsn = "$server://$host:$port";
			if ($server == 'memcache') {
				// memcache server
				$cache = \Temma\Base\Cache::factory($dsn);
				if (!$cache->isEnabled())
					throw new \Exception();
				$val = mt_rand();
				$id = 'test-skrivark-' . uniqid();
				$cache->set($id, $val);
				if ($cache->get($id) != $val)
					throw new \Exception();
			} else if ($server == 'redis') {
				// redis server
				$ndb = \Temma\Base\NDatabase::factory($dsn);
				$val = mt_rand();
				$id = 'test-skrivark-' . uniqid();
				$ndb->set($id, $val);
				if ($ndb->get($id) != $val)
					throw new \Exception();
			}
			$temma['application']['dataSources']['cache'] = $dsn;
			$temma['application']['sessionSource'] = 'cache';
		}
		$this->_writeConf($temma);
	}
	/**
	 * Update some parameters of the 'temma.json' configuration file.
	 * @param	string	$sitename		Name of the site.
	 * @param	string	$baseUrl		Base URL.
	 * @param	string	$emailSender		Sender's email address.
	 * @param	bool	$demomode		True to activate the demo mode.
	 * @param	bool	$searchable		True to make contents searchable.
	 * @param	bool	$allowReadOnly		True to allow read-only access.
	 * @param	bool	$allowPrivatePages	True to allow private pages.
	 * @param	bool	$darkTheme		True to use dark theme.
	 * @param	?string	$disqus			Disqus key.
	 * @param	?string	$gAnalytics		Google Analytics key.
	 * @param	string	$loglevel		Log level ('DEBUG', 'INFO', 'NOTE', 'WARN', 'ERROR').
	 */
	public function updateConfigParameters(string $sitename, string $baseUrl, string $emailSender,
	                                       bool $demomode, bool $searchable, bool $allowReadOnly,
	                                       bool $allowPrivatePages, bool $darkTheme, string $disqus,
	                                       string $gAnalytics, string $loglevel) /* : void */ {
		// update temma.json
		$temma = $this->_readConf();
		$temma['loglevels']['Temma/Base'] = $loglevel;
		$temma['loglevels']['Temma/Web'] = $loglevel;
		$temma['loglevels']['ark'] = $loglevel;
		$temma['autoimport'] = [
			'sitename'          => $sitename,
			'baseURL'           => $baseUrl,
			'emailSender'       => $emailSender,
			'demoMode'          => $demomode,
			'searchable'        => $searchable,
			'allowReadOnly'     => $allowReadOnly,
			'allowPrivatePages' => $allowPrivatePages,
			'darkTheme'         => $darkTheme,
			'disqus'            => $disqus,
			'googleAnalytics'   => $gAnalytics,
		];
		$this->_writeConf($temma);
		// management of the demo mode
		if ($demomode) {
			// check is there is some pages
			if (!$this->_loader->pageDao->nbrPages()) {
				// fill the database with demo data
				$sql = file_get_contents($this->_loader->config->appPath . '/etc/demo-data.sql');
				$sql = explode(';', $sql); 
				foreach ($sql as $request) {
					$request = trim($request);
					if (!empty($request))
						$this->_loader->dataSources['db']->exec($request);
				}
			}
		}
	}
	/** Update the 'temma.json' configuration file to finish the installation process. */
	public function updateConfigFinish() /* : void */ {
		$temma = $this->_readConf();
		$temma['application']['rootController'] = 'Page';
		$temma['plugins'] = [
			'_pre' => ['Authentication'],
		];
		$this->_writeConf($temma);
	}

	/* ********** PRIVATE METHODS ********** */
	/**
	 * Read the 'temma.json' configuration file.
	 * @return	array	Associative array.
	 */
	private function _readConf() : array {
		$appPath = $this->_loader->config->appPath;
		$temma = json_decode(file_get_contents("$appPath/etc/temma.json"), true);
		return ($temma);
	}
	/**
	 * Update the 'temma.json' configuration file.
	 * @param	array	$temma	Configuration content.
	 */
	private function _writeConf(array $temma) /* : void */ {
		$appPath = $this->_loader->config->appPath;
		file_put_contents("$appPath/etc/temma.json", json_encode($temma, JSON_PRETTY_PRINT));
	}
}
