<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    die;
}
if ( !function_exists( 'wal_is_single_or_archive_book' ) ) {
    function wal_is_single_or_archive_book() {
        return ( is_singular( 'book' ) || wal_is_archive_book() ? true : false );
    }

}
if ( !function_exists( 'wal_is_archive_book' ) ) {
    function wal_is_archive_book() {
        return ( is_post_type_archive( 'book' ) || is_tax( 'genre' ) ? true : false );
    }

}
if ( !function_exists( 'wal_get_template_loader' ) ) {
    function wal_get_template_loader() {
        return Winter_Activity_Log_Global::template_loader();
    }

}
if ( !function_exists( 'wal_get_column_class' ) ) {
    /**
     * @param $int int
     *
     * @return $css_class string
     */
    function wal_get_column_class(  $int  ) {
        switch ( $int ) {
            case 1:
                return 'column-one';
                break;
            case 2:
                return 'column-two';
                break;
            case 3:
                return 'column-three';
                break;
            case 4:
                return 'column-four';
                break;
            case 5:
                return 'column-five';
                break;
            default:
                return 'column-three';
        }
    }

}
if ( !function_exists( 'wal_sanitize_color' ) ) {
    function wal_sanitize_color(  $value  ) {
        if ( false === strpos( $value, 'rgba' ) ) {
            return sanitize_hex_color( $value );
        } else {
            // By now we know the string is formatted as an rgba color so we need to further sanitize it.
            $value = trim( $value, ' ' );
            $red = $green = $blue = $alpha = '';
            sscanf(
                $value,
                'rgba(%d,%d,%d,%f)',
                $red,
                $green,
                $blue,
                $alpha
            );
            return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
        }
    }

}
// Display User IP in WordPress
if ( !function_exists( 'wal_get_the_user_ip' ) ) {
    function wal_get_the_user_ip() {
        if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            // $ip is a valid IP address"
        } else {
            return '';
        }
        return apply_filters( 'wpb_get_ip', $ip );
    }

}
if ( !function_exists( 'wal_get_uri' ) ) {
    function wal_get_uri(  $skip_query_string = FALSE  ) {
        $filename = $_SERVER['REQUEST_URI'];
        $ipos = strpos( $filename, "?" );
        if ( !($ipos === false) && $skip_query_string === TRUE ) {
            $filename = substr( $filename, 0, $ipos );
        }
        return urldecode( $filename );
    }

}
function wal_post_for_log() {
    $log_array = array();
    foreach ( $_POST as $key => $val ) {
        if ( is_array( $val ) ) {
            return wal_sen_rec( $_POST );
        } elseif ( $key == 'content' ) {
            $log_array[sanitize_text_field( $key )] = wp_kses_post( $val );
        } else {
            $log_array[sanitize_text_field( $key )] = sanitize_text_field( $val );
        }
    }
    return $log_array;
}

function wal_get_for_log() {
    $log_array = array();
    foreach ( $_GET as $key => $val ) {
        if ( is_array( $val ) ) {
            return wal_sen_rec( $_POST );
        } elseif ( $key == 'newcontent' || $key == 'content' ) {
            $log_array[sanitize_text_field( $key )] = wp_kses_post( $val );
        } else {
            $log_array[sanitize_text_field( $key )] = sanitize_text_field( $val );
        }
    }
    return $log_array;
}

function wal_sen_rec(  $array  ) {
    $arr_cleaned = array();
    foreach ( $array as $key => $val ) {
        if ( is_array( $val ) ) {
            $arr_cleaned[sanitize_text_field( $key )] = wal_sen_rec( $val );
        } elseif ( $key == 'newcontent' || $key == 'content' ) {
            $arr_cleaned[sanitize_text_field( $key )] = wp_kses_post( $val );
        } else {
            $arr_cleaned[sanitize_text_field( $key )] = sanitize_text_field( $val );
        }
    }
    //dump($arr_cleaned);
    return $arr_cleaned;
}

