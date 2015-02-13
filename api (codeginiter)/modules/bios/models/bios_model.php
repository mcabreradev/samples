<?php

class Bios_model extends MY_Model {

	protected $table = "user_bio";
	protected $primary_key = "user_bio_id";
	protected $fields = array("user_id", "bio", "carrer_aspirations", "dream_job", "hometown", "birthday", "modified_at");

	public $before_create = array('before_create');
	public $before_update = array('before_update');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
				'create' => array(
					array('field' => 'user_id',							'label' => 'User Id',							'rules' => 'required|trim|max_length[10]|is_unique[user_bio.user_id]')
				,	array('field' => 'bio',									'label' => 'Bio',									'rules' => 'trim|max_length[21844]')
				,	array('field' => 'carrer_aspirations',	'label' => 'Carrer Aspirations',	'rules' => 'trim|max_length[21844]')
				,	array('field' => 'dream_job',						'label' => 'Dream Job',						'rules' => 'trim|max_length[21844]')
				,	array('field' => 'hometown',						'label' => 'Hometown',						'rules' => 'trim|max_length[255]')
				,	array('field' => 'birthday',						'label' => 'Birthday',						'rules' => 'trim|required')
			),
				'update' => array(
					array('field' => 'user_id',							'label' => 'User Id',							'rules' => 'trim|max_length[10]|is_unique[user_bio.user_id]')
				,	array('field' => 'bio',									'label' => 'Bio',									'rules' => 'trim|max_length[21844]')
				,	array('field' => 'carrer_aspirations',	'label' => 'Carrer Aspirations',	'rules' => 'trim|max_length[21844]')
				,	array('field' => 'dream_job',						'label' => 'Dream Job',						'rules' => 'trim|max_length[21844]')
				,	array('field' => 'hometown',						'label' => 'Hometown',						'rules' => 'trim|max_length[255]')
				,	array('field' => 'birthday',						'label' => 'Birthday',						'rules' => 'trim|required')
			)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}

	}

	function before_create($user) {
		return $user;
	}

	function before_update($user) {
		$user['modified_at'] = date('Y-m-d H:i:s');
		return $user;
	}

}