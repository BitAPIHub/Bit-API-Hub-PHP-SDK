<?php
/**
 * Copyright 2015 Bit API Hub
 * 
 * Example file for making dynamic API calls through Bit API Hub
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
 * Some API calls do not provide a way for Bit API Hub to validate your call to save time.
 * However, even if we do not have a way to validate the request, you can still make the call
 * anyway in a couple ways.
 * 
 * 1. First, if the dynamic call isn't documented in our system, you just make the call the usual
 *    way. That's the purpose of this example.
 *    
 * 2. If you find that our validation is blocking a legitimate call, you can bypass our validation
 *    techniques by passing the "no-validate" parameter along with your call. Please note that API
 *    providers may choose to force validation on calls they've configured on our system, or even
 *    block dynamic calls they have not configured on our system. That provides them with extra
 *    security, and reduces their bandwidth needs.
 */

$request = array(
	
	'api'		=> 'blockchain',
	'configure'	=> array(
	
		'uri'		=> '/addressbalance/1EzwoHtiXB4iFwedPr49iywjZn2nnekhoj',
		'method'	=> 'get',
		'query'		=> array(
		
			'confirmations'	=> 6,
		
		),
	
	),
	// 'no-validate'	=> true,
	//'language'	=> 'es',
	//'format'	=> 'xml',
	
);

$response = $api->call($request);

// Display our example's data (The default API response format is JSON.)
BitAPIHub_Common::display($response, isset($request['format']) ? $request['format'] : 'json');
