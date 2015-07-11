<?php
/**
 * Copyright 2015 Bit API Hub
 * 
 * Example file for making static API calls through Bit API Hub
 */

/**
 * One time setup
 */

// Require the one-file SDK and the demo data used in every example
require('..'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'bitapihub.php');
require('common.php');

// Create an instance of the SDK
$api = BitAPIHub::instance(BitAPIHub_Common::$config);

/**
 * Now whenever you wish to make an API call, you can do \BitAPIHub::instance()->call($request).
 * For now we have the object set to $api, so let's continue to use it for this example.
 */

$request = array(
	
	'api'			=> 'blockchain',
	'static-call'	=> 'getdifficulty',
	//'format'		=> 'json',
	//'language'		=> 'es',
	
);

$response = $api->call($request);

// Display our example's data (The default API response format is JSON.)
BitAPIHub_Common::display($response, isset($request['format']) ? $request['format'] : 'json');
