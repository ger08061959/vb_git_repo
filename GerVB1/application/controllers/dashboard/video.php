<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("dashboard.php");
class Video extends Dashboard {

	var $title = 'Videos';
	var $model = 'video_model';
	// var $viewName = 'dashboard';

	/*
	 
	 Permissions
	 - 1. need to be logged in
	 - view : view_video
	 - edit : edit_video
	 - delete : remove_video
	 - announce : announce_video
	 - upload : edit_video
	 - configure : edit_video
	 // - authorise->post : authorise_video
	 - publish_video->post : publish_video
	 - statistics : view_video
	 */
	
	
	function __construct()
	{
		parent::__construct();
		
		// copied from admin controller... maybe add to dashboard controller
		$model = $this->model;
		
		$this->load->model($model);
		$this->load->model('organisation_model');
		$this->data['model']       = $model;
		$this->data['modelName']   = $this->$model->modelName;
		$this->data['viewKeys']    = $this->$model->viewKeys;
		$this->data['createKeys']  = isset($this->$model->createKeys) ? $this->$model->createKeys : $this->$model->editKeys;
		$this->data['editKeys']    = $this->$model->editKeys;
		$this->data['tableKeys']   = $this->$model->tableKeys;
		$this->data['fields']      = $this->$model->fields;
		// end
	}

	function index()
	{
		// get a list of videos
		// todo: add filter, add search
		$this->_render_page('default');
	}
	
	function _checkID($id = null){
		if($id){
			$video = $this->db->get_where('video', array('minoto_id' => $id))->row();

			if($video){
				$this->model_id = $video->id;

				$all_publishers = array();
				if( $this->currentOrganisation ){
					// add this organisation
					$all_publishers[] = $this->currentOrganisation->id;
					
					// add all publishers if reseller
					if($this->currentOrganisation->type=="reseller"){
						$publishers = $this->organisation_model->publishers( $this->currentOrganisation->id );
						foreach( $publishers as $publisher ){
							$all_publishers[] = $publisher->id;
						}
					}
				} else {
					// administrator/Datiq
					$publishers = $this->organisation_model->publishers( null );
					foreach( $publishers as $publisher ){
						$all_publishers[] = $publisher->id;
					}
				}
				$allowed = in_array($video->organisation_id, $all_publishers);
				
				if($allowed)
					return $video;

				$this->_setMessage( 'error' , 'Warning!!' , 'Not your video.' );
				redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
			}
		}
		$this->_setMessage( 'error' , 'Warning!!' , 'Invalid or no ID set.' );
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}
	
	function _setPublishers( $publishers )
	{
		$select_publishers = array();
		// todo, if( count($select_publishers) <= 0 ), redirect --> make a new publisher first!
		
		foreach($publishers as $publisher){
			$select_publishers[$publisher->id] = $publisher->name;
		}
		$this->data['fields']['organisation_id'] = array(
			'label' => 'Publisher',
			'type' => 'select',
			'value' => '',
			'values' => $select_publishers
		);
		$this->data['createKeys'][] = 'organisation_id';
		$this->data['hasPublishers'] = true;
	}
	
	function view($id = null)
	{
		$this->_requirePermission('view_video');
		
		$video = $this->_checkID($id);
		$this->data['result'] = (array)$video;
		$this->data['breadcrumbs'] = $this->parser->parse('dashboard/video/includes/breadcrumbs'  , $this->data, TRUE);
		$this->_render_page('default');
	}
	
