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
 * Welcome to the call with the least amount of configuration, and the most to offer, Data Calls.
 * Simply punch in the Data Call you want, and send it on its way.
 */

$request = array(
	
	'data-call'		=> 'custom',
	'call-script'	=> array(
	
		array(
			'api'			=> 'dummy',
			'static-call'	=> 'fakecall'
		),
	
	),
	//'format'	=> 'html',
	//'linkback-color'	=> 'light',
	
);

/**
 * When you're calling a Data Call, you might find that you need to change a couple settings for certain
 * calls. To do so, simply set "call-options" to the number of the call you're trying to change, and then
 * add your changes to that call.
 * 
 * For example, if your third call in your sequence of API calls needs to be updated to set the "dayf"
 * URL query parameter to "5", then you'd change it as follows. The array of calls is zero indexed.
 * 
 * You may also change template data and variables by setting $request['call-options']['template']. See the
 * online documentation for further information on that, as it's beyond the scope of this example.
 */
$request['call-options'][2]['configure']['query']['dayf'] = 5;

/**
 * Now whenever you wish to make an API call, you can do \BitAPIHub::instance()->call($request).
 * For now we have the object set to $api, so let's continue to use it for this example.
 */
$response = $api->call($request);

// Display our example's data (The default API response format is JSON.)
BitAPIHub_Common::display($response, isset($request['format']) ? $request['format'] : 'json');
