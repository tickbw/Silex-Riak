<?php

namespace SilexRiak;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Riak;

class RiakExtension implements ServiceProviderInterface {
	
	public function boot(Application $app) {
	}
	public function register(Application $app) {
		/**
		 * Default options
		 */
		$options = array (
				'server' => null,
				'options' => array (
						'connect' => false 
				) 
		);
		
		$app ['riak'] = $app->share ( function () use($app, $options) {
			
			$configuration = new Configuration ();
			
			$connOpts = isset ( $app ['riak.connection'] ) ? array_merge ( $options, $app ['riak.connection'] ) : $options;
			
			if (isset ( $connOpts ['configuration'] ) && is_callable ( $connOpts ['configuration'] )) {
				call_user_func_array ( $connOpts ['configuration'], array (
						$configuration 
				) );
			}
			
			return new Riak\Client ( $connOpts ['server'], $connOpts ['options'], $configuration );
		} );
		
		$app ['riak.configuration'] = $app->share ( function () use($app) {
			return $app ['riak']->getConfiguration ();
		} );
	}
}