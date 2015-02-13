<?php

class User_schools_current_year_names_model extends MY_Model {

	protected $table = 'user_schools_current_year_names';
	protected $primary_key = 'user_school_current_year_name_id';
	protected $fields = array('name', 'order');


	public function __construct() {
		parent::__construct();

	}

}