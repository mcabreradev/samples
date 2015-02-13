<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cities extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Cities_model', 'Cities');
	}

}