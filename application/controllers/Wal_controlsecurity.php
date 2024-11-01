<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_controlsecurity extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        $this->load->view('wal_controlsecurity/index', $this->data);
    }

	// Called from ajax
	// json for datatables
	public function datatable()
	{
        //$this->enable_error_reporting();
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

        // configuration
        $columns = array('idcontrol', 'title', 'date', 'description');
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

        $recordsTotal = $this->{$controller.'_m'}->total_lang(array(), NULL);
        
        wal_prepare_search_query_GET($columns, $controller.'_m');
        $recordsFiltered = $this->{$controller.'_m'}->total_lang(array(), NULL);

        wal_prepare_search_query_GET($columns, $controller.'_m');
        $data = $this->{$controller.'_m'}->get_pagination_lang($length, $start, array());

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
            if($row->is_sms_enabled == 1)
            {
                $row->icons.= '<i class="glyphicon glyphicon-phone"></i>';
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

    public function wal_unblock_download($echo = true)
	{   
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }

        if($echo === TRUE)
            ob_clean();

        $print_data = get_home_url().'?wal_unblock='.md5(AUTH_KEY.'wal');
        
        header('Content-Type: txt');
        header("Content-Length:".strlen($print_data));
        header("Content-Disposition: attachment; filename=txt_unblock_".date('Y-m-d-H-i-s').".txt");

        echo $print_data;
        
        exit();
    }

    public function bulk_remove($id = NULL, $redirect='1')
	{   
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }

        $this->load->model('control_m');

        // Get parameters
        $log_ids = $this->input->post('log_ids');

        $json = array(
            "log_ids" => $log_ids,
            );

        foreach($log_ids as $log_id)
        {
            if(is_numeric($log_id))
                $this->control_m->delete($log_id);
        }

        echo json_encode($json);
        
        exit();
    }

    public function control_log()
    {
        $this->data['log_data'] = NULL;

        $this->load->model('log_m');
        $this->load->model('control_m');

        $log_id = $this->input->post_get('log_id');
        $control_id = $this->input->post_get('id');

        if(!empty($log_id))
            $this->data['log_data'] = $this->log_m->get($log_id, TRUE);

        // Prepare db data
        $this->data['db_data'] = array();
        if(!empty($control_id))
        {
            $control_data = $this->control_m->get($control_id, TRUE);
            $control_data_rules = $this->control_m->get_rules($control_id);

            $this->data['db_data'] = array(
                'title' => $control_data->title,
                'description' => $control_data->description,
                'is_skip' => $control_data->is_skip,
                'is_block_enabled' => $control_data->is_block_enabled,
                'email' => $control_data->email,
                'is_email_enabled' => $control_data->is_email_enabled,
                'phone' => $control_data->phone,
                'is_sms_enabled' => $control_data->is_sms_enabled
            );

            foreach($control_data_rules as $key=>$row)
            {
                $i_fieldnum = $key+1;

                $this->data['db_data']['control_type_'.$i_fieldnum] = $row->type;
                $this->data['db_data']['control_operator_'.$i_fieldnum] = $row->operator;
                $this->data['db_data']['control_parameter_'.$i_fieldnum] = $row->parameter;
                $this->data['db_data']['control_value_'.$i_fieldnum] = $row->value;
            }
        }

        $this->data['form'] = &$this->form;

        $rules = array(
            array(
                'field' => 'title',
                'label' => __('Title', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'description',
                'label' => __('Description', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'email',
                'label' => __('Email address', 'winter-activity-log'),
                'rules' => 'valid_email'
            )
        );

        if($this->form->run($rules))
        {
            // Save procedure for basic data
 
            $data = $this->control_m->prepare_data($this->input->post(), 
                                                    array('title', 
                                                        'description', 'is_skip', 
                                                        'is_block_enabled', 'email', 
                                                        'is_email_enabled','phone', 
                                                        'is_sms_enabled'));

            $id = $this->control_m->insert($data, $control_id);

            
            // Save procedure for rules data

            $this->control_m->save_rules($id, $this->input->post());
            
            
         //   echo $this->db->last_query();
          //  exit();
            // redirect
            wp_redirect(admin_url("admin.php?page=wal_controlsecurity&function=control_log&id=$id&is_updated=true")); exit;
        }

        // Load view
        $this->load->view('wal_controlsecurity/control_log', $this->data);
    }
    
}