	function edit($id = null)
	{
		$video = $this->_checkID($id);
		$this->_requirePermission('edit_video');
		$model = $this->model;
		if($this->input->post())
		{
			$this->_setValidationRules();
			
			if ($this->form_validation->run() == false)
			{
				$this->data['result'] = $this->input->post();
				$this->_setMessage('error', 'Error!!', validation_errors());
			}
			else
			{
				// $this->db->where('minoto_id', $id)->update('video', $this->input->post());
				$organisation = $this->db->get_where('organisation', array('id' => $video->organisation_id))->row();
				$minoto_id = $organisation ? $organisation->minoto_id : null;
				
				// $minoto_data = $this->minoto->video->post( $minoto_id , $this->input->post()); //put
				$minoto_data = $this->minoto->publisher->updateVideo( $minoto_id , $this->input->post());
				
				$post = $this->input->post();
				$post['minoto_id']  = $id;
				$post['screenshot'] = !empty($minoto_data->screenshot_uri) ? $minoto_data->screenshot_uri : '';
				$post['thumbnail']  = !empty($minoto_data->thumbnail_uri) ? $minoto_data->thumbnail_uri : '';
				$post['duration']   = $minoto_data->length;
				
				$this->$model->update($video->id, $post);
				$this->data['result'] = $post; // $this->input->post();
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully updated item.' );
			}
		}
		else
		{
			$this->data['result'] = (array)$video;
		}
		$this->data['breadcrumbs'] = $this->parser->parse('dashboard/video/includes/breadcrumbs'  , $this->data, TRUE);
		$this->_render_page('default');
	}
	
	function delete($id = null)
	{
		$video = $this->_checkID($id);
		$this->_requirePermission('remove_video');
		$model = $this->model;
		
		$publisher = $this->organisation_model->get( $video->organisation_id );
		
		$publisher_minoto_id = $publisher->minoto_id;
		$video_minoto_id     = $video->minoto_id;
		
		$this->minoto->publisher->removeVideo( $publisher_minoto_id , $video_minoto_id ); // todo, check
		$this->$model->delete($video->id);
		
		$this->_setMessage( 'success' , 'Success!!' , 'Successfully removed item.' );
		redirect($this->directoryName.'/'.$this->controllerName.'/index', 'refresh');
	}
	
	function announce()
	{
		$this->_requirePermission('announce_video');
		$this->load->model('organisation_model');
		$organisation_id = $this->currentUser->organisation_id; // TODO: this MUST be a publisher
		$organisation    = $this->db->get_where('organisation', array('id' => $organisation_id))->row();
		
		// Set a default value for identifier.
		$this->db->from('video');
		$ident = date('Ymd');
		if($organisation){ // we don't know the TARGET publisher so might be a bit off (for reseller/administrators)
			$this->db->where('organisation_id', $organisation_id);
		}
		$this->db->like('identifier', $ident);
		$count = $this->db->count_all_results();
		$this->data['fields']['identifier']['value']= $ident . '.' . sprintf("%05d", $count+1); // pad with 5 zeroes, like in vb2
		
		// if user -> organisation.type == reseller  : we can add to publishers that are children
		// if user -> organisation.type == publisher : we MUST set it.
		if($organisation_id == null){ // $this->ion_auth->is_admin()
			$publishers = $this->organisation_model->publishers( );
			$this->_setPublishers( $publishers );
		} elseif($organisation->type == 'reseller'){
			$publishers = $this->organisation_model->publishers( $organisation_id );
			$this->_setPublishers( $publishers );
		}
		
		if($this->input->post())
		{
			$model = $this->model;
			$this->_setValidationRules();
			
			if ($this->form_validation->run() == false)
			{
				// continue editing
				$this->data['result'] = $this->input->post();
				$validation_errors = validation_errors();
				$this->_setMessage('error', 'Error!!', $validation_errors);
			}
			else
			{
				$post = $this->input->post();
				
				// if reseller, overrule publisher by the post
				// todo: check if valid publisher?
				if(isset($post['organisation_id'])){
					// ALLOWED PUBLISHER?
					$organisation = $this->db->get_where('organisation', array('id' => $post['organisation_id']))->row();
					$organisation_id = $organisation->id;
				}
				
				// if publisher, set organisation_id
				if($organisation->type == 'publisher')
					$post['organisation_id'] = $organisation_id;
					
				$minoto_id = $organisation ? $organisation->minoto_id : null;
				
				// $minoto_data = $this->minoto->video->post( $minoto_id , $post );
				$minoto_data = $this->minoto->publisher->addVideo( $minoto_id , $this->input->post());
				$post['minoto_id'] = $minoto_data->id;
				$post['status'] = 'announced'; // hard
				
				$protection = $post['protection'];
				unset( $post['protection'] ); // UNSET, does NOT exist in database (yet)
				
				$result_id = $this->$model->create($post);
				$this->model_id = $result_id;
				
				// SET PROTECTED
				$this->minoto->video->setProtected($minoto_id, $minoto_data->id, $this->input->post('protection'));
				
				$this->_setMessage( 'success' , 'Success!!' , 'Successfully created item.' );
				$this->_log_activity();
				redirect($this->directoryName.'/'.$this->controllerName.'/upload/'.$minoto_data->id, 'refresh');
			}
		}
		$this->data['breadcrumbs'] = $this->parser->parse('dashboard/video/includes/breadcrumbs'  , $this->data, TRUE);
		$this->_render_page('default');
	}
	
