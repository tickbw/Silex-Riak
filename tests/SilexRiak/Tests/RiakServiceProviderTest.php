<?php

namespace SilexRiak\Tests\Extension;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use SilexRiak\RiakServiceProvider;

class RiakServiceProviderTest extends \PHPUnit_Framework_TestCase {
	
	public function setUp() {
		if (! class_exists ( 'Riak\\Client' )) {
			$this->markTestSkipped ( 'Riak\Client was not installed.' );
		}
	}
	
	public function testRegister() {
		$app = new Application ();
		$app->register ( new RiakServiceProvider (), array () );
		
		$app->get ( '/', function () use($app) {
			$app ['riak'];
		} );
		$request = Request::create ( '/' );
		$app->handle ( $request );
		
		$this->assertInstanceOf ( 'Riak\Client', $app ['riak'] );
	}
	
	public function testConfiguration() {
		$test = $this;
		
		$app = new Application ();
		$app->register ( new RiakServiceProvider (), array (
				'mongodb.connection' => array (
						'configuration' => function ($configuration) use($test) {
							$test->assertInstanceOf ( 'Riak\Client', $configuration );
						}
				) 
		) );
		
		$app->get ( '/', function () use($app) {
			$app ['riak'];
		} );
		$request = Request::create ( '/' );
		$app->handle ( $request );
	}
}