if ( !function_exists( 'wal_resolve_level' ) ) {
    function wal_resolve_level(
        $request_uri = NULL,
        $post = NULL,
        $get = NULL,
        $request_method = NULL,
        $body = NULL,
        $fixed_level = NULL
    ) {
        if ( !empty( $fixed_level ) ) {
            return $fixed_level;
        }
        $level = 1;
        if ( $request_uri === NULL && $post === NULL && $get === NULL ) {
            $request_uri = wal_get_uri();
            $post = wal_post_for_log();
            $get = wal_get_for_log();
            $request_method = sanitize_text_field( $_SERVER['REQUEST_METHOD'] );
        }
        if ( $request_method == 'POST' && (count( $post ) > 0 || is_array( $body )) ) {
            $level = 2;
        }
        // standard wp ajax requests defined to level q
        if ( strpos( $request_uri, 'admin-ajax.php' ) === FALSE && strpos( $request_uri, 'wp-cron.php' ) === FALSE && strpos( $request_uri, 'wp_scrape_nonce' ) === FALSE && strpos( $request_uri, 'wp-json' ) === FALSE ) {
            if ( count( $post ) > 0 ) {
                $level = 3;
            }
        }
        // if ajax
        if ( strpos( $request_uri, 'admin-ajax.php' ) !== FALSE ) {
            // action post contain get-
            if ( substr( $post['action'], 0, 4 ) == 'get-' ) {
                $level = 1;
            }
        }
        if ( strpos( $request_uri, 'post-new.php' ) !== FALSE ) {
            $level = 4;
        }
        if ( strpos( $request_uri, 'edit.php' ) !== FALSE ) {
            $level = 4;
        }
        if ( isset( $body['edit_date'] ) || isset( $body['post_type'] ) || isset( $body['post_ID'] ) || isset( $body['post_status'] ) || isset( $body['content'] ) || isset( $post['post_type'] ) || isset( $post['post_ID'] ) || isset( $post['post_status'] ) || isset( $post['edit_date'] ) || isset( $post['content'] ) ) {
            $level = 4;
        }
        if ( isset( $post['action'] ) && $post['action'] == 'edit-theme-plugin-file' ) {
            $level = 4;
        }
        if ( isset( $get['action'] ) && $get['action'] == 'delete' ) {
            $level = 4;
        }
        if ( isset( $get['action'] ) && $get['action'] == 'trash' ) {
            $level = 4;
        }
        if ( isset( $get['action'] ) && $get['action'] == 'logout' ) {
            $level = 2;
        }
        if ( isset( $get['plugin'] ) || isset( $get['deleted'] ) || isset( $get['trashed'] ) ) {
            $level = 4;
        }
        return $level;
    }

}
if ( !function_exists( 'wal_generate_label_by_level' ) ) {
    function wal_generate_label_by_level(  $level, $message = ''  ) {
        if ( empty( $message ) ) {
            $message = $level;
        }
        if ( $level == 1 ) {
            return '<span class="label label-default" title="' . __( 'Most basic activities log, like when someone open some page', 'winter-activity-log' ) . '">' . $message . '</span>';
        }
        if ( $level == 2 ) {
            return '<span class="label label-primary" title="' . __( 'Something is sent in POST via ajax, sometimes this mean change in database', 'winter-activity-log' ) . '">' . $message . '</span>';
        }
        if ( $level == 3 ) {
            return '<span class="label label-success" title="' . __( 'Something general is sent in POST to regular page, mostly this mean change in database', 'winter-activity-log' ) . '">' . $message . '</span>';
        }
        if ( $level == 4 ) {
            return '<span class="label label-info" title="' . __( 'Editing known contents like post, page or similar', 'winter-activity-log' ) . '">' . $message . '</span>';
        }
        if ( $level == 5 ) {
            return '<span class="label label-warning" title="' . __( 'Critical tasks like FAILED login', 'winter-activity-log' ) . '">' . $message . '</span>';
        }
        if ( $level == 6 ) {
            return '<span class="label label-danger" title="' . __( 'Very critical tasks like hacking', 'winter-activity-log' ) . '">' . $message . '</span>';
        }
        return $message;
    }

}
if ( !function_exists( 'wal_resolve_wp_menu' ) ) {
    function wal_resolve_wp_menu(  $page = NULL, $request_uri = NULL  ) {
        global $submenu, $menu, $pagenow;
        $submenu_c = $submenu;
        $menu_c = $menu;
        $pagenow_c = $pagenow;
        if ( empty( $page ) && empty( $request_uri ) ) {
            return;
        }
        if ( !isset( $submenu_c ) ) {
            $menu_c = get_option( 'winter-activity-log-menuitems' );
            $submenu_c = get_option( 'winter-activity-log-submenuitems' );
        }
        $text = '';
        if ( is_array( $menu_c ) && !empty( $page ) ) {
            foreach ( $menu_c as $key => $row ) {
                if ( in_array( $page, $row ) ) {
                    $text .= $row[0];
                }
            }
        }
        if ( is_array( $submenu_c ) && !empty( $page ) ) {
            foreach ( $submenu_c as $key => $row ) {
                foreach ( $row as $key2 => $row2 ) {
                    if ( in_array( $page, $row2 ) ) {
                        if ( empty( $text ) ) {
                            $text .= $row2[0];
                        } else {
                            $text .= ' > ' . $row2[0];
                        }
                    }
                }
            }
        }
        if ( strpos( $request_uri, 'wp-admin' ) !== FALSE ) {
            $request_uri = basename( $request_uri );
        }
        if ( is_array( $menu_c ) && !empty( $request_uri ) ) {
            foreach ( $menu_c as $key => $row ) {
                if ( in_array( $request_uri, $row ) ) {
                    if ( empty( $text ) ) {
                        $text .= $row[0];
                    } else {
                        $text .= ' > ' . $row[0];
                    }
                }
            }
        }
        if ( is_array( $submenu_c ) && !empty( $request_uri ) ) {
            foreach ( $submenu_c as $key => $row ) {
                foreach ( $row as $key2 => $row2 ) {
                    if ( in_array( $request_uri, $row2 ) ) {
                        if ( empty( $text ) ) {
                            $text .= $row2[0];
                        } else {
                            $text .= ' > ' . $row2[0];
                        }
                    }
                }
            }
        }
        if ( strpos( $request_uri, 'post.php' ) !== FALSE ) {
            if ( empty( $text ) ) {
                $text .= 'Post';
            } else {
                $text .= ' > Post';
            }
        }
        if ( strpos( $request_uri, 'plugins.php' ) !== FALSE ) {
            if ( empty( $text ) ) {
                $text .= 'Plugins';
            } else {
                $text .= ' > Plugins';
            }
        }
        if ( strpos( $request_uri, 'post-new.php' ) !== FALSE ) {
            if ( empty( $text ) ) {
                $text .= 'Post Created';
            } else {
                $text .= ' > Post Created';
            }
        }
        if ( strpos( $request_uri, 'edit.php' ) !== FALSE && strpos( $request_uri, 'trashed' ) !== FALSE ) {
            if ( empty( $text ) ) {
                $text .= 'Post';
            } else {
                $text .= ' > Post';
            }
        }
        if ( strpos( $request_uri, 'edit.php' ) !== FALSE && strpos( $request_uri, 'deleted' ) !== FALSE ) {
            if ( empty( $text ) ) {
                $text .= 'Post';
            } else {
                $text .= ' > Post';
            }
        }
        if ( strpos( $request_uri, 'edit.php' ) !== FALSE && strpos( $request_uri, 'post_status' ) !== FALSE ) {
            if ( empty( $text ) ) {
                $text .= 'Edit Post';
            } else {
                $text .= ' > Edit Post';
            }
        }
        if ( !empty( $text ) ) {
            return $text;
        }
        if ( strpos( $request_uri, 'admin-ajax.php' ) !== FALSE ) {
            return 'Ajax request';
        }
        if ( strpos( $request_uri, 'wp-cron.php' ) !== FALSE ) {
            return 'WP Cron';
        }
        if ( strpos( $request_uri, 'wp_scrape_nonce' ) !== FALSE ) {
            return 'WP Scraping';
        }
        if ( strpos( $request_uri, 'wp-json' ) !== FALSE ) {
            return 'WP JSON';
        }
        if ( strpos( $request_uri, 'post.php?' ) !== FALSE ) {
            return 'Edit post/page';
        }
        if ( strpos( $request_uri, 'wp-login.php' ) !== FALSE ) {
            return 'wp-login.php';
        }
        if ( strpos( $request_uri, 'options.php' ) !== FALSE ) {
            return 'WP Options';
        }
        return $request_uri;
    }

}
function wal_generate_description(  $row, $failed_login = false  ) {
    $desc = '';
    $desc .= wal_resolve_wp_menu( $row['page'], $row['request_uri'] );
    if ( !empty( $row['action'] ) ) {
        $desc .= ' > ' . $row['action'];
    }
    //check for "function" query string
    $request_data = unserialize( $row['request_data'] );
    if ( isset( $request_data['GET']['function'] ) ) {
        $desc .= ' > ' . $request_data['GET']['function'];
    }
    if ( isset( $request_data['GET']['trashed'] ) ) {
        $desc .= ' > Trashed';
    }
    if ( isset( $request_data['GET']['deleted'] ) ) {
        $desc .= ' > Deleted';
    }
    if ( isset( $request_data['POST']['post_ID'] ) ) {
        $post_link = get_edit_post_link( $request_data['POST']['post_ID'] );
        $post_title = '';
        if ( isset( $request_data['POST']['post_title'] ) ) {
            $post_title = ' (' . $request_data['POST']['post_title'] . ')';
        }
        $desc .= ' > ' . "Editing post with ID: <a target=\"_blank\" href=\"{$post_link}\">" . $request_data['POST']['post_ID'] . $post_title . '</a>';
    }
    $other_data = unserialize( $row['other_data'] );
    if ( isset( $other_data['post_revision_id'] ) ) {
        $revision_link = get_admin_url() . 'revision.php?revision=' . $other_data['post_revision_id'];
        $desc .= ' > ' . 'Post revision: <a target="_blank" href="' . $revision_link . '">' . $other_data['post_revision_id'] . '</a>';
    }
    if ( $row['action'] == 'edit-theme-plugin-file' ) {
        if ( isset( $request_data['POST']['file'] ) ) {
            $desc .= ' > ' . 'File: ' . $request_data['POST']['file'];
        }
    }
    if ( isset( $request_data['GET']['plugin'] ) ) {
        $desc .= ' > Plugin: ' . $request_data['GET']['plugin'];
    }
    if ( (strpos( $row['request_uri'], 'wp-login.php' ) !== FALSE || $row['action'] == 'wordfence_ls_authenticate') && isset( $request_data['POST']['pwd'] ) ) {
        if ( $failed_login ) {
            $desc = 'FAILED Login with data';
        } else {
            $desc = 'Login with data';
        }
        if ( isset( $request_data['POST']['log'] ) ) {
            $desc .= ' > Username: ' . $request_data['POST']['log'];
        }
    }
    if ( isset( $request_data['GET']['post_type'] ) ) {
        $desc .= ' > Post Type: ' . $request_data['GET']['post_type'];
    }
    if ( isset( $request_data['GET']['post_status'] ) ) {
        $desc .= ' > Post status: ' . $request_data['GET']['post_status'];
    }
    if ( isset( $request_data['GET']['ids'] ) ) {
        $desc .= ' > Ids: ' . $request_data['GET']['ids'];
    }
    if ( isset( $request_data['GET']['post'] ) && is_numeric( $request_data['GET']['post'] ) ) {
        $desc .= ' > Post ID: ' . $request_data['GET']['post'];
    }
    if ( isset( $request_data['GET']['loggedout'] ) ) {
        $desc .= ' > Logged Out: ' . $request_data['GET']['loggedout'];
    }
    //$desc .= $row->request_uri;
    return $desc;
}

