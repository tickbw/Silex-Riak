<?php

namespace SilexRiak\Tests\Extension;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use SilexRiak\RiakExtension;

class RiakExtensionTest extends \PHPUnit_Framework_TestCase {
	
	public function setUp() {
		if (! class_exists ( 'Riak\\Client' )) {
			$this->markTestSkipped ( 'Riak\Client was not installed.' );
		}
	}
	
	public function testRegister() {
		$app = new Application ();
		$app->register ( new RiakExtension (), array () );
		
		$app->get ( '/', function () use($app) {
			$app ['riak'];
		} );
		$request = Request::create ( '/' );
		$app->handle ( $request );
		
		$this->assertInstanceOf ( 'Riak\Client', $app ['riak'] );
	}
	public function testConfigurationAndEventManager() {
		$test = $this;
		
		$app = new Application ();
		$app->register ( new RiakExtension (), array (
				'mongodb.connection' => array (
						'configuration' => function ($configuration) use($test) {
							$test->assertInstanceOf ( 'Riak\Client', $configuration );
						},
						'eventmanager' => function ($eventmanager) use($test) {
							$test->assertInstanceOf ( 'Doctrine\Common\EventManager', $eventmanager );
						} 
				) 
		) );
		
		$app->get ( '/', function () use($app) {
			$app ['riak'];
		} );
		$request = Request::create ( '/' );
		$app->handle ( $request );
	}
	public function testOptions() {
		$test = $this;
		
		$app = new Application ();
		$app->register ( new RiakExtension (), array (
				'mongodb.connection' => array (
						'server' => '127.0.0.1:9999',
						'options' => array (
								'connect' => false,
								'persistent' => 'c83d9d59bf24ae3a6dc5a30cb47ebbba' 
						) 
				) 
		) );
		
		$app->get ( '/', function () use($app) {
			$app ['riak'];
		} );
		$request = Request::create ( '/' );
		$app->handle ( $request );
		
		$app ['riak']->initialize ();
		$this->assertSame ( '127.0.0.1:9999', $app ['mongodb']->getServer () );
		
		$riak = $app ['riak']->getRiak ();
		$reflect = new \ReflectionClass ( $riak );
		
		$property = $reflect->getProperty ( 'connected' );
		$property->setAccessible ( true );
		$this->assertFalse ( $property->getValue ( $riak ) );
	}
}