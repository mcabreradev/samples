<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Degree_types extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Degree_types_model', 'Degree_types');
	}

	public function read($degree_type_id) {
		if (!is_numeric($degree_type_id)) {
			return $this->api_output_error->raise(5);
		}
		$data = $this->Degree_types->get($degree_type_id);
		if (empty($data)) {
			return $this->api_output_error->raise(2);
		}
		return $this->api_output->send($data);
	}

	public function read_all() {
		$data = $this->Degree_types->get_all();
		return $this->api_output->send($data);
	}

}