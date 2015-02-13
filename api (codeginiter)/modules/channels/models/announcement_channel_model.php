<?php

class Announcement_channel_model extends MY_Model {

	protected $table = 'announcements_channels';
	protected $primary_key = 'announcement_id';
	protected $fields = array('announcement_id', 'channel_id', 'title', 'description', 'created_at');

	public $before_create = array('before_create');
	public $before_update = array('before_update');

  public $per_page = 10;

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
			'create' => array(
					array('field' => 'title',							'label' => 'Title',							'rules' => 'trim|required')
			)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

	function before_create($channel) {
		$channel['created_at'] = date('Y-m-d H:i:s');
		return $channel;
	}

}
