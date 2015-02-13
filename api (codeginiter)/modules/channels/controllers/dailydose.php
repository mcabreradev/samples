<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dailydose extends MY_Controller

    public function __construct() {
        parent::__construct();
        $this->load->model('Channel_model', 'Channels');
        $this->load->model('users/users_model', 'Users');
        $this->load->model('users/auth_tokens_model', 'Auth');
    }

}