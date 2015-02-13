<?php

class Activity_missed_connection_model extends MY_Model
{

    protected $table = 'activities_missed_connections';
    protected $primary_key = 'activity_missed_connection_id';
    protected $fields = array('activity_missed_connection_id', 'user_id', 'activity_checkin_id', 'distance', 'created_at');

    public $before_create = array('before_create');
    public $before_update = array('before_update');

    public function __construct()
    {
        parent::__construct();

        $this->validate = array();
        $this->validate_rules_by_controller_method = array(
            'create' => array(
                array('field' => 'user_id',             'label' => 'user_id',               'rules' => 'trim|required'),
                array('field' => 'activity_checkin_id', 'label' => 'activity_checkin_id',   'rules' => 'trim|required'),
                array('field' => 'distance',            'label' => 'distance',              'rules' => 'trim|required'),
            )
        );

        if (isset($this->validate_rules_by_controller_method[$this->router->method])) {
            $this->validate = $this->validate_rules_by_controller_method[$this->router->method];
        }
    }

    public function get_recent($user_id)
    {
        $query = "
            SELECT
              users.user_id, users.first_name,users.last_name,
              {$this->table}.created_at, {$this->table}.distance
            FROM {$this->table}
            JOIN activities_checkin ON activities_checkin.activity_checkin_id = {$this->table}.activity_checkin_id
            JOIN activities ON activities.activity_id = activities_checkin.activity_id
            JOIN users ON users.user_id = activities.user_id
            WHERE {$this->table}.user_id = $user_id
            ORDER BY {$this->table}.created_at DESC
            LIMIT 0,4
        ";
        return $this->db->query($query)->result();
    }

    function before_create($data)
    {
        return $data;
    }

    function before_update($data)
    {
        return $data;
    }

}