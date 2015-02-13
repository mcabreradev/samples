<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Channels extends MY_Controller {

	public function __construct() {
		parent::__construct();
    $this->load->model('Channel_model', 'Channels');
		$this->load->model('users/users_model', 'Users');
		$this->load->model('users/auth_tokens_model', 'Auth');
	}

	public function read($channel_id) {
    if ( ! is_numeric($channel_id) ) {
      return $this->api_output_error->raise(5);
    }
    $data = $this->Channels->get($channel_id);
    if (empty($data)) {
      return $this->api_output_error->raise(2);
    }
    return $this->api_output->send($data);
	}

	public function read_all() {
    $data = $this->Channels->get_many(array());
    return $this->api_output->send($data);
  }

  public function announcement_channel($channel_id) {
    $this->load->model('Announcement_channel_model', 'Announcement_channel');
    if ( ! is_numeric($channel_id) ) {
      return $this->api_output_error->raise(5);
    }
    $data = $this->Announcement_channel->get($channel_id);
    if (empty($data)) {
      return $this->api_output_error->raise(2);
    }
    return $this->api_output->send($data);
  }

}