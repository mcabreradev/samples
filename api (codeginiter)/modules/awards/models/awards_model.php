<?php

class Awards_model extends MY_Model {

	protected $table = 'awards';
	protected $primary_key = 'award_id';
	protected $fields = array('name');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
				'create' => array(
					array(
						'field' => 'name', 
						'label' => 'Name', 
						'rules' => 'trim|required|xss_clean'
						)
			)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

}