<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

// tgvqjtbHtsqA

require_once(dirname(__FILE__).'/library/OAuthRequester2Legged.php');

/**
 * Minotovideo
 *
 * A library for for interacting with Minoto Video Platform (http://minotovideo.com/).
 * 
 * @package		Minoto
 * @author		Xiao-Hu Tai
 * @copyright	Copyright (c) 2013, Datiq BV
 * @link		http://minotovideo.com
 * @link        http://datiq.com
 */

/**
 * Usage:
 * var api = new Minoto();
 * api->initialize( $params );
 * api->upload( 'video.mp4', 'Video title', 'Video description' );

Available drivers:
	$this->minoto->video->
	$this->minoto->publisher->
	$this->minoto->reseller->
	$this->minoto->statistics->
 
 */
class Minoto extends CI_Driver_Library {

	protected $valid_drivers = array(
		'minoto_video',
		'minoto_publisher',
		'minoto_reseller',
		'minoto_statistics'
	);
	
	public $CI;
	
	public $api;
	public $publisher_secret;
	
	protected $consumer_key;
	protected $consumer_secret;
	protected $token;
	protected $token_secret;
	protected $signature_methods;

	protected $credentials;
	protected $_log;
	
	public $reseller_id;
	public $publisher_id;
	public $video_id;
	
	public function __construct($config = array())
	{
		// parent::__construct();
		$this->CI   =& get_instance();
		
		if(! empty($config))
		{
			$this->initialize($config);
		} else {
			$this->CI->config->load('minoto', FALSE, TRUE);
			$this->initialize($this->CI->config->config);
		}
		
		// todo: make language files for this library
		// $this->CI->load->language('minotovideo');
    }
	
	/**
	 * Set values by key-value pairs. The publisher ID should be set based on the
	 * user/publisher, depending on the roles/rights system. Otherwise the default
	 * publisher is used as defined in the config file.
	 */
	public function initialize($config)
	{
		// $this->_dump($this->CI->config);
		$keys = array(
			'api',
			'consumer_key',
			'consumer_secret',
			'token',
			'token_secret',
			'signature_methods',
			'publisher_id',
			'reseller_id',
			'publisher_secret'
		);
		$prefix = 'minoto_';
		foreach($keys as $key)
		{
			if( isset($config[$prefix.$key]) )
			{
				$this->$key = $config[$prefix.$key];
			}
		}
		$this->_setCredentials();
	}
	
	/**
	 * A helper function that sets credentials for calls to Minoto.
	 */
	protected function _setCredentials()
	{
		$this->credentials = array(
			'consumer_key'      => $this->consumer_key,
			'consumer_secret'   => $this->consumer_secret,
			'token'             => $this->token,
			'token_secret'      => $this->token_secret,
			'signature_methods' => $this->signature_methods
		);
		
		// Instantiate a very basic OAuthStore, to allow 2-legged OAuth
		$store = OAuthStore::instance('2Legged', $this->credentials);
	}
	