if ( !function_exists( 'wal_visitor_type' ) ) {
    function wal_visitor_type(  $page = NULL, $request_uri = NULL, $user_agent = NULL  ) {
        if ( empty( $page ) && empty( $request_uri ) ) {
            return NULL;
        }
        if ( strpos( $request_uri, 'wp-cron.php' ) !== FALSE ) {
            return 'system';
        }
        if ( strpos( $request_uri, 'wp_scrape_nonce' ) !== FALSE ) {
            return 'system';
        }
        if ( $user_agent !== NULL ) {
            $obj = new Winter_Activity_Log_BrowserDetector($user_agent);
            $info = $obj->detect()->getBrowser();
            if ( !empty( $info ) ) {
                return 'guest';
            }
        }
        return 'unknown';
    }

}
function wal_basic_user_info(  $user_id  ) {
    $user_info = get_userdata( $user_id );
    $text = '';
    if ( isset( $user_info->user_login ) ) {
        $text = "#{$user_info->ID} <a target=\"_blank\" href=\"" . admin_url( 'user-edit.php?user_id=' . $user_info->ID ) . "\">{$user_info->user_login}</a> <br /> " . implode( ', ', $user_info->roles ) . " ";
    }
    if ( empty( $text ) ) {
        $text = 'IP: ' . wal_get_the_user_ip();
    }
    return $text;
}

