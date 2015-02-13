<?php

class Courses_model extends MY_Model {

	protected $table = 'courses';
	protected $primary_key = 'course_id';
	protected $fields = array('university_id', 'name', 'season', 'year', 'slug');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
				'create' => array(
					array('field' => 'university_id', 	'label' => 'university_id', 	'rules' => 'integer|required|xss_clean'),
					array('field' => 'name', 						'label' => 'Name', 						'rules' => 'trim|required|xss_clean'),
					array('field' => 'season', 					'label' => 'season', 					'rules' => 'trim|required|xss_clean'),
                    array('field' => 'year', 						'label' => 'year', 						'rules' => 'trim|required|xss_clean'),
                    array('field' => 'slug', 						'label' => 'slug', 						'rules' => 'trim|required|xss_clean')
					),
				'update' => array(
					array('field' => 'university_id', 	'label' => 'university_id', 	'rules' => 'integer|required|xss_clean'),
					array('field' => 'name', 						'label' => 'Name', 						'rules' => 'trim|required|xss_clean'),
					array('field' => 'season', 					'label' => 'season', 					'rules' => 'trim|required|xss_clean'),
					array('field' => 'year', 						'label' => 'year', 						'rules' => 'trim|required|xss_clean'),
                    array('field' => 'slug', 						'label' => 'slug', 						'rules' => 'trim|required|xss_clean')
					)
			);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

	public function get_by_university_id_and_search_str ($university_id, $searchStr) {
		$query = "
			SELECT *
			FROM {$this->table}
			WHERE university_id = {$university_id} AND name LIKE '%{$searchStr}%'
			LIMIT 0,10
		";
		$courses = $this->db->query($query)->result();
		foreach($courses as &$course){
			$course->days = $this->Courses_days->get_many(array('course_id' => $course->course_id));
		}
		return $courses;
	}
}