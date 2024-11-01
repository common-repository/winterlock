<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_related extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{

        $this->data['plugins_list'] = array();
        
        $this->data['plugins_list']['activitytime'] = array(
            'name'=> esc_html__('WP Sessions Time Monitoring Full Automatic','activitytime'),
            'tags'=>esc_html__('accurate, monitoring, session, time, tracking','activitytime'),
            'description'=>esc_html__('Plugin will track accurate activity time on specific page, very useful for cases like content reading time, stream or video watching time, tracking time in LMS online learning system, working time for writing or editing elementor templates, pages editing time, post editing time and similar. Build as extension of WinterLock functionality for Accurate Sessions Time Tracking Features: User time spen...','activitytime'),
        );

        // Load view
        $this->load->view('wal_related/index', $this->data);
    }
    
}
