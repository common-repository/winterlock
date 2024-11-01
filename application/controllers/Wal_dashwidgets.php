<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_dashwidgets extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        //$this->load->view('wal_controlsecurity/index', $this->data);
    }

    public function logs_list()
	{
        $this->load->model('log_m');

        $this->data['logs'] = $this->log_m->get_pagination_lang(10, 0, NULL);




        // Load view
        $this->load->view('wal_dashwidgets/logs_list', $this->data);
    }

}
