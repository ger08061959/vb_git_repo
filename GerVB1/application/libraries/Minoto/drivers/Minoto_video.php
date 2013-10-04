<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

// $this->minoto->video->get($pid, $vid)
// $this->minoto->video->getVideo($pid, $vid)
//
class Minoto_video extends CI_Driver {

	// $this->minoto->vidoe->get($vid)
	public function getVideo($pid = null, $vid)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/'.$publisher_id.'/videos/'.$vid;
		$requestMethod  = 'GET';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	// @param $video Minoto video object
	// --> function only for testing...
	public function getFileUploadForm($pid = null, $video)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/'.$publisher_id.'/videos/'.$video->id.'/file/uploadform';
		// $video->upload_uri;
		$requestMethod  = 'GET';
		$requestParams  = array(
			'UPLOAD_IDENTIFIER' => $video->upload_token,
			'redirect_url' => '',
			'stylesheet_url' => ''
		);
		
		$requester = new OAuthRequester2Legged($requestUrl, $requestMethod, $requestParams);
		$result = $requester->doRequest(array());
		
		if($requester->isSuccess()){
			// $this->log('info', '>>>successful: '.print_r($data, true) );
			return $result['body'];
		} else {
			$this->log('info', '>>>error: '.$requestUrl );
			$this->log('info', '>>>error: '.print_r($result, true) );
			return false;
		}
	}
	
	public function getFileUploadFormUrl($pid, $vid, $token, $redirect)
	{
		return $this->api . '/publishers/'.urlencode($pid).'/videos/'.urlencode($vid).'/file/uploadform?UPLOAD_IDENTIFIER='.urlencode($token).'&redirect_url='.urlencode($redirect);
	}

	public function getUploadProgress($pid, $vid, $token)
	{
		$requestUrl     = $this->api . '/publishers/'.$pid.'/videos/'.$vid.'/file/progress';
		$requestMethod  = 'GET';
		$requestParams  = array( 'UPLOAD_IDENTIFIER' => $token, 'output_format' => 'json' );
		$requester = new OAuthRequester2Legged($requestUrl, $requestMethod, $requestParams);
		$result = $requester->doRequest(array());
		return $result['body'];
		
		
		// $this->log('info', '>>>test: '.$requestUrl );
		// $this->log('info', '>>>test: '.print_r($result, true) );
	}
	//
	// Statistics
	//
	public function getStatistics($pid = null, $vid, $params = array())
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/statistics/intervals';
		$requestMethod  = 'GET';
		
		$requestParams  = array(
			'span' => 'day',
			'video' => $vid
		);
		
		// add from and to iff they are both set
		if( isset($params) && isset($params['from']) && isset($params['to']) ){
			$requestParams['from'] = $params['from'];
			$requestParams['to']   = $params['to'];
		}
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams);
	}
	
	public function getAttentionStatistics($pid = null, $vid, $params = array())
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/statistics/attention';
		$requestMethod  = 'GET';
		
		$requestParams  = array(
			'video' => $vid
		);
		
		// add from and to iff they are both set
		if( isset($params) && isset($params['from']) && isset($params['to']) ){
			$requestParams['from'] = $params['from'];
			$requestParams['to']   = $params['to'];
		}
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams);
	}
	//
	// Screenshots
	//
	public function getScreenshots($pid = null, $vid)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/screenshots';
		$requestMethod  = 'GET';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	public function getScreenshot($pid = null, $vid)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/screenshot';
		$requestMethod  = 'GET';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	public function setScreenshot($pid = null, $vid, $params = array())
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/screenshot';
		$requestMethod  = 'PUT';
		$requestParams  = array();
		
		// $this->log('info', '>>> HELLO: '.$params['screenshot']);
		
		$screenshot          = new stdClass();
		$screenshot->uri     = $params['screenshot'];
		$screenshot->width   = 848;
		$screenshot->height  = 478;
		$requestHeaders     = serialize($screenshot);

		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders);
	}
	//
	// Thumbnails
	//
	public function getThumbnails($pid = null, $vid)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/thumbnails';
		$requestMethod  = 'GET';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	public function getThumbnail($pid = null, $vid)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/thumbnail';
		$requestMethod  = 'GET';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	public function setThumbnail($pid = null, $vid, $params = array())
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/thumbnail';
		$requestMethod  = 'PUT';
		$requestParams  = array();
		
		// $this->log('info', '>>> HELLO: '.$params['screenshot']);
		
		$thumbnail          = new stdClass();
		$thumbnail->uri     = $params['thumbnail'];
		$thumbnail->width   = 848;
		$thumbnail->height  = 478;
		$requestHeaders     = serialize($thumbnail);
		
		
		/*
			'uri' => $post['thumbnail'],
			'width' => 848,
			'height' => 478
		);*/
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders);
	}
	
	//
	// Security
	// $protected ('true'|'false'|'rules')
	//
	public function setProtected($pid = null, $vid, $protected = 'true')
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid;
		$requestMethod  = 'PUT';
		$requestParams  = array();
		
		$video              = new stdClass();
		
		// $this->log('info', '>>> HELLO: ['.$protected.']');
		
		// 'true' does not work, needs to be '1'
		if( $protected == 'true' || $protected=='1' || $protected===true )
			$protected = true;
		else
			$protected = false;
			
		$video->protected   = $protected ? '1' : '0';
		/*
		if($protected){
			$allowedDomains = array(
				// global
				// 'xiao.datiq.net',
				'dev.fwd.datiq.net',
				'stage.fwd.datiq.net',
				'inginsurance.fwd.datiq.net',
				// per publisher, internal and external domains
				'ing-insim.dutchview.nl',
				'ing-internalinsim.dutchview.nl',
				// per publisher, additional domains
				'intranet.fwd.insim.biz'
			);
			// $video->rules = 'domain IN ('.implode(',', $allowedDomains).')';
			$video->rules = '(domain = dev.fwd.datiq.net)';
			$this->log('info', '>>> Minoto_video->setProtected : rules ['.$video->rules.']');
		}
		*/
		// $this->log('info', '>>> HELLO: ['.$video->protected.']');
		
		$requestHeaders     = serialize($video);
		$requestBody        = null;
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
	}
	
	//
	// Transcodings
	//
	public function getTranscodings($pid = null, $vid)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/transcodings';
		$requestMethod  = 'GET';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	public function addTranscoding($pid = null, $vid, $key)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/transcodings';
		$requestMethod  = 'POST';
		$requestParams  = array();
		
		$transcoding      = new stdClass();
		$transcoding->key = $key;
		
		$requestHeaders     = serialize($transcoding);
		$requestBody        = null;
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
	}
	
	public function getTranscodingDetails($pid = null, $vid, $trk)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/transcodings/'.$trk;
		return $this->doRequest($requestUrl);
	}
	
	public function removeTranscoding($pid = null, $vid, $trk)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/'.$vid.'/transcodings/'.$trk;
		$requestMethod  = 'DELETE';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	
	
	// $this->minoto->publisher->videos($pid)
	/*
	public function get($pid = null)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/'.$publisher_id.'/videos';
		$requestMethod  = 'GET';
		// $requestParams  = array();
		// $requestHeaders = array();
		// $requestBody    = null;

		return $this->doRequest($requestUrl, $requestMethod); //, $requestParams, $requestHeaders, $requestBody);
	}
	
	public function post($pid = null, $post)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/'.$publisher_id.'/videos';
		$requestMethod  = 'POST';
		$requestParams  = array();

		$video              = new stdClass();
		
		if(isset($post['minoto_id'])){ // update
			// $video->id      = $post['minoto_id'];
			$requestUrl     .= '/'.$post['minoto_id'];
			$requestMethod  = 'PUT';
		}

		$video->title       = $post['title'];
		$video->description = $post['description'];
		$video->tags        = $post['keywords'];
		
		$requestHeaders     = serialize($video);
		$requestBody        = null;
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
	}
	*/
	
	public function generateSignature($playerId, $videoId, $ip)
	{
		$secret = $this->publisher_secret; // or get from Minoto --> $publisher->secret
		$params = array(
			'ip' => $ip,
			'player_id' => $playerId,
			'video_id' => $videoId
		);
		ksort($params); // sort by keys
		$query = http_build_query($params); // make a query string
 
		$base = $secret .'|'.$query;
		$signature = md5( $base );
		return $signature;
	}
	
	public function generateStreamSignature($videoId, $profileKey, $ip = null)
	{
		$secret = $this->publisher_secret; // or get from Minoto --> $publisher->secret
		$params = array(
			'profile' => $profileKey,
			'video_id' => $videoId
		);
		if($ip)
			$params['ip'] = $ip;
		
		ksort($params);
		$query = http_build_query($params);
 
		$base = $secret .'|'.$query;
		$signature = md5( $base );
		return $signature;
	}
}