	function upload($id = null)
	{
		$model = $this->model;
		$video = $this->_checkID($id);
		
		if( $video->status == 'announced' ) {
			$this->_requirePermission('edit_video');
			$this->data['result'] = (array)$video;
			$this->data['breadcrumbs'] = $this->parser->parse('dashboard/video/includes/breadcrumbs'  , $this->data, TRUE);
			
			// Error!!
			$pub          = $this->db->get_where('organisation', array('id' => $video->organisation_id ))->row();
			$minoto_video = $this->minoto->video->getVideo( $pub->minoto_id, $video->minoto_id );
			if( !isset($minoto_video->upload_uri )) {
				$this->$model->update($video->id, array(
					'status' => 'error'
				));
				$this->_setMessage('error', 'Error!!', 'An error occurred with this video. Try and make a new one.');
				redirect($this->directoryName.'/'.$this->controllerName, 'refresh');
			}
			
			$this->_render_page('default');
		} elseif( $video->status =='error' ) {
			$this->_setMessage( 'error' , 'Error!!' , 'An error occurred with this video. Try and make a new one.');
			redirect($this->directoryName.'/'.$this->controllerName, 'refresh');
		} else {
			$this->_setMessage( 'error' , 'Error!!' , 'Video has already been uploaded.');
			redirect($this->directoryName.'/'.$this->controllerName.'/configure/'.$video->minoto_id, 'refresh');
		}
	}
	
	// public
	// announced -> uploaded
	function uploaded($id = null)
	{
		$model     = $this->model;
		$video     = $this->_checkID($id);

		if($this->input->get('status')){
			if($this->input->get('status')=='ok'){
				$publisher = $this->db->get_where('organisation', array('id' => $video->organisation_id ))->row();
				$minoto_video = $this->minoto->video->getVideo( $publisher->minoto_id, $video->minoto_id );

				if( $minoto_video->status == 'saved' || $minoto_video->status == 'completed' ){
					if( $video->status == 'announced' )
						$this->$model->update($video->id, array('status' => 'uploaded'));

					$this->_setMessage( 'success' , 'Success!!' , 'Successfully uploaded video.');
					$this->_log_activity();
					redirect($this->directoryName.'/'.$this->controllerName.'/configure/'.$video->minoto_id, 'refresh');
				}
			} else {
				// error
				// $this->$model->update($video->id, array('status' => 'error'));
				$this->_setMessage( 'error' , 'Error!!' , $this->input->get('message'));
				redirect($this->directoryName.'/'.$this->controllerName.'/upload/'.$video->minoto_id, 'refresh');
			}
		}
		redirect($this->directoryName.'/'.$this->controllerName, 'refresh');
	}
	
	function progress($pid, $vid, $token)
	{
		echo $this->minoto->video->getUploadProgress( $pid, $vid, $token );
	}
	
