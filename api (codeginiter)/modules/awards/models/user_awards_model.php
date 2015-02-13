<?php

class User_awards_model extends MY_Model {

	protected $table = 'user_awards';
	protected $primary_key = 'user_award_id';
	protected $fields = array('award_id', 'user_id', 'year');

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
				'create' => array(
					array('field' => 'award_id', 'label' => 'award_id', 'rules' => 'integer|required|xss_clean'), 
					array('field' => 'user_id', 'label' => 'user_id', 'rules' => 'integer|required|xss_clean'), 
					array('field' => 'year', 'label' => 'year', 'rules' => 'numeric|required|xss_clean')
					), 
				'delete' => array(
					array('field' => 'award_id', 'label' => 'award_id', 'rules' => 'integer|required|xss_clean'), 
					array('field' => 'user_id', 'label' => 'user_id', 'rules' => 'integer|required|xss_clean'), 
					array('field' => 'year', 'label' => 'year', 'rules' => 'numeric|required|xss_clean|max_length[4]')
					)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

	public function get_user_awards_all($user_id) {
		$this->db->select('user_award_id, name, year, user_award_id as id');
		$this->db->join('awards', 'awards.award_id = user_awards.award_id');
		$this->db->order_by("year", "asc");
		$data = $this->get_many(array('user_id'=>$user_id));
		$result = array();
		foreach ($data as $item) {
			$result[$item->year][] = $item;
		}
		return $result;
	}

	public function get_user_awards($user_award_id, $get_award_id = false) {
		$this->db->select('user_award_id, name, year');
    if ($get_award_id) $this->db->select('user_awards.award_id');
    
		$this->db->where('user_award_id', $user_award_id);
		$this->db->join('awards', 'awards.award_id = user_awards.award_id');
		return $this->get();
	}

}