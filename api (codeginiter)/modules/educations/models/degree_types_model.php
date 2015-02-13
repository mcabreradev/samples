<?php

class Degree_types_model extends MY_Model {

	protected $table = 'degree_types';
	protected $primary_key = 'degree_type_id';
	protected $fields = array('name');


	public function __construct() {
		parent::__construct();

	}

}