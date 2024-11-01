<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_usersessions extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        $this->load->view('wal_usersessions/index', $this->data);
    }

	// Called from ajax
    // json for datatables
    
    /*
        { data: "idarray" },
        { data: "user" },
        { data: "login"   },
        { data: "expiration"  },
        { data: "ip"},
        { data: "delete"    },
        { data: "checkbox"  }
    */
	public function datatable()
	{
        //$this->enable_error_reporting();
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

        // configuration
        $columns = array('idsessions', 'user', 'login', 'expiration', 'ip');
        $controller = 'sessions';

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
        
        $data = $this->{$controller.'_m'}->get_all_sessions($parameters);

        $recordsTotal = count($data);
        
        //wal_prepare_search_query_GET($columns, $controller.'_m');
        $recordsFiltered = count($data);

        //wal_prepare_search_query_GET($columns, $controller.'_m');
        //$data = $this->{$controller.'_m'}->get_pagination_lang($length, $start, array());

        $query = '';

        $gmt_offset = get_option('gmt_offset');

        // Add buttons
        foreach($data as $key=>$row)
        {
            foreach($columns as $val)
            {
            }

            $row['session_time'] = wmvc_seconds_to_hms(time()-$row['login']);

            $row['login'] = $row['login']+$gmt_offset*60*60;
            $row['expiration'] = $row['expiration']+$gmt_offset*60*60;

            $row['login'] = date(get_option('date_format').' '.get_option('time_format'), $row['login']);
            $row['expiration'] = date(get_option('date_format').' '.get_option('time_format'), $row['expiration']);

            $row['delete'] = wmvc_btn_delete(admin_url("admin.php?page=wal_usersessions&function=delete_user_sessions&id=".$row["user_id"]), FALSE, esc_html__('Remove Session, Will Logout user immediately','winter-activity-log')).' ';

            if(get_current_user_id() != $row["user_id"])
            {
                $row['delete'].= wmvc_btn_block(admin_url("admin.php?page=wal_usersessions&function=block_username&id=".$row["user_id"]), FALSE, esc_html__('Disable login for username and logout immediately','winter-activity-log')).' ';
            }

            $row['delete'].= wmvc_btn_view(admin_url("admin.php?page=winteractivitylog&filter_user=".$row["user_id"]), FALSE, esc_html__('Show other logged events','winter-activity-log'));

            $row['checkbox'] = '';

            //$row = (object) $row;

            $data[$key] = $row;
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

    public function delete_user_sessions()
    {
        if(!wal_access_allowed('winterlock_sessions'))
        {
            exit();
        }

        $id = $this->input->post_get('id');

        $sessions = WP_Session_Tokens::get_instance( $id );
        $sessions->destroy_all();

        exit();
    }

    public function block_username()
    {
        if(!wal_access_allowed('winterlock_sessions'))
        {
            exit();
        }

        global $wpdb;

        $id = $this->input->post_get('id');

        // block user by username
        $user = get_user_by( 'id', $id );

        $insert_array =  array(
            'date' => current_time( 'mysql' ),
            'title' => 'Block login by username',
            'description' => 'Blocked rules based on username from sessions manager',
            'is_block_enabled' => '1'
        );

        $wpdb->insert( 
            $wpdb->prefix.'wal_control', 
            $insert_array
        );

        $insert_array =  array(
            'control_id' => $wpdb->insert_id,
            'type' => 'POST',
            'operator' => 'CONTAINS',
            'parameter' => 'log',
            'value' => $user->user_login
        );

        $wpdb->insert( 
            $wpdb->prefix.'wal_control_rule', 
            $insert_array
        );

        $insert_array =  array(
            'date' => current_time( 'mysql' ),
            'title' => 'Block login by email',
            'description' => 'Blocked rules based on email from sessions manager',
            'is_block_enabled' => '1'
        );

        $wpdb->insert( 
            $wpdb->prefix.'wal_control', 
            $insert_array
        );

        $insert_array =  array(
            'control_id' => $wpdb->insert_id,
            'type' => 'POST',
            'operator' => 'CONTAINS',
            'parameter' => 'log',
            'value' => $user->user_email
        );

        $wpdb->insert( 
            $wpdb->prefix.'wal_control_rule', 
            $insert_array
        );

        // destroy user session
        $sessions = WP_Session_Tokens::get_instance( $id );
        $sessions->destroy_all();

        exit();
    }

    public function csv_export_user_sessions($echo = TRUE)
    {
        if(!wal_access_allowed('winterlock_sessions'))
        {
            exit();
        }

        if($echo === TRUE)
            ob_clean();

        $controller = 'sessions';
			
        $this->load->model($controller.'_m');
        
        $data = $this->{$controller.'_m'}->get_all_sessions(array());

        $print_data = '';

        $counter=0;

        $gmt_offset = get_option('gmt_offset');

        foreach($data as $key=>$row)
        {
            $row['session_time'] = wmvc_seconds_to_hms(time()-$row['login']);

            $row['login'] = $row['login']+$gmt_offset*60*60;
            $row['expiration'] = $row['expiration']+$gmt_offset*60*60;

            $row['login'] = date(get_option('date_format').' '.get_option('time_format'), $row['login']);
            $row['expiration'] = date(get_option('date_format').' '.get_option('time_format'), $row['expiration']);
            
            $row['user_id'] = (string) $row['user_id'];

            $data[$key] = $row;
        }

        $skip_cols = array();
        
        foreach($data as $key_log=>$row_log)
        {
            // print only keys if first row
            if($counter==0)
            {
                //Define CSV format for Excel
                $print_data.="sep=;\r\n";

                foreach($row_log as $key=>$val)
                {
                    if(!is_string($key) || in_array($key, $skip_cols))continue;

                    $print_data.='"'.$key.'";';    
                }
                $print_data.="\r\n";
            }

            foreach($row_log as $key=>$val)
            {
                if(!is_string($key) || in_array($key, $skip_cols))continue;

                if(is_string($val))
                {
                    $val_prepared = strip_tags(htmlspecialchars($val));
                    $val_prepared = '"'.$val_prepared.'"';

                    $print_data.=$val_prepared.';';
                }
                else
                {
                    $print_data.=';';
                }
            }
            $print_data.="\r\n";

            $counter++;
        }

        $print_data.= "\r\n";

        if($echo === FALSE)
            return $print_data;

        header('Content-Type: application/csv');
        header("Content-Length:".strlen($print_data));
        header("Content-Disposition: attachment; filename=csv_sessions_".date('Y-m-d-H-i-s', time()+$gmt_offset*60*60).".csv");

        echo $print_data;
        
        exit();

    }

    public function bulk_remove($id = NULL, $redirect='1')
	{   
        if(!wal_access_allowed('winterlock_sessions'))
        {
            exit();
        }

        // Get parameters
        $user_ids = $this->input->post('user_ids');

        $json = array(
            "user_ids" => $user_ids,
            );

        foreach($user_ids as $user_id)
        {
            $user_exp = explode('_', $user_id);
            $user_id = $user_exp[0];

            if(is_numeric($user_id))
            {
                $sessions = WP_Session_Tokens::get_instance( $user_id );
                $sessions->destroy_all();
            }
        }

        echo json_encode($json);
        
        exit();
    }

    public function control_log()
    {
        $this->load->model('log_m');
        $this->load->model('control_m');

        $log_id = $this->input->post_get('log_id');
        $control_id = $this->input->post_get('id');

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
                'is_email_enabled' => $control_data->is_email_enabled
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
                                                        'is_email_enabled'));

            $id = $this->control_m->insert($data, $control_id);
            
            // Save procedure for rules data

            $this->control_m->save_rules($id, $this->input->post());

            // redirect
            wp_redirect(admin_url("admin.php?page=wal_controlsecurity&function=control_log&id=$id&is_updated=true")); exit;
        }

        // Load view
        $this->load->view('wal_controlsecurity/control_log', $this->data);
    }
    
}
