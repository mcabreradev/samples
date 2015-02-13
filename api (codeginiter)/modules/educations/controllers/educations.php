<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Educations extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_schools_model', 'Schools');
        $this->load->model('User_schools_majors_model', 'User_schools_majors');
        $this->load->model('User_schools_minors_model', 'User_schools_minors');
    }

    public function create()
    {
        $data = $this->input->post(NULL, TRUE);
        if (empty($data)) {
            return $this->api_output_error->raise(1);
        }

        $data['university_id'] = $data['university']['university_id'];
        unset($data['university']);
        $data['city_id'] = 1;
        $data['user_id'] = $this->user['user_id'];

        $user_school_id = $this->Schools->insert($data);

        if (isset($data['majors'])) {
            if (!is_array($data['majors'])) {
                $data['majors'] = json_decode($data['majors'], true);
            }
            foreach ($data['majors'] as $major) {
                if (!is_array($major)) $major = json_decode($major, true);
                if ($major['name'] && strlen($major['name'])) {
                    $major['user_school_id'] = $user_school_id;
                    $this->User_schools_majors->insert($major);
                }
            }
            if (!empty($this->User_schools_majors->validation_errors)) {
                return $this->api_output_error->raise(1201, $this->User_schools_majors->validation_errors);
            }
        }

        if (isset($data['minors'])) {
            if (!is_array($data['minors'])) {
                $data['minors'] = json_decode($data['minors'], true);
            }
            foreach ($data['minors'] as $minor) {
                if (!is_array($minor)) $minor = json_decode($minor, true);
                if ($minor['name'] && strlen($minor['name'])) {
                    $minor['user_school_id'] = $user_school_id;
                    $this->User_schools_minors->insert($minor);
                }
            }
            if (!empty($this->User_schools_minors->validation_errors)) {
                return $this->api_output_error->raise(1301, $this->User_schools_minors->validation_errors);
            }
        }

        if (!empty($this->Schools->validation_errors)) {
            return $this->api_output_error->raise(1101, $this->Schools->validation_errors);
        }

        return $this->api_output->send($user_school_id);
    }

    public function _create_default_education($data)
    {
        if (empty($data)) {
            return false;
        }
        $user_school_id = $this->Schools->insert($data);

        if (!empty($this->Schools->validation_errors)) {
            log_message('error', 'controllers/educations.php->_create_default_education: Failed create school. School data: ' . print_r($data, TRUE) . ' Model validation errors: ' . print_r($this->Schools->validation_errors, TRUE));
        }

        return $user_school_id;
    }

    public function update($user_school_id, $data = FALSE)
    {
        $data = $this->input->post(NULL, TRUE);
        if (empty($data)) {
            return $this->api_output_error->raise(1);
        }

        $data['university_id'] = $data['university']['university_id'];
        unset($data['university']);
        $result = $this->Schools->update($user_school_id, $data);

        if (isset($data['majors'])) {
            foreach ($data['majors'] as $major) {
                $major['user_school_id'] = $user_school_id;

                if ($major['name'] && strlen($major['name'])) {
                    if (isset($major['user_school_major_id'])) {
                        $this->User_schools_majors->update($major['user_school_major_id'], $major);
                    } else {
                        $this->User_schools_majors->insert($major);
                    }
                }
            }
        }

        if (isset($data['minors'])) {
            foreach ($data['minors'] as $minor) {
                $minor['user_school_id'] = $user_school_id;

                if ($minor['name'] && strlen($minor['name'])) {
                    if (isset($minor['user_school_minor_id'])) {
                        $this->User_schools_minors->update($minor['user_school_minor_id'], $minor);
                    } else {
                        $this->User_schools_minors->insert($minor);
                    }
                }
            }
        }

        if (!empty($this->Schools->validation_errors)) {
            return $this->api_output_error->raise(1102, $this->Schools->validation_errors);
        }
        if (!empty($this->User_schools_majors->validation_errors)) {
            return $this->api_output_error->raise(1202, $this->User_schools_majors->validation_errors);
        }
        if (!empty($this->User_schools_minors->validation_errors)) {
            return $this->api_output_error->raise(1302, $this->User_schools_minors->validation_errors);
        }

        if (empty($result)) {
            return $this->api_output->send(FALSE);
        }

        return $this->api_output->send(TRUE);
    }

    public function read($user_school_id)
    {
        if (!is_numeric($user_school_id)) {
            return $this->api_output_error->raise(5);
        }
        $data = $this->Schools->get_schools($user_school_id);
        if (empty($data)) {
            return $this->api_output_error->raise(2);
        }
        return $this->api_output->send($data);
    }

    public function current($user_id = false)
    {
        if (!is_numeric($user_id)) {
            $user_id = $this->user['user_id'];
        }
        $data = $this->Schools->get_with_year_name(array('user_id' => $user_id));
        if (empty($data)) {
            return $this->api_output_error->raise(2);
        }
        return $this->api_output->send($data);
    }

    public function read_all($user_id = false)
    {
        $user_id or $user_id = $this->user['user_id'];
        if (!is_numeric($user_id)) {
            return $this->api_output_error->raise(5);
        }

        $data = $this->Schools->get_schools_all($user_id);

        if (empty($data)) {
            $this->Schools->create_temp($user_id);
            $data = $this->Schools->get_schools_all($user_id);
        }

        return $this->api_output->send($data);
    }

    public function delete($user_school_id)
    {
        if (!is_numeric($user_school_id)) {
            return $this->api_output_error->raise(5);
        }
        $result = $this->Schools->delete($user_school_id);
        if (empty($result)) {
            return $this->api_output->send(FALSE);
        }
        return $this->api_output->send(TRUE);
    }

    public function delete_major($user_school_major_id = false)
    {
        if (!$user_school_major_id) {
            return $this->api_output_error->raise(1);
        }
        $user_id = $this->user['user_id'];
        $major = $this->User_schools_majors->getWithUserId($user_school_major_id, $user_id);
        if (is_object($major)) {
            $this->User_schools_majors->delete($user_school_major_id);
        } else {
            return $this->api_output_error->raise(5);
        }
        return $this->api_output->send(TRUE);
    }

    public function delete_minor($user_school_minor_id = false)
    {
        if (!$user_school_minor_id) {
            return $this->api_output_error->raise(1);
        }
        $user_id = $this->user['user_id'];
        $minor = $this->User_schools_minors->getWithUserId($user_school_minor_id, $user_id);
        if (is_object($minor)) {
            $this->User_schools_minors->delete($user_school_minor_id);
        } else {
            return $this->api_output_error->raise(5);
        }
        return $this->api_output->send(TRUE);
    }

    public function campuses()
    {
        $user_edu = $this->Schools->get(array('user_id' => $this->user['user_id']));
        if (empty($user_edu)) {
            return $this->api_output_error->raise(2);
        }

        $campuses = $this->Schools->get_campuses();

        return $this->api_output->send(array(
            'current' => $user_edu->university->name,
            'campuses' => $campuses
        ));
    }

    public function majors($user_school_id = false)
    {
        if ($user_school_id) {
            if (!is_numeric($user_school_id)) {
                return $this->api_output_error->raise(5);
            } else {
                $data = $this->User_schools_majors->get_many(array('user_school_id' => $user_school_id));
            }
        } else {
            $data = $this->User_schools_majors->get_all();
        }
        if (empty($data)) {
            return $this->api_output_error->raise(2);
        }
        return $this->api_output->send($data);
    }

    public function minors($user_school_id = false)
    {
        if ($user_school_id) {
            if (!is_numeric($user_school_id)) {
                return $this->api_output_error->raise(5);
            } else {
                $data = $this->User_schools_minors->get_many(array('user_school_id' => $user_school_id));
            }
        } else {
            $data = $this->User_schools_minors->get_all();
        }
        if (empty($data)) {
            return $this->api_output_error->raise(2);
        }
        return $this->api_output->send($data);
    }

}