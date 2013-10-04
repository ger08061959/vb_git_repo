<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once("admin.php");

class Video extends Admin {

	var $model = 'video_model';
	var $title = 'Administration';
	
	/*
	function create(){
		parent::create();
	}
	
	function edit($id){
		parent::edit($id);
	}
	*/
	
	/**
	 * @param $organisation a minoto publisher object
	 */
	function _synchronize( $organisation, $parent_id = null ){
			// add organisation if not exists
			$data = array(
				'parent_id' => $parent_id,
				'minoto_id' => $organisation->id,
				'type' => $organisation->type,
				'name' => $organisation->name,
				'url' => $organisation->url,
				'enabled' => $organisation->enabled
			);
			
			$my_organisation = $this->db->get_where('organisation', array('minoto_id' => $organisation->id))->row();
			if(!$my_organisation){
				$my_organisation_id = $this->organisation_model->create( $data );
			} else { // update
				// log_message('info', '>>>'.print_r( $my_organisation, true ) );
				$my_organisation_id = $my_organisation->id;
			}
			
			if( $organisation->type == 'reseller' )
			{
				$reseller_id = $organisation->id;
				$suborganisations = $this->minoto->reseller->publishers( $reseller_id );
				foreach( $suborganisations as $suborganisation)
				{
					// recursion, link to parent organisation.
					$this->_synchronize( $suborganisation, $my_organisation_id );
				}
			}
			else // type == 'publisher', publishers has
			{
				$publisher_id = $organisation->id;
				$videos = $this->minoto->publisher->videos( $publisher_id );
				
				foreach($videos as $video){
					$status = !empty( $video->status ) ? ($video->status == 'completed' ? 'uploaded' : $video->status) : '' ;
				
					$data = array(
						'minoto_id' => $video->id,
						'organisation_id' => $my_organisation_id,
						'title' => $video->title,
						'keywords' => $video->tags,
						'description' => $video->description,
						'duration' => $video->length,
						'thumbnail' => !empty($video->thumbnail_uri) ? $video->thumbnail_uri : '',
						'screenshot' => !empty($video->screenshot_uri) ? $video->screenshot_uri : '',
						'date_created' => $video->date,
						'status' => $status
					);
				
					$my_video = $this->db->get_where('video', array('minoto_id' => $video->id))->row();
					if(!$my_video){
						$my_video_id = $this->video_model->create( $data );
					} else {
						$my_video_id = $my_video->id;
					}
					$video_id = $video->id;
				}
			}
	}
	
	function synchronize(){
		$this->load->model('video_model');
		$this->load->model('organisation_model');
	
		$organisations = $this->minoto->reseller->publishers(); // gets all resellers, publishers
		foreach( $organisations as $organisation )
		{
			$this->_synchronize( $organisation );
		}
	}
}
/*

Organisation
------------
id
minoto_id
type (reseller|publisher)
name
url
enabled

Video
-----
minoto_id
status

*/