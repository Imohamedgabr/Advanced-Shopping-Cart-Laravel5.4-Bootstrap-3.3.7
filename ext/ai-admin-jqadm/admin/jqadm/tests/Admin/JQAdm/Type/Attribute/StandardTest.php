<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Admin\JQAdm\Type\Attribute;


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

		$this->object = new \Aimeos\Admin\JQAdm\Type\Attribute\Standard( $this->context, $templatePaths );
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
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->create();
	}


	public function testCreateMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

		$object->create();
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute/type' );

		$param = ['type' => 'unittest', 'id' => $manager->findItem( 'color', [], 'product' )->getId()];
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->copy();

		$this->assertContains( 'color', $result );
	}


	public function testCopyException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->copy();
	}


	public function testCopyMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

		$object->copy();
	}


	public function testDelete()
	{
		$this->assertNotNull( $this->object->delete() );
	}


	public function testDeleteException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients', 'search' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->delete();
	}


	public function testDeleteMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients', 'search' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

		$object->delete();
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute/type' );

		$param = ['type' => 'unittest', 'id' => $manager->findItem( 'color', [], 'product' )->getId()];
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->get();

		$this->assertContains( 'color', $result );
	}


	public function testGetException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->get();
	}


	public function testGetMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

		$object->get();
	}


	public function testGetViewException()
	{
		$object = new \Aimeos\Admin\JQAdm\Type\Attribute\Standard( $this->context, [] );

		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$object->getView();
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute/type' );

		$param = array(
			'type' => 'unittest',
			'item' => array(
				'attribute.type.id' => '',
				'attribute.type.status' => '1',
				'attribute.type.domain' => 'product',
				'attribute.type.code' => 'jqadm@test',
				'attribute.type.label' => 'jqadm test',
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$this->object->save();

		$manager->deleteItem( $manager->findItem( 'jqadm@test', [], 'product' )->getId() );
	}


	public function testSaveException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->save();
	}


	public function testSaveMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->save();
	}


	public function testSaveJQAdmException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->save();
	}


	public function testSearch()
	{
		$param = array(
			'type' => 'unittest', 'lang' => 'de',
			'filter' => array(
				'key' => array( 0 => 'attribute.type.code' ),
				'op' => array( 0 => '==' ),
				'val' => array( 0 => 'color' ),
			),
			'sort' => array( '-attribute.type.id' ),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->search();

		$this->assertContains( '>color<', $result );
	}


	public function testSearchException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'initCriteria' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'initCriteria' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->search();
	}


	public function testSearchMShopException()
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Type\Attribute\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'initCriteria' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'initCriteria' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$object->setView( $this->getViewNoRender() );

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


	protected function getViewNoRender()
	{
		$view = $this->getMockBuilder( '\Aimeos\MW\View\Standard' )
			->setConstructorArgs( array( [] ) )
			->setMethods( array( 'render', 'config' ) )
			->getMock();

		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute/type' );

		$param = ['type' => 'unittest', 'id' => $manager->findItem( 'color', [], 'product' )->getId()];
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$helper = new \Aimeos\MW\View\Helper\Access\Standard( $view, [] );
		$view->addHelper( 'access', $helper );

		return $view;
	}

}
