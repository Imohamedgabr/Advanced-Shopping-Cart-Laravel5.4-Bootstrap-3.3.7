<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2017
 */


namespace Aimeos\Client\Html\Account\Profile;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $templatePaths;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();
		$this->templatePaths = \TestHelperHtml::getHtmlTemplatePaths();
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreateClient()
	{
		$client = \Aimeos\Client\Html\Account\Profile\Factory::createClient( $this->context, $this->templatePaths );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateClientName()
	{
		$client = \Aimeos\Client\Html\Account\Profile\Factory::createClient( $this->context, $this->templatePaths, 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\Html\\Iface', $client );
	}


	public function testCreateClientNameInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Account\Profile\Factory::createClient( $this->context, $this->templatePaths, '$$$' );
	}


	public function testCreateClientNameNotFound()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		\Aimeos\Client\Html\Account\Profile\Factory::createClient( $this->context, $this->templatePaths, 'notfound' );
	}

}
