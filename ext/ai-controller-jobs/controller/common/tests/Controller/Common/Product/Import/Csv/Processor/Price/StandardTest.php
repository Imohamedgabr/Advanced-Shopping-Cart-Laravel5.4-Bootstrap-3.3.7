<?php

namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Price;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $endpoint;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelperCntl::getContext();
		$this->endpoint = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Done( $this->context, [] );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();
	}


	public function testProcess()
	{
		$mapping = array(
			0 => 'price.type',
			1 => 'price.label',
			2 => 'price.currencyid',
			3 => 'price.quantity',
			4 => 'price.value',
			5 => 'price.costs',
			6 => 'price.rebate',
			7 => 'price.taxrate',
			8 => 'price.status',
		);

		$data = array(
			0 => 'default',
			1 => 'EUR 1.00',
			2 => 'EUR',
			3 => 5,
			4 => '1.00',
			5 => '0.20',
			6 => '0.10',
			7 => '20.00',
			8 => 1,
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Price\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );
		$this->assertEquals( 1, count( $listItems ) );

		$this->assertEquals( 1, $listItem->getStatus() );
		$this->assertEquals( 0, $listItem->getPosition() );
		$this->assertEquals( 'price', $listItem->getDomain() );
		$this->assertEquals( 'default', $listItem->getType() );

		$refItem = $listItem->getRefItem();

		$this->assertEquals( 1, $refItem->getStatus() );
		$this->assertEquals( 'default', $refItem->getType() );
		$this->assertEquals( 'product', $refItem->getDomain() );
		$this->assertEquals( 'EUR 1.00', $refItem->getLabel() );
		$this->assertEquals( 5, $refItem->getQuantity() );
		$this->assertEquals( '1.00', $refItem->getValue() );
		$this->assertEquals( '0.20', $refItem->getCosts() );
		$this->assertEquals( '0.10', $refItem->getRebate() );
		$this->assertEquals( '20.00', $refItem->getTaxrate() );
		$this->assertEquals( 1, $refItem->getStatus() );
	}


	public function testProcessMultiple()
	{
		$mapping = array(
			0 => 'price.value',
			1 => 'price.value',
			2 => 'price.value',
			3 => 'price.value',
		);

		$data = array(
			0 => '1.00',
			1 => '2.00',
			2 => '3.00',
			3 => '4.00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Price\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$pos = 0;
		$listItems = $product->getListItems();

		$this->assertEquals( 4, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( $data[$pos], $listItem->getRefItem()->getValue() );
			$pos++;
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'price.value',
		);

		$data = array(
			0 => '1.00',
		);

		$dataUpdate = array(
			0 => '2.00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Price\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$object->process( $product, $dataUpdate );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );

		$this->assertEquals( '2.00', $listItem->getRefItem()->getValue() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'price.value',
		);

		$data = array(
			0 => '1.00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Price\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Price\Standard( $this->context, [], $this->endpoint );
		$object->process( $product, [] );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 0, count( $listItems ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'price.value',
			1 => 'price.value',
		);

		$data = array(
			0 => '1.00',
			1 => '',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Price\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'price.value',
			1 => 'product.lists.type',
			2 => 'price.value',
			3 => 'product.lists.type',
		);

		$data = array(
			0 => '1.00',
			1 => 'test',
			2 => '2.00',
			3 => 'default',
		);

		$this->context->getConfig()->set( 'controller/common/product/import/csv/processor/price/listtypes', array( 'default' ) );

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Price\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( '2.00', $listItem->getRefItem()->getValue() );
	}


	/**
	 * @param string $code
	 */
	protected function create( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$typeManager = $manager->getSubManager( 'type' );

		$typeSearch = $typeManager->createSearch();
		$typeSearch->setConditions( $typeSearch->compare( '==', 'product.type.code', 'default' ) );
		$typeResult = $typeManager->searchItems( $typeSearch );

		if( ( $typeItem = reset( $typeResult ) ) === false ) {
			throw new \RuntimeException( 'No product type "default" found' );
		}

		$item = $manager->createItem();
		$item->setTypeid( $typeItem->getId() );
		$item->setCode( $code );

		return $manager->saveItem( $item );
	}


	protected function delete( \Aimeos\MShop\Product\Item\Iface $product )
	{
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $this->context );
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$listManager = $manager->getSubManager( 'lists' );

		foreach( $product->getListItems('price') as $listItem )
		{
			$priceManager->deleteItem( $listItem->getRefItem()->getId() );
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	/**
	 * @param string $code
	 */
	protected function get( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('price') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}
}