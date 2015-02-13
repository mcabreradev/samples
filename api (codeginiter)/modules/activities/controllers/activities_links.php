<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Activities_links extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Activities_model', 'Activities');
        $this->load->model('Activity_link_model', 'ActivitiesLinks');
    }

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);
        if (empty($data)) {
            return $this->api_output_error->raise(1);
        }
        
        $user_id = $this->user['user_id'];
        if (empty($user_id)) {
            return $this->api_output_error->raise(401);
        }
        
        $id = $this->ActivitiesLinks->insert($data);
        if (!empty($this->ActivitiesLinks->validation_errors)) {
            return $this->api_output_error->raise(1002, $this->ActivitiesLinks->validation_errors);
        }

        return $this->api_output->send($id);
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

        $data = $this->ActivitiesLinks->get(array('activity_id' => $activity_id));
        if (empty($data)) {
            return $this->api_output_error->raise(2);
        }

        return $this->api_output->send($data);
    }

}