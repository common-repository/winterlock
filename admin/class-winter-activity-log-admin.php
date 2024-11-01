<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://listing-themes.com/
 * @since      1.0.0
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/admin
 * @author     Sandi Winter <sandi@winter.hr>
 */

class Winter_Activity_Log_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Winter_Activity_Log_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Winter_Activity_Log_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/winter-activity-log-admin.css', array(), $this->version, 'all' );

		wp_register_style('winter-activity-log_basic_wrapper', plugin_dir_url( __FILE__ ).'css/basic.css', false, '1.0.0' );

		wp_register_style( 'dataTables-select', plugin_dir_url( __FILE__ ) . 'css/select.dataTables.min.css' );

		wp_register_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', false, '1.0.0' );
		
		wp_enqueue_style( 'font-awesome' );

		wp_enqueue_style( 'winter-activity-log-style', plugin_dir_url( __FILE__ ) . 'css/style.css', false, '1.0.1' );

        if(is_rtl()){
           wp_enqueue_style( 'winter-activity-log-rtl',  plugin_dir_url( __FILE__ ) . 'css/style_rtl.css');
		}

		wp_register_style( 'datetime-picker-css', plugin_dir_url( __FILE__ ) . 'js/datetime-picker/css/bootstrap-datetimepicker.css' );

		wp_register_style( 'jquery-confirm', plugin_dir_url( __FILE__ ) . 'js/jquery-confirm/jquery-confirm.min.css' );

		wp_enqueue_style( 'jquery-confirm' );

		wp_enqueue_style( 'datetime-picker-css' );

		
                
                
            if(get_option('wal_checkbox_enable_winterlock_dash_styles') > 0){
                wp_enqueue_style('winter-activity-admin-ui-dashboard', plugin_dir_url( __FILE__ ) . 'css/frontend-dashboard.css', array(), '1.1' );
            }
	
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Winter_Activity_Log_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Winter_Activity_Log_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_dequeue_script('datatables');
		wp_deregister_script('datatables');

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/winter-activity-log-admin.js', array( 'jquery' ), $this->version, false );

                wp_register_script( 'datatables', plugin_dir_url( __FILE__ ) . 'js/datatables.min.js', array( 'jquery' ), false, false );
                wp_register_script( 'dataTables-responsive', plugin_dir_url( __FILE__ ) . 'js/dataTables.responsive.js', array( 'jquery' ), false, false );
                wp_register_script( 'dataTables-select', plugin_dir_url( __FILE__ ) . 'js/dataTables.select.min.js', array( 'jquery' ), false, false );

		wp_register_script( 'datetime-picker-moment', plugin_dir_url( __FILE__ ) . 'js/datetime-picker/js/moment-with-locales.js', false, false, false );
		wp_register_script( 'datetime-picker-bootstrap', plugin_dir_url( __FILE__ ) . 'js/datetime-picker/js/bootstrap-datetimepicker.min.js', false, false, false );

		wp_register_script( 'jquery-confirm', plugin_dir_url( __FILE__ ) . 'js/jquery-confirm/jquery-confirm.min.js' );

		wp_enqueue_script( 'jquery-confirm' );

		wp_enqueue_script( 'datetime-picker-moment' );
		wp_enqueue_script( 'datetime-picker-bootstrap' );
                
                $custom_js= '';
                $custom_js.="
                    jQuery('document').ready(function($){
                    $('#remove_all_logs').off().click(function(e){
                        e.preventDefault();
                        var that;
                        that = $(this);
                        var load_indicator = that.find('.fa-custom-ajax-indicator');

                        $.confirm({
                            boxWidth: '300px',
                            useBootstrap: false,
                            closeIcon: true,
                            draggable: true,
                            backgroundDismiss: true, // this will just close the modal
                            title: '".esc_html__('Are you sure?', 'winter-activity-log')."',
                            content: '".esc_html__('All logs will be removed!', 'winter-activity-log')."',
                            buttons: {
                                confirm: {
                                    text: '".esc_html__('Clear Log', 'winter-activity-log')."',
                                    btnClass: 'btn-blue',
                                    action: function(){
                                        var data = {
                                            'page': 'winteractivitylog',
                                            'function': 'clear_all_log',
                                            'action': 'winter_activity_log_action',
                                        };

                                        load_indicator.removeClass('hidden');
                                        sw_log_notify('".__('Removing log data...', 'winter-activity-log')."', 'loading');
                                        $.post('".esc_url(admin_url( 'admin-ajax.php' ))."', data, 
                                            function(data){
                                            if(data.message)
                                                sw_log_notify(data.message);

                                            if(data.success)
                                            {
                                                sw_log_notify('".__('Log is cleared', 'winter-activity-log')."');
                                            }
                                        }).success(function(){
                                            load_indicator.addClass('hidden');
                                        });
                                    }
                                },
                                cancel: function () {
                                },
                            },
                        })
                    });
                    
                    function sw_log_notify(text, type, popup_place) {
                        var $ = jQuery;
                        if(!$('.sw_log_notify-box').length) $('body').append('<div class=\"sw_log_notify-box\"></div>')
                        if(typeof text==\"undefined\") var text = 'Undefined text';
                        if(typeof type==\"undefined\") var type = 'success';
                        if(typeof popup_place==\"undefined\") var popup_place = $('.sw_log_notify-box');
                        var el_class = '';
                        var el_timer= 5000;
                        switch(type){
                            case \"success\" : el_class = \"success\";
                                            break
                            case \"error\" : el_class = \"error\";
                                            break
                            case \"loading\" : el_class = \"loading\";
                                             el_timer = 2000;
                                            break
                            default : el_class = \"success\";
                                            break
                        }

                        /* notify */
                        var html = '';
                        html = '<div class=\"sw_log_notify '+el_class+'\">'+text+'</div>';
                        var notification = $(html).appendTo(popup_place).delay(100).queue(function () {
                                            $(this).addClass('show')
                                                setTimeout(function() {
                                                    notification.removeClass('show')
                                                    setTimeout(function() {
                                                        notification.remove();
                                                    }, 1000);     
                                                }, el_timer);  
                                            })
                        /* end notify */
                    }
                })
                ";

                wp_add_inline_script( 'winter-activity-log', $custom_js );
    
	}

	/**
	 * Admin AJAX
	 */

	public function winter_activity_log_action()
	{
		global $Winter_MVC;

		$page = '';
		$function = '';

		if(isset($_POST['page']))$page = sanitize_text_field(wmvc_xss_clean($_POST['page']));
		if(isset($_POST['function']))$function = sanitize_text_field(wmvc_xss_clean($_POST['function']));

		$Winter_MVC = new MVC_Loader(plugin_dir_path( __FILE__ ).'../');
		$Winter_MVC->load_helper('basic');
		$Winter_MVC->load_controller($page, $function, array());
	}

	/**
	 * Admin Page Display
	 */
	public function admin_page_display() {
		global $Winter_MVC;

		$page = '';
		$function = '';

		if(isset($_GET['page']))$page = sanitize_text_field(wmvc_xss_clean($_GET['page']));
		if(isset($_GET['function']))$function = sanitize_text_field(wmvc_xss_clean($_GET['function']));

		$Winter_MVC = new MVC_Loader(plugin_dir_path( __FILE__ ).'../');
		$Winter_MVC->load_helper('basic');
		$Winter_MVC->load_controller($page, $function, array());
	}

		/**
		 * To add Plugin Menu and Settings page
		 */
		public function plugin_menu() {

			ob_start();
			//ob_flush();

			// Show menu only for approved admins
			$allowed_admins = get_option('wal_allowed_admins');
			if(wmvc_user_in_role('administrator') || wmvc_user_in_role('super-admin'))
			if(is_array($allowed_admins) && count($allowed_admins) > 0)
			{
				if(!in_array(get_current_user_id(), $allowed_admins))
					return;
			}

			$role = get_role('administrator');

			if(is_object($role))
			{
				$role->add_cap('winterlock_view'); 
				$role->add_cap('winterlock_logs'); 
				$role->add_cap('winterlock_sessions'); 
				$role->add_cap('winterlock_reports'); 
				$role->add_cap('winterlock_cloud'); 
			}

			$allowed_roles = get_option('wal_allowed_roles');

			if(is_array($allowed_roles) && count($allowed_roles) > 0)
			{
				foreach($allowed_roles as $key => $val)
				{
					$role = get_role($key);

					if(is_object($role))
					{
						$role->add_cap('winterlock_view'); 
						$role->add_cap('winterlock_logs'); 
						$role->add_cap('winterlock_sessions');
					}
				}
			}

			$all_roles = wmvc_roles_array();
			foreach($all_roles as $row)
			{
				if($row['role'] == 'administrator')continue;

				if(is_array($allowed_roles))
				if(!in_array($row['role'], $allowed_roles))
				{
					$role = get_role($row['role']);

					if(is_object($role))
					{
						$role->remove_cap('winterlock_view'); 
						$role->remove_cap('winterlock_logs'); 
						$role->remove_cap('winterlock_sessions'); 
						$role->remove_cap('winterlock_reports'); 
						$role->remove_cap('winterlock_cloud'); 
					}
				}
			}


			
			require_once WINTER_ACTIVITY_LOG_PATH . 'vendor/boo-settings-helper/class-boo-settings-helper.php';

            add_menu_page(__('Winter Activity Log Settings','winter-activity-log'), __('WinterLock','winter-activity-log'), 
                'winterlock_view', 'winteractivitylog', array($this, 'admin_page_display'),
                //plugin_dir_url( __FILE__ ) . 'resources/logo.png',
                'dashicons-visibility',
                32 );
			
            add_submenu_page('winteractivitylog', 
                            __('Activity Log','winter-activity-log'), 
                            __('Activity Log','winter-activity-log'),
                            'winterlock_logs', 'winteractivitylog', array($this, 'admin_page_display'));
							
			add_submenu_page('winteractivitylog', 
                            __('Favourite Logs','winter-activity-log'), 
                            __('Favourite Logs','winter-activity-log'),
							'winterlock_logs', 'wal_favouritelogs', array($this, 'admin_page_display'));

			add_submenu_page('winteractivitylog', 
                            __('Disabled Logs','winter-activity-log'), 
                            __('Disabled Logs','winter-activity-log'),
							'winterlock_logs', 'wal_disabledlogs', array($this, 'admin_page_display'));

			add_submenu_page('winteractivitylog', 
                            __('Control Security','winter-activity-log'), 
                            __('Control Security','winter-activity-log'),
							'winterlock_logs', 'wal_controlsecurity', array($this, 'admin_page_display'));

			add_submenu_page('winteractivitylog', 
                            __('Log Alerts','winter-activity-log'), 
                            __('Log Alerts','winter-activity-log'),
                            'winterlock_logs', 'wal_logalerts', array($this, 'admin_page_display'));

			add_submenu_page('winteractivitylog', 
                            __('History','winter-activity-log'), 
                            __('History','winter-activity-log'),
                            'winterlock_logs', 'wal_history', array($this, 'admin_page_display'));

			add_submenu_page('winteractivitylog', 
                            __('User Sessions','winter-activity-log'), 
                            __('User Sessions','winter-activity-log'),
							'winterlock_sessions', 'wal_usersessions', array($this, 'admin_page_display'));

			add_submenu_page('winteractivitylog', 
                            __('Reports','winter-activity-log'), 
                            __('Reports','winter-activity-log'),
							'winterlock_reports', 'wal_reports', array($this, 'admin_page_display'));
							
			add_submenu_page('winteractivitylog', 
                            __('Cloud Integration','winter-activity-log'), 
                            __('Cloud Integration','winter-activity-log'),
                            'winterlock_cloud', 'wal_cloudintegration', array($this, 'admin_page_display'));
                            
            add_submenu_page('winteractivitylog', 
                            __('Related plugins','winter-activity-log'), 
                            __('Related plugins','winter-activity-log'),
                            'winterlock_logs', 'wal_related', array($this, 'admin_page_display'));


			// If not administrator


			/*
			add_submenu_page('winteractivitylog', 
                            __('Settings','winter-activity-log'), 
                            __('Settings','winter-activity-log'),
							'manage_options', 'settings', array($this, 'admin_page_display'));
			*/

			$login_block_desc = '<div class="winterlock_wrap"><div class="alert alert-danger" role="alert">'.
								__('This may block your access to website', 'winter-activity-log').'<br />'.
								__('Save this link to txt file on your computer, will help you to unblock your website in such cases:', 'winter-activity-log').'<br />'.
								get_home_url().'?wal_unblock='.md5(AUTH_KEY.'wal').'</div></div>';
								
			$users_admins = get_users([ 'role__in' => [ 'administrator', 'super-admin' ] ]);
			$users_prepare = array();
			foreach($users_admins as $row)
			{
				$users_prepare[$row->ID] = $row->display_name;
			}

			$roles_prepare = array();
			$roles_prepare_all = array();
			$all_roles = wmvc_roles_array();

			foreach($all_roles as $row)
			{
				$roles_prepare_all[$row['role']] = $row['role'].', '.$row['name'];

				if($row['role'] == 'administrator')continue;

				$roles_prepare[$row['role']] = $row['role'].', '.$row['name'];
			}



			$general_class = 'wal-pro';

			if ( winteractivitylog()->is_plan_or_trial('lite') )
				$general_class = '';

			$winteractivitylog_settings = array(
				'tabs'     => true,
				'prefix'   => 'wal_',
				'menu'     => array(
					'slug'       => 'wal_settings',
					'page_title' => __( 'Winter Activity Log Settings', 'winter-activity-log' ),
					'menu_title' => __( 'Settings ', 'winter-activity-log' ),
					'parent'     => 'winteractivitylog',
					'submenu'    => true
				),
				'sections' => array(
					//General Section
					array(
						'id'    => 'wal_general_section',
						'title' => __( 'General Section', 'winter-activity-log' ),
						'desc'  => __( 'These are general settings', 'winter-activity-log' ),
					),
					//Logging level
					array(
						'id'    => 'wal_log_level',
						'title' => __( 'Logging Level', 'winter-activity-log' ),
						'desc'  => __( 'These are Logging Level Settings', 'winter-activity-log' ),
					),
					//Events
					
					array(
						'id'    => 'wal_log_events',
						'title' => __( 'Enabled Events', 'winter-activity-log' ),
						'desc'  => __( 'These are Predefined Events', 'winter-activity-log' ),
						'callback'  => 'sw_wal_log_events_print',
					),
					//Show column names
					
					array(
						'id'    => 'wal_log_columns',
						'title' => __( 'Table column names', 'winter-activity-log' ),
						'desc'  => __( 'These are Columns of tables', 'winter-activity-log' ),
					)
					
				),
				'fields'   => array(
					// fields for General section
					'wal_general_section' => array(
						array(
							'id'    => 'checkbox_hide_ip',
							'label' => __( 'Hide IP for logs', 'winter-activity-log' ),
							'desc'  => __( 'Usualy used for GDPR purposes', 'winter-activity-log' ),
							'type'  => 'checkbox',
							//'default' => '1',
						),
						array(
							'id'    => 'log_days',
							'label' => __( 'Delete logs after', 'winter-activity-log' ),
							'desc'  => __( 'Days', 'winter-activity-log' ),
							'sanitize_callback' => 'absint',
							'class'	=> $general_class,
						),
                                                    array(
							'id'    => 'Remove All Logs',
							'label' => __( 'Remove All Logs', 'winter-activity-log' ),
							'desc'  => '<input type="button" id="remove_all_logs" class="button button-primary" value="'.__('Remove All Logs','winter-activity-log').'">',
							'sanitize_callback' => 'absint',
							'type'  => 'html',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'checkbox_failed_login_block',
							'label' => __( 'Disable user/ip after 5 failed login attemps for 24h', 'winter-activity-log' ),
							'desc'  => $login_block_desc,
							'type'  => 'checkbox',
							'class'	=> $general_class,
                        ),
                        array(
							'id'    => 'checkbox_disable_multilogin',
							'label' => __( 'Disable login from multiple IP-s in same time', 'winter-activity-log' ),
							'desc'  => __( 'Great to disable login credentials sharing on courses, LMS, memberships, hidden paid content or similar scenarious', 'winter-activity-log' ),
							'type'  => 'checkbox',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'allowed_admins',
							'label' => __( 'Only this admins allowed', 'winter-activity-log' ),
							'desc'  => __( 'Allow only this specific admins to see logs', 'winter-activity-log' ),
							'type'  => 'multicheck',
							'options' => $users_prepare
						),
						array(
							'id'    => 'allowed_roles',
							'label' => __( 'Allow WinterLock access also to roles', 'winter-activity-log' ),
							'desc'  => __( 'Except administrators, also this roles will be able to access', 'winter-activity-log' ),
							'type'  => 'multicheck',
							'options' => $roles_prepare
						),
                        array(
							'id'    => 'checkbox_enable_winterlock_dash_styles',
							'label' => __( 'Enable WinterLock Layout in complete dash', 'winter-activity-log' ),
							'desc'  => __( 'When you enable this, complete WP Dashboard will receive new look based on WinterLock Plugin', 'winter-activity-log' ),
							'type'  => 'checkbox',
							//'default' => '1',
						),
						array(
							'id'    => 'checkbox_disable_hints',
							'label' => __( 'Disable hints', 'winter-activity-log' ),
							'desc'  => __( 'Will hide questions and video guides in dashboard', 'winter-activity-log' ),
							'type'  => 'checkbox',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'checkbox_disable_dashwidgets',
							'label' => __( 'Disable Dash Widgets', 'winter-activity-log' ),
							'desc'  => __( 'Will hide all WinterLock widgets visible when you logged in to Wordpress dashboard', 'winter-activity-log' ),
							'type'  => 'checkbox',
							'class'	=> $general_class,
                        ),
						array(
							'id'    => 'clickatell_one_api_key',
							'label' => __( 'Clickatell One API Key', 'winter-activity-log' ),
							'desc'  => __( 'This is required to send SMS alerts, receive it on https://clickatell.com', 'winter-activity-log' ),
							//'sanitize_callback' => 'absint',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'checkbox_enable_whatsapp_clickatell',
							'label' => __( 'Use WhatsApp in Clickatell', 'winter-activity-log' ),
							'desc'  => __( 'Will send WhatsApp message instead of SMS when using One API on Clickatell, WhatsApp must be enabled on Clickatell', 'winter-activity-log' ),
							'type'  => 'checkbox',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'clickatell_http_api_key',
							'label' => __( 'Clickatell HTTP API Key', 'winter-activity-log' ),
							'desc'  => __( 'This is required to send SMS alerts if above is not defined, receive it on https://clickatell.com', 'winter-activity-log' ),
							//'sanitize_callback' => 'absint',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'smsapicom_http_api_key',
							'label' => __( 'smsapi.com API Key', 'winter-activity-log' ),
							'desc'  => __( 'This is required to send SMS alerts if above is not defined, receive it on https://smsapi.com', 'winter-activity-log' ),
							//'sanitize_callback' => 'absint',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'smsapicom_sender_name',
							'label' => __( 'smsapi.com Sender Name', 'winter-activity-log' ),
							'desc'  => __( 'This can be defined in SMSApi.com dashboard, is required to send sms, or "Test" will be used', 'winter-activity-log' ),
							//'sanitize_callback' => 'absint',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'smsto_api_key',
							'label' => __( 'sms.to API Key', 'winter-activity-log' ),
							'desc'  => __( 'This is required to send SMS alerts if above is not defined, receive it on https://sms.to', 'winter-activity-log' ),
							//'sanitize_callback' => 'absint',
							'class'	=> $general_class,
						),
						array(
							'id'    => 'wal_smsto_senderid',
							'label' => __( 'sms.to Sender ID', 'winter-activity-log' ),
							'desc'  => __( 'This can be defined in sms.to dashboard, is required to send sms', 'winter-activity-log' ),
							//'sanitize_callback' => 'absint',
							'class'	=> $general_class,
						),
						/*
						array(
							'id'      => 'archive_column',
							'label'   => __( 'Archive Column', 'winter-activity-log' ),
							'type'    => 'select',
							'options' => array(
								'column-two'   => __( 'Two Columns', 'winter-activity-log' ),
								'column-three' => __( 'Three Columns', 'winter-activity-log' ),
								'column-four'  => __( 'Four Columns', 'winter-activity-log' ),
								'column-five'  => __( 'ThreFivee Columns', 'winter-activity-log' ),
							)
						),
						array(
							'id'                => 'text_field1',
							'label'             => __( 'Abs Int', 'winter-activity-log' ),
							'desc'              => __( 'Text input description', 'winter-activity-log' ),
							'placeholder'       => __( 'Text Input placeholder', 'winter-activity-log' ),
							'type'              => 'text',
							'default'           => 'Title',
							'sanitize_callback' => 'absint'
						),
						array(
							'id'    => 'color_test',
							'label' => __( 'Advance Field 1', 'winter-activity-log' ),
							'type'  => 'color',
						),
						array(
							'id'                => 'number_field1',
							'label'             => __( 'Number Input', 'winter-activity-log' ),
							'desc'              => __( 'Number field with validation callback `floatval`', 'winter-activity-log' ),
							'placeholder'       => __( '1.99', 'winter-activity-log' ),
//						'min'               => 0,
//						'max'               => 99,
//						'step'              => '0.01',
							'type'              => 'number',
							'default'           => '50',
							'sanitize_callback' => 'floatval'
						),
						array(
							'id'          => 'textarea_field1',
							'label'       => __( 'Textarea Input', 'winter-activity-log' ),
							'desc'        => __( 'Textarea description', 'winter-activity-log' ),
							'placeholder' => __( 'Textarea placeholder', 'winter-activity-log' ),
							'type'        => 'textarea'
						),
						array(
							'id'   => 'html',
							'desc' => __( 'HTML area description. You can use any <strong>bold</strong> or other HTML elements.', 'winter-activity-log' ),
							'type' => 'html'
						),
						array(
							'id'    => 'checkbox_field1',
							'label' => __( 'Checkbox', 'winter-activity-log' ),
							'desc'  => __( 'Checkbox Label', 'winter-activity-log' ),
							'type'  => 'checkbox',
							''
						),

						array(
							'id'      => 'multi_op_test',
							'label'   => __( 'Radio Button', 'winter-activity-log' ),
							'desc'    => __( 'A radio button', 'winter-activity-log' ),
							'type'    => 'multicheck',
							'options' => array(
								'multi_1' => 'Radio 1',
								'multi_2' => 'Radio 2',
								'multi_3' => 'Radio 3'
							),
							'default' => array(
								'multi_1' => 'multi_1',
								'multi_3' => 'multi_3'
							)
						),
						array(
							'id'      => 'radio_test2',
							'label'   => __( 'Radio Button', 'winter-activity-log' ),
							'desc'    => __( 'A radio button', 'winter-activity-log' ),
							'type'    => 'radio',
							'options' => array(
								'radio_1' => 'Radio 1',
								'radio_2' => 'Radio 2',
								'radio_3' => 'Radio 3'
							),
							'default' => array(
								'radio_1' => 'radio_1',
								'radio_2' => 'radio_2'
							)
						),

						array(
							'id'      => 'select_test1',
							'label'   => __( 'A Dropdown', 'winter-activity-log' ),
							'desc'    => __( 'Dropdown description', 'winter-activity-log' ),
							'type'    => 'select',
							'default' => 'option_2',
							'options' => array(
								'option_1' => 'Option 1',
								'option_2' => 'Option 2',
								'option_3' => 'Option 3'
							),
						),

						array(
							'id'      => 'pages_test',
							'label'   => __( 'Pages Field Type', 'winter-activity-log' ),
							'desc'    => __( 'List of Pages', 'winter-activity-log' ),
							'type'    => 'pages',
							'options' => array(
								'post_type' => 'post'
							),
//						'post_type' => 'page'
						),

						array(
							'id'      => 'posts_test',
							'label'   => __( 'Posts Field Type', 'winter-activity-log' ),
//						'desc'    => __( 'List of Posts', 'winter-activity-log' ),
							'type'    => 'posts',
							'options' => array(
								'post_type' => 'event'
							),

						),

						array(
							'id'      => 'password_test',
							'label'   => __( 'Password', 'winter-activity-log' ),
							'desc'    => __( 'Password description', 'winter-activity-log' ),
							'type'    => 'password',
							'default' => '',
						),
						array(
							'id'      => 'file_test',
							'label'   => __( 'File', 'winter-activity-log' ),
							'desc'    => __( 'File description', 'winter-activity-log' ),
							'type'    => 'file',
							'default' => '',
							'options' => array(
								'btn' => 'Get it'
							)
						),
						array(
							'id'      => 'media_test',
							'label'   => __( 'Media', 'winter-activity-log' ),
							'desc'    => __( 'Media', 'winter-activity-log' ),
							'type'    => 'media',
							'default' => '',
							'options' => array(
								'btn'       => 'Get the image',
								'width'     => 900,
//							'height'    => 300,
								'max_width' => 900
							)

						)
						*/
					),
					'wal_log_level' => apply_filters( 'wal/admin/settings/advance/fields',
						array(
							/*
							array(
								'id'    => 'test_field_xyz',
								'label' => __( 'Test Field xyz', 'winter-activity-log' ),
								'type'  => 'text',
								//'sanitize_callback' => 'absint'
							),
							*/
							array(
								'id'    => 'checkbox_log_plugin_disable',
								'label' => __( 'Disable Log our plugin', 'winter-activity-log' ),
								'desc'  => __( 'Log our plugin activities', 'winter-activity-log' ),
								'type'  => 'checkbox',
								'class'	=> $general_class,
								//'default' => '1',
							),
							array(
								'id'    => 'checkbox_log_cron_disable',
								'label' => __( 'Disable Log WP Cron', 'winter-activity-log' ),
								'desc'  => __( 'Log WordPress Cronjob activities', 'winter-activity-log' ),
								'type'  => 'checkbox',
								'class'	=> $general_class,
								//'default' => '1',
							),
							array(
								'id'    => 'checkbox_log_level_1_disable',
								'label' => __( 'Disable Log Level 1', 'winter-activity-log' ),
								'desc'  => __( 'Most basic activities log, like when someone open some page', 'winter-activity-log' ),
								'type'  => 'checkbox',
								'class'	=> $general_class,
								//'default' => '1',
							),
							array(
								'id'    => 'checkbox_log_level_2_disable',
								'label' => __( 'Disable Log level 2', 'winter-activity-log' ),
								'desc'  => __( 'Something is sent in POST via ajax, sometimes this mean change in database', 'winter-activity-log' ),
								'type'  => 'checkbox',
								//'default' => '0',
							),
							array(
								'id'    => 'checkbox_log_level_3_disable',
								'label' => __( 'Disable Log level 3', 'winter-activity-log' ),
								'desc'  => __( 'Something general is sent in POST to regular page, mostly this mean change in database', 'winter-activity-log' ),
								'type'  => 'checkbox',
								//'default' => '0',
							),
							array(
								'id'    => 'log_only_roles',
								'label' => __( 'Log activity only from this roles', 'winter-activity-log' ),
								'desc'  => __( 'WinterLock will skip all activities by other roles if any selected here', 'winter-activity-log' ),
								'type'  => 'multicheck',
								'class'	=> $general_class,
								'options' => $roles_prepare_all
							)
						)
                                            ),
					'wal_log_columns' => apply_filters( 'wal/admin/settings/advance/fields',
						array(
							array(
								'id'    => 'log_hide_columns',
								'label' => __( 'Hide Columns', 'winter-activity-log' ),
								'type'  => 'multicheck',
								'class'	=> $general_class,
                                                                'options' => array(
                                                                    'level' => __( 'Hide Level Column', 'winter-activity-log' ),
                                                                    'date' => __( 'Hide Date', 'winter-activity-log' ),
                                                                    'avatar' => __( 'Hide Avatar', 'winter-activity-log' ),
                                                                    'user' => __( 'Hide User', 'winter-activity-log' ),
                                                                    'ip' => __( 'Hide Ip', 'winter-activity-log' ),
                                                                    'description' => __( 'Hide Description', 'winter-activity-log' )
                                                                ),
							)
						)
						)
				)

			);

			new Boo_Settings_Helper2( $winteractivitylog_settings );
                        
			/*
			add_submenu_page('winteractivitylog', 
				__('Help','winter-activity-log'), 
				__('Help','winter-activity-log'),
				'manage_options', 'wal_help', array($this, 'admin_page_display'));

			add_submenu_page('winteractivitylog', 
				__('Upgrade','winter-activity-log'), 
				__('Upgrade','winter-activity-log'),
				'manage_options', 'wal_upgrade', array($this, 'admin_page_display'));

			*/

		}
                
}

if(isset($_POST['option_page']) && $_POST['option_page'] == "wal_settings_wal_log_events" ) {
    add_action( 'whitelist_options', 'whitelist_custom_options_page',11 );
    function whitelist_custom_options_page() {

        $sw_wal_log_events_options = array();

        /* generate events list with false */
        $events_list = sw_wal_log_generate_events(false);

        if(empty($events_list)) {
            wp_redirect(sanitize_text_field($_POST['sw_option_redirect']).'&helper_class=alert-success&updated=custom_message&message='.__( 'Missing predefined_events.xml', 'winter-activity-log' )); 
        }

        foreach ($_POST as $key => $value) {
            if(stripos($key, 'sw_code_') !== FALSE){
                $events_list[$key] = true;
            }
        }

        update_option('sw_wal_log_events_options', $events_list);

        wp_redirect(sanitize_text_field($_POST['sw_option_redirect']).'&updated=true'); 
        exit();
    }
}

/* return array with events list */
function sw_wal_log_get_events_xml(){
        $predefine_settings = plugin_dir_path( __FILE__ ).'../predefined_events.xml';
        if(!file_exists($predefine_settings)) {
            return false;
        }
        $xml = @simplexml_load_string(file_get_contents($predefine_settings)); 
        return $xml;
}

/* 
 * Generate events array with all data
 * @param $event_status (string) 
 *  - 'all' by default show all events
 *  - 'only_activated' show only activated events
 *  - 'only_deactivated' show only deactivated events
 * 
 *
 * 
 * return array with events list 
 *  */
function sw_wal_log_generate_events_list($event_status = 'all'){
    
        $xml = sw_wal_log_get_events_xml();
        $sw_wal_log_events_options = get_option('sw_wal_log_events_options');
        
        $checked = 'checked="checked"';
        /* generate events list with false */
        $events_list = [];
        if(!empty($xml))foreach($xml as $cat){
                if(empty($cat)) continue;
                if(!empty($cat))foreach($cat as $event){
                    
                    /* check on activated */
                    if($event_status=='only_activated' && $sw_wal_log_events_options){
                        if(isset($sw_wal_log_events_options['sw_code_'.$event->code]) && (bool)$sw_wal_log_events_options['sw_code_'.$event->code]===false) {
                            continue;
                        }
                    }
                    
                    if($event_status=='only_deactivated'){
                        if(!$sw_wal_log_events_options){
                            continue;
                        }
                        elseif(isset($sw_wal_log_events_options['sw_code_'.$event->code]) && (bool)$sw_wal_log_events_options['sw_code_'.$event->code]===true) {
                            continue;
                        }
                    }
                    
                    $_event = [];
                    $_event['code'] = (string)trim($event->code);
                    $_event['links'] = (string)trim($event->links);
                    $_event['description'] = (string)trim($event->description);
                    $_event['requests'] = [];
                    if(isset($event->requests->request) && !empty($event->requests->request)) foreach ($event->requests->request as $request) {
                        $_request = [];
                        $_request['type'] = trim((string)$request->type);
                        $_request['parameter'] = trim((string)$request->parameter);
                        $_request['operator'] = trim((string)$request->operator);
                        $_request['value'] = trim((string)$request->value);
                        $_request['_object'] = (array)$request;
                        $_event['requests'][] = $_request;
                    }
                    $events_list['sw_code_'.esc_html($event->code)] = $_event;
            }
        }
        return $events_list;
}

/* return array with events sw_code_{code_id}=>$flag */
function sw_wal_log_generate_events($flag = true){
        $xml = sw_wal_log_get_events_xml(); 
        if(!$xml) return false;
        
        /* generate events list with false */
        $events_list = [];
        if(!empty($xml))foreach($xml as $cat){
                if(empty($cat)) continue;
                if(!empty($cat))foreach($cat as $event){
                    $events_list['sw_code_'.esc_html($event->code)] = $flag;
            }
        }
  
        return $events_list;
}

/* return array with enabled events sw_code_{code_id}=>$flag */
function sw_wal_log_events_enabled($flag = true){
        $xml = sw_wal_log_get_events_xml(); 
        if(!$xml) return false;
        
        /* generate events list with false */
        $events_list = [];
        if(!empty($xml))foreach($xml as $cat){
                if(empty($cat)) continue;
                if(!empty($cat))foreach($cat as $event){
                    $events_list['sw_code_'.esc_html($event->code)] = $flag;
            }
        }
  
        return $events_list;
}

function sw_wal_log_events_print (){
    $xml = sw_wal_log_get_events_xml(); 
    
    echo '<div class="winterlock_wrap"><div class="alert alert-info" role="alert">'.
            sprintf(__( 'Missing event here? No problem you can add your own in Winterlock->%2$sActivity Log%1$s-> on wanted event click on lock icon or add manually %3$shere%1$s', 'winter-activity-log' ),
                    '</a>',
                    '<a href="'.menu_page_url( 'winteractivitylog', false ).'">',
                    '<a href="'.menu_page_url( 'wal_controlsecurity', false ).'&function=control_log&subfunction=block&log_id'.'">')
            .'</div></div>';
    
    if(!$xml) {
        echo '<div class="winterlock_wrap"><div class="alert alert-info" role="alert">'.__( 'Missing predefined_events.xml', 'winter-activity-log' ).'</div></div>';
        return false;
    }
    
    $sw_wal_log_events_options = get_option('sw_wal_log_events_options');
    
    global $wp;
    $current_url ="//".$_SERVER['HTTP_HOST'].urldecode($_SERVER['REQUEST_URI']);
    ?>
    <input type="hidden" name="sw_option_redirect" value="<?php echo esc_url($current_url);?>">
   
        <table class='wp-list-table wsal-tab widefat wsal-sub-tab sw_wal_log_events_print'>
            <thead>
                <tr>
                    <th width="48"><input class="selcect_deselect_checkbox" type="checkbox" style="margin: 0;"></th>
                    <th width="80"><?php echo __( 'Code', 'winter-activity-log' );?></th>
                    <th><?php echo __( 'Description', 'winter-activity-log' );?></th>
                    <th width="80"><?php echo __( 'Control Log', 'winter-activity-log' );?></th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($xml))foreach($xml as $cat):?>
                    <?php if(empty($cat)) continue; ?>
                    <tr>
                        <td colspan="7">
                            <h3 class="sub-category"><?php echo esc_html((string)($cat->Attributes()->name));?></h3>
                        </td>
                    </tr>
                    <?php if(!empty($cat))foreach($cat as $event):?>
                    <tr>
                        <?php 
                        $checked = 'checked="checked"';
                        if(isset($sw_wal_log_events_options["sw_code_".esc_attr($event->code)]) && $sw_wal_log_events_options["sw_code_".esc_html($event->code)] == false) {
                            $checked = '';
                        }

                        ?>
                        <td><input type="checkbox" value="1" name="sw_code_<?php echo esc_attr($event->code);?>" <?php echo esc_html($checked);?>></td>
                        <td><?php echo esc_html($event->code);?></td>
                        <td><?php echo esc_html($event->description);?></td>
                        <td align="center"><div class="winterlock_wrap"><?php echo wp_kses_post(wmvc_btn_block(admin_url('admin.php?page=wal_controlsecurity&function=control_log&subfunction=block&code='.trim($event->code)), false));?> </div></td>
                    </tr>
                    <?php endforeach;?>
                <?php endforeach;?>
            </tbody>
        </table>
   
    <script>
        jQuery(document).ready(function($){
            $('.sw_wal_log_events_print .selcect_deselect_checkbox').on('click', function(e){
                //$(this).prop('checked','checked');
                if($(this).prop('checked')){
                    $('.sw_wal_log_events_print tbody input[type="checkbox"]').prop('checked', true);
                } else {
                    $('.sw_wal_log_events_print tbody input[type="checkbox"]').prop('checked', false);
                }
            })
            
            var sw_selcect_deselect_checkbox_udpate = function(){
                var all_checked = true;
                $('.sw_wal_log_events_print tbody input[type="checkbox"]').each(function(){
                    if(!$(this).prop('checked')) {
                        all_checked = false;
                        return false;
                    }
                })
                if(all_checked) {
                     $('.sw_wal_log_events_print .selcect_deselect_checkbox').prop('checked', true);
                } else {
                     $('.sw_wal_log_events_print .selcect_deselect_checkbox').prop('checked', false);
                }
            }
            
            sw_selcect_deselect_checkbox_udpate();
            $('.sw_wal_log_events_print tbody input[type="checkbox"]').on('change', function(e){
                sw_selcect_deselect_checkbox_udpate();
            })
        })
    </script>
    <?php
}


function sw_wal_log_is_visible_table_column($column_name = ''){
    if(empty($column_name)) return false;
    // log_only_roles
    $log_visible_columns= get_option( 'wal_log_hide_columns' );
    
    if(!empty($log_visible_columns)) {
        if(isset($log_visible_columns[$column_name])) {
            return false;
        } else {
            return true;
        }
    }
    return true;
}