<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2017
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Common\Decorator;


/**
 * Index rebuild decorator for JQAdm clients
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Index extends Base
{
	/**
	 * Updates the index after deleting the item
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function delete()
	{
		$result = $this->getClient()->delete();

		$ids = (array) $this->getView()->param( 'id' );
		\Aimeos\MShop\Factory::createManager( $this->getContext(), 'index' )->deleteItems( $ids );

		return $result;
	}


	/**
	 * Rebuilds the index after saving the item
	 *
	 * @return string|null admin output to display or null for redirecting to the list
	 */
	public function save()
	{
		$result = $this->getClient()->save();
		$item = $this->getView()->item;

		if( $item->getId() !== null ) {
			\Aimeos\MShop\Factory::createManager( $this->getContext(), 'index' )->saveItem( $item );
		}

		return $result;
	}
}
