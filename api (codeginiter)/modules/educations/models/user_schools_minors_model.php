<?php

class User_schools_minors_model extends MY_Model {

	protected $table = 'user_schools_minors';
	protected $primary_key = 'user_school_minor_id';
	protected $fields = array('user_school_id', 'name');


	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
				'create' => array(
					array('field' => 'user_school_id', 	'label' => 'user_school_id', 	'rules' => 'integer|required|xss_clean'), 
					array('field' => 'name', 						'label' => 'Name', 						'rules' => 'trim|required|xss_clean')
			), 
				'update' => array(
					array('field' => 'name', 						'label' => 'Name', 						'rules' => 'trim|required|xss_clean')
				)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}
  
  public function getWithUserId($user_school_minor_id, $user_id) {
    $this->db->select('user_schools_minors.*, user_schools.user_id');
    $this->db->join('user_schools', 'user_schools.user_school_id = user_schools_minors.user_school_minor_id');
    return $this->db->get($this->table)->row();
  }

}