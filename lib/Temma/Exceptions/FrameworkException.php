<?php

namespace Temma\Exceptions;

/**
 * Exception for Temma framework errors.
 *
 * @author      Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2007-2019, Amaury Bouchard
 * @package     Temma
 * @subpackage  Exceptions
 */
class FrameworkException extends \Exception {
	/** Configuration error. */
	const CONFIG = 0;
	/** Controller loading error. */
	const NO_CONTROLLER = 1;
	/** No action available. */
	const NO_ACTION = 2;
	/** No view available. */
	const NO_VIEW = 3;
	/** No template available. */
	const NO_TEMPLATE = 4;
}

