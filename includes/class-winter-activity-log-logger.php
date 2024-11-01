<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die;
}
/**
 * Functionality for Logger, saving data and control security
 *
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/includes
 * @author     SWIT <support@swit.hr>
 */
if ( !class_exists( 'Winter_Activity_Log_Logger' ) ) {
    class Winter_Activity_Log_Logger
    {
        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string $plugin_name The ID of this plugin.
         */
        private  $plugin_name ;
        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string $version The current version of this plugin.
         */
        private  $version ;
        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         *
         * @param      string $plugin_name The name of the plugin.
         * @param      string $version The version of this plugin.
         */
        public function __construct( $plugin_name, $version )
        {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->template_loader = wal_get_template_loader();
        }
        
        /**
         * hooked into 'init' action hook
         */
        public function init()
        {
            $this->sessions_log();

            //unblock in case of trouble:
            if ( isset( $_GET['wal_unblock'] ) ) {
                
                if ( $_GET['wal_unblock'] == md5( AUTH_KEY . 'wal' ) ) {
                    global  $wpdb ;
                    $wpdb->query( 'UPDATE ' . $wpdb->prefix . 'wal_control SET is_block_enabled = 0;' );

                    $wpdb->query( 'UPDATE ' . $wpdb->prefix . 'wal_sessions SET is_visit_end = 1 '.
                                          ' WHERE is_visit_end = 0 ');

                    exit( 'UNBLOCKED ALL RULES' );
                }
            
            }
            
            if ( $this->activity_log_control() == TRUE ) {
                if ( $this->skip_deactivated_events() ) {
                    return TRUE;
                }
                $this->activity_log_request();
            }
            
            add_action( 'wal_my_hourly_event', array( $this, 'wal_do_this_hourly' ) );
            
            if ( isset( $_GET['the_cron_test'] ) ) {
                $this->wal_do_this_hourly();
                die;
            }
        
        }

        public function sessions_log()
        {
            global  $wpdb;
            
            $table_name = $wpdb->prefix . 'wal_sessions';

            if( get_option( 'wal_checkbox_disable_multilogin', '0' ) == '1')
            {
                // update is_visit_end

                $query = 'UPDATE '.$table_name.' SET is_visit_end=1, time_sec_total = '.
                        'TIME_TO_SEC(TIMEDIFF(time_end, time_start))'.   
                        ' WHERE is_visit_end = 0 ';

                // time calculation, if time_end and time_start older then 5min

                $query .= ' AND time_end < \''.date("Y-m-d H:i:s", current_time( 'timestamp' ) -5*60).'\'';
                $query .= ' AND time_start < \''.date("Y-m-d H:i:s", current_time( 'timestamp' ) -5*60).'\'';

                //echo $query;

                $wpdb->query($query);

                if(isset($_GET['action']))
                if($_GET['action'] == 'logout')
                {
                    $query = 'UPDATE '.$table_name.' SET is_visit_end=1, time_sec_total = '.
                    'TIME_TO_SEC(TIMEDIFF(time_end, time_start))'.   
                    ' WHERE user_id='.get_current_user_id();
                    $query .= ' AND ip = \''.wal_get_the_user_ip().'\'';

                    //echo $query;

                    $wpdb->query($query);

                    return;
                }
            }

            // sessions log
            if( get_option( 'wal_checkbox_disable_multilogin', '0' ) == '1' && !empty(get_current_user_id()))
            {
                $insert_array = array();

                $insert_array['time_start'] = current_time( 'mysql' );
                $insert_array['time_end'] = '';
                $insert_array['user_id'] = get_current_user_id();
                $insert_array['user_info'] = wal_basic_user_info(get_current_user_id());
                $insert_array['ip'] = wal_get_the_user_ip();
                $insert_array['is_visit_end'] = 0;
                $insert_array['other_data'] = '';

                $query = 'SELECT * FROM '.$table_name.' WHERE is_visit_end = 0 ';
                $query .= ' AND user_id='.get_current_user_id();

                $check_exists = $wpdb->get_row($query);

                if(empty($check_exists))
                {
                    // insert
                    $wpdb->insert( $table_name, $insert_array );
                    $id = $wpdb->insert_id;
                }
                else
                {
                    // update

                    $query = 'UPDATE '.$table_name.' SET time_end=\''.current_time( 'mysql' ).
                    '\' WHERE is_visit_end = 0 ';

                    $query .= ' AND user_id='.get_current_user_id();

                    //echo $query;

                    $wpdb->query($query);

                    // delete all old sessions

                    $query = 'DELETE FROM '.$table_name.' '.
                    ' WHERE (time_end = \'0000-00-00 00:00:00\' ';

                    // time calculation, if older then half hour and not refreshed

                    $query .= ' AND time_start < \''.date("Y-m-d H:i:s", current_time( 'timestamp' ) -10*60).'\' ) OR ';

                    // older then 10 days
                    $query .= ' time_start < \''.date("Y-m-d H:i:s", current_time( 'timestamp' ) -10*24*60*60).'\'   '; 

                    //echo $query;

                    $wpdb->query($query);
                }
            }
            elseif( get_option( 'wal_checkbox_disable_multilogin', '0' ) == '1' && empty(get_current_user_id()))
            {

                // detect failed login attemp
                $user_name = NULL;
                $user_pass = NULL;

                $failed_login = FALSE;
                if ( isset( $_POST['log'] ) ) {
                    $user_name = sanitize_user($_POST['log']);
                }
                if ( isset( $_POST['username'] ) ) {
                    $user_name = sanitize_user($_POST['username']);
                }
                if ( isset( $_POST['pwd'] ) ) {
                    $user_pass = $_POST['pwd']; // going thru hash so no need to sanitize
                }
                if ( isset( $_POST['password'] ) ) {
                    $user_pass = $_POST['password']; // going thru hash so no need to sanitize
                }
                
                if ( !is_null( $user_name ) && !is_null( $user_pass ) ) {

                    $user_object = wal_wp_authenticate( $user_name, $user_pass );
                    // issue with wordfence login security

                    if(is_numeric($user_object->ID))
                    {
                        $query = 'SELECT * FROM '.$table_name.' WHERE is_visit_end = 0 ';
                        $query .= ' AND user_id='.$user_object->ID;

                        $check_exists = $wpdb->get_row($query);
                    }

                    if(empty($check_exists))
                    {

                    }
                    else
                    {
                        if ( $user_object === NULL || is_wp_error( $user_object ) ) {
                            $failed_login = TRUE;
                        }
                        else
                        {
                            // check if multiple login (different IP) and block
                            if($check_exists->ip != wal_get_the_user_ip() && $user_object->ID == $check_exists->user_id)
                                exit('USER ALREADY LOGGED IN WITH DIFFERENT IP');
                        }
                    }
                }
            }
        }
        
		public function wal_do_this_hourly() {
			// do something every hour
			global $wpdb, $Winter_MVC;

			if ( winteractivitylog()->is_plan_or_trial('standard') ) {

                //dump($Winter_MVC);
                
                $Winter_MVC = new MVC_Loader(plugin_dir_path( __FILE__ ).'../');
                $Winter_MVC->load_helper('basic');
				$Winter_MVC->model('report_m');

				$all_reports=$Winter_MVC->report_m->get_by(array("report_email != '' && report_email IS NOT NULL"=> NULL));

				//dump($all_reports);

				foreach($all_reports as $report)
				{
					if( empty($report->date_sent) || strtotime($report->date_sent) < strtotime( current_time( 'mysql' ). ' - '.$report->scheduling_period.' days') )
					{
						$Winter_MVC->report_m->report_sendemail($report->idreport);
					}
				}

			}
		}
        
        public function activity_log_control()
        {
            global  $wpdb ;
            // [Check if log only for specific roles is activated in settings]
            $log_only_roles = get_option( 'wal_log_only_roles' );
            
            if ( is_array( $log_only_roles ) && count( $log_only_roles ) > 0 ) {
                $skip_logging = true;
                foreach ( $log_only_roles as $key => $val ) {
                    if ( wmvc_user_in_role( $key ) ) {
                        $skip_logging = false;
                    }
                    
                    if ( isset( $_POST['log'] ) ) {
                        $user = get_user_by( 'login', sanitize_user(wmvc_xss_clean($_POST['log'])) );
                        if ( is_object( $user ) ) {
                            if ( in_array( $key, (array) $user->roles ) ) {
                                $skip_logging = false;
                            }
                        }
                    }
                
                }
                if ( $skip_logging ) {
                    return false;
                }
            }
            
            // [/Check if log only for specific roles is activated in settings]
            $action = '';
            if ( isset( $_GET['action'] ) ) {
                $action = sanitize_text_field(wmvc_xss_clean( $_GET['action'] ));
            }
            if ( empty($action) ) {
                if ( isset( $_POST['action'] ) ) {
                    $action = sanitize_text_field(wmvc_xss_clean( $_POST['action'] ));
                }
            }
            $page = '';
            if ( isset( $_GET['page'] ) ) {
                $page = sanitize_text_field(wmvc_xss_clean( $_GET['page'] ));
            }
            $result_control = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wal_control' );
            $result_control_rule = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wal_control_rule' );
            $prepare_rules = array();
            foreach ( $result_control_rule as $row ) {
                $prepare_rules[$row->control_id][] = $row;
            }
            foreach ( $result_control as $row ) {
                
                if ( isset( $prepare_rules[$row->idcontrol] ) ) {
                    /*
                    					
                    [0]=>
                      object(stdClass)#1403 (6) {
                        ["idcontrol_rule"]=>
                        string(3) "130"
                        ["control_id"]=>
                        string(1) "3"
                        ["type"]=>
                        string(7) "GENERAL"
                        ["operator"]=>
                        string(8) "CONTAINS"
                        ["parameter"]=>
                        string(14) "REQUEST_METHOD"
                        ["value"]=>
                        string(4) "POST"
                      }
                    */
                    //dump($prepare_rules[$row->idcontrol]);
                    //exit();
                    $headers = getallheaders();
                    $rule_confirmed = TRUE;
                    foreach ( $prepare_rules[$row->idcontrol] as $rule ) {
                        //dump($rule);
                        
                        if ( $rule->type == 'POST' ) {
                            if ( empty($rule->parameter) && empty($rule->value) ) {
                                if ( count( $_POST ) == 0 ) {
                                    continue;
                                }
                            }
                            if ( !empty($rule->parameter) && empty($rule->value) ) {
                                if ( isset( $_POST[$rule->parameter] ) ) {
                                    continue;
                                }
                            }
                            if ( isset( $_POST[$rule->parameter] ) ) {
                                
                                if ( $rule->operator == 'CONTAINS' ) {
                                    
                                    if ( strpos( $_POST[$rule->parameter], $rule->value ) !== FALSE ) {
                                        // if contains
                                        continue;
                                    } else {
                                        // if not contains
                                    }
                                
                                } elseif ( $rule->operator == 'NOT_CONTAINS' ) {
                                    
                                    if ( strpos( $_POST[$rule->parameter], $rule->value ) !== FALSE ) {
                                        // if contains
                                    } else {
                                        // if not contains
                                        continue;
                                    }
                                
                                }
                            
                            }
                        } elseif ( $rule->type == 'GET' ) {
                            if ( empty($rule->parameter) && empty($rule->value) ) {
                                if ( count( $_GET ) == 0 ) {
                                    continue;
                                }
                            }
                            if ( !empty($rule->parameter) && empty($rule->value) ) {
                                if ( isset( $_GET[$rule->parameter] ) ) {
                                    continue;
                                }
                            }
                            if ( isset( $_GET[$rule->parameter] ) ) {
                                
                                if ( $rule->operator == 'CONTAINS' ) {
                                    
                                    if ( strpos( $_GET[$rule->parameter], $rule->value ) !== FALSE ) {
                                        // if contains
                                        continue;
                                    } else {
                                        // if not contains
                                    }
                                
                                } elseif ( $rule->operator == 'NOT_CONTAINS' ) {
                                    
                                    if ( strpos( $_GET[$rule->parameter], $rule->value ) !== FALSE ) {
                                        // if contains
                                    } else {
                                        // if not contains
                                        continue;
                                    }
                                
                                }
                            
                            }
                        } elseif ( $rule->type == 'HEADER' ) {
                            //dump($headers);
                            if ( empty($rule->parameter) && empty($rule->value) ) {
                                if ( count( $headers ) == 0 ) {
                                    continue;
                                }
                            }
                            if ( !empty($rule->parameter) && empty($rule->value) ) {
                                if ( isset( $headers[$rule->parameter] ) ) {
                                    continue;
                                }
                            }
                            if ( isset( $headers[$rule->parameter] ) ) {
                                
                                if ( $rule->operator == 'CONTAINS' ) {
                                    
                                    if ( strpos( $headers[$rule->parameter], $rule->value ) !== FALSE ) {
                                        // if contains
                                        continue;
                                    } else {
                                        // if not contains
                                    }
                                
                                } elseif ( $rule->operator == 'NOT_CONTAINS' ) {
                                    
                                    if ( strpos( $headers[$rule->parameter], $rule->value ) !== FALSE ) {
                                        // if contains
                                    } else {
                                        // if not contains
                                        continue;
                                    }
                                
                                }
                            
                            }
                        } elseif ( $rule->type == 'GENERAL' ) {
                            $request_body = '';
                            // when using application/json as the HTTP Content-Type in the request
                            $post = json_decode( file_get_contents( 'php://input' ), true );
                            if ( json_last_error() == JSON_ERROR_NONE ) {
                                $request_body = $post;
                            }
                            $general = array(
                                'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
                                'BODY'           => $request_body,
                                'ip'             => wal_get_the_user_ip(),
                                'user_id'        => get_current_user_id(),
                                'page'           => wmvc_xss_clean( $page ),
                                'action'         => wmvc_xss_clean( $action ),
                                'request_uri'    => wal_get_uri(),
                            );
                            if ( is_array( $request_body ) ) {
                                foreach ( $request_body as $key => $val ) {
                                    $general[$key] = $val;
                                }
                            }
                            //dump($general);
                            if ( empty($rule->parameter) && empty($rule->value) ) {
                                if ( count( $general ) == 0 ) {
                                    continue;
                                }
                            }
                            if ( !empty($rule->parameter) && empty($rule->value) ) {
                                if ( isset( $general[$rule->parameter] ) ) {
                                    continue;
                                }
                            }
                            if ( isset( $general[$rule->parameter] ) ) {
                                
                                if ( $rule->operator == 'CONTAINS' ) {
                                    
                                    if ( strpos( $general[$rule->parameter], $rule->value ) !== FALSE ) {
                                        // if contains
                                        continue;
                                    } else {
                                        // if not contains
                                    }
                                
                                } elseif ( $rule->operator == 'NOT_CONTAINS' ) {
                                    
                                    if ( strpos( $general[$rule->parameter], $rule->value ) !== FALSE ) {
                                        // if contains
                                    } else {
                                        // if not contains
                                        continue;
                                    }
                                
                                }
                            
                            }
                        }
                        
                        //dump($rule);
                        //dump($rule_confirmed);
                        $rule_confirmed = FALSE;
                    }
                }
                
                if ( $rule_confirmed === TRUE ) 
                {

                    if ( winteractivitylog()->is_plan_or_trial('standard') ) {

						if($row->is_email_enabled == '1')
						{
							$to = $row->email;

							if(empty($to))
							{
								$to = get_bloginfo('admin_email');
							}

							$subject = __('Email alert for Security rule','winter-activity-log').': '.$row->idcontrol.', '.date( 'Y-m-d' );
							$body = __('Hi','winter-activity-log').','."<br /><br />\n\n";
							$body.= __('Rule trigered:','winter-activity-log').' <a href="'.
									admin_url("admin.php?page=wal_controlsecurity&function=control_log&id=".$row->idcontrol).
									'">'.$row->idcontrol."</a><br /><br />\n\n";
							$body.= __('Date:','winter-activity-log').' '.wal_get_date()."<br /><br />\n";
							$body.= $row->title."<br />\n";

							$headers = array('Content-Type: text/html; charset=UTF-8');

							wp_mail( $to, $subject, $body, $headers );
						}

						if($row->is_sms_enabled == '1')
						{
							$to = $row->phone;

							if(!empty($to))
							{
								$message = 'WinterLock Rule '.$row->idcontrol.' '.$row->title;

								wal_send_sms($to, $message);
								//wal_smsto($to, $message);
							}
						}

						if($row->is_block_enabled == '1')
						{
							exit('BLOCKED BY SECURITY');
						}

					}

                    if ( $row->is_skip == '1' ) {
                        return FALSE;
                    }
                }
            }
            return TRUE;
        }
        
        public function activity_log_request()
        {
            // Insert into DB table wal_log with fastest possible way
            global  $wpdb ;
            $table_name = $wpdb->prefix . 'wal_log';
            $action = '';
            if ( isset( $_GET['action'] ) ) {
                $action = sanitize_text_field(wmvc_xss_clean( $_GET['action'] ));
            }
            if ( empty($action) ) {
                if ( isset( $_POST['action'] ) ) {
                    $action = sanitize_text_field(wmvc_xss_clean( $_POST['action'] ));
                }
            }
            $page = '';
            if ( isset( $_GET['page'] ) ) {
                $page = sanitize_text_field(wmvc_xss_clean( $_GET['page'] ));
            }
            $request_body = '';
            // when using application/json as the HTTP Content-Type in the request
            $post = json_decode( file_get_contents( 'php://input' ), true );
            if ( json_last_error() == JSON_ERROR_NONE ) {
                $request_body = $post;
            }
            
            // [SKIP PARTS]
            if ( $action == 'heartbeat' ) {
                return;
            }
            if ( $action == 'AjaxRefresh' ) {
                return;
            }
            // detect failed login attemp
            $user_name = NULL;
            $user_pass = NULL;
            $failed_login = FALSE;
            if ( isset( $_POST['log'] ) ) {
                $user_name = sanitize_user($_POST['log']);
            }
            if ( isset( $_POST['username'] ) ) {
                $user_name = sanitize_user($_POST['username']);
            }
            if ( isset( $_POST['pwd'] ) ) {
                $user_pass = $_POST['pwd'];  // going thru hash so no need to sanitize
            }
            if ( isset( $_POST['password'] ) ) {
                $user_pass = $_POST['password'];  // going thru hash so no need to sanitize
            }

            $_post_filtered = wmvc_xss_clean( wal_post_for_log() ); // we need full post filtered to save it serialized in db for laterinvestivation on activity
            
            if ( !is_null( $user_name ) && !is_null( $user_pass ) ) {
                $user_object = wal_wp_authenticate( $user_name, $user_pass );
                // issue with wordfence login security
                
                if ( $user_object === NULL || is_wp_error( $user_object ) ) {
                    foreach ( $_post_filtered as $key => $val ) {
                        if ( strpos( $key, 'pwd' ) !== FALSE || strpos( $key, 'password' ) !== FALSE ) {
                            $_post_filtered[sanitize_text_field($key)] = 'DISABLED_BY_SECURITY';
                        }
                    }
                    $failed_login = TRUE;
                    
                    if ( get_option( 'wal_checkbox_failed_login_block', '0' ) == '1' ) {
                        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'wal_failed_attemps WHERE date <  \'' . date( "Y-m-d h:i:s", strtotime( "-1 day" ) ) . '\';' );
                        $insert_array = array(
                            'date'    => current_time( 'mysql' ),
                            'user_id' => get_current_user_id(),
                            'ip'      => wal_get_the_user_ip(),
                            'data'    => serialize( array(
                            'GET'            => wmvc_xss_clean( wal_get_for_log() ),
                            'POST'           => $_post_filtered,
                            'COOKIE'         => wmvc_xss_clean( $_COOKIE ),
                            'REQUEST_METHOD' => sanitize_text_field($_SERVER['REQUEST_METHOD']),
                        ) ),
                        );
                        $wpdb->insert( $wpdb->prefix . 'wal_failed_attemps', $insert_array );
                        // get all by ip order by date DESC
                        $res_failed = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wal_failed_attemps WHERE (ip = \'' . wal_get_the_user_ip() . '\' OR user_id = \'' . get_current_user_id() . '\') AND date >  \'' . date( "Y-m-d h:i:s", strtotime( "-1 day" ) ) . '\';' );
                        
                        if ( count( $res_failed ) >= 5 ) {
                            //define security block for this user
                            $insert_array = array(
                                'date'             => current_time( 'mysql' ),
                                'title'            => 'Block by failed logins',
                                'description'      => 'Blocked rules beacause of failed login attemps IP:' . wal_get_the_user_ip(),
                                'is_block_enabled' => '1',
                            );
                            $wpdb->insert( $wpdb->prefix . 'wal_control', $insert_array );
                            $insert_array = array(
                                'control_id' => $wpdb->insert_id,
                                'type'       => 'GENERAL',
                                'operator'   => 'CONTAINS',
                                'parameter'  => 'ip',
                                'value'      => wal_get_the_user_ip(),
                            );
                            $wpdb->insert( $wpdb->prefix . 'wal_control_rule', $insert_array );
                        }
                    
                    }
                
                }
            
            }
            
            //get log level
            $log_level = wal_resolve_level(
                wal_get_uri(),
                wal_post_for_log(),
                wal_get_for_log(),
                sanitize_text_field($_SERVER['REQUEST_METHOD']),
                $request_body,
                ( $failed_login ? 5 : NULL )
            );
            // skip passwords saving
            foreach ( $_post_filtered as $key => $val ) {
                if ( strpos( $key, 'pwd' ) !== FALSE || strpos( $key, 'password' ) !== FALSE ) {
                    $_post_filtered[$key] = 'DISABLED_BY_SECURITY';
                }
            }
            // [/SKIP PARTS]
            // [SKIP LOG]
            if ( get_option( 'wal_checkbox_log_level_1_disable', '1' ) == '1' ) {
                if ( $log_level == 1 ) {
                    return NULL;
                }
            }
            if ( get_option( 'wal_checkbox_log_level_2_disable', '0' ) == '1' ) {
                if ( $log_level == 2 ) {
                    return NULL;
                }
            }
            if ( get_option( 'wal_checkbox_log_level_3_disable', '0' ) == '1' ) {
                if ( $log_level == 3 ) {
                    return NULL;
                }
            }
            
            if ( get_option( 'wal_checkbox_log_plugin_disable', '1' ) == '1' ) {
                if ( strpos( wal_get_uri(), 'wal' ) !== FALSE ) {
                    return NULL;
                }
                if ( strpos( wal_get_uri(), 'winteractivitylog' ) !== FALSE ) {
                    return NULL;
                }
                if ( isset( $_POST['action'] ) && strpos( $_POST['action'], 'winter_activity' ) !== FALSE ) {
                    return NULL;
                }
                if ( isset( $_POST['action'] ) && strpos( $_POST['action'], 'activitytime' ) !== FALSE ) {
                    return NULL;
                }
            }
            
            if ( get_option( 'wal_checkbox_log_cron_disable', '1' ) == '1' ) {
                if ( strpos( wal_get_uri(), 'cron' ) !== FALSE ) {
                    return NULL;
                }
            }
            // [/SKIP LOG]
            // [REMOVE OLD LOGS]
            if ( get_option( 'wal_log_days' ) > 0 ) {
                if ( is_numeric( get_option( 'wal_log_days' ) ) ) {
                    $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'wal_log WHERE date <  \'' . date( "Y-m-d h:i:s", strtotime( "-" . get_option( 'wal_log_days' ) . " day" ) ) . '\';' );
                }
            }
            // [/REMOVE OLD LOGS]
            $post_revision_id = NULL;
            
            if ( isset( $_post_filtered['post_ID'] ) ) {
                $post_revisions =  wp_get_post_revisions( $_post_filtered['post_ID'] );
                $post_revision_id = array_shift( $post_revisions );
                if ( isset( $post_revision_id->ID ) ) {
                    $post_revision_id = $post_revision_id->ID;
                }
            }
            
            
            if ( isset( $request_body['id'] ) ) {
                $post_revisions =  wp_get_post_revisions( $request_body['id'] );
                $post_revision_id = array_shift($post_revisions);
                if ( isset( $post_revision_id->ID ) ) {
                    $post_revision_id = $post_revision_id->ID;
                }
            }
            
            $user_ip = wal_get_the_user_ip();
            if ( get_option( 'wal_checkbox_hide_ip' ) == 1 ) {
                $user_ip = 'DISABLED';
            }
            $insert_array = array(
                'date'         => current_time( 'mysql' ),
                'level'        => $log_level,
                'user_id'      => get_current_user_id(),
                'ip'           => $user_ip,
                'request_uri'  => wal_get_uri(),
                'page'         => wmvc_xss_clean( $page ),
                'action'       => wmvc_xss_clean( $action ),
                'is_favourite' => 0,
                'request_data' => serialize( array(
                    'GET'            => wal_get_for_log(),
                    'POST'           => $_post_filtered,
                    'COOKIE'         => wmvc_xss_clean( $_COOKIE ),
                    'REQUEST_METHOD' => sanitize_text_field($_SERVER['REQUEST_METHOD']),
                    'BODY'           => $request_body,
                    '',
                ) ),
                'header_data'  => serialize( getallheaders() ),
                'other_data'   => serialize( array(
                'post_revision_id' => $post_revision_id,
            ) ),
            );
            $insert_array = array_merge( $insert_array, array(
                'description' => wal_generate_description( $insert_array, $failed_login ),
                'user_info'   => wal_user_info(
                get_current_user_id(),
                getallheaders(),
                $page,
                wal_get_uri()
            ),
            ) );
            $wpdb->insert( $table_name, $insert_array );
            $id = $wpdb->insert_id;
            return $id;
        }
        
        /* return true if user none activaty */
        public function skip_deactivated_events()
        {
            $events = sw_wal_log_generate_events_list( 'only_deactivated' );
            $action = '';
            if ( isset( $_GET['action'] ) ) {
                $action = sanitize_text_field(wmvc_xss_clean( $_GET['action'] ));
            }
            if ( empty($action) ) {
                if ( isset( $_POST['action'] ) ) {
                    $action = sanitize_text_field(wmvc_xss_clean( $_POST['action'] ));
                }
            }
            $page = '';
            if ( isset( $_GET['page'] ) ) {
                $page = sanitize_text_field(wmvc_xss_clean( $_GET['page'] ));
            }
            $headers = getallheaders();
            $rule_confirmed = TRUE;
            foreach ( $events as $event ) {
                $_event_blocked = 0;
                foreach ( $event['requests'] as $rule ) {
                    //dump($rule);
                    
                    if ( $rule['type'] == 'POST' ) {
                        if ( empty($rule['parameter']) && empty($rule['value']) ) {
                            if ( count( $_POST ) == 0 ) {
                                $_event_blocked++;
                            }
                        }
                        if ( !empty($rule['parameter']) && empty($rule['value']) ) {
                            if ( isset( $_POST[$rule['parameter']] ) ) {
                                $_event_blocked++;
                            }
                        }
                        if ( isset( $_POST[$rule['parameter']] ) ) {
                            
                            if ( $rule['operator'] == 'CONTAINS' ) {
                                
                                if ( strpos( $_POST[$rule['parameter']], $rule['value'] ) !== FALSE ) {
                                    // if contains
                                    $_event_blocked++;
                                } else {
                                    // if not contains
                                }
                            
                            } elseif ( $rule['operator'] == 'NOT_CONTAINS' ) {
                                
                                if ( strpos( $_POST[$rule['parameter']], $rule['value'] ) !== FALSE ) {
                                    // if contains
                                } else {
                                    // if not contains
                                    $_event_blocked++;
                                }
                            
                            }
                        
                        }
                    } elseif ( $rule['type'] == 'GET' ) {
                        if ( empty($rule['parameter']) && empty($rule['value']) ) {
                            if ( count( $_GET ) == 0 ) {
                                $_event_blocked++;
                            }
                        }
                        if ( !empty($rule['parameter']) && empty($rule['value']) ) {
                            if ( isset( $_GET[$rule['parameter']] ) ) {
                                $_event_blocked++;
                            }
                        }
                        if ( isset( $_GET[$rule['parameter']] ) ) {
                            
                            if ( $rule['operator'] == 'CONTAINS' ) {
                                
                                if ( strpos( $_GET[$rule['parameter']], $rule['value'] ) !== FALSE ) {
                                    // if contains
                                    $_event_blocked++;
                                } else {
                                    // if not contains
                                }
                            
                            } elseif ( $rule['operator'] == 'NOT_CONTAINS' ) {
                                
                                if ( strpos( $_GET[$rule['parameter']], $rule['value'] ) !== FALSE ) {
                                    // if contains
                                } else {
                                    // if not contains
                                    $_event_blocked++;
                                }
                            
                            }
                        
                        }
                    } elseif ( $rule['type'] == 'HEADER' ) {
                        //dump($headers);
                        if ( empty($rule['parameter']) && empty($rule['value']) ) {
                            if ( count( $headers ) == 0 ) {
                                $_event_blocked++;
                            }
                        }
                        if ( !empty($rule['parameter']) && empty($rule['value']) ) {
                            if ( isset( $headers[$rule['parameter']] ) ) {
                                $_event_blocked++;
                            }
                        }
                        if ( isset( $headers[$rule['parameter']] ) ) {
                            
                            if ( $rule['operator'] == 'CONTAINS' ) {
                                
                                if ( strpos( $headers[$rule['parameter']], $rule['value'] ) !== FALSE ) {
                                    // if contains
                                    $_event_blocked++;
                                } else {
                                    // if not contains
                                }
                            
                            } elseif ( $rule['operator'] == 'NOT_CONTAINS' ) {
                                
                                if ( strpos( $headers[$rule['parameter']], $rule['value'] ) !== FALSE ) {
                                    // if contains
                                } else {
                                    // if not contains
                                    $_event_blocked++;
                                }
                            
                            }
                        
                        }
                    } elseif ( $rule['type'] == 'GENERAL' ) {
                        $request_body = '';
                        // when using application/json as the HTTP Content-Type in the request
                        $post = json_decode( file_get_contents( 'php://input' ), true );
                        if ( json_last_error() == JSON_ERROR_NONE ) {
                            $request_body = $post;
                        }
                        $general = array(
                            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
                            'BODY'           => $request_body,
                            'ip'             => wal_get_the_user_ip(),
                            'user_id'        => get_current_user_id(),
                            'page'           => wmvc_xss_clean( $page ),
                            'action'         => wmvc_xss_clean( $action ),
                            'request_uri'    => wal_get_uri(),
                        );
                        if ( is_array( $request_body ) ) {
                            foreach ( $request_body as $key => $val ) {
                                $general[$key] = $val;
                            }
                        }
                        if ( empty($rule['parameter']) && empty($rule['value']) ) {
                            if ( count( $general ) == 0 ) {
                                $_event_blocked++;
                            }
                        }
                        if ( !empty($rule['parameter']) && empty($rule['value']) ) {
                            if ( isset( $general[$rule['parameter']] ) ) {
                                $_event_blocked++;
                            }
                        }
                        if ( isset( $general[$rule['parameter']] ) ) {
                            
                            if ( $rule['operator'] == 'CONTAINS' ) {
                                
                                if ( strpos( $general[$rule['parameter']], $rule['value'] ) !== FALSE ) {
                                    // if contains
                                    $_event_blocked++;
                                } else {
                                    // if not contains
                                }
                            
                            } elseif ( $rule['operator'] == 'NOT_CONTAINS' ) {
                                
                                if ( strpos( $general[$rule['parameter']], $rule['value'] ) !== FALSE ) {
                                    // if contains
                                } else {
                                    // if not contains
                                    $_event_blocked++;
                                }
                            
                            }
                        
                        }
                    }
                    
                    //dump($rule);
                }
                if ( !empty($event['requests']) && count( $event['requests'] ) == $_event_blocked ) {
                    $rule_confirmed = FALSE;
                }
            }
            if ( !(bool) $rule_confirmed ) {
                return TRUE;
            }
            return FALSE;
        }
    
    }
}