function wal_user_info(
    $user_id,
    $header_data = array(),
    $page = NULL,
    $request_uri = NULL
) {
    $user_info = get_userdata( $user_id );
    $text = '';
    if ( isset( $user_info->user_login ) ) {
        $text = "#{$user_info->ID} <a target=\"_blank\" href=\"" . admin_url( 'user-edit.php?user_id=' . $user_info->ID ) . "\">{$user_info->user_login}</a> <br /> " . implode( ', ', $user_info->roles ) . " ";
    }
    $user_agent = NULL;
    if ( !empty( $header_data["User-Agent"] ) ) {
        $user_agent = $header_data["User-Agent"];
    }
    if ( empty( $text ) ) {
        $text = '-';
    }
    if ( isset( $user_info->ID ) ) {
    } elseif ( wal_visitor_type( $page, $request_uri, $user_agent ) === 'system' ) {
        $text = 'System';
    } elseif ( wal_visitor_type( $page, $request_uri, $user_agent ) === 'guest' ) {
        $text = 'Guest';
    } elseif ( wal_visitor_type( $page, $request_uri, $user_agent ) === 'unknown' ) {
        $text = 'Not logged in';
    }
    return $text;
}

function &wal_get_instance() {
    global $Winter_MVC;
    return $Winter_MVC;
}

