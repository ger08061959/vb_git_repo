<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video_model extends MY_Model
{
	var $table     = 'video';
	var $modelName = 'Video';
	
	// override
	function create(array $data)
	{
		$data['date_created']  = $this->currentDate;
		$data['date_modified'] = $this->currentDate;
		$data['created_by']    = $this->currentUser->id; // CodeIgniter allows me to access this variable from the controller, not sure if clean...
		return parent::create($data);
	}
	
	// override
	function update($id, array $data)
	{
		$data['date_modified'] = $this->currentDate;
		return parent::update($id, $data);
	}
	
	var $fields = array(
		'id' => array(
			'label' => 'ID',
			'type' => 'hidden'
		),
		'minoto_id' => array(
			'label' => 'External ID',
			'type' => 'hidden'
		),
		'business_unit' => array(
			'label' => 'Business Unit',
			'type' => 'select-custom-fwd'
		),
		'identifier' => array(
			'label' => 'Identifier',
			'type' => 'text',
			'rules' => 'required'
		),
		'title' => array(
			'label' => 'Title',
			'type' => 'text',
			'rules' => 'required'
		),
		'author' => array(
			'label' => 'Author',
			'type' => 'hidden' // hide, use created_by
		),
		'caption' => array(
			'label' => 'Caption',
			'type' => 'hidden'
		),
		'source' => array(
			'label' => 'Content creator',
			'type' => 'text'
		),
		'description' => array(
			'label' => 'Description',
			'type' => 'textarea',
			'rules' => 'required'
		),
		'keywords' => array(
			'info' => 'Seperate keywords with a comma',
			'label' => 'Tags',
			'type' => 'tags',
			'rules' => 'required'
		),
		'copyright' => array(
			'label' => 'Copyright',
			'type' => 'text'
			// 'values' => array('avro' => 'AVRO','copyright' => 'Copyright 2') // @todo
		),
		'metaoption_1' => array(
			'label' => 'Producer',
			'type' => 'text',
		),
		'metaoption_2' => array(
			'label' => 'Music Copyright',
			'type' => 'text',
		),
		'metaoption_3' => array(
			'label' => 'UNUSED',
			'type' => 'text',
		),
		'metaoption_4' => array(
			'label' => 'Stock',
			'type' => 'text',
		),
		'metaoption_5' => array(
			'label' => 'Campagne',
			'type' => 'text',
		),
		'metaoption_6' => array(
			'label' => 'Tutorial',
			'type' => 'text',
		),
		'metaoption_7' => array(
			'label' => 'UNUSED',
			'type' => 'text',
		),
		'date_created' => array(
			'label' => 'Created on',
			'type' => 'echo' // 'datetime'
		),
		'date_modified' => array(
			'label' => 'Modified on',
			'type' => 'datetime'
		),
		'date_deleted' => array(
			'label' => 'Deleted on',
			'type' => 'datetime'
		),
		'date_release' => array(
			'label' => 'Publication date',
			'type' => 'hidden' // datetime
		),
		'date_expiration' => array(
			'label' => 'Expiration date',
			'type' => 'datetime',
			'info' => 'This video cannot be viewed after this date.',
			'value' => '2020-12-31 23:59:59' // set default value into the future
		),
		'created_by' => array(
			'label' => 'Created by',
			'type' => 'select' // todo foreign
		),
		'deleted_by' => array(
			'label' => 'Deleted by',
			'type' => 'select' // todo foreign
		),
		'thumbnail' => array(
			'label' => '',
			'display' => 'image'
		),
		'status' => array(
			'html_escape' => false,
			'label' => 'Status',
			'type' => 'select',
			'values' => array(
				// inverse info important warning success
				'error'      => '<span class="label label-important">Error</span>',
				''           => '<span class="label">Announced</span>', // default
				'announced'  => '<span class="label">Announced</span>',
				'uploaded'   => '<span class="label label-warning">Uploaded</span>',
				//'authorised' => '<span class="label label-info">Authorised</span>',
				'published'  => '<span class="label label-success">Published</span>',
				'expired'    => '<span class="label label-important">Expired</span>' // unpublished
			)
		),
		'duration' => array(
			'info' => 'hh:mm:ss',
			'label' => 'Duration',
			'type' => 'echo', // can not edit, because it comes from Minoto API/CDN
			'value' => '0', // default value
			'display' => 'duration' // duration convert seconds into hh:mm:ss
		),
		// This field does not exist in the database, but is to be defined in Minoto API/CDN
		'protection' => array(
			'label' => 'Internal or External',
			'info' => 'Internal videos will be protected. External videos are accessible by the public.',
			'type' => 'select',
			'values' => array(
				'true' => 'Internal Video',
				'false' => 'External Video'
			)
		)
	);
	var $viewKeys   = array(
		'identifier',
		'business_unit',
		'title',
		//'author',
		//'caption',
		'source',
		'description',
		'keywords',
		'copyright',
		'metaoption_2', // music copyright
		'date_created',
		'date_modified',
		//'date_release',
		'date_expiration',
		'metaoption_1',
		//'metaoption_3',
		'metaoption_4',
		'metaoption_5',
		'metaoption_6',
		//'metaoption_7',
		'status',
		'duration'
	);
	
	var $createKeys = array(
		'identifier',
		'business_unit',
		'title',
		//'author',
		//'caption',
		'source',
		'description',
		'keywords',
		'copyright',
		'metaoption_2', // music copyright
		'date_created', // cannot be edited
		//'date_release',
		'date_expiration',
		'metaoption_1',
		//'metaoption_3',
		'metaoption_4',
		'metaoption_5',
		'metaoption_6',
		//'metaoption_7',
		'protection'
	);
	
	var $editKeys   = array(
		'identifier',
		'business_unit',
		'title',
		//'author',
		//'caption',
		'source',
		'description',
		'keywords',
		'copyright',
		'metaoption_2', // music copyright
		'date_created', // cannot be edited
		//'date_release',
		'date_expiration',
		'metaoption_1',
		//'metaoption_3',
		'metaoption_4',
		'metaoption_5',
		'metaoption_6',
		//'metaoption_7',
		'duration'
	);
	var $tableKeys  = array(
		'id',
		'minoto_id',
		'identifier',
		'title',
		// 'caption',
		// 'organisation_id',
		'date_created',
		'date_modified',
		'status'
	);
}
