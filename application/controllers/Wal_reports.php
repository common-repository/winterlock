<?php
defined('WINTER_MVC_PATH') OR exit('No direct script access allowed');

class Wal_reports extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}
    
	public function index()
	{
        // Load view
        $this->load->view('wal_reports/index', $this->data);
    }
	
    public function report_edit()
    {
        $this->load->model('log_m');
        $this->load->model('report_m');

        $report_id = $this->input->post_get('id');

        // Prepare db data
        $this->data['db_data'] = NULL;

        if(!empty($report_id))
            $this->data['db_data'] = $this->report_m->get($report_id, TRUE);

        //dump($this->data['db_data']);

        //echo $this->db->last_query();

        $this->data['form'] = &$this->form;

        $rules = array(
            array(
                'field' => 'report_name',
                'label' => __('Report name', 'winter-activity-log'),
                'rules' => 'required'
            ),
            array(
                'field' => 'report_email',
                'label' => __('Report email', 'winter-activity-log'),
                'rules' => 'valid_email'
            ),
            array(
                'field' => 'scheduling_period',
                'label' => __('Scheduling period', 'winter-activity-log'),
                'rules' => 'intval'
            ),
            array(
                'field' => 'format',
                'label' => __('Format', 'winter-activity-log'),
                'rules' => ''
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
            ),
            array(
                'field' => 'date_start',
                'label' => __('Start date', 'winter-activity-log'),
                'rules' => 'valid_date'
            ),
            array(
                'field' => 'date_end',
                'label' => __('End date', 'winter-activity-log'),
                'rules' => 'valid_date'
            )
        );

        if($this->form->run($rules))
        {
            // Save procedure for basic data
 
            $data = $this->report_m->prepare_data($this->input->post(), 
                                                    array('report_name', 
                                                        'report_email', 'scheduling_period', 
                                                        'format', 'by_user', 'by_description', 
                                                        'by_ip', 'level', 'request_uri', 
                                                        'date_start', 'date_end'));
            if(!empty($data['date_start']))
            {
                $data['date_start'] = date("Y-m-d H:i:s", strtotime($data['date_start']));
            }

            if(!empty($data['date_end']))
            {
                $data['date_end'] = date("Y-m-d H:i:s", strtotime($data['date_end']));
            }

            // for array checkboxes prepare as string
            if(!empty($data['level']))
            {
                if(is_array($data['level']))
                {
                    $data['level'] = implode(',', $data['level']);
                }
            }

            $id = $this->report_m->insert($data, $report_id);

            // redirect
            wp_redirect(admin_url("admin.php?page=wal_reports&function=report_edit&id=$id&is_updated=true")); exit;
        }

        // Load view
        $this->load->view('wal_reports/report_edit', $this->data);
    }
    

	// Called from ajax
	// json for datatables
	public function datatable()
	{
        //$this->enable_error_reporting();
        remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

        // configuration
        $columns = array('idreport', 'report_name', 'report_email', 'scheduling_period', 'format');
        $controller = 'report';

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
            $row->edit =wmvc_btn_open(admin_url("admin.php?page=wal_reports&function=report_edit&id=".$row->{"id$controller"}), '');

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
        if(!wal_access_allowed('winterlock_reports'))
        {
            exit();
        }

        $this->load->model('report_m');

        // Get parameters
        $report_ids = $this->input->post('report_ids');

        $json = array(
            "report_ids" => $report_ids,
            );

        foreach($report_ids as $report_id)
        {
            if(is_numeric($report_id))
                $this->report_m->delete($report_id);
        }

        echo json_encode($json);
        
        exit();
    }

    public function report_download()
    {
        $this->load->model('log_m');
        $this->load->model('report_m');

        $report_id = $this->input->post_get('id');

        $this->report_m->report_download($report_id);

        exit();
    }

}
