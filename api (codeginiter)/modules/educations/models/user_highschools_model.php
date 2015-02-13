<?php

class User_highschools_model extends MY_Model {

	protected $table = 'user_highschools';
	protected $primary_key = 'user_highschool_id';
	protected $fields = array('user_id', 'city_id', 'name', 'finished_year', 'city', 'state', 'country');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
				'create' => array(
					array('field' => 'user_id', 				'label' => 'user_id', 			'rules' => 'integer|required|xss_clean'), 
					array('field' => 'city_id', 				'label' => 'city_id', 			'rules' => 'integer|required|xss_clean'), 
					array('field' => 'name', 						'label' => 'name', 					'rules' => 'trim|required|xss_clean'), 
					array('field' => 'finished_year', 	'label' => 'finished_year', 'rules' => 'integer|required|xss_clean'),
					array('field' => 'city',            'label' => 'city',          'rules' => 'trim|required|xss_clean'),
					array('field' => 'state',           'label' => 'state',         'rules' => 'trim|required|xss_clean'),
					array('field' => 'country',         'label' => 'country',       'rules' => 'trim|required|xss_clean')
			), 
				'update' => array(
					array('field' => 'user_id', 			'label' => 'user_id', 				'rules' => 'integer|trim|xss_clean'), 
					array('field' => 'city_id', 			'label' => 'city_id', 				'rules' => 'integer|trim|xss_clean'), 
					array('field' => 'name', 					'label' => 'name', 						'rules' => 'trim|required|xss_clean'), 
					array('field' => 'finished_year', 'label' => 'finished_year', 	'rules' => 'integer|required|xss_clean'),
					array('field' => 'city',            'label' => 'city',          'rules' => 'trim|required|xss_clean'),
					array('field' => 'state',           'label' => 'state',         'rules' => 'trim|required|xss_clean'),
					array('field' => 'country',         'label' => 'country',       'rules' => 'trim|required|xss_clean')
				)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

}