<?php

class States_model extends MY_Model {

	protected $table = 'states';
	protected $primary_key = 'state_id';
	protected $fields = array('name', 'country_id');


	public function __construct() {
		parent::__construct();

	}

}