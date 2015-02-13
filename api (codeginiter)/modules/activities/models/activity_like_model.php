<?php

class Activity_like_model extends MY_Model {

	protected $table = 'activities_likes';
	protected $primary_key = 'activity_like_id';
	protected $fields = array('activity_like_id', 'activity_id', 'user_id', 'created_at');

	public $before_create = array('before_create');
	public $before_update = array('before_update');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
			'create' => array(
					array('field' => 'activity_id',					'label' => 'activity_id',			   	'rules' => 'trim|required'),
					array('field' => 'user_id',							'label' => 'user_id',							'rules' => 'trim|required'),
			)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}
  
	function before_create($data) {
		$data['created_at'] = date('Y-m-d H:i:s');
		return $data;
	}

	function before_update($data) {
		return $data;
	}

}