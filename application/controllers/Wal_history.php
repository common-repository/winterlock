<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_history extends Winter_MVC_Controller {

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

        // Load view
        $this->load->view('wal_history/index', $this->data);
    }
    
	public function edit_history()
	{
        $this->load->model('History_m');

        $id = $this->input->post_get('id');
        $this->data['popup'] = $this->input->post_get('popup');

        $this->data['form_data'] = $this->history_m->get($id, TRUE);

        if($this->data['popup'] == 'ajax')
        {
            ob_clean();
            ob_start();
        }

        // Load view
        $this->load->view('wal_history/edit_history', $this->data);
    }
    
	public function save_history()
	{
        $this->load->model('History_m');

        $id = $this->input->post_get('id');

        $this->history_m->update(array('is_favourite'=>1), $id);

        exit();
    }
    
	public function save_history_rem()
	{
        $this->load->model('History_m');

        $id = $this->input->post_get('id');

        $this->history_m->update(array('is_favourite'=>0), $id);

        exit();
	}

	// Called from ajax
	// json for datatables
	public function datatable()
	{
        //$this->enable_error_reporting();
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

        // configuration
        $columns = array('idhistory', 'level', 'date', 'avatar', 'user_info', 'description', 'page', 'action');
        $controller = 'history';
        
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

            if(empty($row->user_info) || $row->user_info == '-') {
                $row->user_info  = '#'.$row->user_id.' <a target="_blank" href="'.admin_url('user-edit.php?user_id='.$row->user_id).'">'.wp_kses_post($user_info->user_nicename).'</a> <br /> '.implode(',', $user_info->roles);
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
                $row->ip=wp_kses_post($resolved_ip);
            
            $options = '';
            $options.=wmvc_btn_delete_noconfirm(admin_url("admin.php?page=wal_history&function=remove&history_id=".intval($row->{"id$controller"})));
            /*
            $options.=wmvc_btn_block(admin_url("admin.php?page=wal_controlsecurity&function=control_history&subfunction=block&history_id=".$row->{"id$controller"})).' ';

            if($row->is_favourite == 1)
            {
                $options.=wmvc_btn_save(admin_url("admin.php?page=wal_history&function=save_history_rem&id=".$row->{"id$controller"}), '').' ';
            }
            else
            {
                $options.=wmvc_btn_save(admin_url("admin.php?page=wal_history&function=save_history&id=".$row->{"id$controller"})).' ';
            }*/
            


            $options.=wmvc_btn_open_ajax(admin_url("admin.php?page=wal_history&function=edit_history&id=".$row->{"id$controller"}));

            $row->edit = $options;
            $row->delete =wmvc_btn_delete_noconfirm(admin_url("admin.php?page=listing_manage&function=remlisting&id=".$row->{"id$controller"}));
            
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
    
     
    public function bulk_remove($id = NULL, $redirect='1')
	{   
        if(!wal_access_allowed('winterlock_logs'))
        {
            exit();
        }

        $this->load->model('history_m');

        // Get parameters
        $history_ids = $this->input->post('history_ids');

        $json = array(
            "history_ids" => $history_ids,
            );

        foreach($history_ids as $history_id)
        {
            if(is_numeric($history_id))
                $this->history_m->delete($history_id);
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

        $this->load->model('history_m');
        // Get parameters
        $id = $this->input->post_get('history_id');
        
        if(is_numeric($id))
            $this->history_m->delete($id);
        
        if(empty($redirect)):
            wp_redirect(admin_url("admin.php?page=wal_history&is_updated=true")); exit;
        else:
            wp_redirect($redirect); exit;
        endif;
        
        exit();
    }
    
    public function export_csv(){
        $this->load->model('history_m');
        $histories = $this->history_m->get();
        
        $csv=array();
        /* special field */
        $csv_header['idhistory']='idhistory';
        $csv_header['level']='level';
        $csv_header['date']='date';
        $csv_header['user_id']='user_id';
        $csv_header['user_info']='user_info';
        $csv_header['ip']='ip';
        $csv_header['page']='page';
        $csv_header['request_uri']='request_uri';
        $csv_header['description']='description';
        /* end special field */
        
        $csv_t=array();
       // $csv_t[]=implode(';', $csv_header);
        $csv_t=array();
        foreach ($histories as $key => $value) {
            $csv_t[$key]['idhistory'] =  '"'.$value->idhistory.'"';
            $csv_t[$key]['level'] =  '"'.$value->level.'"';
            $csv_t[$key]['date'] =  '"'.$value->date.'"';
            $csv_t[$key]['user_id'] =  '"'.$value->user_id.'"';
            $csv_t[$key]['user_info'] =  '"'.strip_tags($value->user_info).'"';
            $csv_t[$key]['ip'] =  '"'.$value->ip.'"';
            $csv_t[$key]['page'] =  '"'.$value->page.'"';
            $csv_t[$key]['request_uri'] =  '"'.$value->request_uri.'"';
            $csv_t[$key]['description'] =  '"'.strip_tags($value->description).'"';
        }
        // create csv file, and skip not use feilds from bd
        $fieldId=1;
        foreach ($csv_t as $row) {
            $row_t=$csv_header;
            foreach ($csv_header as $key => $value) {
               if(isset($row[$key]))
                $row_t[$key]=$row[$key];
            }
            $csv[]= implode(';',  $row_t);
            $fieldId++;
        }
        array_unshift($csv, implode(';', $csv_header));
        $csv=implode(PHP_EOL, $csv);
        
        ob_clean();
        ob_start();
            
        $date = date('Y-m-d H:i:s');
        $filename = 'export_'.$date.'.csv';
        
        // Generate the server headers
        if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
        {
                header('Content-Type: "text/csv"');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header("Content-Transfer-Encoding: binary");
                header('Pragma: public');
                header("Content-Length: ".strlen($csv));
        }
        else
        { 
                header('Content-Type: "text/csv"');
                header('Content-Disposition: attachment; filename="'.$filename.'"');
                header("Content-Transfer-Encoding: binary");
                header('Expires: 0');
                header('Pragma: no-cache');
                header("Content-Length: ".strlen($csv));
        }

        exit($csv);
        //wp_redirect(admin_url('admin.php?page=wal_history'));
    }
    
    
    public function history_refresh(){
        $this->load->model('history_m');

        $from_date = date('Y-m-d H:i:s', strtotime("-7 day"));
        $to_date = date('Y-m-d H:i:s');
        
        if(!winteractivitylog()->is_free_plan()){
            if(isset($_POST['from_date']) && !empty($_POST['from_date']))
                $from_date = date('Y-m-d H:i:s', strtotime(sanitize_text_field($_POST['from_date'])));
            if(isset($_POST['to_date']) && !empty($_POST['to_date']))
                $to_date = date('Y-m-d H:i:s', strtotime(sanitize_text_field($_POST['to_date'])));
        }
        global $wpdb;
        /* clear TABLE */
        $wpdb->query("DELETE FROM `"/*.$wpdb->prefix*/.$this->history_m->_table_name."`");
        $wpdb->query("ALTER TABLE `"/*.$wpdb->prefix*/.$this->history_m->_table_name."` AUTO_INCREMENT = 1");
        
        /*
         * 1. User tample analyze
         */
        

        if(defined('CUSTOM_USER_TABLE'))
            $users_table = '`'.CUSTOM_USER_TABLE.'`';
        else
            $users_table = '`'.$wpdb->prefix.'users`';
        
        $users = $wpdb->get_results("SELECT * FROM $users_table WHERE user_registered > '$from_date' AND user_registered < '$to_date'");

        if(!empty($users)){
            foreach ($users as $key => $user) {
                $date = current_time( 'mysql' );
                $date = $user->user_registered;
                
                $description = __('Created User with ID', 'winter-activity-log').': <a href="'.admin_url('user-edit.php?user_id='.$user->ID).'">'.$user->ID.'</a>';
                $user_info = get_userdata($user->ID);
                if($user_info) {
                    $description .= ' ('.$user_info->user_nicename.') '.implode(',', $user_info->roles);
                }
                
                $this->insert_history($date, $user->ID, $description);
            }
            
        }
        /*
         * 2. Post tample analyze
         */
        
        $table = '`'.$wpdb->prefix.'posts`';
        $data = $wpdb->get_results("SELECT * FROM $table WHERE NOT `post_type` = 'int' AND  post_date > '$from_date' AND post_date < '$to_date'");
        
        $accoc_data = [];
        foreach ($data as $key => $value) {
            $accoc_data[$value->ID] = $value;
        }
        
        if(!empty($data)){
            foreach ($data as $key => $value) {
                
                if($value->post_status=='auto-draft' || $value->post_type=='customize_changeset') continue;
                
                $link = $value->guid;
                if($value->post_type == "post" || $value->post_type == "page") {
                    $link = admin_url('post.php?post='.$value->ID.'&action=edit');
                }
                
                $description = __('Created post type', 'winter-activity-log');
                $description .= ': "'.$value->post_type.'" ';
                $description .=  __('with ID', 'winter-activity-log').': <a href="'.$link.'">'.$value->ID.'</a> ('.$value->post_title.')';
                
                if($value->post_type == "revision") {
                    if(!isset($accoc_data[$value->post_parent])) continue;
                    $link = admin_url('revision.php').'?revision='.$value->ID;
                    $parent = $accoc_data[$value->post_parent];
                    $description = __('Editing post with ID', 'winter-activity-log').': <a href="'.admin_url('post.php?post='.$parent->ID.'&action=edit').'">'.$parent->ID.'</a> ('.$parent->post_title.')'.__(' > Post revision', 'winter-activity-log').': <a href="'.$link.'">'.$value->ID.'</a>';
                } 
                
                
                $date = current_time( 'mysql' );
                $date = $value->post_modified;
                
                $this->insert_history($date, $value->post_author, $description, $link);
            }
            
        }
        
        /*
         * 2. Comment tample analyze
         */
        
        $table = '`'.$wpdb->prefix.'comments`';
        $data = $wpdb->get_results("SELECT * FROM $table WHERE comment_date > '$from_date' AND comment_date < '$to_date'");
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $description = __('WP Comment', 'winter-activity-log');
                $description .= ' ID: <a href="'.admin_url('comment.php?action=editcomment&c='.$value->comment_ID).'">'.$value->comment_ID.'</a>';
                $date = current_time( 'mysql' );
                $date = $value->comment_date;

                $this->insert_history($date, $value->user_id, $description,admin_url('comment.php?action=editcomment&c='.$value->comment_ID));
            }
            
        }
        
        /*
         * 3. Fetch all data by %date%
        */
                
        if(true){
            $all_tables=$wpdb->get_results("SHOW TABLES");
            $skip_tables = ['comments','commentmeta','posts','links','options','postmeta','posts','termmeta','terms','term_relationships','term_taxonomy','usermeta',
                            'users','wal_cloud','wal_control','wal_failed_attemps','wal_history','wal_control_rule','wal_log','wal_report','sw_'];

            $prefix = $wpdb->prefix;
            array_walk($skip_tables, function(&$item) use ($prefix){$item = $prefix.$item;});
            $data_tables = [];
            foreach ($all_tables as $table)
            {
                $table_name = current($table);
                if(!$this->similar_in_array($table_name, $skip_tables)){
                    $columns=$wpdb->get_results("SHOW COLUMNS FROM ".$table_name);
                    $f_date = $this->generate_history_get_date_field($columns);
                    $f_title = $this->generate_history_get_title_field($columns);
                    $f_link = $this->generate_history_get_link_field($columns);
                    $f_user = $this->generate_history_get_user_field($columns);
                    $f_id = $this->generate_history_get_id_field($columns);

                    /* add history */
                    if(!empty($f_date) && !empty($f_title) && !empty($f_id)){
                        $table = '`'.$table_name.'` ';
                        $data = $wpdb->get_results("SELECT * FROM $table WHERE $f_date > '$from_date' AND $f_date < '$to_date'");
                        if(!empty($data)){
                            foreach ($data as $key => $value) {
                                $h_date = $value->$f_date;
                                $h_user = '';
                                if(!empty($f_user) && is_int($value->$f_user))
                                    $h_user = $value->$f_user;
                                
                                $h_description = '';
                                $h_link = '';
                                $h_description = __('Event in db table', 'winter-activity-log').' '.$table_name.', ';
                                $h_description .=  __('created row id', 'winter-activity-log').': '.$value->$f_id.' '.$value->$f_title;

                                $this->insert_history($h_date, $h_user, $h_description, $h_link);
                            }
                        }
                    }
                }
            }
        }    
        
        exit();
        
    } 
    
    private function similar_in_array($needle = '', $array  = [])
    {
        if(empty($needle) || empty($array)) return false;
        foreach ($array as $value)
        {
            if( stripos( $value , $needle ) !== FALSE )
            {
                return true;
            }
        }
        return false;
    }
    
    private function _generate_history_get_field($columns = array(),$keys = array()){
        if(empty($columns) || empty($keys)) return '';
        
        $field_name = '';
        $priority_columns = [];
        foreach ($columns as $column) {
            foreach ($keys as $k => $v) {
                if(is_array($v)){
                    $_accept = true;
                    foreach ($v as $v2) {
                        if(stripos($column->Field, $v2) === FALSE){
                            $_accept = false;
                        }   
                    }
                    if($_accept)
                        $priority_columns[$k] = $column->Field;
                } else{
                    if(stripos($column->Field, $v) !== FALSE){
                        $priority_columns[$k] = $column->Field;
                    }
                }
            }
        }
        if(!empty($priority_columns)) {
            ksort($priority_columns);
            reset($priority_columns);
            $field_name = current($priority_columns);
        }
        return $field_name;
    }
    
    
    private function generate_history_get_date_field($columns){
        return $this->_generate_history_get_field($columns, [['mod','date'],['up','date'],'date']);
    }
    
    private function generate_history_get_title_field($columns){
        return $this->_generate_history_get_field($columns, ['title','name']);
    }
    
    private function generate_history_get_link_field($columns){
        return $this->_generate_history_get_field($columns, []);
    }
    
    private function generate_history_get_user_field($columns){
        return $this->_generate_history_get_field($columns, ['post_author','profile','user']);
    }
    
    private function generate_history_get_id_field($columns){
        reset($columns);
        $first_key = current($columns);
        if(stripos($first_key->Field, 'id') !== FALSE){
            return $first_key->Field;
        }
        
        return $this->_generate_history_get_field($columns, ['ID']);
    }
    
    private function get_user_session_ip($user_id = NULL) {
        $ip = '';
        
        if(empty($user_id)) return $ip;
        $session_tokens = get_user_meta($user_id, 'session_tokens');
        if($session_tokens && is_array($session_tokens)) {
            $session_tokens = reset($session_tokens);
            if($session_tokens && is_array($session_tokens)) {
                $session_tokens = reset($session_tokens);
                if(isset($session_tokens['ip'])){
                   $ip = $session_tokens['ip'];
                }    
            }
        }
        
        return $ip;
    }
    
    private function insert_history($date = '', $user_id='', $description='', $page='') {
        $ip = '';
        $insert_data = array(
            'date' => $date, 
            'level' => 1, 
            'user_id' =>$user_id, 
            'ip' => $this->get_user_session_ip($user_id), 
            'request_uri' => '',
            'page' => $page, 
            'action' => '', 
            'is_favourite' => 0, 
            'request_data' => serialize(array('GET'=>'', 'POST'=>'', 
                                'COOKIE'=>'', 'REQUEST_METHOD'=>'', 'BODY'=>'', '')), 
            'header_data' => '',
            'other_data' => '',
            'description' => $description,
            'user_info' => $this->generate_user_info($user_id)
        );

        $this->history_m->insert($insert_data);
        
        return TRUE;
    }

    private function generate_user_info($user_id){
        $return = '';
        if(!empty($user_id)) {
            $user_info = get_userdata($user_id);
            if($user_info) {
                $return = '#'.$user_id.' <a target="_blank" href="'.admin_url('user-edit.php?user_id='.$user_id).'">'.$user_info->user_nicename.'</a> <br /> '.implode(',', $user_info->roles);
            }
        }
        return $return;
    }
    
}
