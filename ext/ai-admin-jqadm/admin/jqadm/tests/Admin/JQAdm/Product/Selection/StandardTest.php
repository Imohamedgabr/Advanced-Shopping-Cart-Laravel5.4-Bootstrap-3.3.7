<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2017
 */


namespace Aimeos\Admin\JQAdm\Product\Selection;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->view = \TestHelperJqadm::getView();
		$this->context = \TestHelperJqadm::getContext();
		$templatePaths = \TestHelperJqadm::getTemplatePaths();

		$this->object = new \Aimeos\Admin\JQAdm\Product\Selection\Standard( $this->context, $templatePaths );
		$this->object->setAimeos( \TestHelperJqadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->view, $this->context );
	}


	public function testCreate()
	{
		$param = array(
			'site' => 'unittest',
			'selection' => array(
				'product.id' => array( 0 => '' ),
				'product.code' => array( 0 => 'testprod' ),
				'product.label' => array( 0 => 'test product' ),
				'attr' => array(
					'id' => array( 0 => '123' ),
					'label' => array( 0 => 'test attribute' ),
					'ref' => array( 0 => 'testprod' ),
				)
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->createItem();
		$result = $this->object->create();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'item-selection', $result );
		$this->assertContains( 'testprod', $result );
		$this->assertContains( 'value="123"', $result );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$this->view->item = $manager->findItem( 'U:TEST', array( 'product' ) );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'U:TESTSUB01', $result );
		$this->assertContains( 'U:TESTSUB02', $result );
		$this->assertContains( 'U:TESTSUB03', $result );
		$this->assertContains( 'U:TESTSUB04', $result );
		$this->assertContains( 'U:TESTSUB05', $result );
		$this->assertContains( 'value="30"', $result );
		$this->assertContains( 'value="32"', $result );
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$this->view->item = $manager->findItem( 'U:TEST', ['product'] );

		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'U:TEST', $result );
		$this->assertContains( 'value="U:TESTSUB01"', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$item = $manager->findItem( 'U:TEST' );
		$item->setCode( 'jqadm-test-selection' );
		$item->setId( null );

		$item = $manager->saveItem( $item );

		$attrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );
		$attrItem = $attrManager->findItem( 'xs', [], 'product', 'size' );


		$param = array(
			'site' => 'unittest',
			'selection' => array(
				'product.lists.id' => array( 0 => '' ),
				'product.id' => array( 0 => $item->getId() ),
				'product.code' => array( 0 => 'testprod' ),
				'product.label' => array( 0 => 'test product' ),
				'attr' => array(
					'id' => array( 0 => $attrItem->getId() ),
					'label' => array( 0 => 'test attribute' ),
					'ref' => array( 0 => 'testprod' ),
				)
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();

		$item = $manager->getItem( $item->getId(), array( 'product' ) );
		$variants = $item->getListItems( 'product', 'default' );

		if( empty( $variants ) ) {
			throw new \RuntimeException( 'No variant products available' );
		}

		$variant = $manager->getItem( reset( $variants )->getRefId(), array( 'attribute' ) );
		$attributes = $variant->getListItems( 'attribute', 'variant' );

		$manager->deleteItems( array( $item->getId(), $variant->getId() ) );


		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $variants ) );
		$this->assertEquals( 1, count( $attributes ) );
		$this->assertEquals( 'testprod', $variant->getCode() );
		$this->assertEquals( $attrItem->getId(), reset( $attributes )->getRefId() );
	}


	public function testSaveException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Product\Selection\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->view = \TestHelperJqadm::getView();
		$this->view->item = \Aimeos\MShop\Factory::createManager( $this->context, 'product' )->createItem();

		$object->setView( $this->view );

		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$object->save();
	}


	public function testSaveMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Product\Selection\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$this->view = \TestHelperJqadm::getView();
		$this->view->item = \Aimeos\MShop\Factory::createManager( $this->context, 'product' )->createItem();

		$object->setView( $this->view );

		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$object->save();
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$this->object->getSubClient( 'unknown' );
	}
}