	function configure($id = null)
	{
		$this->_requirePermission('edit_video');
		$video = $this->_checkID($id);
		$publisher = $this->db->get_where('organisation', array('id' => $video->organisation_id ))->row();
		// $publisher->minoto_id;
				
		if(in_array( $video->status, array("uploaded", "authorised", "published"))){
		
			if($this->input->post() !== false)
			{
				$this->_log_activity();
				
				// log_message('info', '>>>>'.print_r( $this->input->post(), true ));
				// ajax -> thumbnail
				if($this->input->post('thumbnail')  !== false){
					// $thumbnail = $this->input->post('thumbnail');
					$this->minoto->video->setThumbnail($publisher->minoto_id, $id , $this->input->post());
					$this->_refresh($id);
					$this->_setMessage('success', 'Success!!', ' Thumbnail successfully updated.');
					redirect($this->directoryName.'/'.$this->controllerName.'/configure/'.$video->minoto_id.'#tab-thumbnails', 'location', 302);
				}
				
				// ajax -> screenshot
				if($this->input->post('screenshot')  !== false){
					// $screenshot = $this->input->post('screenshot');
					$this->minoto->video->setScreenshot($publisher->minoto_id, $id, $this->input->post());
					$this->_refresh($id);
					$this->_setMessage('success', 'Success!!', ' Screenshot successfully updated.');
					redirect($this->directoryName.'/'.$this->controllerName.'/configure/'.$video->minoto_id.'#tab-screenshots', 'location', 302);
				}
				
				// transcodings
				if($this->input->post('transcoding') !== false){
					$key    = $this->input->post('key');
					$action = $this->input->post('action');
					if($action=='add'){
						$this->minoto->video->addTranscoding($publisher->minoto_id, $id, $key);
					} elseif($action=='remove') {
						$this->minoto->video->removeTranscoding($publisher->minoto_id, $id, $key );
					}
					redirect($this->directoryName.'/'.$this->controllerName.'/configure/'.$video->minoto_id.'#tab-transcodings', 'location', 302);
				}
				
				// protected
				if($this->input->post('protection')!== false){
					$this->_setMessage( 'success' , 'Success!!' , 'Video protection settings have been updated.');
					$this->minoto->video->setProtected($publisher->minoto_id, $id, $this->input->post('protection'));
					$this->_refresh($id);
					redirect($this->directoryName.'/'.$this->controllerName.'/configure/'.$video->minoto_id.'#tab-security', 'location', 302);
				}
			}
			$playerId    = $publisher->player_minoto_id ? $publisher->player_minoto_id : 3133;
			$minotovideo = $this->minoto->video->getVideo($publisher->minoto_id, $id);
			if($minotovideo->protected=='true')
				$protection = true;
			else
				$protection = false;
			
			if($protection)
				$overrideUrl = !empty( $publisher->publish_url_2 ) ? $publisher->publish_url_2 : false;
			else
				$overrideUrl = !empty( $publisher->publish_url_1 ) ? $publisher->publish_url_1 : false;
			
			$this->data['overrideUrl'] = $overrideUrl ? $overrideUrl : base_url();
			$this->data['iframeUrl'] = $this->_generateIframeUrl($playerId, $id, $overrideUrl);
			$this->data['shareUrl']  = $this->_generateShareUrl($playerId, $id, $overrideUrl);
			$this->data['embedUrl']  = $this->_generateEmbedUrl($playerId, $id, $overrideUrl);
			$this->data['playerUrl'] = $this->_getPlayerUrl();
			$this->data['result'] = (array)$video;
			$this->data['breadcrumbs'] = $this->parser->parse('dashboard/video/includes/breadcrumbs'  , $this->data, TRUE);
			$this->_render_page('default');
		} else {
			$this->_setMessage( 'error' , 'Error!!' , 'Upload a video file first.');
			redirect($this->directoryName.'/'.$this->controllerName.'/upload/'.$video->minoto_id, 'refresh');
		}
	}
	
