<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Winteractivitylog extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        global $submenu, $menu;

        if(get_option( 'winter-activity-log-menuitems' ) === FALSE)
        {
            add_option( 'winter-activity-log-menuitems', $menu);
            add_option( 'winter-activity-log-submenuitems', $submenu);
        }
        else
        {
            update_option( 'winter-activity-log-menuitems', $menu);
            update_option( 'winter-activity-log-submenuitems', $submenu);
        }

        if ( !winteractivitylog()->is_plan_or_trial('lite') )
        {
            $prefix = 'wal_';

            update_option($prefix.'log_days', '7');
            update_option($prefix.'checkbox_failed_login_block', '0');
        }

        // Load view
        $this->load->view('winteractivitylog/index', $this->data);
    }
    
	public function edit_log()
	{
        $this->load->model('Log_m');

        $id = $this->input->post_get('id');
        $this->data['popup'] = $this->input->post_get('popup');

        $this->data['form_data'] = $this->log_m->get($id, TRUE);

        if($this->data['popup'] == 'ajax')
        {
            ob_clean();
            ob_start();
        }

        // Load view
        $this->load->view('winteractivitylog/edit_log', $this->data);
    }
    
	public function save_log()
	{
        $this->load->model('Log_m');

        $id = $this->input->post_get('id');

        $this->log_m->update(array('is_favourite'=>1), $id);

        exit();
    }
    
	public function save_log_rem()
	{
        $this->load->model('Log_m');

        $id = $this->input->post_get('id');

        $this->log_m->update(array('is_favourite'=>0), $id);

        exit();
	}

	// Called from ajax
	// json for datatables
	public function datatable()
	{
        //$this->enable_error_reporting();
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

        // configuration
        $columns = array('idlog', 'level', 'date', 'avatar', 'user_info', 'ip', 'description', 'page', 'action');
        $controller = 'log';
        
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
        $data = $this->{$controller.'_m'}->get_pagination_lang($length, $start, NULL);

        $query = $this->db->last_query();

        // Add buttons
        foreach($data as $key=>$row)
        {
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

            $request_data = unserialize($row->request_data);

            $row->level = wal_generate_label_by_level($row->level);

            $row->date = date(get_option('date_format').' '.get_option('time_format'), strtotime($row->date));

            $user_info = get_userdata($row->user_id);

            $row->avatar = '';

            $user_agent = NULL;
            $header_data = unserialize($row->header_data);
            if(!empty($header_data["User-Agent"]))
            {
                $user_agent = $header_data["User-Agent"];
            }
            
            if(empty($row->user_id))
            {
                $row->user_id = '-';
            }

            if(isset($user_info->ID))
            {
                $row->avatar = '<img class="avatar" src="'.esc_url( get_avatar_url( $user_info->ID ) ).'" />';
            }
            elseif(wal_visitor_type($row->page, $row->request_uri, $user_agent ) === 'system')
            {
                $row->avatar = '<span class="dashicons dashicons-wordpress wal-system-icon"></span>';
            }
            elseif(wal_visitor_type($row->page, $row->request_uri, $user_agent ) === 'guest')
            {
                $row->avatar  = '<span class="dashicons dashicons-before dashicons-admin-users wal-system-icon"></span>';
            }
            elseif(wal_visitor_type($row->page, $row->request_uri, $user_agent ) === 'unknown')
            {
                $row->avatar  = '-';
            }

            //if(isset($row->field_10))
            //    $row->field_10 = "<a href='".admin_url("admin.php?page=listing_addlisting&id=".$row->{"id$controller"})."'>".$row->field_10."</a>";
            
            //if(sw_settings('show_categories'))
            //{
            //    if($row->category_id != '-')
            //    $row->category_id = $row->category_id.', '.$this->treefield_m->get_value($row->category_id);
			//}
            
            $resolved_ip = resolve_ip($row->ip);

            if(!empty($resolved_ip))
                $row->ip=$resolved_ip;

            $options = '';
            $options.=wmvc_btn_block(admin_url("admin.php?page=wal_controlsecurity&function=control_log&subfunction=block&log_id=".intval($row->{"id$controller"}))).' ';
            $options.=wmvc_btn_hide(admin_url("admin.php?page=wal_controlsecurity&function=control_log&subfunction=hide&log_id=".intval($row->{"id$controller"}))).' ';

            if($row->is_favourite == 1)
            {
                $options.=wmvc_btn_save(admin_url("admin.php?page=winteractivitylog&function=save_log_rem&id=".intval($row->{"id$controller"})), '').' ';
            }
            else
            {
                $options.=wmvc_btn_save(admin_url("admin.php?page=winteractivitylog&function=save_log&id=".intval($row->{"id$controller"}))).' ';
            }
            


            $options.=wmvc_btn_open_ajax(admin_url("admin.php?page=winteractivitylog&function=edit_log&id=".intval($row->{"id$controller"})));

            $row->edit = $options;
            //$row->delete =wmvc_btn_delete_noconfirm(admin_url("admin.php?page=listing_manage&function=remlisting&id=".$row->{"id$controller"}));
            
            $resolved_menu = wal_resolve_wp_menu($row->page, $row->request_uri);

            if(!empty($resolved_menu))
                $row->page="$resolved_menu";

            //if($row->is_activated==0)
            //    $row->{"id$controller"} .= ' <span class="label label-danger">'.__("Not activated", "sw_win").'</span>';

            //if($row->is_activated == 1 && sw_settings('expire_days') > 0)
            //{
            //    if(strtotime($row->date_modified) < time()-sw_settings('expire_days')*86400)
            //        $row->{"id$controller"} .= ' <span class="label label-danger">'.__("Expired", "sw_win").'</span>';
            //}

            //$row->image_filename = '<img src="'.esc_url(_show_img($row->image_filename, '50x50', false)).'" style="height:50px; width:50px" />';
            $row->checkbox = '';

            $row->ip = wp_kses_post($row->ip);
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
    
    public function filter_save ($id = NULL, $redirect='1') {
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }

        $ajax_output = array();
        $ajax_output['message'] = '';
        $ajax_output['success'] = false;
        
        $name_val = 'winterlock_save_search_filter_Userid'.get_current_user_id();
        $options = get_option( $name_val );

        $filter_name = '';
        $filter_par = '';

        if(!empty($_POST['filter_name']))
            $filter_name = sanitize_text_field($_POST['filter_name']);

        if(!empty($_POST['filter_param']))
            $filter_par = sanitize_text_field($_POST['filter_param']);
        
        $json_string = $filter_par;

        $json_string = stripslashes($json_string); 
        $filter_par = json_decode($json_string);
        
        $options[] = [
            'name'=> $filter_name,
            'filter_par'=> serialize($filter_par)
        ];
        
        update_option($name_val, $options);
        
        $ajax_output['success'] = true;
        $json_output = json_encode($ajax_output);
        //$length = mb_strlen($json_output);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length); // special characters causing troubles

        echo $json_output;
        exit();
    }
    
    
    public function filter_get ($id = NULL, $redirect='1') {
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }

        $ajax_output = array();
        $ajax_output['message'] = '';
        $ajax_output['success'] = false;
        
        $name_val = 'winterlock_save_search_filter_Userid'.get_current_user_id();
        $options = get_option( $name_val );
        
        $results = [];
        if(!empty($options))
        foreach ($options as $key => $filter) {
            $results[] = [
                'filterid'=> $key,
                'name'=> $filter['name'],
                'filter_par'=> json_encode(unserialize($filter['filter_par']))
            ];
            
        }
        
        $ajax_output['results'] = $results;
        $ajax_output['success'] = true;
        $json_output = json_encode($ajax_output);
        //$length = mb_strlen($json_output);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length); // special characters causing troubles

        echo $json_output;
        exit();
    }
    
    public function filter_remove ($id = NULL, $redirect='1') {
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }
        
        $ajax_output = array();
        $ajax_output['message'] = '';
        $ajax_output['success'] = false;
        
        $parameters = array();
        $parameters['filter_id'] = NULL;

        if(isset($_POST['filter_id']))
            $parameters['filter_id'] = sanitize_key($_POST['filter_id']);

        $name_val = 'winterlock_save_search_filter_Userid'.get_current_user_id();
        $options = get_option( $name_val );
        

        if(!empty($options) && isset($options[$parameters['filter_id']])) {
            unset($options[$parameters['filter_id']]);
            update_option($name_val, $options);
        }
        
        //$ajax_output['results'] = $results;
        $ajax_output['success'] = true;
        $json_output = json_encode($ajax_output);
        //$length = mb_strlen($json_output);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length); // special characters causing troubles

        echo $json_output;
        exit();
    }
    
    
    public function bulk_remove($id = NULL, $redirect='1')
	{   
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }

        $this->load->model('log_m');

        // Get parameters
        $log_ids = $this->input->post('log_ids');

        $json = array(
            "log_ids" => $log_ids,
            );

        foreach($log_ids as $log_id)
        {
            if(is_numeric($log_id))
                $this->log_m->delete($log_id);
        }

        echo json_encode($json);
        
        exit();
    }
    
    public function remove($id = NULL, $redirect='')
	{   
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }

        $this->load->model('log_m');
        // Get parameters
        $id = $this->input->post_get('log_id');
        
        if(is_numeric($id))
            $this->log_m->delete($id);
        
        if(empty($redirect)):
            wp_redirect(admin_url("admin.php?page=winteractivitylog&is_updated=true")); exit;
        else:
            wp_redirect($redirect); exit;
        endif;
        
        exit();
    }
    
    public function clear_all_log ($id = NULL, $redirect='1') {
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }
        
        $ajax_output = array();
        $ajax_output['message'] = '';
        $ajax_output['success'] = false;
        
        $this->load->model('log_m');
        /* clear TABLE */
        global $wpdb;
        $wpdb->query("DELETE FROM `".$this->log_m->_table_name."`");
        $wpdb->query("ALTER TABLE `".$this->log_m->_table_name."` AUTO_INCREMENT = 1");
        $ajax_output['success'] = true;
        
        $json_output = json_encode($ajax_output);
        //$length = mb_strlen($json_output);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache');
        header('Content-Type: application/json; charset=utf8');
        //header('Content-Length: '.$length); // special characters causing troubles

        echo $json_output;
        exit();
    }
    
}
