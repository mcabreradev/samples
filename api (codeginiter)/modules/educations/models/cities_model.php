<?php

class Cities_model extends MY_Model {

	protected $table = 'cities';
	protected $primary_key = 'city_id';
	protected $fields = array('name', 'state_id');


	public function __construct() {
		parent::__construct();
	}

}