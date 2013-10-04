<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//
// $this->minoto->reseller->get($rid)
// $this->minoto->reseller->getReseller($rid)
// $this->minoto->reseller->publishers($rid)
// $this->minoto->reseller->update($rid, $post)
// $this->minoto->reseller->addReseller($rid, $post)
// $this->minoto->reseller->addPublisher($rid, $post)
//
class Minoto_reseller extends CI_Driver {
	/*
	public function get($rid = null)
	{
		$reseller_id = !empty($rid) ? $rid : $this->reseller_id;
		$requestUrl  = $this->api . '/publishers/'.$reseller_id.'/publishers';
		return $this->doRequest($requestUrl);
	}
	*/
	public function getReseller($rid = null)
	{
		$reseller_id = !empty($rid) ? $rid : $this->reseller_id;
		$requestUrl  = $this->api . '/publishers/'.$reseller_id;
		return $this->doRequest($requestUrl);
	}
	
	public function publishers($rid = null)
	{
		$reseller_id   = !empty($rid) ? $rid : $this->reseller_id;
		$requestUrl     = $this->api . '/publishers/'.$reseller_id.'/publishers';
		return $this->doRequest($requestUrl);
	}
	
	public function update($rid = null, $post)
	{
		$reseller_id   = !empty($rid) ? $rid : $this->reseller_id;
		$requestUrl     = $this->api . '/publishers/'.$reseller_id;
		$requestMethod  = 'PUT';
		$requestParams  = array();

		$reseller          = new stdClass();
		$reseller->name    = $post['name'];
		$reseller->url     = $post['url'];
		$reseller->enabled = 'true'; // $post['enabled'];
		
		$requestHeaders     = serialize($reseller);
		$requestBody        = null;
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
	}
	
	public function updateSecret($id)
	{
		$requestUrl     = $this->api . '/publishers/'.$id;
		$requestMethod  = 'PUT';
		$requestParams  = array();
		$item->secret = $this->publisher_secret;
		$requestHeaders = serialize($item);
		$requestBody = null;
		
		return $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
	}
	
	public function addReseller($rid = null, $post)
	{
		return $this->addPublisher($rid, $post);
	}
	
	public function addPublisher($rid = null, $post)
	{
		$reseller_id   = !empty($rid) ? $rid : $this->reseller_id;
		$requestUrl     = $this->api . '/publishers/'.$reseller_id.'/publishers';
		$requestMethod  = 'POST';
		$requestParams  = array();

		$publisher          = new stdClass();
		$publisher->type    = $post['type']; // reseller|publisher
		$publisher->name    = $post['name'];
		$publisher->url     = $post['url'];
		$publisher->enabled = 'true'; // $post['enabled'];
		$publisher->secret  = $this->publisher_secret; // in config, not sure if this actually works... so update it anyways.
		
		$requestHeaders     = serialize($publisher);
		$requestBody        = null;
		
		$response = $this->doRequest($requestUrl, $requestMethod, $requestParams, $requestHeaders, $requestBody);
		
		if($response){
			$this->updateSecret( $response->id );
		}
		
		return $response;
	}

	public function remove($rid)
	{
		$requestUrl     = $this->api . '/publishers/' . $rid;
		$requestMethod  = 'DELETE';
		return $this->doRequest($requestUrl, $requestMethod);
	}
	
	public function removePublisher($pid)
	{
		// $reseller_id   = !empty($rid) ? $rid : $this->reseller_id;
		$requestUrl     = $this->api . '/publishers/' . $pid;
		$requestMethod  = 'DELETE';
		return $this->doRequest($requestUrl, $requestMethod);
	}
}