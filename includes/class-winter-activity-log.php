<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://listing-themes.com/
 * @since      1.0.0
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/includes
 * @author     Sandi Winter <sandi@winter.hr>
 */
class Winter_Activity_Log {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Winter_Activity_Log_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WINTER_ACTIVITY_LOG_VERSION' ) ) {
			$this->version = WINTER_ACTIVITY_LOG_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'winter-activity-log';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_logging_hooks();
		$this->define_plugins_upgrade_hooks();
		
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Winter_Activity_Log_Loader. Orchestrates the hooks of the plugin.
	 * - Winter_Activity_Log_i18n. Defines internationalization functionality.
	 * - Winter_Activity_Log_Admin. Defines all hooks for the admin area.
	 * - Winter_Activity_Log_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The global class holding methods shared across all classes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-winter-activity-log-global.php';

		/**
		 * Contains helper functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/helper-functions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-winter-activity-log-browserdetector.php';
		
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-winter-activity-log-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-winter-activity-log-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-winterlock-review-request.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-winter-activity-log-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-winter-activity-log-public.php';

		/**
		 * The class responsible for defining all actions for logging
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-winter-activity-log-logger.php';

		// Load Winter MVC core
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/Winter_MVC/init.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/dash-widgets/logs-list.php';

		$this->loader = new Winter_Activity_Log_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Winter_Activity_Log_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Winter_Activity_Log_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Winter_Activity_Log_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 1 );

		//add_action( 'wp_ajax_my_action', 'winter_activity_log_action' );
		$this->loader->add_action(
			'wp_ajax_winter_activity_log_action',
			$plugin_admin,
			'winter_activity_log_action'
		);

		//add_action( 'wp_ajax_nopriv_my_action', 'winter_activity_log_action' );
		/*
		$this->loader->add_action(
			'wp_ajax_nopriv_winter_activity_log_action',
			$plugin_admin,
			'winter_activity_log_action'
		);
		*/

		/**
		 * Adding Plugin Admin Menu
		 */
		$this->loader->add_action(
			'admin_menu',
			$plugin_admin,
			'plugin_menu'
		);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Winter_Activity_Log_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Winter_Activity_Log_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Defining all action and filter hooks for logging
	 */
	public function define_logging_hooks() {

		$logging_hooks = new Winter_Activity_Log_Logger( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $logging_hooks, 'init' );
		
	}

	public function define_plugins_upgrade_hooks()
	{
		require_once  plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-winter-activity-log-activator.php';

		$this->loader->add_action( 'plugins_loaded', 'Winter_Activity_Log_Activator', 'plugins_loaded' );
	}

}

