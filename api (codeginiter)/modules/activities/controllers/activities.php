<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activities extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Activities_model', 'Activities');
		$this->load->model('Activity_share_model', 'ActivitiesShare');
		$this->load->model('Activity_Image_Model', 'ActivitiesImage');
    $this->load->model('users/users_model', 'Users');
		$this->load->model('users/group_users_model', 'GroupUsers');
		$this->load->model('users/auth_tokens_model', 'Auth');
	}

	public function share() {
		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}

		$id = $this->ActivitiesShare->insert($data);
		if(!empty($this->ActivitiesShare->validation_errors)){
      return $this->api_output_error->raise(1201, $this->ActivitiesShare->validation_errors);
    }

		return $this->api_output->send($id);
	}

	public function create() {
		$data = $this->input->post(NULL,TRUE);
		if (empty($data)) {
			return $this->api_output_error->raise(1);
		}
		$data['user_id'] = $this->user['user_id'];
		$activity_id = $this->Activities->insert($data);
		if(!empty($this->Activities->validation_errors)){
      return $this->api_output_error->raise(1201, $this->Activities->validation_errors);
    }

		return $this->api_output->send($activity_id);
	}

	public function delete($activity_id) {
		// Validate user_id
		$user_id = $this->user['user_id'];
		if (empty($user_id)) {
				return $this->api_output_error->raise(401);
		}
		$activity = $this->Activities->get($activity_id);

    if (is_object($activity) && $user_id == $activity->user_id) {
      $result = $this->Activities->delete($activity_id);
      $this->ActivitiesShare->delete(array('original_activity_id' => $activity_id));
    } else {
      $result = false;
    }

		return $this->api_output->send($result);
	}

	public function read($activity_id) {
		// Validate user_id
		$user_id = $this->user['user_id'];
		if (empty($user_id)) {
				return $this->api_output_error->raise(401);
		}
		$activity = $this->Activities->get($activity_id);

    if (is_object($activity) && $activity->activities_image_id) {
      $activity->images = $this->ActivitiesImage->get_by_activity($activity->activity_id);
      $activity->cover  = $activity->images ? $this->config->item('router.site.url').'image/activity--thumb--'.reset($activity->images)->filename : false;
    }

		return $this->api_output->send($activity);
	}

	public function read_all($type = 'all', $page = 1) {

    if ($this->input->post('search')) {
      $search = $this->input->post('search');
    } else {
      $search = false;
    }

    if ($type == 'null') { $type = 'all'; }
		// Validate user_id
		$user_id = $this->user['user_id'];
		if (empty($user_id)) {
			return $this->api_output_error->raise(401);
		}

    $data = $this->Activities->get_by_type($type, $search, $this->get_allowed_users_ids('activity.view'), $page);

    if ( ! is_array($data) ) {
			return $this->api_output_error->raise(2);
		}

    foreach ($data as &$activity) {
      $activity->images = $this->ActivitiesImage->get_by_activity($activity->activity_id);
      $activity->cover  = $activity->images ? $this->config->item('router.site.url').'image/activity--thumb--'.reset($activity->images)->filename : false;
			$activity->image  = $activity->images ? $this->config->item('router.site.url').'image/activity--'.reset($activity->images)->filename : false;
    }

		return $this->api_output->send($data);
	}

	public function read_by_user($read_by_user_id, $page = 1) {
		// Validate user_id
		$user_id = $this->user['user_id'];
		if (empty($user_id)) {
			return $this->api_output_error->raise(401);
		}

    $data = $this->Activities->get_by_user($read_by_user_id, $this->get_allowed_users_ids('activity.view'), $page);

    if ( ! is_array($data) ) {
			return $this->api_output_error->raise(2);
		}

    foreach ($data as &$activity) {
      $activity->images = $this->ActivitiesImage->get_by_activity($activity->activity_id);
      $activity->cover  = $activity->images ? $this->config->item('router.site.url').'image/activity--thumb--'.reset($activity->images)->filename : false;
			$activity->image  = $activity->images ? $this->config->item('router.site.url').'image/activity--'.reset($activity->images)->filename : false;
    }

		return $this->api_output->send($data);
	}

	public function read_for_radar($type = 'all', $page = 1) {
    $campus   = $this->input->post('campus');
    $interval = $this->input->post('interval');

		// Validate user_id
		$user_id = $this->user['user_id'];
		if (empty($user_id)) {
			return $this->api_output_error->raise(401);
		}

    $data = $this->Activities->get_for_radar($type, $campus, $interval, $this->get_allowed_users_ids('radar.view'), $page);

    if ( ! is_array($data) ) {
			return $this->api_output_error->raise(2);
		}

    foreach ($data as &$activity) {
      $activity->images = $this->ActivitiesImage->get_by_activity($activity->activity_id);
      $activity->cover  = $activity->images ? $this->config->item('router.site.url').'image/activity--thumb--'.reset($activity->images)->filename : false;
      $marker = $this->GroupUsers->belongs_to_groups($activity->user_id, $activity->user_id);
      $activity->marker = !empty($marker) ? $marker[0]->color : 'blue';
    }

		return $this->api_output->send($data);
	}

  public function get_shared_count($activity_id = false) {
    $count = count($this->ActivitiesShare->get_many(array('original_activity_id' => $activity_id)));
    return $this->api_output->send(array('count' => $count));
  }

  private function get_allowed_users_ids($resource_name) {
    $result = array();
    $this->load->model('privacy/user_privacy_states_model', 'UserPrivacyStates');
		$this->load->model('privacy/privacy_resources_model', 'PrivacyResources');
		$this->load->model('privacy/group_users_model', 'GroupUsers');

    $resource = $this->PrivacyResources->get(array('name' => $resource_name));
    if (empty($resource)) { return false; }
    $user_states = $this->UserPrivacyStates->getResourceStates($resource->privacy_resource_id, $this->user['user_id']);
    if (empty($user_states)) { return false; }

    foreach($user_states as $state) {
      /*
       * privacy_state_id 1 in db is All Students
       * privacy_state_id 2 in db is My Connections
       * privacy_state_id 3 in db is Only Me
       */
      if ($state->privacy_state_id == 1 && $state->is_active) {
        $result = false;
      } else if ($state->privacy_state_id == 2 && $state->is_active) {
        //@TODO not implemented getting my connections, so now it working like All Students
        $result = false;
      } else if ($state->privacy_state_id == 3 && $state->is_active) {
        $result = array($this->user['user_id']);
      } else if ($state->is_group && $state->is_active) {
        if (!is_array($result)) $result = array();

        $GroupUsers = $this->GroupUsers->get_many(array('group_id' => $state->is_group));

        foreach($GroupUsers as $item) {
          $result[] = $item->user_id;
        }
      }
    }

    return $result;
  }

}