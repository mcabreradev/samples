<?php

class Activity_link_model extends MY_Model {

	protected $table = 'activities_links';
	protected $primary_key = 'activity_link_id';
	protected $fields = array('activity_link_id', 'activity_id', 'url', 'thumb', 'video_url', 'title', 'description');

	public $before_create = array('before_create');
	public $before_update = array('before_update');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
			'create' => array(
					array('field' => 'activity_id',					'label' => 'activity_id',			   	'rules' => 'trim|required'),
					array('field' => 'url',						'label' => 'url',							'rules' => 'trim|required'),
					array('field' => 'title',						'label' => 'title',							'rules' => 'trim|required'),
			)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

    public function get_recent() {
        $query="
            SELECT
              users.user_id, users.first_name,users.last_name,
              activities.created_at,
              venues.name as venue_name
            FROM {$this->table}
            JOIN activities ON activities.activity_id = {$this->table}.activity_id
            JOIN venues ON venues.venue_id = {$this->table}.venue_id
            JOIN users ON users.user_id = activities.user_id
            ORDER BY activities.created_at DESC
            LIMIT 0,4
        ";
        return $this->db->query($query)->result();
    }
  
	function before_create($data) {
		return $data;
	}

	function before_update($data) {
		return $data;
	}

}