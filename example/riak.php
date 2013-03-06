<?php

require_once __DIR__ . '/../vendor/autoload.php';

class MyListener {
    function postConnect() { 
        print_r(func_get_args()); 
    }
}

$app = new Silex\Application();

$app->register(new SilexRiak\RiakServiceProvider(), array(
    'riak.connection'    => array(
        'configuration' => function($configuration) {
            $configuration->setLoggerCallable(function($logs) {
                print_r($logs);
            });    
        }
    )
));

$app->get('/', function() use($app) {
    
    # Choose a bucket name
    $bucket = $app['riak']->bucket('test');
    
    # Supply a key under which to store your data
    $person = $bucket->newObject('riak_developer_1', array(
    'name' => "John Smith",
    'age' => 28,
    'company' => "Facebook"
    		));
    
    # Save the object to Riak
    $person->store();
    
    # Fetch the object
    $person = $bucket->get('riak_developer_1');
    
    # Update the object
    $person->data['company'] = "Google";
    $person->store();
    
    print_r($person);
});

$app->run();