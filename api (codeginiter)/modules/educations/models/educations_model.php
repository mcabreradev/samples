<?php

class Users_model extends MY_Model {

	protected $table = "users";
	protected $primary_key = "user_id";
	protected $fields = array("first_name", "last_name", "email", "gender", "password", "password_hash", "created_at", "modified_at", "school_id", "reset_token", "reset_token_expires");

	public $before_create = array('before_create');
	public $before_update = array('before_update');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
				'create' => array(
					array('field' => 'first_name',				'label' => 'First Name',				'rules' => 'trim|required|xss_clean')
				, array('field' => 'last_name',					'label' => 'Last Name',					'rules' => 'trim|required|xss_clean')
				, array('field' => 'email',							'label' => 'Email',							'rules' => 'trim|required|max_length[255]|valid_email|is_unique[users.email]|xss_clean')
				, array('field' => 'gender',						'label' => 'Gender',						'rules' => 'trim|required|xss_clean')
				, array('field' => 'password',					'label' => 'Password',					'rules' => 'trim|required|min_length[1]|max_length[255]|xss_clean')
				, array('field' => 'confirm_password',	'label' => 'Confirm Password',	'rules' => 'trim|required|min_length[1]|max_length[255]|matches[password]|xss_clean')
			)
			,	'generate_reset_token' => array(
					array('field' => 'email',							'label' => 'Email',							'rules' => 'trim|required|max_length[255]|valid_email|xss_clean')
			)
			,	'reset_password' => array(
					array('field' => 'password',					'label' => 'Password',					'rules' => 'trim|required|min_length[1]|max_length[255]|xss_clean')
				,	array('field' => 'confirm_password',	'label' => 'Confirm Password',	'rules' => 'trim|required|min_length[1]|max_length[255]|matches[password]|xss_clean')
				,	array('field' => 'reset_token',				'label' => 'Reset Token',				'rules' => 'trim|exact_length[32]|xss_clean')
			)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

	function before_create($user) {
		$user['created_at'] = date('Y-m-d H:i:s');
		$user['password_hash'] = md5(microtime());
		$user['password'] = $this->get_hashed_password($user['password'], $user['password_hash']);
		$user['confirm_password'] = $this->get_hashed_password($user['confirm_password'], $user['password_hash']);
		return $user;
	}

	function before_update($user) {
		if ( isset($user['password']) AND isset($user['confirm_password']) ) {
			$user['password_hash'] = md5(microtime());
			$user['confirm_password'] = $this->get_hashed_password($user['confirm_password'], $user['password_hash']);
			$user['password'] = $this->get_hashed_password($user['password'], $user['password_hash']);
		}
		$user['modified_at'] = date('Y-m-d H:i:s');
		return $user;
	}

	function get_hashed_password($password, $hash) {
		return md5($password . $this->config->item('user.encryption_key') . $hash);
	}

	function check_password($password) {
		$user = $this->get(get_current_user_id_by_accessid('@@HARDCODED'));
		$hash_to_check = $this->get_hashed_password($password, $user->password_hash);
		if( $hash_to_check !== $user->password ){
			return FALSE;
		}
		return TRUE;
	}

}