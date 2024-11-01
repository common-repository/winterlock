<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://activity-log.com/
 * @since             1.0.4
 * @package           Winter_Activity_Log
 *
 * @wordpress-plugin
 * Plugin Name:       Activity Log WinterLock
 * Plugin URI:        https://activity-log.com/
 * Description:       Most detailed WP System Log / Activity Log with User Tracking, Immediate Logout and Requests Control based on specific criteria, lock, block, email alert, hide etc.
 * Version:           1.2.4
 * Author:            SWIT
 * Author URI:        https://swit.hr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       winter-activity-log
 * Domain Path:       /languages
 *
 * 
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
$time_before = microtime( true );
if ( !function_exists( 'activate_winter_activity_log' ) ) {
    /**
     * Currently plugin version.
     * Start at version 1.0.0 and use SemVer - https://semver.org
     * Rename this for your plugin and update it as you release new versions.
     */
    define( 'WINTER_ACTIVITY_LOG_VERSION', '1.2.3' );
    define( 'WINTER_ACTIVITY_LOG_NAME', 'winter-activity-log' );
    define( 'WINTER_ACTIVITY_LOG_PATH', plugin_dir_path( __FILE__ ) );
    define( 'WINTER_ACTIVITY_LOG_URL', plugin_dir_url( __FILE__ ) );
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-winter-activity-log-activator.php
     */
    function activate_winter_activity_log() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-winter-activity-log-activator.php';
        Winter_Activity_Log_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-winter-activity-log-deactivator.php
     */
    function deactivate_winter_activity_log() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-winter-activity-log-deactivator.php';
        Winter_Activity_Log_Deactivator::deactivate();
    }

    register_activation_hook( __FILE__, 'activate_winter_activity_log' );
    register_deactivation_hook( __FILE__, 'deactivate_winter_activity_log' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-winter-activity-log.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_winter_activity_log() {
        $plugin = new Winter_Activity_Log();
        $plugin->run();
    }

    // [Freemius]
    if ( function_exists( 'winteractivitylog' ) ) {
        winteractivitylog()->set_basename( false, __FILE__ );
    } else {
        if ( !function_exists( 'winteractivitylog' ) ) {
            // Create a helper function for easy SDK access.
            function winteractivitylog() {
                global $winteractivitylog;
                if ( !isset( $winteractivitylog ) ) {
                    // Include Freemius SDK.
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                    $winteractivitylog = fs_dynamic_init( array(
                        'id'             => '5253',
                        'slug'           => 'winterlock',
                        'type'           => 'plugin',
                        'public_key'     => 'pk_453c5698d371cb2dcc7a41e27cfb8',
                        'is_premium'     => false,
                        'has_addons'     => false,
                        'has_paid_plans' => true,
                        'trial'          => array(
                            'days'               => 30,
                            'is_require_payment' => false,
                        ),
                        'menu'           => array(
                            'slug' => 'winteractivitylog',
                        ),
                        'anonymous_mode' => !file_exists( WINTER_ACTIVITY_LOG_PATH . 'premium_functions.php' ),
                        'is_live'        => true,
                    ) );
                }
                return $winteractivitylog;
            }

            // Init Freemius.
            winteractivitylog();
            // Signal that SDK was initiated.
            do_action( 'winteractivitylog_loaded' );
        }
        // ... Your plugin's main file logic ...
        run_winter_activity_log();
    }
    function winteractivitylog_custom_connect_message_on_update(
        $message,
        $user_first_name,
        $plugin_title,
        $user_login,
        $site_link,
        $freemius_link
    ) {
        return sprintf(
            __( 'Hey %1$s' ) . ',<br>' . __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'winterlock' ),
            $user_first_name,
            '<b>' . $plugin_title . '</b>',
            '<b>' . $user_login . '</b>',
            $site_link,
            $freemius_link
        );
    }

    winteractivitylog()->add_filter(
        'connect_message_on_update',
        'winteractivitylog_custom_connect_message_on_update',
        10,
        6
    );
    // [/Freemius]
}
$time_executing = microtime( true ) - $time_before;
//echo '<!-- TimeWinterLock: '.esc_html($time_executing).' -->';