<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Admin\JQAdm\Locale\Currency;


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

		$this->object = new \Aimeos\Admin\JQAdm\Locale\Currency\Standard( $this->context, $templatePaths );
		$this->object = new \Aimeos\Admin\JQAdm\Common\Decorator\Page( $this->object, $this->context, $templatePaths );
		$this->object->setAimeos( \TestHelperJqadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->view, $this->context );
	}


	public function testCreate()
	{
		$this->object->create();
	}


	public function testCreateException()
	{
		$object = $this->getClientMock( 'getSubClients' );

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->create();
	}


	public function testCreateMShopException()
	{
		$object = $this->getClientMock( 'getSubClients' );

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->create();
	}


	public function testCopy()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['id' => 'EUR'] );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->copy();

		$this->assertContains( 'Euro', $result );
	}


	public function testCopyException()
	{
		$object = $this->getClientMock( 'getSubClients' );

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->copy();
	}


	public function testCopyMShopException()
	{
		$object = $this->getClientMock( 'getSubClients' );

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->copy();
	}


	public function testDelete()
	{
		$this->assertNotNull( $this->object->delete() );
	}


	public function testDeleteException()
	{
		$object = $this->getClientMock( 'getSubClients' );

		$object->expects( $this->exactly( 2 ) )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->delete();
	}


	public function testDeleteMShopException()
	{
		$object = $this->getClientMock( 'getSubClients' );

		$object->expects( $this->exactly( 2 ) )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->delete();
	}


	public function testGet()
	{
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['id' => 'EUR'] );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->get();

		$this->assertContains( 'EUR', $result );
	}


	public function testGetException()
	{
		$object = $this->getClientMock( 'getSubClients' );

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->get();
	}


	public function testGetMShopException()
	{
		$object = $this->getClientMock( 'getSubClients' );

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->get();
	}


	public function testGetViewException()
	{
		$object = new \Aimeos\Admin\JQAdm\Locale\Currency\Standard( $this->context, [] );

		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$object->getView();
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'locale/currency' );

		$param = array(
			'item' => array(
				'locale.currency.id' => '',
				'locale.currency.status' => '1',
				'locale.currency.code' => 'XXX',
				'locale.currency.label' => 'jqadm test',
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->save();

		$item = $manager->findItem( 'XXX' );
		$manager->deleteItem( $item->getId() );

		$this->assertEquals( 'jqadm test', $item->getLabel() );
	}


	public function testSaveException()
	{
		$object = $this->getClientMock( 'fromArray' );

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->save();
	}


	public function testSaveMShopException()
	{
		$object = $this->getClientMock( 'fromArray' );

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->save();
	}


	public function testSaveJQAdmException()
	{
		$object = $this->getClientMock( 'fromArray' );

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->save();
	}


	public function testSearch()
	{
		$param = array(
			'lang' => 'de',
			'filter' => array(
				'key' => array( 0 => 'locale.currency.code' ),
				'op' => array( 0 => '==' ),
				'val' => array( 0 => 'EUR' ),
			),
			'sort' => array( '-locale.currency.id' ),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->search();

		$this->assertContains( '>EUR<', $result );
	}


	public function testSearchException()
	{
		$object = $this->getClientMock( 'initCriteria' );

		$object->expects( $this->once() )->method( 'initCriteria' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->search();
	}


	public function testSearchMShopException()
	{
		$object = $this->getClientMock( 'initCriteria' );

		$object->expects( $this->once() )->method( 'initCriteria' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->search();
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$this->object->getSubClient( '$unknown$' );
	}


	public function testGetSubClientUnknown()
	{
		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$this->object->getSubClient( 'unknown' );
	}


	public function getClientMock( $method )
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Locale\Currency\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( [$method] )
			->getMock();

		$object->setAimeos( \TestHelperJqadm::getAimeos() );
		$object->setView( $this->getViewNoRender() );

		return $object;
	}


	protected function getViewNoRender()
	{
		$view = $this->getMockBuilder( '\Aimeos\MW\View\Standard' )
			->setConstructorArgs( array( [] ) )
			->setMethods( array( 'render', 'config' ) )
			->getMock();

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['id' => 'EUR'] );
		$view->addHelper( 'param', $helper );

		$helper = new \Aimeos\MW\View\Helper\Access\Standard( $view, [] );
		$view->addHelper( 'access', $helper );

		return $view;
	}

}
