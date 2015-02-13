<?php

class User_schools_courses_model extends MY_Model
{

    protected $table = 'user_schools_courses';
    protected $primary_key = 'user_school_course_id';
    protected $fields = array('user_school_id', 'course_id');

    public $per_page = 6;
    public $per_first_page = 12;

    public function __construct()
    {
        parent::__construct();

        $this->validate = array();
        $this->validate_rules_by_controller_method = array(
            'create' => array(
                array('field' => 'user_school_id', 'label' => 'user_school_id', 'rules' => 'integer|required|xss_clean'),
                array('field' => 'course_id', 'label' => 'Course', 'rules' => 'integer|required|xss_clean')
            ),
            'update' => array(
                array('field' => 'user_school_id', 'label' => 'user_school_id', 'rules' => 'integer|required|xss_clean'),
                array('field' => 'course_id', 'label' => 'Course', 'rules' => 'integer|required|xss_clean')
            )
        );

        if (isset($this->validate_rules_by_controller_method[$this->router->method])) {
            $this->validate = $this->validate_rules_by_controller_method[$this->router->method];
        }

        $this->load->model('educations/user_schools_model', 'Schools');
        $this->load->model('universities/universities_model', 'Universities');
        $this->load->model('educations/courses_model', 'Courses');
        $this->load->model('educations/courses_days_model', 'Courses_days');
    }

    public function get_courses($user_school_course_id)
    {

        $this->db->select($this->table . '.user_school_course_id, user_school_id, name, season, year');
        $this->db->where($this->table . '.user_school_course_id', $user_school_course_id);
        $course = $this->get();
        $course['days'] = $this->Courses_days->get_all($course['course_id']);
        return $course;
    }

    public function get_course($course_slug)
    {
        $this->load->model('universities/universities_model', 'University');
        $courses_table = $this->Courses->get_table();
        $universities_table = $this->University->get_table();
        $this->db->select('c.course_id, c.name, c.season, c.year, u.name AS university')
            ->join($courses_table . ' c', 'c.course_id = sc.course_id')
            ->join($universities_table . ' u', 'u.university_id = c.university_id')
            ->where('c.slug', $course_slug);
        $course = $this->db->get($this->table . ' AS sc')->row();
        is_object($course) and $course->days = $this->Courses_days->get_all($course->course_id);
        return $course;
    }

    protected function _set_page($page)
    {
        $per_page = $page > 1 ? $this->per_page : $this->per_first_page;
        $offset = $page > 1 ? $this->per_first_page + $this->per_page * ($page - 2) : 0;
        $this->db->limit($per_page)->offset($offset);
    }

    public function get_students_in_class($course_id, $page = 1)
    {
        $this->load->model('users/users_model', 'User');
        $user_schools_table = $this->Schools->get_table();
        $users_table = $this->User->get_table();
        $this->_set_page($page);
        $this->db->select('u.user_id, u.first_name, u.last_name')
            ->join($user_schools_table . ' s', 's.user_school_id = sc.user_school_id')
            ->join($users_table . ' u', 'u.user_id = s.user_id')
            ->where('sc.course_id', $course_id)
            ->order_by('u.last_name')
            ->order_by('u.first_name');
        $users = $this->db->get($this->table . ' AS sc')->result();
        return $users;
    }

    public function get_total_students_in_class($course_id)
    {
        $user_schools_table = $this->Schools->get_table();
        $this->db->select('s.user_id')
            ->join($user_schools_table . ' s', 's.user_school_id = sc.user_school_id')
            ->where('sc.course_id', $course_id);
        $users = $this->db->get($this->table . ' AS sc')->num_rows();
        return $users;
    }


    public function get_courses_by_user_school($user_school_id)
    {
        $courses_table = $this->Courses->get_table();
        $this->db->join($courses_table, $courses_table . '.course_id = ' . $this->table . '.course_id');
        $user_school_courses = $this->get_many(array('user_school_id' => $user_school_id));
        foreach ($user_school_courses as &$user_school_course) {
            $user_school_course->days = $this->Courses_days->get_all(array('course_id'=>$user_school_course->course_id));
        }
        return $user_school_courses;
    }

    public function get_courses_by_university($university_id, $page, $semester, $letter, $search)
    {
        $courses_table = $this->Courses->get_table();
        $universities_table = $this->Universities->get_table();

        //Prepare filters BEGIN
        if ($semester != '-1') {
            $semester = str_replace("%20", " ", $semester);
            $semester_filter = explode(" ", $semester);
            $semester_filter_season = $semester_filter[0];
            $semester_filter_year = $semester_filter[1];
        }
        if ($search != '-1') {
            $search = str_replace("%20", " ", $search);
        }
        //Prepare filters END

        $query = "
			SELECT {$courses_table}.*
			FROM {$this->table}
			JOIN {$courses_table} ON {$courses_table}.course_id = {$this->table}.course_id " .
            ($letter != '-1' ? " AND {$courses_table}.name LIKE '{$letter}%' " : "") .
            ($search != '-1' ? " AND {$courses_table}.name LIKE '%{$search}%' " : "") .
            ($semester != '-1' ? " AND {$courses_table}.season = '{$semester_filter_season}' AND {$courses_table}.year = '{$semester_filter_year}'" : "") . "
		";
        if ($university_id != -1) {
            $query .= "
				JOIN {$universities_table} ON
					{$universities_table}.university_id = {$courses_table}.university_id
					AND {$universities_table}.university_id = {$university_id}
			";
        }
        $take_count = $page == 1 ? 15 : 9;
        $skip = ($page - 1) * ($take_count); // $take_count+1 is because we will check if there are more then we need -> we can load more
        $take_count_with_one_more = $take_count + 1;
        $query .= "
            GROUP BY {$courses_table}.course_id
            LIMIT $skip, $take_count_with_one_more
        ";
        $user_school_courses = $this->db->query($query)->result();
        $load_more_enabled = FALSE;
        if (count($user_school_courses) > $take_count) {
            $load_more_enabled = TRUE;
            $user_school_courses = array_slice($user_school_courses, 0, $take_count);
        }
        foreach ($user_school_courses as &$user_school_course) {
            $user_school_course->days = $this->Courses_days->get_all(array('course_id' => $user_school_course->course_id));
        }
        return array('courses' => $user_school_courses, 'load_more_enabled' => $load_more_enabled);
    }

    // public function get_schools_all($user_id) {
    // 	$schools_table = $this->Schools->get_table();

    // 	$this->db->select($this->table.'.user_school_course_id, '.$this->table.'.user_school_id, '.$this->table.'.name, season, year, day_name, start_time, end_time');
    // 	$this->db->join($schools_table, $schools_table.'.user_school_id = '.$this->table.'.user_school_id');

    // 	$courses = $this->get_many(array('user_id'=>$user_id));
    //    foreach($courses as &$course){
    //      $course['days'] = $this->Courses_days->get_all($course['course_id']);
    //    }
    //    return $courses;
    // }

}