<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activities_likes extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Activities_model', 'Activities');
		$this->load->model('Activity_like_model', 'ActivitiesLikes');
	}

	public function create() {
		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}
    
    $data['user_id'] = $this->user['user_id'];
		if (empty($data['user_id'])) {
			return $this->api_output_error->raise(401);
		}
    
    $is_exist = $this->ActivitiesLikes->get($data);
    if (is_object($is_exist)) {
      $this->ActivitiesLikes->delete($is_exist->activity_like_id);
    } else {
      $activity_like_id = $this->ActivitiesLikes->insert($data);
      if(!empty($this->ActivitiesLikes->validation_errors)){
        return $this->api_output_error->raise(1002, $this->ActivitiesLikes->validation_errors);
      }
    }
    
		return $this->api_output->send('success');
	}

	public function read_all($activity_id = false) {
		if (empty($activity_id)) {
			return $this->api_output_error->raise(1);
		}
    
		$data = $this->ActivitiesLikes->get_many(array('activity_id' => $activity_id));
		if (!is_array($data) ) {
			return $this->api_output_error->raise(2);
		}

		return $this->api_output->send($data);
	}

}