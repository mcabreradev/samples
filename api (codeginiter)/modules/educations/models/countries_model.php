<?php

class Countries_model extends MY_Model {

	protected $table = 'countries';
	protected $primary_key = 'country_id';
	protected $fields = array('name');


	public function __construct() {
		parent::__construct();

	}

}