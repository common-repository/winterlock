<?php

/**
 * Fired during plugin activation
 *
 * @link       https://listing-themes.com/
 * @since      1.0.0
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/includes
 * @author     Sandi Winter <sandi@winter.hr>
 */
class Winter_Activity_Log_Activator {

	public static $wal_db_version = 1.5;

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// TODO: Database insert tables etc.

		$prefix = 'wal_';
		
		add_option($prefix.'checkbox_log_plugin_disable', '1');
		add_option($prefix.'checkbox_log_cron_disable', '1');
		add_option($prefix.'checkbox_log_level_1_disable', '1');
		add_option($prefix.'checkbox_log_level_2_disable', '0');
		add_option($prefix.'checkbox_log_level_3_disable', '0');
        add_option($prefix.'checkbox_log_level_3_disable', '0');
        add_option($prefix.'checkbox_disable_multilogin', '0');
		add_option($prefix.'log_days', '7');
		add_option($prefix.'checkbox_hide_ip', '0');
		add_option($prefix.'checkbox_failed_login_block', '0');
		add_option($prefix.'checkbox_enable_winterlock_dash_styles', '0');

		if (! wp_next_scheduled ( 'wal_my_hourly_event' )) {
			wp_schedule_event(time(), 'hourly', 'wal_my_hourly_event');
		 }

	}

	public static function plugins_loaded(){

		if ( get_site_option( 'wal_db_version' ) === false ||
		     get_site_option( 'wal_db_version' ) < self::$wal_db_version ) {
			self::wal_install();
		}

	}


	// https://codex.wordpress.org/Creating_Tables_with_Plugins
	public static function wal_install() {
		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		// For init version 1.0
		if(get_site_option( 'wal_db_version' ) === false)
		{
			
		
			// Main table for logging

			$table_name = $wpdb->prefix . 'wal_log';

			$sql = "CREATE TABLE IF NOT EXISTS $table_name (
				`idlog` int(11) NOT NULL AUTO_INCREMENT,
				`level` int(11) DEFAULT NULL,
				`date` datetime DEFAULT NULL,
				`user_id` int(11) DEFAULT NULL,
				`user_info`text COLLATE utf8_unicode_ci,
				`ip` varchar(160) COLLATE utf8_unicode_ci NULL,
				`page` varchar(160) COLLATE utf8_unicode_ci NULL,
				`request_uri` varchar(160) COLLATE utf8_unicode_ci NULL,
				`action` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				`is_favourite` tinyint(1) DEFAULT NULL,
				`request_data` longtext COLLATE utf8_unicode_ci,
				`header_data` text COLLATE utf8_unicode_ci,
				`other_data`text COLLATE utf8_unicode_ci,
				`description`text COLLATE utf8_unicode_ci,
				PRIMARY KEY  (idlog)
			) $charset_collate COMMENT='Winter Activity Log Plugin Data';";
		
			dbDelta( $sql );

			// Table for control/security data

			$table_name = $wpdb->prefix . 'wal_control';

			$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
				  `idcontrol` int(11) NOT NULL AUTO_INCREMENT,
				  `date` datetime DEFAULT NULL,
				  `title` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `description` text COLLATE utf8_unicode_ci,
				  `is_skip` tinyint(1) DEFAULT NULL,
				  `is_block_enabled` tinyint(1) DEFAULT NULL,
				  `email` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `is_email_enabled` tinyint(1) DEFAULT NULL,
				  PRIMARY KEY  (idcontrol)
				) $charset_collate;";

			dbDelta( $sql );

			// Table for control/security rules

			$table_name = $wpdb->prefix . 'wal_control_rule';

			$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
				  `idcontrol_rule` int(11) NOT NULL AUTO_INCREMENT,
				  `control_id` int(11) NOT NULL,
				  `type` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `operator` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `parameter` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `value` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  PRIMARY KEY  (idcontrol_rule)
				) $charset_collate;";

			dbDelta( $sql );

			// Table for failed attemps

			$table_name = $wpdb->prefix . 'wal_failed_attemps';

			$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
				  `idfailed_attemps` int(11) NOT NULL AUTO_INCREMENT,
				  `date` datetime DEFAULT NULL,
				  `user_id` int(11) DEFAULT NULL,
				  `ip` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `data` text COLLATE utf8_unicode_ci DEFAULT NULL,
				  PRIMARY KEY  (idfailed_attemps)
				) $charset_collate;";

			dbDelta( $sql );

			// Table for reports

			$table_name = $wpdb->prefix . 'wal_report';

			$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
				  `idreport` int(11) NOT NULL AUTO_INCREMENT,
				  `date` datetime DEFAULT NULL,
				  `report_name` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `report_email` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `scheduling_period` int(11) DEFAULT NULL COMMENT 'days',
				  `format` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `by_user` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `by_ip` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `level` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `request_uri` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `date_start` datetime DEFAULT NULL,
				  `date_end` datetime DEFAULT NULL,
				  `date_sent` datetime DEFAULT NULL,
				  PRIMARY KEY  (idreport)
				) $charset_collate;";

			dbDelta( $sql );

			// Table for cloud

			$table_name = $wpdb->prefix . 'wal_cloud';

			$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
				  `idcloud` int(11) NOT NULL AUTO_INCREMENT,
				  `date` datetime DEFAULT NULL,
				  `title` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `component` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `program_name` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `host` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `port` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `by_user` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `by_ip` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `level` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `request_uri` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
				  `other_data` text COLLATE utf8_unicode_ci,
				  PRIMARY KEY  (idcloud)
				) $charset_collate;";

			dbDelta( $sql );

			update_option( 'wal_db_version', "1" );
		}

		// [For init version 1.1]

		// Table for reports

		$table_name = $wpdb->prefix . 'wal_report';

