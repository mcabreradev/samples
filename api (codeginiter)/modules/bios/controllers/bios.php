<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bios extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('bios_model', 'Bios');
		$this->load->model('users/users_model', 'Users');
		$this->load->model('users/auth_tokens_model', 'Auth');
	}

	public function _create($data) {
		$this->Bios->insert($data);
	}

	public function read($user_id = FALSE) {
		// Validate user_id
		empty($user_id) and $user_id = $this->user['user_id'];
		if ( ! is_numeric($user_id) ) {
			return $this->api_output_error->raise(5);
		}

		// Load bios of user
		$data = $this->Bios->get(array('user_id'=>$user_id));
		if (empty($data)) {
			return $this->api_output_error->raise(2);
		}
		return $this->api_output->send($data);
	}

	public function update() {
		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}

		// Validate user_id
		$user_id = $this->user['user_id'];
		if (empty($user_id)) {
			return $this->api_output_error->raise(1002, $this->Bios->validation_errors);
		}

		// Load bios of user
		$user_bios = $this->Bios->get(array('user_id'=>$user_id));
		if (empty($user_bios)) {
			$this->Bios->insert(array('user_id'=>$user_id));
			$user_bios = $this->Bios->get(array('user_id'=>$user_id));
		}

		// Update bios of user
		$result = $this->Bios->update($user_bios->user_bio_id, $data);
		if( ! empty($this->Bios->validation_errors ) ){
			return $this->api_output_error->raise(6, $this->Bios->validation_errors);
		}
		if ($result == '') {
			return $this->api_output->send(FALSE);
		}
		return $this->api_output->send(TRUE);
	}

}