<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Admin\JQAdm\Attribute\Image;


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

		$this->object = new \Aimeos\Admin\JQAdm\Attribute\Image\Standard( $this->context, $templatePaths );
		$this->object->setAimeos( \TestHelperJqadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->view, $this->context );
	}


	public function testCreate()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );

		$this->view->item = $manager->createItem();
		$result = $this->object->create();

		$this->assertContains( 'item-image', $result );
		$this->assertNull( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );

		$this->view->item = $manager->findItem( 'xs', ['media'], 'product', 'size' );
		$result = $this->object->copy();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'src="/prod_97x93/199_prod_97x93.jpg"', $result );
	}


	public function testDelete()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );

		$this->view->item = $manager->createItem();
		$result = $this->object->delete();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );

		$this->view->item = $manager->findItem( 'xs', ['media'], 'product', 'size' );
		$result = $this->object->get();

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertContains( 'src="/prod_97x93/199_prod_97x93.jpg"', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );

		$item = $manager->findItem( 'xs', [], 'product', 'size' );
		$item->setCode( 'jqadm-test-image' );
		$item->setId( null );

		$item = $manager->saveItem( $item );


		$param = array(
			'site' => 'unittest',
			'image' => array(
				'attribute.lists.id' => array( '' ),
				'media.languageid' => array( 'de' ),
				'media.label' => array( 'test' ),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$file = $this->getMockBuilder( '\Psr\Http\Message\UploadedFileInterface' )->getMock();
		$request = $this->getMockBuilder( '\Psr\Http\Message\ServerRequestInterface' )->getMock();
		$request->expects( $this->any() )->method( 'getUploadedFiles' )
			->will( $this->returnValue( array( 'image' => array( 'files' => array( $file ) ) ) ) );

		$helper = new \Aimeos\MW\View\Helper\Request\Standard( $this->view, $request, '127.0.0.1', 'test' );
		$this->view ->addHelper( 'request', $helper );

		$this->view->item = $item;


		$name = 'AdminJQAdmAttributeImageSave';
		$this->context->getConfig()->set( 'controller/common/media/name', $name );

		$cntlStub = $this->getMockBuilder( '\\Aimeos\\Controller\\Common\\Media\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'add' ) )
			->getMock();

		\Aimeos\Controller\Common\Media\Factory::injectController( '\\Aimeos\\Controller\\Common\\Media\\' . $name, $cntlStub );

		$cntlStub->expects( $this->once() )->method( 'add' );


		$mediaStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Media\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		\Aimeos\MShop\Factory::injectManager( $this->context, 'media', $mediaStub );

		$mediaStub->expects( $this->once() )->method( 'saveItem' )
			->will( $this->returnValue( $mediaStub->createItem() ) );


		\Aimeos\MShop\Factory::setCache( true );

		$result = $this->object->save();

		\Aimeos\MShop\Factory::setCache( false );

		$item = $manager->getItem( $item->getId(), array( 'media' ) );
		$manager->deleteItem( $item->getId() );

		$this->assertNull( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
		$this->assertEquals( 1, count( $item->getListItems( 'media' ) ) );
	}


	public function testSaveException()
	{
		$object = $this->getClientMock( 'fromArray' );

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$object->save();
	}


	public function testSaveMShopException()
	{
		$object = $this->getClientMock( 'fromArray' );

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$object->save();
	}


	public function testSearch()
	{
		$this->assertNull( $this->object->search() );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\Aimeos\Admin\JQAdm\Exception' );
		$this->object->getSubClient( 'unknown' );
	}


	public function getClientMock( $method )
	{
		$object = $this->getMockBuilder( '\Aimeos\Admin\JQAdm\Attribute\Image\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( [$method] )
			->getMock();

		$view = \TestHelperJqadm::getView();
		$view->item = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' )->createItem();

		$object->setAimeos( \TestHelperJqadm::getAimeos() );
		$object->setView( $view );

		return $object;
	}
}
