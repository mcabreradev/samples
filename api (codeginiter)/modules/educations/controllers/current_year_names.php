<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Current_year_names extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User_schools_current_year_names_model', 'User_schools_current_year_names');
	}

	public function read($user_school_current_year_name_id) {
		if (!is_numeric($user_school_current_year_name_id)) {
			return $this->api_output_error->raise(5);
		}
		$data = $this->User_schools_current_year_names->get($user_school_current_year_name_id);
		if (empty($data)) {
			return $this->api_output_error->raise(2);
		}
		return $this->api_output->send($data);
	}

	public function read_all() {
		$user_id = $this->user['user_id'];
		if (!is_numeric($user_id) ) {
			return $this->api_output_error->raise(5);
		}

		$data = $this->User_schools_current_year_names->get_many(array('user_id'=>$user_id));
		return $this->api_output->send($data);
	}
	
	public function get_all() {
		$data = $this->User_schools_current_year_names->get_all();
		return $this->api_output->send($data);
	}

}