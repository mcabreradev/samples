<?php

class User_schools_model extends MY_Model
{

    protected $table = 'user_schools';
    protected $primary_key = 'user_school_id';
    protected $fields = array(
        'user_id', 'university_id', 'degree_type_id', 'expected_graduation',
        'start_date', 'end_date', 'user_school_current_year_name_id'
    );


    public function __construct()
    {
        parent::__construct();

        $this->validate = array();
        $this->validate_rules_by_controller_method = array(
            'create' => array(
                array('field' => 'user_id', 'label' => 'user_id', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'university_id', 'label' => 'University', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'degree_type_id', 'label' => 'degree_type_id', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'expected_graduation', 'label' => 'expected_graduation', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'start_date', 'label' => 'start_date', 'rules' => 'trim|xss_clean'),
                array('field' => 'end_date', 'label' => 'end_date', 'rules' => 'trim|xss_clean'),
                array('field' => 'user_school_current_year_name_id', 'label' => 'user_school_current_year_name_id', 'rules' => 'integer|required|xss_clean')
            ),
            'update' => array(
                array('field' => 'user_id', 'label' => 'user_id', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'university_id', 'label' => 'University', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'degree_type_id', 'label' => 'degree_type_id', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'expected_graduation', 'label' => 'expected_graduation', 'rules' => 'trim|required|xss_clean'),
                array('field' => 'start_date', 'label' => 'start_date', 'rules' => 'trim|xss_clean'),
                array('field' => 'end_date', 'label' => 'end_date', 'rules' => 'trim|xss_clean'),
                array('field' => 'user_school_current_year_name_id', 'label' => 'user_school_current_year_name_id', 'rules' => 'integer|required|xss_clean')
            )
        );

        if (isset($this->validate_rules_by_controller_method[$this->router->method])) {
            $this->validate = $this->validate_rules_by_controller_method[$this->router->method];
        }

        $this->load->model('educations/user_schools_majors_model', 'User_schools_majors');
        $this->load->model('educations/user_schools_minors_model', 'User_schools_minors');
        $this->load->model('universities/universities_model', 'Universities');
    }

    public $after_get = array('get_university');

    function get_university($user_school)
    {
        $user_school->university = $this->Universities->get($user_school->university_id);
        unset($user_school->university_id);
        return $user_school;
    }

    public function get_schools_all($user_id)
    {
        $this->load->model('educations/user_schools_courses_model', 'User_schools_courses');
        $user_schools_majors_table = $this->User_schools_majors->get_table();
        $user_schools_minors_table = $this->User_schools_minors->get_table();

        $result = array();
        $schools = $this->db
            ->order_by('user_school_id', 'asc')
            ->get_where($this->get_table(), array('user_id' => (int)$user_id))
            ->result();
        if (empty($schools)) {
            return $schools;
        }
        foreach ($schools as $item) {
            $item->university = $this->Universities->get($item->university_id);
            unset($item->university_id);

            $degree_type = $this->db->select('name')->get('degree_types', array('degree_type_id' => $item->degree_type_id))->result();
            $item->degree_type = null;
            if (!empty($degree_type)) {
                $item->degree_type = $degree_type[0]->name;
            }

            if ($item->user_school_current_year_name_id) {
                $query = "
					SELECT name
					FROM user_schools_current_year_names
					WHERE user_school_current_year_name_id = {$item->user_school_current_year_name_id}
				";
                $user_school_current_year_name = $this->db->query($query)->row();
                $item->user_school_current_year_name = $user_school_current_year_name ? $user_school_current_year_name->name : NULL;
            }

            $majors = $this->db->get_where($user_schools_majors_table, array('user_school_id' => $item->user_school_id))->result();
            $item->majors = $majors;

            $minors = $this->db->get_where($user_schools_minors_table, array('user_school_id' => $item->user_school_id))->result();
            $item->minors = $minors;

            $item->courses = $this->User_schools_courses->get_courses_by_user_school($item->user_school_id);

            $item->minors = $minors;

            $result[] = $item;
        }

        $highschools = $this->db->get_where('user_highschools', array('user_id' => $item->user_school_id))->result();
        return $result;
    }

    public function get_schools($user_school_id)
    {
        $user_schools_majors_table = $this->User_schools_majors->get_table();
        $user_schools_minors_table = $this->User_schools_minors->get_table();

        $this->db->select($this->table . '.user_school_id, ' . $this->table . '.user_id, '.$this->table.'.university_id, degree_type_id, expected_graduation,
											user_school_current_year_name_id, start_date, end_date,
											user_school_major_id, user_school_minor_id');
        $this->db->where($this->table . '.user_school_id', $user_school_id);
        $this->db->join($user_schools_majors_table, $user_schools_majors_table . '.user_school_id = ' . $this->table . '.user_school_id');
        $this->db->join($user_schools_minors_table, $user_schools_minors_table . '.user_school_id = ' . $this->table . '.user_school_id');

        $item = $this->get();
        return $item;
    }

    public function create_temp($user_id)
    {
        $this->db->set('user_id', $user_id);
        $this->db->insert($this->table);
    }

    function get_campuses()
    {
        $this->db->select('universities.name as campus');
        $this->db->where('deleted', 0);
        $this->db->order_by('universities.name', 'asc');

        return $this->db->get('universities')->result();
    }

    public function get_with_year_name($where)
    {
        $this->db->select('us.*, yn.name as year_name')
            ->join('user_schools_current_year_names yn', 'yn.user_school_current_year_name_id = us.user_school_current_year_name_id', 'left');
        $result = $this->db->get_where($this->table . ' AS us', $where)->row();
        $result and $result->university = $this->Universities->get($result->university_id);
        return $result;
    }

}