if ( !function_exists( 'wal_prepare_search_query_GET' ) ) {
    function wal_prepare_search_query_GET(  $columns = array(), $model_name = NULL, $external_columns = array()  ) {
        $CI =& wal_get_instance();
        $search_par = array_merge( wal_get_for_log(), wal_post_for_log() );
        $search_par = wmvc_xss_clean( $search_par );
        $smart_search = '';
        if ( isset( $search_par['search'] ) ) {
            $smart_search = sanitize_text_field( $search_par['search']['value'] );
        }
        $available_fields = $CI->{$model_name}->get_available_fields();
        //$table_name = substr($model_name, 0, -2);
        $columns_original = array();
        foreach ( $columns as $key => $val ) {
            $columns_original[$val] = $val;
            // if column contain also "table_name.*"
            $splited = explode( '.', $val );
            if ( wmvc_count( $splited ) == 2 ) {
                $val = $splited[1];
            }
            if ( isset( $available_fields[$val] ) ) {
            } else {
                if ( !in_array( $columns[$key], $external_columns ) ) {
                    unset($columns[$key]);
                }
            }
        }
        if ( wmvc_count( $search_par ) > 0 ) {
            unset($search_par['search']);
            // For quick/smart search
            if ( wmvc_count( $columns ) > 0 && !empty( $smart_search ) ) {
                $gen_q = '';
                foreach ( $columns as $key => $value ) {
                    $value = sanitize_text_field( $value );
                    if ( substr_count( $value, 'id' ) > 0 && is_numeric( $smart_search ) ) {
                        $gen_q .= "{$value} = {$smart_search} OR ";
                    } else {
                        if ( substr_count( $value, 'date' ) > 0 ) {
                            //$gen_search = wmvc_generate_slug($smart_search, ' ');
                            $gen_q .= "{$value} LIKE '%{$smart_search}%' OR ";
                        } else {
                            $gen_q .= "{$value} LIKE '%{$smart_search}%' OR ";
                        }
                    }
                }
                $gen_q = substr( $gen_q, 0, -4 );
                if ( !empty( $gen_q ) ) {
                    $CI->db->where( "({$gen_q})" );
                }
            }
            // For column search
            if ( isset( $search_par['columns'] ) ) {
                $gen_q = '';
                //var_dump($search_par['columns']);
                foreach ( $search_par['columns'] as $key => $row ) {
                    if ( !empty( $row['search']['value'] ) ) {
                        if ( isset( $row['data'] ) && !empty( $row['data'] ) && in_array( $row['data'], $columns ) ) {
                            $col_name = sanitize_text_field( $columns[$key] );
                            if ( isset( $row['data'] ) ) {
                                $col_name = sanitize_text_field( $row['data'] );
                            }
                            if ( substr_count( $row['data'], 'id' ) > 0 && is_numeric( $row['search']['value'] ) ) {
                                // ID is always numeric
                                $gen_q .= $col_name . " = " . sanitize_text_field( $row['search']['value'] ) . " AND ";
                            } else {
                                if ( substr_count( $row['data'], 'date' ) > 0 ) {
                                    // DATE VALUES
                                    $gen_search = $row['search']['value'];
                                    $detect_date = strtotime( $gen_search );
                                    if ( is_numeric( $detect_date ) && $detect_date > 1000 ) {
                                        $gen_search = date( 'Y-m-d H:i:s', $detect_date );
                                        $gen_q .= $col_name . " > '" . $gen_search . "' AND ";
                                    } else {
                                        $gen_q .= $col_name . " LIKE '%" . $gen_search . "%' AND ";
                                    }
                                } else {
                                    if ( substr_count( $row['data'], 'is_' ) > 0 ) {
                                        // CHECKBOXES
                                        if ( $row['search']['value'] == 'on' ) {
                                            $gen_search = 1;
                                            $gen_q .= $col_name . " LIKE '%" . $gen_search . "%' AND ";
                                        } else {
                                            if ( $row['search']['value'] == 'off' ) {
                                                $gen_q .= $col_name . " IS NULL AND ";
                                            }
                                        }
                                    } else {
                                        $gen_q .= $col_name . " LIKE '%" . sanitize_text_field( $row['search']['value'] ) . "%' AND ";
                                    }
                                }
                            }
                        }
                    }
                }
                $gen_q = substr( $gen_q, 0, -5 );
                if ( !empty( $gen_q ) ) {
                    $CI->db->where( "({$gen_q})" );
                }
            }
            // order
            /*
            ["order"]=>
            array(1) {
            	[0]=>
            	array(2) {
            	["column"]=>
            	string(1) "0"
            	["dir"]=>
            	string(4) "desc"
            	}
            }
            */
            if ( isset( $search_par['order'] ) ) {
                foreach ( $search_par['order'] as $order_row ) {
                    $CI->db->order_by( sanitize_sql_orderby( $columns[$order_row['column']] . ' ' . $order_row['dir'] ) );
                }
            }
        }
    }

}
# replace PAPERTRAIL_HOSTNAME and PAPERTRAIL_PORT
# see http://help.papertrailapp.com/ for additional PHP syslog options
function wal_send_remote_syslog(
    $PAPERTRAIL_HOSTNAME,
    $PAPERTRAIL_PORT,
    $message,
    $component = "web",
    $program = "next_big_thing"
) {
    if ( !function_exists( 'socket_create' ) ) {
        return;
    }
    $sock = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
    foreach ( explode( "\n", $message ) as $line ) {
        $syslog_message = "<22>" . date( 'M d H:i:s ' ) . $program . ' ' . $component . ': ' . $line;
        $ret = socket_sendto(
            $sock,
            $syslog_message,
            strlen( $syslog_message ),
            0,
            $PAPERTRAIL_HOSTNAME,
            $PAPERTRAIL_PORT
        );
        //if($ret === FALSE)
        //	echo "error: " . socket_strerror(socket_last_error($sock)) . "\n";
    }
    socket_close( $sock );
}