/*
		$myTable = $wpdb->get_row("SELECT * FROM $table_name");


		//Add column if not present.
		if(isset($myTable->by_description))
		{

		}
		else
		{
			$wpdb->query("ALTER TABLE `$table_name` ADD `by_description` VARCHAR(160) NULL ;");
		}
*/

		//Add column if not present.
		$columns = $wpdb->get_results("SHOW COLUMNS FROM ".$table_name." WHERE Field = 'by_description'");

		if(!isset($columns[0]))
		{
			$wpdb->query("ALTER TABLE `$table_name` ADD `by_description` VARCHAR(160) NULL ;");
		}

		// [For init version 1.1]

		// Table for control security

		$table_name = $wpdb->prefix . 'wal_control';

		$columns = $wpdb->get_results("SHOW COLUMNS FROM ".$table_name." WHERE Field = 'is_sms_enabled'");

		if(!isset($columns[0]))
		{
			$wpdb->query("ALTER TABLE `$table_name` ADD `is_sms_enabled` tinyint(1) DEFAULT NULL ;");
		}

		$columns = $wpdb->get_results("SHOW COLUMNS FROM ".$table_name." WHERE Field = 'phone'");

		if(!isset($columns[0]))
		{
			$wpdb->query("ALTER TABLE `$table_name` ADD `phone` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL ;");
		}

		// [For init version 1.2]

		// Table for cloud logging

		$table_name = $wpdb->prefix . 'wal_cloud';

		$columns = $wpdb->get_results("SHOW COLUMNS FROM ".$table_name." WHERE Field = 'database_name'");

		if(!isset($columns[0]))
		{
			$wpdb->query("ALTER TABLE `$table_name` ADD `database_name` VARCHAR(160) NULL , ADD `database_tablename` VARCHAR(160) NULL , ADD `database_username` VARCHAR(160) NULL , ADD `database_password` VARCHAR(160) NULL ;");
		}

		// [For init version 1.3]

		// History table

		$table_name = $wpdb->prefix . 'wal_history';

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			`idhistory` int(11) NOT NULL AUTO_INCREMENT,
			`level` int(11) DEFAULT NULL,
			`date` datetime DEFAULT NULL,
			`user_id` int(11) DEFAULT NULL,
			`user_info`text COLLATE utf8_unicode_ci,
			`ip` varchar(160) COLLATE utf8_unicode_ci NULL,
			`page` varchar(160) COLLATE utf8_unicode_ci NULL,
			`request_uri` varchar(160) COLLATE utf8_unicode_ci NULL,
			`action` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
			`is_favourite` tinyint(1) DEFAULT NULL,
			`request_data` longtext COLLATE utf8_unicode_ci,
			`header_data` text COLLATE utf8_unicode_ci,
			`other_data`text COLLATE utf8_unicode_ci,
			`description`text COLLATE utf8_unicode_ci,
			PRIMARY KEY  (idhistory)
		) $charset_collate COMMENT='Winter Activity Log Plugin Data';";
	
        dbDelta( $sql );

        // [For init version 1.5]

        // Sessions table

        $table_name = $wpdb->prefix . 'wal_sessions';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            `idsessions` int(11) NOT NULL AUTO_INCREMENT,
            `time_start` datetime DEFAULT NULL,
            `time_end` datetime DEFAULT NULL,
            `time_sec_total` int(11) DEFAULT NULL,
            `user_id` int(11) DEFAULT NULL,
            `user_info`text COLLATE utf8_unicode_ci,
            `ip` varchar(160) COLLATE utf8_unicode_ci NULL,
            `is_visit_end` tinyint(1) DEFAULT NULL,
            `other_data` text COLLATE utf8_unicode_ci,
            PRIMARY KEY  (idsessions)
        ) $charset_collate;";
    
        dbDelta( $sql );
	
		update_option( 'wal_db_version', self::$wal_db_version );
	}

	// TODO: if need to install some default data
	function wal_install_data() {
		global $wpdb;
		
		$welcome_name = 'Mr. WordPress';
		$welcome_text = 'Congratulations, you just completed the installation!';
		
		$table_name = $wpdb->prefix . 'wal_log';
		
		$wpdb->insert( 
			$table_name, 
			array( 
				'time' => current_time( 'mysql' ), 
				'name' => $welcome_name, 
				'text' => $welcome_text, 
			) 
		);
	}

}
