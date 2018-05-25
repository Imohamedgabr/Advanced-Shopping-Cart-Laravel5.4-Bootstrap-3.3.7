<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\MW\View\Helper\Site;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$view = new \Aimeos\MW\View\Standard();
		$view->pageSitePath = [
			1 => new TestSite( 1, 'label1' ),
			3 => new TestSite( 3, 'label3' ),
		];

		$this->object = new \Aimeos\MW\View\Helper\Site\Standard( $view );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\View\\Helper\\Site\\Iface', $this->object->transform() );
	}


	public function testLabel()
	{
		$this->assertEquals( 'label3', $this->object->transform()->label() );
	}


	public function testMatch()
	{
		$this->assertEquals( 'label1', $this->object->transform()->match( 1 ) );
	}


	public function testReadonly()
	{
		$this->assertEquals( 'readonly', $this->object->transform()->readonly( 1 ) );
	}
}


class TestSite
{
	private $id;
	private $label;

	public function __construct( $id, $label )
	{
		$this->id = $id;
		$this->label = $label;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getLabel()
	{
		return $this->label;
	}
}