<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activities_images extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Activities_model', 'Activities');
		$this->load->model('Activity_image_model', 'ActivitiesImages');
	}

	public function create() {
		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}
    
		$data['user_id'] = $this->user['user_id'];
		$activity_image_id = $this->ActivitiesImages->insert($data);
		if(!empty($this->ActivitiesImages->validation_errors)){
      return $this->api_output_error->raise(1002, $this->ActivitiesImages->validation_errors);
    }
    
		return $this->api_output->send($activity_image_id);
	}
}