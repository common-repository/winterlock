<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_cloudintegration extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        $this->load->view('wal_cloudintegration/index', $this->data);
    }
	
    public function cloud_edit()
    {
        $this->load->model('log_m');
        $this->load->model('cloud_m');

        $cloud_id = $this->input->post_get('id');

        // Prepare db data
        $this->data['db_data'] = NULL;

        if(!empty($cloud_id))
            $this->data['db_data'] = $this->cloud_m->get($cloud_id, TRUE);

        //dump($this->data['db_data']);

        //echo $this->db->last_query();

        $this->data['form'] = &$this->form;

        $rules = array(
            array(
                'field' => 'title',
                'label' => __('Title', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'component',
                'label' => __('Component', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'program_name',
                'label' => __('Program name', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'host',
                'label' => __('Host', 'winter-activity-log'),
                'rules' => 'required|sockets_enabled'
            ),
            array(
                'field' => 'port',
                'label' => __('Port', 'winter-activity-log'),
                'rules' => 'required|numeric'
            ),
            array(
                'field' => 'by_user',
                'label' => __('By user', 'winter-activity-log'),
                'rules' => ''
            ),
            array(
                'field' => 'by_ip',
                'label' => __('By IP', 'winter-activity-log'),
                'rules' => ''
            ),
            array(
                'field' => 'level',
                'label' => __('Level', 'winter-activity-log'),
                'rules' => ''
            ),
            array(
                'field' => 'request_uri',
                'label' => __('Request uri', 'winter-activity-log'),
                'rules' => ''
            )
        );

        $this->form->add_error_message('sockets_enabled', 'mod php sockets must be enabled');

        if($this->form->run($rules))
        {
            // Save procedure for basic data
 
            $data = $this->cloud_m->prepare_data($this->input->post(), 
                                                    array('title', 
                                                        'component', 'program_name', 
                                                        'host', 'port', 'by_user', 
                                                        'by_ip', 'level', 'request_uri'));

            // for array checkboxes prepare as string
            if(!empty($data['level']))
            {
                if(is_array($data['level']))
                {
                    $data['level'] = implode(',', $data['level']);
                }
            }

            $id = $this->cloud_m->insert($data, $cloud_id);

            //echo $this->db->last_query();

            // redirect
            wp_redirect(admin_url("admin.php?page=wal_cloudintegration&function=cloud_edit&id=$id&is_updated=true")); exit;
        }

        // Load view
        $this->load->view('wal_cloudintegration/cloud_edit', $this->data);
    }


    public function mysql_edit()
    {
        $this->load->model('log_m');
        $this->load->model('cloud_m');

        $cloud_id = $this->input->post_get('id');

        // Prepare db data
        $this->data['db_data'] = NULL;

        if(!empty($cloud_id))
            $this->data['db_data'] = $this->cloud_m->get($cloud_id, TRUE);

        //dump($this->data['db_data']);

        //echo $this->db->last_query();

        $this->data['form'] = &$this->form;

        $rules = array(
            array(
                'field' => 'title',
                'label' => __('Title', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'host',
                'label' => __('Host', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'port',
                'label' => __('Port', 'winter-activity-log'),
                'rules' => 'required|numeric'
            ),
            array(
                'field' => 'database_name',
                'label' => __('Database Name', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'database_tablename',
                'label' => __('Database Table Name', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'database_username',
                'label' => __('Database Username', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'database_password',
                'label' => __('Database Password', 'winter-activity-log'),
                'rules' => 'required'
            ),

            array(
                'field' => 'by_user',
                'label' => __('By user', 'winter-activity-log'),
                'rules' => ''
            ),
            array(
                'field' => 'by_ip',
                'label' => __('By IP', 'winter-activity-log'),
                'rules' => ''
            ),
            array(
                'field' => 'level',
                'label' => __('Level', 'winter-activity-log'),
                'rules' => ''
            ),
            array(
                'field' => 'request_uri',
                'label' => __('Request uri', 'winter-activity-log'),
                'rules' => ''
            )
        );

        if($this->form->run($rules))
        {
            // Save procedure for basic data
 
            $data = $this->cloud_m->prepare_data($this->input->post(), 
                                                    array('title', 
                                                        'host', 'port', 'database_name', 'database_tablename', 
                                                        'database_username', 'database_password', 'by_user', 
                                                        'by_ip', 'level', 'request_uri'));

            // for array checkboxes prepare as string
            if(!empty($data['level']))
            {
                if(is_array($data['level']))
                {
                    $data['level'] = implode(',', $data['level']);
                }
            }

            $id = $this->cloud_m->insert($data, $cloud_id);

            //echo $this->db->last_query();

            // redirect
            wp_redirect(admin_url("admin.php?page=wal_cloudintegration&function=mysql_edit&id=$id&is_updated=true")); exit;
        }

        // Load view
        $this->load->view('wal_cloudintegration/mysql_edit', $this->data);
    }

    public function cloud_test()
    {
        $this->load->model('log_m');
        $this->load->model('cloud_m');

        $cloud_id = $this->input->post_get('id');

        // Prepare db data
        $this->data['db_data'] = NULL;

        if(!empty($cloud_id))
            $this->data['db_data'] = $this->cloud_m->get($cloud_id, TRUE);

        //dump($this->data['db_data']);

        //echo $this->db->last_query();

        $this->data['form'] = &$this->form;


        // Load view
        $this->load->view('wal_cloudintegration/cloud_test', $this->data);
    }


    public function mysql_test()
    {
        $this->load->model('log_m');
        $this->load->model('cloud_m');

        $cloud_id = $this->input->post_get('id');

        // Prepare db data
        $this->data['db_data'] = NULL;

        if(!empty($cloud_id))
            $this->data['db_data'] = $this->cloud_m->get($cloud_id, TRUE);

        //dump($this->data['db_data']);

        //echo $this->db->last_query();

        $this->data['form'] = &$this->form;


        // Load view
        $this->load->view('wal_cloudintegration/mysql_test', $this->data);
    }


	// Called from ajax
	// json for datatables
	public function datatable()
	{
        //$this->enable_error_reporting();
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

        // configuration
        $columns = array('idcloud', 'title', 'component', 'program_name');
        $controller = 'cloud';

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

        //dump($query);
        //dump($length);
        //dump($start);
        //exit();

        

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
            $row->edit =wmvc_btn_open(admin_url("admin.php?page=wal_cloudintegration&function=cloud_edit&id=".$row->{"id$controller"}), '');

            if(!empty($row->database_name))
                $row->edit =wmvc_btn_open(admin_url("admin.php?page=wal_cloudintegration&function=mysql_edit&id=".$row->{"id$controller"}), '');


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

    public function bulk_remove($id = NULL, $redirect='1')
	{   
        if(!wal_access_allowed('winterlock_cloud'))
        {
            exit();
        }

        $this->load->model('cloud_m');

        // Get parameters
        $cloud_ids = $this->input->post('cloud_ids');

        $json = array(
            "cloud_ids" => $cloud_ids,
            );

        foreach($cloud_ids as $cloud_id)
        {
            if(is_numeric($cloud_id))
                $this->cloud_m->delete($cloud_id);
        }

        echo json_encode($json);
        
        exit();
    }

}
