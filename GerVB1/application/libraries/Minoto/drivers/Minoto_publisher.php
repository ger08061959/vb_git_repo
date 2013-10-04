<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//
// $this->minoto->publisher->get($pid)
// $this->minoto->publisher->getPublisher($pid)
// $this->minoto->publisher->videos($pid)
// $this->minoto->publisher->update($pid, $post)
// $this->minoto->publisher->addVideo($pid, $post)
//
class Minoto_publisher extends CI_Driver {
	/*
	public function get($rid = null)
	{
		$reseller_id    = !empty($rid) ? $rid : $this->reseller_id;
		$requestUrl     = $this->api . '/publishers/'.$reseller_id.'/publishers';
		
		return $this->doRequest($requestUrl);
	}
	*/

	public function getPublisher($pid = null)
	{
		$publisher_id = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl   = $this->api . '/publishers/'.$publisher_id;
		return $this->doRequest($requestUrl);
	}
	
	public function getVideo($pid = null, $vid)
	{
		// todo. use $this->minoto->video->getVideo( $pid, $vid )
	}
	
	public function videos($pid = null)
	{
		$publisher_id = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl   = $this->api . '/publishers/'.$publisher_id.'/videos';
		return $this->doRequest($requestUrl);
	}
	
	public function update($pid = null, $post) // see also reseller->update()
	{
		$publisher_id = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/'.$publisher_id;
		$requestMethod  = 'PUT';
		$requestParams  = array();

		$publisher          = new stdClass();
		$publisher->name    = $post['name'];
		$publisher->url     = $post['url'];
		$publisher->enabled = 'true'; // $post['enabled'];
		$publisher->secret  = $this->publisher_secret; // in config
		
		$requestHeaders     = serialize($publisher);
		$requestBody        = null;
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
	}
	
	public function remove($pid) // see also reseller->remove()
	{
		$requestUrl     = $this->api . '/publishers/' . $pid;
		$requestMethod  = 'DELETE';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	public function addVideo($pid = null, $post)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/'.$publisher_id.'/videos';
		$requestMethod  = 'POST';
		$requestParams  = array();
		
		$video              = new stdClass();
		$video->title       = isset( $post['title'] ) ?$post['title'] : 'Untitled';
		$video->description = isset( $post['description'] ) ? $post['description'] : '';
		$video->tags        = isset( $post['keywords'] ) ? $post['keywords'] : '';
		
		$video->published   = 'true';
		// Expiration
		//$video->published   = 'limited';
		//$video->published_from = isset( $post['date_release'] ) ? $post['date_release'] : '2013-01-01 00:00:00';
		//$video->published_to   = isset( $post['date_expiration'] ) ? $post['date_expiration'] : '2050-01-01 00:00:00';
		
		$requestHeaders     = serialize($video);
		$requestBody        = null;
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
	}
	
	public function updateVideo($pid = null, $post)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/' . $post['minoto_id'];
		$requestMethod  = 'PUT';
		$requestParams  = array();
		
		$video              = new stdClass();
		$video->title       = isset( $post['title'] ) ?$post['title'] : 'Untitled';
		$video->description = isset( $post['description'] ) ? $post['description'] : '';
		$video->tags        = isset( $post['keywords'] ) ? $post['keywords'] : '';
		// $video->protected = '0';
		// $video->published = 'false';
		
		
		$video->published   = 'true';
		// Expiration
		//$video->published   = 'limited';
		//$video->published_from = isset( $post['date_release'] ) ? $post['date_release'] : '2013-01-01 00:00:00';
		//$video->published_to   = isset( $post['date_expiration'] ) ? $post['date_expiration'] : '2050-01-01 00:00:00';
		
		$requestHeaders     = serialize($video);
		$requestBody        = null;
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
	}

	public function removeVideo($pid = null, $vid)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/videos/' . $vid;
		$requestMethod  = 'DELETE';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	

	public function getTranscodingPresets($pid = null)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/config/presets';
		return $this->doRequest($requestUrl);
	}
	
	public function getTranscodingPresetDetails($pid = null, $prk)
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/config/presets/'.$prk;
		return $this->doRequest($requestUrl);
	}
	
	public function getStatistics($pid = null, $params = array())
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/statistics/intervals';
		$requestMethod  = 'GET';
		$requestParams  = array(
			'from' => date( 'Y-m-d' , strtotime('-2 week')),
			'span' => 'day'
		);
		return $this->doRequest($requestUrl, $requestMethod, $requestParams);
	}
	
	public function getTopVideos($pid = null, $params = array())
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/statistics/videos';
		$requestMethod  = 'GET';
		$requestParams  = array(
			'from' => date( 'Y-m-01' ),// strtotime('-3 week')), // '2013-07-01'
			'span' => 'month'
		);
		return $this->doRequest($requestUrl, $requestMethod, $requestParams);
	}
	
	public function getUsage($pid = null, $params = array())
	{
		$publisher_id   = !empty($pid) ? $pid : $this->publisher_id;
		$requestUrl     = $this->api . '/publishers/' . $publisher_id . '/usage';
		$requestMethod  = 'GET';
		$requestParams  = array();
		$requestParams['span'] = 'day';

		// add from and to iff they are both set
		if( isset($params) && isset($params['from']) && isset($params['to']) ){
			$requestParams['from'] = $params['from'];
			$requestParams['to']   = $params['to'];
		}
		return $this->doRequest($requestUrl, $requestMethod, $requestParams);
	}
}