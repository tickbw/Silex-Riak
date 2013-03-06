<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new SilexRiak\RiakServiceProvider(), array(
    'riak.connection'    => array(
        'host' => '127.0.0.1',
    	'port' => '8098'
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
    
    return print_r($person,true);
});

$app->run();