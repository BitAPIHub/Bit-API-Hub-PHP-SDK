<?php
/**
 * Copyright 2015 Bit API Hub
 * 
 * Data common to all examples
 */

class BitAPIHub_Common
{
	/**
	 * Configuration for every example call
	 * @var array
	 */
	public static $config = array(

		'oauth' => array(
	
			'oauth_consumer_key'	=> '5674241b-6fe7-48c3-abb5-286d26d91879',
			'oauth_consumer_secret'	=> '11a9e89e-07b7-4fc5-8304-d171ff63f132',
			
		),
		
	);
	
	/**
	 * Display the example output
	 * 
	 * @param mixed $response	The local error array from cURL, or the JSON string requested from Bit API Hub
	 * @param string $format	The response format requested from the server
	 */
	public static function display($response, $format)
	{
		if (is_array($response)) {
		
			// An internal error occurred. Check your cURL settings.
			var_dump($response);
		
		} else{
		
			/**
			 * You have a response from the server in the format you've selected. If you didn't select a format in
			 * your request, then you'll receive the response in JSON format. For this example, we'll proceed with
			 * the default JSON response.
			 */
		
			$response_array = json_decode($response, true);
			
			if (isset($response_array['errors'])) {
		
				// You've got problems. :)
				$errors = $response_array['errors'];
		
				$message_format = 'HTTP Status Code: %d<br>Error Type: %s<br>Error Message:<br>%s';
		
				// Show the error from the server.
				echo sprintf($message_format, $errors['code'], $errors['type'], nl2br($errors['message']));
		
			} else {
		
				/**
				 * There weren't any errors, so we use the response data as we so desire. For this example, we'll just
				 * print it to the screen.
				 */
				
				$response_encoded = $format === 'json' ? $response : htmlspecialchars($response);
				
				echo '
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/styles/tomorrow.min.css">
				<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/8.5/highlight.min.js"></script>
				<script>hljs.initHighlightingOnLoad();</script>
				
				<span style="font-family:arial">Response:</span>
				<pre><code class="'.$format.'" style="white-space:normal">'.$response_encoded.'</code></pre>';
		
				/**
				 * The API server will send you the HTTP status code directly with your response for easy parsing, but
				 * you may also grab the status code that cURL reports by using $api->http_status.
				*/
				
				echo '<span style="font-family:arial">HTTP status code: '.BitAPIHub::instance()->http_status.'</span>';
				
				// Display the HTML response if we have one.
				if (empty($response_array) && $format === 'html') {
					
					echo '<br><br><span style="font-family:arial">HTML:</span><br><br>'.$response;
					
				}
		
			}
		
		}
	}
}
