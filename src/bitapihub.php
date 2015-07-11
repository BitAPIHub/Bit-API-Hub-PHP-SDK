<?php
/**
 * Copyright 2015 Bit API Hub
 * 
 * Official Bit API Hub PHP SDK
 */
 
class BitAPIHub
{
	/**
	 * @var array $instances The instances of \BitAPIHub
	 */
	protected static $instances = array();
	
	/**
	 * @var array $config The array of configuration data for the SDK
	 */
	protected $config = array();
	
	/**
	 * @var int $http_status The HTTP status code as reported by cURL
	 */
	public $http_status = 0;
	
	/**
	 * @var VERSION The SDK version
	 */
	const VERSION = 0.1;
	
	/**
	 * The \BitAPIHub multiton instance
	 * 
	 * @param array $config An array of any configuration data to customize the SDK with
	 * 						It must at least contain your credentials when the class is instantiated, the array is
	 * 						irrelevant otherwise
	 * 
	 * @return object The \BitAPIHub object
	 */
	public static function instance(array $config = array(), $instance = '_default_')
	{
		if (!array_key_exists($instance, static::$instances)) {
			static::$instances[$instance] = new static($config);
		}
		
		return static::$instances[$instance];
	}
	
	/**
	 * Let's roll - The engine uses a stripped down version of OAuth 1.0a.
	 * 
	 * @param array $config An array of any configuration data to customize the SDK with
	 * 						Must at least contain your credentials.
	 */
	public function __construct(array $config = array())
	{
		// Don't be a tyrant
		$original_tz = date_default_timezone_get();
		
		// Set the timezone to UTC so it's synced with the API engine for time().
		date_default_timezone_set('UTC');
		
		// Defaults
		$this->config = array(
			
			'oauth'	=> array(
				
				'oauth_nonce'				=> $this->nonce(),
				'oauth_timestamp'			=> time(),

				/**
				 * User defined
				 */
				
				'oauth_consumer_key'		=> null,
				'oauth_consumer_secret'		=> null,
			
			),
			
			'url'		=> 'https://api.bitapihub1.com/v1/',
			'method'	=> 'POST',
			
		);
		
		// Default cURL options
		$this->config['curl'] = array(
			
			CURLOPT_URL				=> $this->config['url'],
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_USERAGENT		=> 'Bit API Hub Official SDK/'.static::VERSION,
			CURLOPT_CONNECTTIMEOUT	=> 5, // Decrease this if it becomes a problem.
			CURLOPT_PROTOCOLS		=> CURLPROTO_HTTPS,
			CURLOPT_SSL_VERIFYPEER	=> 1,
			
		);
		
		// Set the user defined configuration data
		$this->config = array_replace_recursive($this->config, $config);
		
		// Set the timezone back so the customer application doesn't break.
		date_default_timezone_set($original_tz);
		
		/**
		 * TIPS FOR IMPROVING PERFORMANCE
		 *
		 * Consider changing CURLOPT_CONNECTTIMEOUT to a longer wait time in seconds if you're posting
		 * large amounts of data. If you notice a performance decrease in your API usage, try reducing
		 * the number of seconds to wait.  The default is 5 seconds as opposed to the cURL default of
		 * 5 minutes.
		 *
		 * Use a different instance of the class object to maintain different settings for each type of
		 * call you wish to make. @See \BitAPIHub::instance()
		 *
		 * DEBUGGING?
		 * When you're debugging your script, flip on verbose mode with CURLOPT_VERBOSE.
		 *
		 * CURLOPT_VERBOSE @link http://curl.haxx.se/libcurl/c/CURLOPT_VERBOSE.html
		 */
	}
	
	/**
	 * Make an API call to Bit API Hub
	 * 
	 * @param array $call_data The array of data to send in your request
	 * 
	 * @return mixed The local error array, or the response from Bit API Hub
	 */
	public function call(array $call_data = array())
	{
		// Make sure the script knows what call data we're using
		$this->config['body'] = $this->format_post_fields($call_data);
		
		// Add additional data to the cURL request
		$this->config['curl'] += array(
			
			CURLOPT_POSTFIELDS		=> $this->config['body'], // Only post is supported for now.
			CURLOPT_HTTPHEADER		=> array($this->header()),
			CURLOPT_CUSTOMREQUEST	=> $this->config['method'],
			
		);
		
		// Run the cURL request
		$curl = curl_init();
		curl_setopt_array($curl, $this->config['curl']);
		
		// Run the call
		$api_response = curl_exec($curl);
		
		// As we ran the call, we can now grab the error information if any exists.
		$curl_error = curl_error($curl);
		
		/*
		 * Set the status code in case the developer wishes to use it instead of any code returned by
		 * the API server.
		 */
		$this->http_status	= curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		// If the cURL request failed, respond with an internal message.
		if($curl_error){
		
			return array(
				
				'internal'	=> true,
				'message'	=> $curl_error,
				'details'	=> curl_getinfo($curl),
				
			);
		
		// Talked to the API server
		} else {
			
			// Return the data from the API server.
			return $api_response;
			
		}
	}
	
	/**
	 * Create the string we're going to sign
	 * 
	 * @return string The string to sign
	 */
	protected function string_to_sign()
	{
		// Preserve the original config
		$oa_vars = $this->config['oauth'];
		
		// Encode the post contents
		$oa_vars['body'] = urlencode(urlencode(base64_encode(json_encode($this->config['body']))));
		
		// Sort the vars as per OAuth 1.0a spec
		ksort($oa_vars);
		
		// Encode all OAuth specific vars
		$oa_vars_encoded = array();
		
		foreach ($oa_vars as $key => $value) {
			$oa_vars_encoded[] = $key.'='.$value;
		}
		
		// Create the string to sign and return it
		return urlencode(implode('&', $oa_vars_encoded));
	}
	
	/**
	 * Generate the signature for the call.
	 * 
	 * @return string The signature string
	 */
	protected function sign()
	{
		$hash = hash_hmac('sha256', $this->string_to_sign(), $this->config['oauth']['oauth_consumer_secret']);
		return urlencode(base64_encode($hash));
	}
	
	/**
	 * Generate the authorization headers for the call.
	 * 
	 * @return string The authorization header string
	 */
	protected function header()
	{
		// Don't send the secret, and preserve the original config
		$oa_vars = $this->config['oauth'];
		unset($oa_vars['oauth_consumer_secret']);
		
		$headers = array('oauth_signature' => $this->sign()) + $oa_vars;
		
		$header_parts = array();
		
		foreach($headers as $key => $val){
			$header_parts[] = $key.'='.$val;
		}
		
		return "X-Authorization: OAuth ".implode('&',$header_parts);
	}
	
	/**
	 * Generate a nonce for the OAuth call (The API engine only accepts an alphanumeric nonce for now.)
	 * 
	 * @return string The nonce for use in the OAuth call
	 */
	protected function nonce()
	{
		return str_replace(array('=', '+', '/'), '', base64_encode(openssl_random_pseudo_bytes(32)));
	}
	
	/**
	 * Format the array of post data for use with CURLOPT_POSTFIELDS. We may need to add a way to process
	 * files later on, so we encode each key separatly if it's an array.
	 * 
	 * @param array $post_fields The array of post data sent to Bit API Hub
	 * @return array $post_fields with any second level arrays JSON encoded
	 */
	protected function format_post_fields(array $post_fields)
	{
		foreach ($post_fields as $key => $val) {
			$post_fields[$key] = json_encode($val);
		}
		
		return $post_fields;
	}
}