# send_remote_syslog("Test");
# send_remote_syslog("Any log message");
# send_remote_syslog("Something just happened", "other-component");
# send_remote_syslog("Something just happened", "a-background-job-name", "whatever-app-name");
function wal_remotedb_log(  $row_cloud, $log_message, $insert_array  ) {
    global $wpdb;
    $servername = $row_cloud->host . ':' . $row_cloud->port;
    $username = $row_cloud->database_username;
    $password = $row_cloud->database_password;
    $dbname = $row_cloud->database_name;
    // Create connection
    $conn = mysqli_connect(
        $servername,
        $username,
        $password,
        $dbname
    );
    // Check connection
    if ( !$conn ) {
        //die("Connection failed: " . mysqli_connect_error());
        return;
    }
    // sql to create table
    $table_name = $row_cloud->database_tablename;
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (\r\n\t\t`idlog` int(11) NOT NULL AUTO_INCREMENT,\r\n\t\t`level` int(11) DEFAULT NULL,\r\n\t\t`date` datetime DEFAULT NULL,\r\n\t\t`user_id` int(11) DEFAULT NULL,\r\n\t\t`user_info`text COLLATE utf8_unicode_ci,\r\n\t\t`ip` varchar(160) COLLATE utf8_unicode_ci NULL,\r\n\t\t`page` varchar(160) COLLATE utf8_unicode_ci NULL,\r\n\t\t`request_uri` varchar(160) COLLATE utf8_unicode_ci NULL,\r\n\t\t`action` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,\r\n\t\t`is_favourite` tinyint(1) DEFAULT NULL,\r\n\t\t`request_data` longtext COLLATE utf8_unicode_ci,\r\n\t\t`header_data` text COLLATE utf8_unicode_ci,\r\n\t\t`other_data`text COLLATE utf8_unicode_ci,\r\n\t\t`description`text COLLATE utf8_unicode_ci,\r\n\t\tPRIMARY KEY  (idlog)\r\n\t) {$charset_collate} COMMENT='Winter Activity Log Plugin Data';";
    if ( $conn->query( $sql ) === TRUE ) {
        //echo "Table $table_name created successfully";
    } else {
        //echo "Error creating table: " . $conn->error;
    }
    $values = '';
    foreach ( $insert_array as $key => $row ) {
        $values .= "'" . str_replace( "'", "\\'", $row ) . "',";
    }
    $values = substr( $values, 0, -1 );
    $sql = "INSERT INTO {$table_name} (" . join( ', ', array_keys( $insert_array ) ) . ")\r\n\t\t\tVALUES ({$values})";
    if ( $conn->query( $sql ) === TRUE ) {
        //echo "New record created successfully";
    } else {
        //echo "Error: " . $sql . "<br>" . $conn->error;
    }
    //exit();
    $conn->close();
}