	function download($id = null, $key, $signature = null)
	{
		if($this->ion_auth->logged_in())
		{
			$this->_requirePermission('edit_video');
		}
		else
		{
			// IF NOT LOGGED IN, REQUIRES SINGATURE!!!
			if(!isset($signature) || $signature == null) {
				echo 'An error occurred: invalid signature.';
				return;
			}
		}

		$video = $this->_checkID($id);

		$minotoDownloadUrl  = "http://streams.minoto-video.com/id/";
		$minotoDownloadFile = $id.'_'.$key.'.mp4';
		$trueSignature      = $this->minoto->video->generateStreamSignature($id, $key);

		if(!isset($signature) || $signature == null) // where are logged in, but without signature.
		{
			$signature = $trueSignature;
		}
		else // check whether signature is correct
		{
			if( $signature != $trueSignature ){
				echo 'An error occurred: invalid signature.';
				return;
			}
		}
		$minotoDownloadSignature = '?signature='.$signature;

		header('Content-Type: video/mp4');
		header('Content-Disposition: attachment; filename="'.$minotoDownloadFile.'"');
		// header('Content-Length: '.filesize($minotoDownloadUrl.$minotoDownloadFile.$minotoDownloadSignature) );
		readfile($minotoDownloadUrl.$minotoDownloadFile.$minotoDownloadSignature);
	}
	
	function screenshot($id)
	{
		$video = $this->_checkID($id);
		$screenshot = $video->screenshot;
		if(empty($screenshot))
		{
			$pub = $this->db->get_where('organisation', array('id' => $video->organisation_id ))->row();
			$minoto_video = $this->minoto->video->getVideo( $pub->minoto_id, $video->minoto_id );
			$screenshot = $minoto_video->screenshot_uri;
		}
		header('Content-Type: image/jpg');
		header('Content-Disposition: attachment; filename="'.$id.'_screenshot.jpg"');
		// header('Content-Length: '.filesize($video->screenshot) );
		readfile($video->screenshot);
	}
	
	function publish($id = null)
	{
		$video = $this->_checkID($id);
		$model = $this->model;
		if($video->status=="uploaded" ){

			if( $this->input->post() ) // TODO - check permissions.
			{
				$this->_requirePermission('publish_video');
				$this->$model->update($video->id, array('status' => 'published')); // error?
				$this->_setMessage( 'success' , 'Success!!' , 'This video is published and can be viewed on the web!');
				$this->_log_activity();
				redirect($this->directoryName.'/'.$this->controllerName.'/statistics/'.$video->minoto_id, 'refresh');
			}
			$this->data['result'] = (array)$video;
			$this->data['breadcrumbs'] = $this->parser->parse('dashboard/video/includes/breadcrumbs'  , $this->data, TRUE);
			$this->_render_page('default');
		} elseif($video->status=="published") {
			$this->_setMessage( 'error' , 'Error!!' , 'This video is already published.');
			redirect($this->directoryName.'/'.$this->controllerName.'/statistics/'.$video->minoto_id, 'refresh');
		} else {
			$this->_setMessage( 'error' , 'Error!!' , 'This video needs to be uploaded first.');
			redirect($this->directoryName.'/'.$this->controllerName.'/upload/'.$video->minoto_id, 'refresh');
		}

	}
	
	function statistics($id = null)
	{
		$video = $this->_checkID($id);
		if($video->status=="uploaded" || $video->status=="published"){
			// TODO get statistics
			$this->_requirePermission('view_video');
			$this->data['result'] = (array)$video;
			$this->data['breadcrumbs'] = $this->parser->parse('dashboard/video/includes/breadcrumbs'  , $this->data, TRUE);
			$this->_render_page('default');
		} else {
			$this->_setMessage( 'error' , 'Error!!' , 'This video needs to be published first.');
			redirect($this->directoryName.'/'.$this->controllerName.'/publish/'.$video->minoto_id, 'refresh');
		}
		
	}
	
