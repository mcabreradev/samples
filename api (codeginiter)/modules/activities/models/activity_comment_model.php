<?php

class Activity_comment_model extends MY_Model
{

    protected $table = 'activities_comments';
    protected $primary_key = 'activity_comment_id';
    protected $fields = array('activity_comment_id', 'activity_id', 'user_id', 'content', 'created_at');

    public $before_create = array('before_create');
    public $before_update = array('before_update');

    public function __construct()
    {
        parent::__construct();

        $this->validate = array();
        $this->validate_rules_by_controller_method = array(
            'create' => array(
                array('field' => 'activity_id', 'label' => 'activity_id', 'rules' => 'trim|required'),
                array('field' => 'user_id', 'label' => 'user_id', 'rules' => 'trim|required'),
                array('field' => 'content', 'label' => 'Content', 'rules' => 'trim|required')
            )
        );

        if (isset($this->validate_rules_by_controller_method[$this->router->method])) {
            $this->validate = $this->validate_rules_by_controller_method[$this->router->method];
        }
        $this->load->model('educations/user_schools_model','Schools');
    }

    function get($activity_id, $user_id)
    {
        $query = "
        SELECT activities_comments.*, users.first_name, users.last_name, users.gender, IF(users.user_id = $user_id,1,0) as belongs_to_me
        FROM {$this->table}
        JOIN users ON users.user_id = activities_comments.user_id
        WHERE activities_comments.activity_id = $activity_id
        ORDER BY created_at ASC";
        $comments = $this->db->query($query)->result();

        foreach ($comments as &$comment) {
            $this->db->where("target_table", "users_profile");
            $this->db->where("target_id", $comment->user_id);
            $image = $this->db->get('images')->row();
            if (is_object($image)) {
                $image = $this->config->item('router.site.url') . 'image/' . str_replace('/', '--', $image->filepath . $image->filename);
                $comment->profileImage = $image;
            } else {
                $comment->profileImage = false;
            }

            $education = $this->Schools->get_with_year_name(array('user_id' => $comment->user_id));
            if (is_object($education)) {
                $comment->education = $education;
            } else {
                $comment->education = false;
            }
        }

        return $comments;
    }

    function before_create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    function before_update($data)
    {
        return $data;
    }

    function insert_report($activity_comment_id, $user_id)
    {
        return $this->db->insert('activities_comments_reports', array('activity_comment_id' => $activity_comment_id, 'user_id' => $user_id));
    }
}