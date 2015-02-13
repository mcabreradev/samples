<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Awards extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User_awards_model', 'User_awards');
		$this->load->model('Awards_model', 'Awards');
		$this->load->model('users/users_model', 'Users');
		$this->load->model('users/auth_tokens_model', 'Auth');		

	}

	public function create() {

		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}

		$data_award = $this->Awards->get('name', $data['name']);
		if (!empty($data_award)) {
			$award_id = $data_award->award_id;
		} else {
			$award_id = $this->Awards->insert(array('name' => $data['name']));
		}

		$user_awards_id = $this->User_awards->insert(array(
			'award_id' => $award_id,
			'user_id' => $this->user['user_id'],
			'year' => $data['year']
			));

		if( ! empty($this->User_awards->validation_errors ) ){
			return $this->api_output_error->raise(1002, $this->User_awards->validation_errors);
		}
		if (!is_numeric($user_awards_id)) {
			return $this->api_output_error->raise(1001);
		}
		return $this->api_output->send($user_awards_id);
	}
  
  public function update($user_awards_id) {
    if (!is_numeric($user_awards_id) ) {
			return $this->api_output_error->raise(5);
		}
    $data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}
    
    $data_user_award = $this->User_awards->get_user_awards($user_awards_id, true);
    if (empty($data_user_award)) {
			return $this->api_output_error->raise(2);
		}
    
    $this->Awards->update($data_user_award->award_id, array('name' => $data['name']));
    $this->User_awards->update($user_awards_id, array('year' => $data['year']));
    
    $data_user_award = $this->User_awards->get_user_awards($user_awards_id);
    
    return $this->api_output->send($data_user_award);
  }

	public function read($user_awards_id) {	
		if (!is_numeric($user_awards_id) ) {
			return $this->api_output_error->raise(5);
		}
		$data_user_awards = $this->User_awards->get_user_awards($user_awards_id);
		if (empty($data_user_awards)) {
			return $this->api_output_error->raise(2);
		}
		return $this->api_output->send($data_user_awards);
	}

	public function read_all($user_id = false) {
		// Validate user_id
		$user_id or $user_id = $this->user['user_id'];	
		if ( ! is_numeric($user_id)) {
			return $this->api_output_error->raise(5);
		}

		$data = $this->User_awards->get_user_awards_all($user_id);

		if (!is_array($data) ) {
			return $this->api_output_error->raise(2);
		}

		return $this->api_output->send($data);
	}

	public function delete($user_awards_id) {
		if (!is_numeric($user_awards_id) ) {
			return $this->api_output_error->raise(5);
		}
		$result = $this->User_awards->delete($user_awards_id);
		if ($result == '') {
			return $this->api_output->send(FALSE);
		}
		return $this->api_output->send(TRUE);
	}
}