<?php

class Courses_days_model extends MY_Model {

	protected $table = 'courses_days';
	protected $primary_key = 'course_day_id';
	protected $fields = array('course_id', 'day_name', 'start_time', 'end_time');


	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
				'create' => array(
					array('field' => 'course_id', 						'label' => 'course_id', 						'rules' => 'integer|required|xss_clean'),
					array('field' => 'day_name', 							'label' => 'day_name', 							'rules' => 'trim|required|xss_clean'),
					array('field' => 'start_time', 						'label' => 'start_time', 						'rules' => 'required|xss_clean'),
					array('field' => 'end_time', 							'label' => 'end_time', 							'rules' => 'required|xss_clean'),
			),
				'update' => array(
					array('field' => 'course_id', 						'label' => 'course_id', 						'rules' => 'integer|xss_clean'),
					array('field' => 'day_name', 							'label' => 'day_name', 							'rules' => 'trim|required|xss_clean'),
					array('field' => 'start_time', 						'label' => 'start_time', 						'rules' => 'required|xss_clean'),
					array('field' => 'end_time', 							'label' => 'end_time', 							'rules' => 'required|xss_clean'),
				)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

}