<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Degree_types extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('States_model', 'States');
	}

}