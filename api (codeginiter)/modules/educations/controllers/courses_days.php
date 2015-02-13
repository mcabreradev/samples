<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Courses_days extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Courses_days_model', 'Courses_days');
	}

	public function get_all($course_id) {
		if (!is_numeric($user_school_course_id)) {
			return $this->api_output_error->raise(5);
		}

		$data = $this->Courses_days->get_many(array('course_id'=>$course_id));
		return $this->api_output->send($data);
	}

}