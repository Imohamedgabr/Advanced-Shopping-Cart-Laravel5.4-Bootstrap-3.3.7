<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Controller\Common\Coupon\Import\Csv\Processor\Code;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $endpoint;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelperCntl::getContext();
		$this->endpoint = new \Aimeos\Controller\Common\Coupon\Import\Csv\Processor\Done( $this->context, [] );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();
	}


	public function testProcess()
	{
		$mapping = array(
			0 => 'coupon.code.code',
			1 => 'coupon.code.count',
			2 => 'coupon.code.datestart',
			3 => 'coupon.code.dateend',
		);

		$data = array(
			0 => 'jobimporttest',
			1 => '10',
			2 => '2000-01-01 00:00:00',
			3 => '',
		);

		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'coupon' );
		$codeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'coupon/code' );

		$coupon = $manager->saveItem( $manager->createItem()->setProvider( 'test' ) );
		$couponCode = $codeManager->createItem();
		$couponCode->setParentId( $coupon->getId() );

		$object = new \Aimeos\Controller\Common\Coupon\Import\Csv\Processor\Code\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $couponCode, $data );

		$codeManager->deleteItem( $couponCode->getId() );
		$manager->deleteItem( $coupon->getId() );


		$this->assertEquals( 10, $couponCode->getCount() );
		$this->assertEquals( 'jobimporttest', $couponCode->getCode() );
		$this->assertEquals( '2000-01-01 00:00:00', $couponCode->getDateStart() );
		$this->assertEquals( null, $couponCode->getDateEnd() );
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'coupon.code.code',
			1 => 'coupon.code.count',
		);

		$data = array(
			0 => 'jobimporttest',
			1 => '10',
		);

		$dataUpdate = array(
			0 => 'jobimporttest',
			1 => '5',
		);


		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'coupon' );
		$codeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'coupon/code' );

		$coupon = $manager->saveItem( $manager->createItem()->setProvider( 'test' ) );
		$couponCode = $codeManager->createItem();
		$couponCode->setParentId( $coupon->getId() );

		$object = new \Aimeos\Controller\Common\Coupon\Import\Csv\Processor\Code\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $couponCode, $data );
		$object->process( $couponCode, $dataUpdate );

		$codeManager->deleteItem( $couponCode->getId() );
		$manager->deleteItem( $coupon->getId() );


		$this->assertEquals( 5, $couponCode->getCount() );
		$this->assertEquals( 'jobimporttest', $couponCode->getCode() );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'coupon.code.code',
			1 => 'coupon.code.count',
		);

		$data = array(
			0 => 'jobimporttest',
			1 => '',
		);


		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'coupon' );
		$codeManager = \Aimeos\MShop\Factory::createManager( $this->context, 'coupon/code' );

		$coupon = $manager->saveItem( $manager->createItem()->setProvider( 'test' ) );
		$couponCode = $codeManager->createItem();
		$couponCode->setParentId( $coupon->getId() );

		$object = new \Aimeos\Controller\Common\Coupon\Import\Csv\Processor\Code\Standard( $this->context, $mapping, $this->endpoint );
		$object->process( $couponCode, $data );

		$codeManager->deleteItem( $couponCode->getId() );
		$manager->deleteItem( $coupon->getId() );


		$this->assertEquals( 0, $couponCode->getCount() );
	}
}