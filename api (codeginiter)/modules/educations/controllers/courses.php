<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Courses extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_schools_model', 'User_schools');
        $this->load->model('User_schools_courses_model', 'User_schools_courses');
        $this->load->model('courses_model', 'Courses');
        $this->load->model('courses_days_model', 'Courses_days');
    }

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);
        if (empty($data)) {
            return $this->api_output_error->raise(1);
        }
        if (!isset($data['course_id'])) {
            $user_school_id = $data['user_school_id'];
            unset($data['user_school_id']);
            $data['slug'] = $this->__generate_unique_slug($data['name']);
            $data['course_id'] = $this->Courses->insert($data);
            $data['user_school_id'] = $user_school_id;
        }
        $user_school_course_id = $this->User_schools_courses->insert($data);

        if (!empty($this->User_schools_courses->validation_errors)) {
            return $this->api_output_error->raise(1401, $this->User_schools_courses->validation_errors);
        }
        foreach ($data['days'] as $day) {
            $day['course_id'] = $data['course_id'];
            //validate day start time < day end time
            $start_time_date = DateTime::createFromFormat('H:i:s', $day['start_time']);
            $end_time_date = DateTime::createFromFormat('H:i:s', $day['end_time']);
            $isTimeValid = $start_time_date < $end_time_date;
            if (!$isTimeValid) {
                return $this->api_output_error->raise(1402, "Start time should be less than end time");
            } else {
                $this->Courses_days->insert($day);
            }
        }

        if (!empty($this->Courses_days->validation_errors)) {
            return $this->api_output_error->raise(1402, $this->User_schools_courses->validation_errors);
        }

        return $this->api_output->send($user_school_course_id);
    }

    public function update($user_school_course_id, $data = FALSE)
    {
        $data = $this->input->post(NULL, TRUE);
        if (empty($data)) {
            return $this->api_output_error->raise(1);
        }
        $data['slug'] = $this->__generate_unique_slug($data['name']);
        $result_courses = $this->User_schools_courses->update($user_school_course_id, $data);

        if (!empty($this->User_schools_courses->validation_errors)) {
            return $this->api_output_error->raise(1402, $this->User_schools_courses->validation_errors);
        }

        $existed_days = $this->Courses_days->get_all(array('course_id' => $data['course_id']));
//        log_message('error', '---EXISTED DAYS: '.print_r($existed_days,TRUE));
        if (isset($data['days'])) {
            foreach ($data['days'] as $day) {
                //validate day start time < day end time
                $start_time_date = DateTime::createFromFormat('H:i:s', $day['start_time']);
                $end_time_date = DateTime::createFromFormat('H:i:s', $day['end_time']);
                $isTimeValid = $start_time_date < $end_time_date;
                if (!$isTimeValid) {
                    return $this->api_output_error->raise(1402, "Start time should be less than end time");
                }
                if (isset($day['course_day_id'])) {
                    $result_courses_days = $this->Courses_days->update($day['course_day_id'], $day);
                    for ($i = 0; $i < count($existed_days); $i++) {
                        if ($existed_days[$i]->course_day_id == $day['course_day_id']) {
//                            log_message('error', "---REMOVE DAY FROM EXISTED DAYS: ".print_r($existed_days[$i],TRUE));
                            array_splice($existed_days, $i, 1);
                            break;
                        }
                    }
                } else {
                    $day['course_id'] = $data['course_id'];
                    $this->Courses_days->insert($day);
                    if (!empty($this->Courses_days->validation_errors)) {
                        log_message('error', "INSERT COURSE DAY ERROR. DAY: " . print_r($day, TRUE) . " ERRORS: " . $this->User_schools_courses->validation_errors);
                    }
                }
            }
        }

//        log_message('error', "---EXISTED DAYS FOR REMOVING: ".print_r($existed_days,TRUE));
        foreach($existed_days as $course_day){
//            log_message('error', "---REMOVE DAY FROM DB: ".print_r($course_day,TRUE));
            $result = $this->Courses_days->delete($course_day->course_day_id);
//            log_message('error', "---REMOVE DAY FROM DB RESULT: ".print_r($result,TRUE));
        }

        if ($result_courses == '') {
            return $this->api_output->send(FALSE);
        }
        if ($result_courses_days == '') {
            return $this->api_output->send(FALSE);
        }

        return $this->api_output->send(TRUE);
    }

    public function read($user_school_course_id)
    {
        $course = $this->User_schools_courses->get_course($user_school_course_id);
        if (empty($course)) {
            return $this->api_output_error->raise(2);
        } else {
            $course->total = $this->User_schools_courses->get_total_students_in_class($course->course_id);
        }
        return $this->api_output->send($course);
    }

    public function users($course_id, $page = 1)
    {
        if (!is_numeric($course_id) or !is_numeric($page)) {
            return $this->api_output_error->raise(5);
        }
        $users = $this->User_schools_courses->get_students_in_class($course_id, $page);
        if (is_array($users)) {
            $users = $this->load->controller('users/users/_populate_students_data', array('users' => $users));
        } else {
            return $this->api_output_error->raise(2);
        }
        return $this->api_output->send($users);
    }

    public function read_all()
    {
        $user_id = $this->user['user_id'];
        if (!is_numeric($user_id)) {
            return $this->api_output_error->raise(5);
        }
        $all_courses = array();
        $user_schools = $this->User_schools->get_many(array('user_id' => $user_id));
        foreach ($user_schools as $user_school) {
            $user_school_courses = $this->User_schools_courses->get_many(array('user_school_id' => $user_school->user_school_id));
            $all_courses = array_merge($all_courses, $user_school_courses);
        }
        return $this->api_output->send($all_courses);
    }

    public function read_all_by_university($university_id, $searchStr)
    {
        $user_id = $this->user['user_id'];
        if (!is_numeric($user_id)) {
            return $this->api_output_error->raise(5);
        }

        $courses = $this->Courses->get_by_university_id_and_search_str($university_id, $searchStr);
        return $this->api_output->send($courses);
    }

    public function delete($user_school_course_id)
    {
        if (!is_numeric($user_school_course_id)) {
            return $this->api_output_error->raise(5);
        }
        $result = $this->User_schools_courses->delete($user_school_course_id);
        if (empty($result)) {
            return $this->api_output->send(FALSE);
        }
        return $this->api_output->send(TRUE);
    }

    private function __generate_unique_slug($name)
    {
        $slug = $this->__generate_slug($name);
        $courses_with_similar_slug = $this->Courses->select('slug')->get_all(array('slug LIKE' => "%{$slug}%"));
//        log_message('error','---SELECT LIKE: '.$this->Courses->db->last_query());
        if (count($courses_with_similar_slug) > 0) {
//            log_message('error','---SIMILAR: '.print_r($courses_with_similar_slug,TRUE));
            return $this->__get_unique_slug($slug, $courses_with_similar_slug);
        }
//        log_message('error','--- NO SIMILAR :)');
        return $slug;
    }

    private function __generate_slug($course_name)
    {
        $slug = strtolower($course_name);
        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace(':', '-', $slug);
        $slug = str_replace('/', '-', $slug);
        $slug = str_replace('---', '-', $slug);
//        log_message('error','--- GENERATED SLUG: '.$slug);
        return $slug;
    }

    private function __get_unique_slug($slug, $courses)
    {
        foreach ($courses as $course) {
            if ($slug == $course->slug) {
                $slug = $slug . '-' . strtolower(random_string('alnum', 4));
                return $this->__get_unique_slug($slug, $courses);
            }
        }
//        log_message('error','--- GENERATED UNIQUE SLUG: '.$slug);
        return $slug;
    }
}