<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Countries extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Countries_model', 'Countries');
	}

}