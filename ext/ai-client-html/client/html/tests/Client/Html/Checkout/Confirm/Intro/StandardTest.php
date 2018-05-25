<?php

namespace Aimeos\Client\Html\Checkout\Confirm\Intro;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2017
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Checkout\Confirm\Intro\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.service.code', 'paypalexpress' ) );

		$items = $manager->searchItems( $search );
		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}


		$view = \TestHelperHtml::getView();
		$view->confirmOrderItem = $item;
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-confirm-intro">', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$this->object->process();
	}
}
