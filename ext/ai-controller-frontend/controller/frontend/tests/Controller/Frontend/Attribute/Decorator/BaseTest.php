<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Controller\Frontend\Attribute\Decorator;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $stub;


	protected function setUp()
	{
		$this->context = \TestHelperFrontend::getContext();

		$this->stub = $this->getMockBuilder( '\Aimeos\Controller\Frontend\Attribute\Standard' )
			->disableOriginalConstructor()
			->getMock();

		$this->object = $this->getMockBuilder( '\Aimeos\Controller\Frontend\Attribute\Decorator\Base' )
			->setConstructorArgs( [$this->stub, $this->context] )
			->getMockForAbstractClass();
	}


	protected function tearDown()
	{
		unset( $this->context, $this->object, $this->stub );
	}


	public function testConstructException()
	{
		$stub = $this->getMockBuilder( '\Aimeos\Controller\Frontend\Iface' )->getMock();

		$this->setExpectedException( '\Aimeos\Controller\Frontend\Exception' );

		$this->getMockBuilder( '\Aimeos\Controller\Frontend\Attribute\Decorator\Base' )
			->setConstructorArgs( [$stub, $this->context] )
			->getMockForAbstractClass();
	}


	public function testCall()
	{
		$stub = $this->getMockBuilder( '\Aimeos\Controller\Frontend\Attribute\Standard' )
			->disableOriginalConstructor()
			->setMethods( ['invalid'] )
			->getMock();

		$object = $this->getMockBuilder( '\Aimeos\Controller\Frontend\Attribute\Decorator\Base' )
			->setConstructorArgs( [$stub, $this->context] )
			->getMockForAbstractClass();

		$stub->expects( $this->once() )->method( 'invalid' )->will( $this->returnValue( true ) );

		$this->assertTrue( $object->invalid() );
	}


	public function testAddFilterTypes()
	{
		$search = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' )->createSearch();

		$this->stub->expects( $this->once() )->method( 'addFilterTypes' )
			->will( $this->returnArgument( 0 ) );

		$this->assertInstanceOf( '\Aimeos\MW\Criteria\Iface', $this->object->addFilterTypes( $search, [] ) );
	}


	public function testCreateFilter()
	{
		$search = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' )->createSearch();

		$this->stub->expects( $this->once() )->method( 'createFilter' )
			->will( $this->returnValue( $search ) );

		$this->assertInstanceOf( '\Aimeos\MW\Criteria\Iface', $this->object->createFilter() );
	}


	public function testGetItem()
	{
		$item = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' )->createItem();

		$this->stub->expects( $this->once() )->method( 'getItem' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $this->object->getItem( -1 ) );
	}


	public function testGetItems()
	{
		$this->stub->expects( $this->once() )->method( 'getItems' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getItems( [-1], ['media'] ) );
	}


	public function testSearchItems()
	{
		$filter = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' )->createSearch();

		$this->stub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->searchItems( $filter, ['media'] ) );
	}


	public function testGetController()
	{
		$result = $this->access( 'getController' )->invokeArgs( $this->object, [] );

		$this->assertSame( $this->stub, $result );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( '\Aimeos\Controller\Frontend\Attribute\Decorator\Base' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
