<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Activities_checkins extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Activities_model', 'Activities');
        $this->load->model('Activity_checkin_model', 'ActivitiesCheckins');
        $this->load->model('users/users_model', 'Users');
        $this->load->model('users/auth_tokens_model', 'Auth');
    }

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);
        if (empty($data)) {
            return $this->api_output_error->raise(1);
        }

        $activity_checkin_id = $this->ActivitiesCheckins->insert($data);
        if (!empty($this->ActivitiesCheckins->validation_errors)) {
            return $this->api_output_error->raise(1002, $this->ActivitiesCheckins->validation_errors);
        }

        return $this->api_output->send($activity_checkin_id);
    }

    public function read($activity_id = false)
    {
        if (empty($activity_id)) {
            return $this->api_output_error->raise(1);
        }

        $user_id = $this->user['user_id'];
        if (empty($user_id)) {
            return $this->api_output_error->raise(401);
        }

        $data = $this->ActivitiesCheckins->get(array('activity_id' => $activity_id));
        if (empty($data)) {
            return $this->api_output_error->raise(2);
        }

        return $this->api_output->send($data);
    }

    public function recent() {
        $user_id = $this->user['user_id'];
        if (empty($user_id)) {
            return $this->api_output_error->raise(401);
        }

        $data = $this->ActivitiesCheckins->get_recent();
        if (!is_array($data) ) {
            return $this->api_output_error->raise(2);
        }

        return $this->api_output->send($data);
    }

}