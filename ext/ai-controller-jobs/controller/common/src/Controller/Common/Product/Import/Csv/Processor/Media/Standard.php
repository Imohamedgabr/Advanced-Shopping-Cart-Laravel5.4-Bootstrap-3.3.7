<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Media;


/**
 * Media processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard
	extends \Aimeos\Controller\Common\Product\Import\Csv\Processor\Base
	implements \Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface
{
	/** controller/common/product/import/csv/processor/media/name
	 * Name of the media processor implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the processor class name
	 * @since 2015.10
	 * @category Developer
	 */

	private $listTypes;


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param \Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface $object Decorated processor
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, array $mapping,
			\Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface $object = null )
	{
		parent::__construct( $context, $mapping, $object );

		/** controller/common/product/import/csv/processor/media/listtypes
		 * Names of the product list types for media that are updated or removed
		 *
		 * If you want to associate media items manually via the administration
		 * interface to products and don't want these to be touched during the
		 * import, you can specify the product list types for these media
		 * that shouldn't be updated or removed.
		 *
		 * @param array|null List of product list type names or null for all
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/common/product/import/csv/domains
		 * @see controller/common/product/import/csv/processor/attribute/listtypes
		 * @see controller/common/product/import/csv/processor/catalog/listtypes
		 * @see controller/common/product/import/csv/processor/product/listtypes
		 * @see controller/common/product/import/csv/processor/price/listtypes
		 * @see controller/common/product/import/csv/processor/text/listtypes
		 */
		$this->listTypes = $context->getConfig()->get( 'controller/common/product/import/csv/processor/media/listtypes' );
	}


	/**
	 * Saves the product related data to the storage
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( \Aimeos\MShop\Product\Item\Iface $product, array $data )
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop\Factory::createManager( $context, 'media' );
		$listManager = \Aimeos\MShop\Factory::createManager( $context, 'product/lists' );
		$separator = $context->getConfig()->get( 'controller/common/product/import/csv/separator', "\n" );

		$manager->begin();

		try
		{
			$delete = $listMap = [];
			$map = $this->getMappedChunk( $data, $this->getMapping() );
			$listItems = $product->getListItems( 'media', $this->listTypes );

			foreach( $listItems as $listItem )
			{
				if( ( $refItem = $listItem->getRefItem() ) !== null ) {
					$listMap[ $refItem->getUrl() ][ $refItem->getType() ][ $listItem->getType() ] = $listItem;
				}
			}

			foreach( $map as $pos => $list )
			{
				if( $this->checkEntry( $list ) === false ) {
					continue;
				}

				$urls = explode( $separator, $list['media.url'] );
				$type = $this->getValue( $list, 'media.type', 'default' );
				$typecode = $this->getValue( $list, 'product.lists.type', 'default' );

				foreach( $urls as $url )
				{
					if( isset( $listMap[$url][$type][$typecode] ) )
					{
						$listItem = $listMap[$url][$type][$typecode];
						$refItem = $listItem->getRefItem();
						unset( $listItems[ $listItem->getId() ] );
					}
					else
					{
						$listItem = $listManager->createItem();
						$refItem = $manager->createItem();
					}

					$list['media.typeid'] = $this->getTypeId( 'media/type', 'product', $type );
					$list['media.domain'] = 'product';
					$list['media.url'] = $url;

					$refItem->fromArray( $this->addItemDefaults( $list ) );
					$refItem = $manager->saveItem( $refItem );

					$list['product.lists.typeid'] = $this->getTypeId( 'product/lists/type', 'media', $typecode );
					$list['product.lists.parentid'] = $product->getId();
					$list['product.lists.refid'] = $refItem->getId();
					$list['product.lists.domain'] = 'media';

					$listItem->fromArray( $this->addListItemDefaults( $list, $pos++ ) );
					$listManager->saveItem( $listItem, false );
				}
			}

			foreach( $listItems as $listItem ) {
				$delete[] = $listItem->getRefId();
			}

			$manager->deleteItems( $delete );
			$listManager->deleteItems( array_keys( $listItems ) );

			$data = $this->getObject()->process( $product, $data );

			$manager->commit();
		}
		catch( \Exception $e )
		{
			$manager->rollback();
			throw $e;
		}

		return $data;
	}


	/**
	 * Adds the text item default values and returns the resulting array
	 *
	 * @param array $list Associative list of domain item keys and their values, e.g. "media.status" => 1
	 * @return array Given associative list enriched by default values if they were not already set
	 */
	protected function addItemDefaults( array $list )
	{
		if( !isset( $list['media.label'] ) ) {
			$list['media.label'] = $list['media.url'];
		}

		if( !isset( $list['media.preview'] ) ) {
			$list['media.preview'] = $list['media.url'];
		}

		if( !isset( $list['media.status'] ) ) {
			$list['media.status'] = 1;
		}

		return $list;
	}


	/**
	 * Checks if an entry can be used for updating a media item
	 *
	 * @param array $list Associative list of key/value pairs from the mapping
	 * @return boolean True if valid, false if not
	 */
	protected function checkEntry( array $list )
	{
		if( !isset( $list['media.url'] ) || $list['media.url'] === '' || isset( $list['product.lists.type'] )
				&& $this->listTypes !== null && !in_array( $list['product.lists.type'], (array) $this->listTypes )
		) {
			return false;
		}

		return true;
	}
}
