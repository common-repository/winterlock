<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_favouritelogs extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        $this->load->view('wal_favouritelogs/index', $this->data);
    }

	// Called from ajax
	// json for datatables
	public function datatable_saved()
	{
        //$this->enable_error_reporting();
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

        // configuration
        $columns = array('idlog', 'level', 'date', 'avatar', 'user_info', 'ip', 'description', 'page', 'action', 'is_favourite');
        $controller = 'log';

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

        $recordsTotal = $this->{$controller.'_m'}->total_lang(array('is_favourite'=>1), NULL);
        
        wal_prepare_search_query_GET($columns, $controller.'_m');
        $recordsFiltered = $this->{$controller.'_m'}->total_lang(array('is_favourite'=>1), NULL);

        wal_prepare_search_query_GET($columns, $controller.'_m');
        $data = $this->{$controller.'_m'}->get_pagination_lang($length, $start, array('is_favourite'=>1));

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
            $options.=wmvc_btn_block(admin_url("admin.php?page=winteractivitylog&function=block_log&id=".$row->{"id$controller"})).' ';
            $options.=wmvc_btn_hide(admin_url("admin.php?page=winteractivitylog&function=hide_log&id=".$row->{"id$controller"})).' ';

            if($row->is_favourite == 1)
            {
                $options.=wmvc_btn_save(admin_url("admin.php?page=winteractivitylog&function=save_log_rem&id=".$row->{"id$controller"}), '').' ';
            }
            else
            {
                $options.=wmvc_btn_save(admin_url("admin.php?page=winteractivitylog&function=save_log&id=".$row->{"id$controller"})).' ';
            }
            


            $options.=wmvc_btn_open(admin_url("admin.php?page=winteractivitylog&function=edit_log&id=".$row->{"id$controller"}));

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
    
}
