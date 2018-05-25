<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2017
 */


namespace Aimeos\Controller\Frontend;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateController()
	{
		$controller = \Aimeos\Controller\Frontend\Factory::createController( \TestHelperFrontend::getContext(), 'basket' );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\Frontend\\Iface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Factory::createController( \TestHelperFrontend::getContext(), '' );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Factory::createController( \TestHelperFrontend::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Factory::createController( \TestHelperFrontend::getContext(), 'notexist' );
	}


	public function testCreateSubControllerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Factory::createController( \TestHelperFrontend::getContext(), 'basket/notexist' );
	}


	public function testClear()
	{
		$cache = \Aimeos\Controller\Frontend\Factory::setCache( true );

		$context = \TestHelperFrontend::getContext();

		$controller1 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'basket' );
		\Aimeos\Controller\Frontend\Factory::clear();
		$controller2 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'basket' );

		\Aimeos\Controller\Frontend\Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = \Aimeos\Controller\Frontend\Factory::setCache( true );

		$context = \TestHelperFrontend::getContext();

		$basket1 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'basket' );
		$catalog1 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'catalog' );
		\Aimeos\Controller\Frontend\Factory::clear( (string) $context );

		$basket2 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'basket' );
		$catalog2 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'catalog' );

		\Aimeos\Controller\Frontend\Factory::setCache( $cache );

		$this->assertNotSame( $basket1, $basket2 );
		$this->assertNotSame( $catalog1, $catalog2 );
	}


	public function testClearSpecific()
	{
		$cache = \Aimeos\Controller\Frontend\Factory::setCache( true );

		$context = \TestHelperFrontend::getContext();

		$basket1 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'basket' );
		$catalog1 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'catalog' );
		\Aimeos\Controller\Frontend\Factory::clear( (string) $context, 'basket' );

		$basket2 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'basket' );
		$catalog2 = \Aimeos\Controller\Frontend\Factory::createController( $context, 'catalog' );

		\Aimeos\Controller\Frontend\Factory::setCache( $cache );

		$this->assertNotSame( $basket1, $basket2 );
		$this->assertSame( $catalog1, $catalog2 );
	}

}