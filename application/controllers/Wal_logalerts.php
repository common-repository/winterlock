<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_logalerts extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        $this->load->view('wal_logalerts/index', $this->data);
    }

	// Called from ajax
	// json for datatables
	public function datatable()
	{
        //$this->enable_error_reporting();
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

        // configuration
        $columns = array('idcontrol', 'title', 'date', 'description', 'is_email_enabled');
        $controller = 'control';

        // fixed filtering
        //$_POST['columns'][9]['data'] = 'is_favourite';
        //$_POST['columns'][9]['searchable'] = 'true';
        //$_POST['columns'][9]['search']['value'] = 1;
        
        // Fetch parameters
        $parameters = $this->input->post();
        $draw = $this->input->post_get('draw');
        $start = $this->input->post_get('start');
        $length = $this->input->post_get('length');
		$search = $this->input->post_get('search');

        if(isset($search['value']))
            $parameters['searck_tag'] = $search['value'];
			
		$this->load->model($controller.'_m');

        $recordsTotal = $this->{$controller.'_m'}->total_lang(array('is_email_enabled'=>1), NULL);
        
        wal_prepare_search_query_GET($columns, $controller.'_m');
        $recordsFiltered = $this->{$controller.'_m'}->total_lang(array('is_email_enabled'=>1), NULL);

        wal_prepare_search_query_GET($columns, $controller.'_m');
        $data = $this->{$controller.'_m'}->get_pagination_lang($length, $start, array('is_email_enabled'=>1));

        $query = $this->db->last_query();

        // Add buttons
        foreach($data as $key=>$row)
        {
            $row = wmvc_xss_clean_object($row);

            foreach($columns as $val)
            {
                if(isset($row->$val))
                {
                    
                }
                elseif(isset($row->json_object))
                {
                    $json = json_decode($row->json_object);
                    if(isset($json->$val))
                    {
                        $row->$val = $json->$val;
                    }
                    else
                    {
                        $row->$val = '-';
                    }
                }
                else
                {
                    $row->$val = '-';
                }
            }


            $row->date = date(get_option('date_format').' '.get_option('time_format'), strtotime($row->date));

            $row->icons = '';
            if($row->is_email_enabled == 1)
            {
                $row->icons.= '<i class="glyphicon glyphicon-envelope"></i>';
            }
            if($row->is_block_enabled == 1)
            {
                $row->icons.= ' <i class="glyphicon glyphicon-lock"></i>';
            }
            if($row->is_skip == 1)
            {
                $row->icons.= ' <i class="glyphicon glyphicon-eye-close"></i>';
            }

            $row->description = wp_kses_post(character_hard_limiter($row->description, 50));

            $row->edit =wmvc_btn_open(admin_url("admin.php?page=wal_controlsecurity&function=control_log&id=".$row->{"id$controller"}), '');

            $row->checkbox = '';
        }

        //format array is optional
        $json = array(
                "parameters" => $parameters,
                "query" => $query,
                "draw" => $draw,
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data
                );

        //$length = strlen(json_encode($data));
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length);
        echo json_encode($json);
        
        exit();
    }
    
}
