<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activities_comments extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Activities_model', 'Activities');
		$this->load->model('Activity_comment_model', 'ActivitiesComments');
		$this->load->model('users/users_model', 'Users');
		$this->load->model('users/auth_tokens_model', 'Auth');
	}

	public function create() {
		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}
    
		$data['user_id'] = $this->user['user_id'];
		$activity_comment_id = $this->ActivitiesComments->insert($data);
		if(!empty($this->ActivitiesComments->validation_errors)){
      return $this->api_output_error->raise(1002, $this->ActivitiesComments->validation_errors);
    }
    
		return $this->api_output->send($activity_comment_id);
	}

	public function read_all($activity_id = false) {
		if (empty($activity_id)) {
			return $this->api_output_error->raise(1);
		}
    
		$user_id = $this->user['user_id'];
		if (empty($user_id)) {
			return $this->api_output_error->raise(401);
		}

		$data = $this->ActivitiesComments->get($activity_id,$user_id);
		if (!is_array($data) ) {
			return $this->api_output_error->raise(2);
		}

		return $this->api_output->send($data);
	}

    public function report($activity_comment_id) {
        $data['user_id'] = $this->user['user_id'];
        $activity_comment_report_id = $this->ActivitiesComments->insert_report($activity_comment_id,$data['user_id']);
        return $this->api_output->send($activity_comment_report_id);
    }

    public function delete($id) {
        if ( ! is_numeric($id) ) {
            return $this->api_output_error->raise(5);
        }
        $result = $this->ActivitiesComments->delete($id);
        if ($result == '') {
            return $this->api_output->send(FALSE);
        }
        return $this->api_output->send(TRUE);
    }
}