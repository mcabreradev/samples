<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activities_missed_connections extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Activity_missed_connection_model', 'ActivityMissedConnections');
    }

    public function recent() {
        $user_id = $this->user['user_id'];
        if (empty($user_id)) {
            return $this->api_output_error->raise(401);
        }

        $data = $this->ActivityMissedConnections->get_recent($user_id);
        if (!is_array($data) ) {
            return $this->api_output_error->raise(2);
        }

        return $this->api_output->send($data);
    }

}