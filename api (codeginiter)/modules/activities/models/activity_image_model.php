<?php

class Activity_image_model extends MY_Model
{

	protected $table = 'activities_images';
	protected $primary_key = 'activities_image_id';
	protected $fields = array('activities_image_id', 'activity_id', 'album_id', 'filename', 'created_at', 'modified_at', 'deleted');

	public $before_create = array('before_create');
	public $before_update = array('before_update');

	public function __construct()
	{
			parent::__construct();

			$this->validate = array();
			$this->validate_rules_by_controller_method = array(
					'create' => array(
							array('field' => 'activity_id', 'label' => 'activity_id', 'rules' => 'trim|required'),
							array('field' => 'filename', 'label' => 'filename', 'rules' => 'trim|required'),
					)
			);

			if (isset($this->validate_rules_by_controller_method[$this->router->method])) {
					$this->validate = $this->validate_rules_by_controller_method[$this->router->method];
			}
	}

	function before_create($data)
	{
			$data['created_at'] = date('Y-m-d H:i:s');
			return $data;
	}

	function before_update($data)
	{   
			$data['modified_at'] = date('Y-m-d H:i:s');
			return $data;
	}
	
	public function get_by_activity($activity_id) {
    $this->db->order_by('created_at', 'desc');
    $result = $this->db->get_where('activities_images', array(
			'deleted'     => false,
			'activity_id' => $activity_id
		))->result();
    return $result;
  }
}