<?php

class Activities_model extends MY_Model {

	protected $table = 'activities';
	protected $primary_key = 'activity_id';
	protected $fields = array('activity_id', 'user_id', 'content', 'location_id', 'created_at', 'modified_at');

	public $before_create = array('before_create');
	public $before_update = array('before_update');

  public $per_page = 7;

	public function __construct() {
		parent::__construct();

		$this->validate = array();
		$this->validate_rules_by_controller_method = array(
			'create' => array(
					array('field' => 'content',							'label' => 'Content',							'rules' => 'trim|required')
			)
		);

		if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
			$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
		}
	}

  function get($activity_id) {
    $this->db->select('activities_links.activity_link_id');
    $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id', 'left');

    $this->db->select('activities_images.activities_image_id');
    $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id', 'left');

    $this->db->select('activities.*');
    $this->db->where('activities.activity_id', $activity_id);

    return $this->db->get($this->table)->row();
  }

  function get_by_type($type = 'all', $search = false, $user_states = false, $page = 1) {
    $output = array();

    $this->db->select('activities_reshare.original_activity_id as shared_activity_id');
    $this->db->join('activities_reshare', 'activities_reshare.activity_id = activities.activity_id', 'left');

    $this->db->select('activities_checkin.*');
    $this->db->join('activities_checkin', 'activities_checkin.activity_id = activities.activity_id', 'left');

    $this->db->select('activities_images.activities_image_id');
    $this->db->select('activities_links.activity_link_id');

    if ($type == 'all') {
      $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id', 'left');
      $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id', 'left');
    } elseif ($type == 'text') {
      $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id', 'left');
      $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id', 'left');
    } elseif ($type == 'image') {
      $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id');
      $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id', 'left');
    } elseif ($type == 'link') {
      $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id', 'left');
      $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id');
    }

    $this->db->select('activities.*'); // important to be last! don't change line

    if (is_array($user_states)) {
      $this->db->where_in('activities.user_id', $user_states);
    }

    if ($search) {
      $this->db->like('activities.content', $search);
    }

    $this->db->limit($this->per_page);
    $this->db->offset($this->per_page * ($page - 1));
    $this->db->order_by('activities.created_at', 'desc');
    $this->db->group_by('activities.activity_id');
    $result = $this->db->get($this->table)->result();

    return $result;
  }

  function get_by_user($read_by_user_id, $user_states = false, $page = 1) {
    $output = array();

    $this->db->select('activities_reshare.original_activity_id as shared_activity_id');
    $this->db->join('activities_reshare', 'activities_reshare.activity_id = activities.activity_id', 'left');

    $this->db->select('activities_checkin.*');
    $this->db->join('activities_checkin', 'activities_checkin.activity_id = activities.activity_id', 'left');

    $this->db->select('activities_images.activities_image_id');
    $this->db->select('activities_links.activity_link_id');

    $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id', 'left');
    $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id', 'left');

    $this->db->select('activities.*'); // important to be last! don't change line

    if (is_array($user_states)) {
      $this->db->where_in('activities.user_id', $user_states);
    }

    $this->db->limit($this->per_page);
    $this->db->offset($this->per_page * ($page - 1));
    $this->db->order_by('activities.created_at', 'desc');
    $this->db->group_by('activities.activity_id');
    $this->db->where('user_id',$read_by_user_id);
    $result = $this->db->get($this->table)->result();

    return $result;
  }

  function get_for_radar($type = 'all', $campus = false, $interval = 0, $user_states = false, $page = 1) {
    $output = array();

    $this->db->select('universities.name as campus_name');
    $this->db->join('user_schools', 'user_schools.user_id = activities.user_id');
    $this->db->join('universities', 'user_schools.university_id = universities.university_id');
    $this->db->where('universities.name', $campus);

    $this->db->select('activities_checkin.*');
    $this->db->join('activities_checkin', 'activities_checkin.activity_id = activities.activity_id');

    $this->db->select('activities_images.activities_image_id');
    $this->db->select('activities_links.*');

    if ($type == 'all') {
      $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id', 'left');
      $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id', 'left');
    } elseif ($type == 'text') {
      $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id', 'left');
      $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id', 'left');
    } elseif ($type == 'image') {
      $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id');
      $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id', 'left');
    } elseif ($type == 'link') {
      $this->db->join('activities_images', 'activities_images.activity_id = activities.activity_id', 'left');
      $this->db->join('activities_links', 'activities_links.activity_id = activities.activity_id');
    }

    $this->db->select('activities.*'); // important to be last! don't change line

    if (is_array($user_states)) {
      $this->db->where_in('activities.user_id', $user_states);
    }

    if ($interval) {
      $date_from = time()-($interval*60);
      $formated_date_from = date('Y-m-d H:i:s', $date_from);
      $this->db->where('activities.created_at >=', $formated_date_from);
    }

    $this->db->limit($this->per_page);
    $this->db->offset($this->per_page * ($page - 1));
    $this->db->order_by('activities.created_at', 'desc');
    $this->db->group_by('activities.activity_id');

    $result = $this->db->get($this->table)->result();

    return $result;
  }

	function before_create($activity) {
		$activity['created_at'] = date('Y-m-d H:i:s');
		return $activity;
	}

	function before_update($activity) {
		$activity['modified_at'] = date('Y-m-d H:i:s');
		return $activity;
	}

}
