<?php

require_once __DIR__ . '/../vendor/autoload.php';

class MyListener {
    function postConnect() { 
        print_r(func_get_args()); 
    }
}

$app = new Silex\Application();

$app->register(new SilexRiak\RiakExtension(), array(
    'riak.connection'    => array(
        'configuration' => function($configuration) {
            $configuration->setLoggerCallable(function($logs) {
                print_r($logs);
            });    
        }
    )
));

$app->get('/', function() use($app) {
    $dbs = $app['riak']->listDatabases();
    return 'You have ' . count($dbs) . ' Databases';
});

$app->run();