function wal_access_allowed(  $cap  ) {
    $allowed_admins = get_option( 'wal_allowed_admins' );
    if ( wmvc_user_in_role( 'administrator' ) || wmvc_user_in_role( 'super-admin' ) ) {
        if ( is_array( $allowed_admins ) && count( $allowed_admins ) > 0 ) {
            if ( !in_array( get_current_user_id(), $allowed_admins ) ) {
                return false;
            }
        }
        return true;
    }
    $allowed_roles = get_option( 'wal_allowed_roles' );
    if ( is_array( $allowed_roles ) && count( $allowed_roles ) > 0 ) {
        foreach ( $allowed_roles as $key => $val ) {
            if ( wmvc_user_in_role( $key ) ) {
                return true;
            }
        }
    }
    return false;
}

function wal_send_sms(  $phone_number, $message  ) {
    if ( !empty( get_option( 'wal_clickatell_one_api_key' ) ) ) {
        wal_send_sms_one( $phone_number, $message );
    } else {
        if ( !empty( get_option( 'wal_clickatell_http_api_key' ) ) ) {
            wal_send_sms_http( $phone_number, $message );
        } else {
            if ( !empty( get_option( 'wal_smsapicom_http_api_key' ) ) ) {
                wal_sms_send_smsapicom( $phone_number, $message );
            } else {
                if ( !empty( get_option( 'wal_smsto_api_key' ) ) ) {
                    wal_smsto( $phone_number, $message );
                }
            }
        }
    }
}

function wal_get_date() {
    $date_format = get_option( 'date_format' );
    $time_format = get_option( 'time_format' );
    $date = date( "{$date_format} {$time_format}", current_time( 'timestamp' ) );
    return $date;
}

function wal_wp_authenticate(  $username, $password  ) {
    $username = sanitize_user( $username );
    $password = trim( $password );
    // get user by login via the supplied username (form input)
    $user = get_user_by( 'login', $username );
    if ( !isset( $user->user_pass ) ) {
        $user = get_user_by( 'email', $username );
        if ( !isset( $user->user_pass ) ) {
            return NULL;
        }
    }
    if ( !wp_check_password( $password, $user->user_pass, $user->ID ) ) {
        return NULL;
    }
    return $user;
}
