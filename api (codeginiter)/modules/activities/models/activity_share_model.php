<?php

class Activity_share_model extends MY_Model {

	protected $table = 'activities_reshare';
	protected $primary_key = 'activity_reshare_id';
	protected $fields = array('activity_reshare_id', 'original_activity_id', 'activity_id');

	public $before_create = array('before_create');
	public $before_update = array('before_update');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
			'create' => array(
					array('field' => 'activity_id',					 'label' => 'activity_id', 'rules' => 'trim|required'),
					array('field' => 'original_activity_id', 'label' => 'user_id',		 'rules' => 'trim|required'),
			)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}
  
	function before_create($data) {
		return $data;
	}

	function before_update($data) {
		return $data;
	}

}