	public function log($level = 'error', $message, $php_error = FALSE)
	{
		$this->_log =& load_class('Log');
		$this->_log =& load_class('Minoto_Log','libraries','');
		$this->_log->write_log($level, $message, $php_error);
	}
	/*
	@param string	uri				might include parameters
	@param string	method			GET, PUT, POST etc.
	@param string	parameters		additional post parameters, urlencoded (RFC1738)
	@param array	headers			headers for request
	@param string	body			optional body of the OAuth request (POST or PUT)
	
	function __construct ( $uri = null, $method = 'GET', $parameters = '', $headers = array(), $body = null )
	*/
	public function doRequest($requestUrl = null, $requestMethod = 'GET', $requestParams = array(), $requestHeaders = array(), $requestBody = null)
	{
		$requester = new OAuthRequester2Legged($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
		
		// todo?
		$curl_options = array(CURLOPT_HTTPHEADER => array('Content-Type: vnd.com.minoto-video.video+php', 'Accept: vnd.com.minoto-video.video+php'));
		
		$result = $requester->doRequest($curl_options);
		$data   = unserialize( $result['body'] );
		
		if($requester->isSuccess()){
			// $this->_dump($data);
			// $this->log('info', '>>>successful: '.print_r($data, true) );
			return $data;
		} else {
			// $this->_dump($data);
			$this->log('info', '>>>error: '.$requestUrl.' -->'.print_r($data, true) );
			return false;
			// throw new Exception('-->' . $data);
		}
	}
	
	/*
	 * Adding a video goes in to steps.
	 * 1) Do a POST request with a vnd.com.minoto-video.video resource to announce the video
	 * 2) Extract the upload url and upload token from the response and use this for the actual file upload
	 */
	public function upload($file, $title, $description)
	{
		$file = dirname(__FILE__).'/'.$file; // TESTING ONLY.
		echo '<pre>'.$file.'</pre>';
		
		// 1: Create resource to post
		$video              = new stdClass();
		$video->title       = $title;
		$video->description = $description;

		$params = array();
		
		// Build the request
		$requestUrl = $this->api . '/publishers/'.$this->publisher_id.'/videos';
		$requester = new OAuthRequester2Legged($requestUrl, 'POST', $params, serialize($video));

		// Add Accept header to request php representation
		$curl_options = array(CURLOPT_HTTPHEADER => array('Content-Type: vnd.com.minoto-video.video+php', 'Accept: vnd.com.minoto-video.video+php'));

		// Perform the request.
		$result = $requester->doRequest($curl_options);

		// Check if the result was succesful
		if(!$requester->isSuccess()){
			$errors = unserialize($result['body']);
			
			// todo: log
			$this->_dump($errors);
			exit;
		}

		// Unserialize results
		$video = unserialize($result['body']);
		
		$this->_dump($video);

		$upload_uri   = $video->upload_uri;
		$upload_token = $video->upload_token;
		$curl_command = 'curl -s -F "UPLOAD_IDENTIFIER=' . $upload_token . '" -F "file=@' . $file . '" "' . $upload_uri . '"';

		// print "Curl command: \n";
		// print $curl_command; 
		$return_var = null;
		$output_arr = null;
		exec($curl_command, $output_arr, $return_var);
		$output = implode("\n", $output_arr);

		if($return_var != 0) {
			print "Failed during file upload:";
			print $output;
			exit($return_var);
		} else {
			print $video->id;
			exit(0);
		}
	}
	
	/**
	 * Returns an array with videos
	 */
	public function getVideos()
	{
		$params = array();
		$requester = new OAuthRequester2Legged($this->api . '/publishers/'.$this->publisher_id.'/videos', 'GET', $params);
		$curl_options = array(CURLOPT_HTTPHEADER => array('Content-Type: vnd.com.minoto-video.video+php', 'Accept: vnd.com.minoto-video.video+php'));
		$result = $requester->doRequest($curl_options);
		
		if(!$requester->isSuccess()){
			echo 'Error';
		} else {
			echo 'Success';
		}
		$data = unserialize($result['body']);
		$this->_dump($data);
		
		if(!$requester->isSuccess())
		{
			$error = unserialize($result['body']);
			throw new Exception('Minotovideo->getVideos: ' . $error);
		}
		return $data;
	}
	/**
	 * Returns the video given the $video_id.
	 * Video not found.
	 */
	public function getVideo($video_id)
	{
		$params = array();
		$requestUrl= $this->api . '/publishers/'.$this->publisher_id.'/videos/'.$video_id;
		$requester = new OAuthRequester2Legged($requestUrl, 'GET', $params);
		$curl_options = array(CURLOPT_HTTPHEADER => array('Content-Type: vnd.com.minoto-video.video+php', 'Accept: vnd.com.minoto-video.video+php'));
		$result = $requester->doRequest($curl_options);

		if(!$requester->isSuccess()){
			echo 'Error';
			$errors = unserialize($result['body']);
			$this->_dump($errors);
			
			echo "Minotovideo->getVideo($video_id): " . $errors[0]->message;
			
			//throw new Exception("Minotovideo->getVideo($video_id): " . $error[0]->message);
			exit;
		} else {
			echo 'Success';
			// $this->_dump($result);
			$body = unserialize($result['body']);
			$this->_dump($body);
		}
	}

	public function updateVideo($params)
	{
		$params;
	}
	
	public function _dump($var)
	{
		echo '<pre>';
		// var_dump($var);
		print_r($var);
		echo '</pre>';
	}
	
}