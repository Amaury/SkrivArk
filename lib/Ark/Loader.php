<?php

namespace Ark;

/**
 * Dependency injection container for SkrivArk.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2019, Amaury Bouchard
 * @package	SkrivArk
 * @subpackage	Ark
 */
class Loader extends \Temma\Base\Loader {
	/**
	 * Builder method, used to create Ark objects.
	 * @param	string	$key	Requested object name.
	 * @return	mixed	The desired object, or null.
	 */
	public function builder(string $key) {
		if ($key == 'userDao')
			return (new \Ark\UserDao($this->dataSources['db']));
		if ($key == 'pageDao')
			return (new \Ark\PageDao($this->dataSources['db']));
		if ($key == 'installBo')
			return (new \Ark\InstallBo($this));
		if ($key == 'exportBo')
			return (new \Ark\ExportBo($this));
		if ($key == 'communicationBo')
			return (new \Ark\CommunicationBo($this));
		return (null);
	}
}

