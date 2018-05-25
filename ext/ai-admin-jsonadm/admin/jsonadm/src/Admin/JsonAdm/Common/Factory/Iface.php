<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Common\Factory;


/**
 * JSON API client factory interface
 *
 * @package Admin
 * @subpackage JsonAdm
 */
interface Iface
{
	/**
	 * Creates a new client based on the name
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the client separated by slashes, e.g "product/stock"
	 * @param string|null $name Name of the client implementation ("Standard" if null)
	 * @return \Aimeos\Admin\JsonAdm\Iface Client Interface
	 */
	public static function createClient( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $path, $name = null );
}