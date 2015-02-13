<?php

class Dailydose_model extends MY_Model {

  protected $table = 'dailydose';
  protected $primary_key = 'channel_id';
  protected $fields = array('dailydose_id', 'title', 'content',  'cover_image', 'created_at', 'modified_at');

  public $before_create = array('before_create');
  public $before_update = array('before_update');

  public $per_page = 10;

  public function __construct() {
    parent::__construct();

    $this->validate = array();
    $this->validate_rules_by_controller_method = array(
      'create' => array(
          array('field' => 'title',             'label' => 'Title',             'rules' => 'trim|required'),
          array('field' => 'content',           'label' => 'Title',             'rules' => 'trim|required')
      )
    );

    if ( isset($this->validate_rules_by_controller_method[$this->router->method]) ) {
      $this->validate = $this->validate_rules_by_controller_method[$this->router->method];
    }
  }

  function before_create($dailydose) {
    $dailydose['created_at'] = date('Y-m-d H:i:s');
    $dailydose['modified_at'] = date('Y-m-d H:i:s');
    return $dailydose;
  }

}