	function embed($playerId, $id = null)
	{
		$video               = $this->_checkID($id);
		$video_minoto_id     = $video->minoto_id;
		$publisher           = $this->organisation_model->get( $video->organisation_id );
		$publisher_minoto_id = $publisher->minoto_id;
		$minotovideo         = $this->minoto->video->getVideo($publisher_minoto_id, $video_minoto_id);
		$protected           = $minotovideo->protected;
		
		// TOD - Check whether video is expired. This won't work on URLs...
		$expired    = false;
		$expiration = strtotime($video->date_expiration);
		$now        = time();
		if( $expiration < $now ){
			$expired = true;
			// echo 'This video expired on <strong>'.$video->date_expiration.'</strong>.';
			redirect('expired', 'location', 302); // so no video will be shown. If a client asks why no video is shown, then it might be that it is expired.
		}
		
		$url = "http://embed.minoto-video.com/$playerId/$id"; // @todo customize publisher_base_urls
		
		// do some ip checks and stuff here, check published status etc ??
		$this->_initWhitelist($publisher->id);
		if($protected && $this->whitelist->allowed()){
			$ip = $_SERVER['REMOTE_ADDR'];
			$signature = $this->minoto->video->generateSignature($playerId, $id, $ip);
			$url .= '?ip='.$ip.'&signature='.$signature;
		} else {
			// no signature
		}
		
		//redirect($url, 'refresh');
		redirect($url, 'location', 302);
	}
	
	function iframe($playerId, $id)
	{
		$video               = $this->_checkID($id);
		$video_minoto_id     = $video->minoto_id;
		$publisher           = $this->organisation_model->get( $video->organisation_id );
		$publisher_minoto_id = $publisher->minoto_id;
		$minotovideo         = $this->minoto->video->getVideo($publisher_minoto_id, $video_minoto_id);
		$protected           = $minotovideo->protected;
		
		// Check whether video is expired.
		$expired    = false;
		$expiration = strtotime($video->date_expiration);
		$now        = time();
		// echo '<!--'.$expiration.'/'.$now.'-->';
		if( $expiration < $now ){
			$expired = true;
			echo '<p>This video expired on <strong>'.$video->date_expiration.'</strong>.</p>';
			return;
		}
		
		// do some ip checks and stuff here, check published status etc ??
		$ip        = $_SERVER['REMOTE_ADDR'];
		$signature = '';
		$this->_initWhitelist($publisher->id);
		if($this->whitelist->allowed()){
			$signature = $this->minoto->video->generateSignature($playerId, $id, $ip);
		}
		$this->data['playerUrl'] = $this->_getPlayerUrl();
		$this->data['ip']        = $ip;
		$this->data['signature'] = $signature;
		$this->data['playerId']  = $playerId;
		$this->data['videoId']   = $id;
		
		return $this->parser->parse('dashboard/video/iframe', $this->data);
	}
	
	function play($playerId, $id)
	{
		$video           = $this->_checkID($id);
		$video_minoto_id = $video->minoto_id;
	
		$publisher           = $this->organisation_model->get( $video->organisation_id );
		$publisher_minoto_id = $publisher->minoto_id;
		
		$minotovideo = $this->minoto->video->getVideo($publisher->minoto_id, $id);
		if($minotovideo->protected=='true')
			$protection = true;
		else
			$protection = false;
		
		if($protection)
			$overrideUrl = !empty( $publisher->publish_url_2 ) ? $publisher->publish_url_2 : false;
		else
			$overrideUrl = !empty( $publisher->publish_url_1 ) ? $publisher->publish_url_1 : false;
		
		$ip        = $_SERVER['REMOTE_ADDR'];
		$signature = '';
		$this->_initWhitelist($publisher->id);
		if($this->whitelist->allowed()){
			$signature = $this->minoto->video->generateSignature($playerId, $id, $ip);
		}
		
		$this->data['url']       = $this->_generateEmbedUrl($playerId, $id, $overrideUrl);
		$this->data['iframeUrl'] = $this->_generateIframeUrl($playerId, $id, $overrideUrl);
		$this->data['playerUrl'] = $this->_getPlayerUrl();
		$this->data['ip']        = $ip;
		$this->data['signature'] = $signature;
		$this->data['playerId']  = $playerId;
		$this->data['videoId']   = $id;
		$this->data['the_video'] = (array)$video;
		$this->_render_page('simple');
	}

