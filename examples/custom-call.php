<?php
/**
 * Copyright 2015 Bit API Hub
 * 
 * Example file for making custom API calls through Bit API Hub
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

/**
 * This example demonstrates how you can call any API in the world with just one line of code, $api->Call($array).
 * Simply use the dynamic call syntax with an added parameter to call the "custom" API, and you're set to go.
 */

$request = array(
	
	'api'		=> 'custom',
	'configure'	=> array(
	
		'url'		=> 'https://api.github.com',
		'uri'		=> '/repos/vmg/redcarpet/issues',
		'method'	=> 'get',
		'query'		=> array(
		
			'state'	=> 'closed',
		
		),
	
	),
	//'language'	=> 'es',
	//'format'	=> 'xml',
	
);

$response = $api->call($request);

// Display our example's data (The default API response format is JSON.)
BitAPIHub_Common::display($response, isset($request['format']) ? $request['format'] : 'json');
