<?php

namespace SilexRiak;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Riak;

class RiakServiceProvider implements ServiceProviderInterface {
	
	public function boot(Application $app) {
	
	}
	
	public function register(Application $app) {
		
		/**
		 * Default options
		 */
		$options = array (
				'host' => '127.0.0.1',
				'port' => '8098'
		);
		
		$app ['riak'] = $app->share ( function () use($app, $options) {
			
			$connOpts = isset ( $app ['riak.connection'] ) ? array_merge ( $options, $app ['riak.connection'] ) : $options;
			
			if (isset ( $connOpts ['configuration'] ) && is_callable ( $connOpts ['configuration'] )) {
				call_user_func_array ( $connOpts ['configuration'], array (
						$configuration 
				) );
			}
			
			return new Riak\Client ( $connOpts ['host'], $connOpts ['port'], $configuration );
		} );
		
		$app ['riak.configuration'] = $app->share ( function () use($app) {
			return $app ['riak']->getConfiguration ();
		} );
	}
}