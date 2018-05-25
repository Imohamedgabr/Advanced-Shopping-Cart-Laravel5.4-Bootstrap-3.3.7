<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Product\Import\Csv\Processor;


/**
 * Abstract class with common methods for all CSV import processors
 *
 * @package Controller
 * @subpackage Common
 */
class Base
	extends \Aimeos\Controller\Common\Product\Import\Csv\Base
{
	private $context;
	private $mapping;
	private $object;


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
		$this->context = $context;
		$this->mapping = $mapping;
		$this->object = $object;
	}


	/**
	 * Adds the list item default values and returns the resulting array
	 *
	 * @param array $list Associative list of domain item keys and their values, e.g. "product.lists.status" => 1
	 * @param integer $pos Computed position of the list item in the associated list of items
	 * @return array Given associative list enriched by default values if they were not already set
	 */
	protected function addListItemDefaults( array $list, $pos )
	{
		if( !isset( $list['product.lists.position'] ) ) {
			$list['product.lists.position'] = $pos;
		}

		if( !isset( $list['product.lists.status'] ) ) {
			$list['product.lists.status'] = 1;
		}

		return $list;
	}


	/**
	 * Returns the context item
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the mapping list
	 *
	 * @return array Associative list of field positions in CSV as keys and domain item keys as values
	 */
	protected function getMapping()
	{
		return $this->mapping;
	}


	/**
	 * Returns the decorated processor object
	 *
	 * @return \Aimeos\Controller\Common\Product\Import\Csv\Processor\Iface Processor object
	 * @throws \Aimeos\Controller\Jobs\Exception If no processor object is available
	 */
	protected function getObject()
	{
		if( $this->object === null ) {
			throw new \Aimeos\Controller\Jobs\Exception( 'No processor object available' );
		}

		return $this->object;
	}


	/**
	 * Returns the value from the list or the default value
	 *
	 * @param array $list Associative list of key/value pairs
	 * @param string $key Key for the value to retrieve
	 * @param mixed $default Default value if key isn't found
	 * @param mixed Value for the key in the list of the default value
	 */
	protected function getValue( array $list, $key, $default )
	{
		return ( isset( $list[$key] ) ? $list[$key] : $default );
	}
}