	function player()
	{
		$url = 'http://play.minoto-video.com/mvp-player/bootstrap-me.js';
		
		if(isset( $_GET['beta'] ))
			$url = 'http://scriptz.minoto-video.com/mvp-player/bootstrap-me.js';
		
		redirect($url, 'location', 302);
	}

	// refresh some minoto video data.
	function _refresh($id)
	{
		$video = $this->_checkID($id);
		// $this->_requirePermission('edit_video');
		$model = $this->model;
		$organisation = $this->db->get_where('organisation', array('id' => $video->organisation_id))->row();
		$minoto_id = $organisation ? $organisation->minoto_id : null;
		$minoto_data = $this->minoto->video->getVideo( $minoto_id , $video->minoto_id);

		$data['minoto_id']  = $id;
		$data['screenshot'] = !empty($minoto_data->screenshot_uri) ? $minoto_data->screenshot_uri : '';
		$data['thumbnail']  = !empty($minoto_data->thumbnail_uri) ? $minoto_data->thumbnail_uri : '';
		$data['duration']   = $minoto_data->length;

		$this->$model->update($video->id, $data);
	}
	
	function _initMetadata( $organisation_id )
	{
		$metadata = $this->db->where('organisation_id', $video_metadata)->get('video_metadata')->result();
		$this->data['metadata'] = $metadata;
	}

	//
	// Video Embedding Functions
	// -------------------------
	/*
	- dashboard/video/play/
	- dashboard/video/iframe/
	- dashboard/video/embed/
	*/
	
	function _initWhitelist( $organisation_id )
	{
		// only used in video --> move to Embed?
		// only used in `iframe`, `embed`, `play`

		$this->load->library('whitelist');
		$whitelist_data = $this->db->where( 'organisation_id', $organisation_id )
			->or_where( 'organisation_id', null )
			->get('whitelist')->result();
			
		foreach($whitelist_data as $whitelist_item) {
			$this->whitelist->add( $whitelist_item->ip );
		}
		
		$whitelist_domains = $this->db->where( 'organisation_id', $organisation_id )
			->or_where( 'organisation_id', null )
			->get('whitelist_domain')->result();
			
		foreach($whitelist_domains as $whitelist_item) {
			$this->whitelist->addDomain( $whitelist_item->domain );
		}
	}
	
	function _generateShareUrl($playerId, $videoId, $overrideUrl = false)
	{
		$url  = $overrideUrl ? $overrideUrl : base_url();
		$url .= "dashboard/video/play/$playerId/$videoId";
		return $url;
	}
	
	function _getPlayerUrl()
	{
		return base_url() . 'dashboard/video/player'; // ?beta=1
	}
	
	function _generateIframeUrl($playerId, $videoId, $overrideUrl = false)
	{
		$url  = $overrideUrl ? $overrideUrl : base_url();
		$url .= "dashboard/video/iframe/$playerId/$videoId";
		return $url;
	}
	
	function _generateEmbedUrl($playerId, $videoId, $overrideUrl = false)
	{
		$url  = $overrideUrl ? $overrideUrl : base_url();
		$url .= "dashboard/video/embed/$playerId/$videoId";
		return $url;
	}
	

	
	// from admin controller. TODO move to dashboard controller...
	function _setValidationRules($keys = 'editKeys')
	{
		$model  = $this->model;
		$fields = $this->$model->fields;
		
		foreach( $this->$model->$keys as $key)
		{
			$field = $fields[$key];
			
			if(isset($field['rules']))
				$this->form_validation->set_rules($key, 'lang:'.$field['label'], $field['rules']);
		}
	}
}
