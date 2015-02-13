<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Highschools extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User_highschools_model', 'User_highschools');
	}

	public function create() {
		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}
    
    $data['city_id'] = 1;
    $data['user_id'] = $this->user['user_id'];
		$user_highschool_id = $this->User_highschools->insert($data);

		if(!empty($this->User_highschools->validation_errors)){
			return $this->api_output_error->raise(1501, $this->User_highschools->validation_errors);
		}

		return $this->api_output->send($user_highschool_id);
	}

	public function update($user_highschool_id, $data=FALSE) {
		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}
		$result = $this->User_highschools->update($user_highschool_id, $data);

		if(!empty($this->User_highschools->validation_errors)){
			return $this->api_output_error->raise(1502, $this->User_highschools->validation_errors);
		}

		if(empty($result)) {
			return $this->api_output->send(FALSE);
		}

		return $this->api_output->send(TRUE);
	}

	public function read($user_highschool_id) {
		return $this->read_all();
		// if (!is_numeric($user_highschool_id)) {
		// 	return $this->api_output_error->raise(5);
		// }
		// $data = $this->User_highschools->get($user_highschool_id);
		// if (empty($data)) {
		// 	return $this->api_output_error->raise(2);
		// }
		// return $this->api_output->send($data);
	}

	public function read_all() {
		$user_id = $this->user['user_id'];
		// die(var_dump($user_id));		
		if (!is_numeric($user_id) ) {
			return $this->api_output_error->raise(5);
		}

		$data = $this->User_highschools->get_many(array('user_id'=>$user_id));
		return $this->api_output->send($data);
	}

	public function delete($user_highschool_id) {
		if (!is_numeric($user_highschool_id) ) {
			return $this->api_output_error->raise(5);
		}
		$result = $this->User_highschools->delete($user_highschool_id);
		if (empty($result)) {
			return $this->api_output->send(FALSE);
		}
		return $this->api_output->send(TRUE);
	}
}