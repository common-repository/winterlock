<?php

add_action('wp_dashboard_setup', array('Wal_Dashboard_Widget_List','init') );

class Wal_Dashboard_Widget_List {

    /**
     * The id of this widget.
     */
    const wid = 'wal_latest_activity_logs_widget';

    /**
     * Hook to wp_dashboard_setup to add the widget.
     */
    public static function init() {
        //Register widget settings...
        self::update_dashboard_widget_options(
            self::wid,                                  //The  widget id
            array(                                      //Associative array of options & default values
                'example_number' => 42,
            ),
            true                                        //Add only (will not update existing options)
        );

        if(get_option('wal_checkbox_disable_dashwidgets') > 0){

        }
        else
        {
            //Register the widget...
            wp_add_dashboard_widget(
                self::wid,                                  //A unique slug/ID
                __( 'Latest Activity Logs', 'winter-activity-log' ),//Visible name for the widget
                array('Wal_Dashboard_Widget_List','widget'),      //Callback for the main widget content
                array('Wal_Dashboard_Widget_List','config')       //Optional callback for widget configuration content
            );
        }

    }

    /**
     * Load the widget code
     */
    public static function widget() 
    {
        global $Winter_MVC;

        $output = '';
        $atts = array();
        $instance = NULL;

		$page = 'wal_dashwidgets';
		$function = 'logs_list';

        $Winter_MVC = new MVC_Loader(plugin_dir_path( __FILE__ ).'../../');
		$Winter_MVC->load_helper('basic');
		$Winter_MVC->load_controller($page, $function, array());
    }

    /**
     * Load widget config code.
     *
     * This is what will display when an admin clicks
     */
    public static function config() {
        wp_redirect(admin_url("admin.php?page=wal_settings")); exit;
    }

    /**
     * Gets the options for a widget of the specified name.
     *
     * @param string $widget_id Optional. If provided, will only get options for the specified widget.
     * @return array An associative array containing the widget's options and values. False if no opts.
     */
    public static function get_dashboard_widget_options( $widget_id='' )
    {
        //Fetch ALL dashboard widget options from the db...
        $opts = get_option( 'dashboard_widget_options' );

        //If no widget is specified, return everything
        if ( empty( $widget_id ) )
            return $opts;

        //If we request a widget and it exists, return it
        if ( isset( $opts[$widget_id] ) )
            return $opts[$widget_id];

        //Something went wrong...
        return false;
    }

    /**
     * Gets one specific option for the specified widget.
     * @param $widget_id
     * @param $option
     * @param null $default
     *
     * @return string
     */
    public static function get_dashboard_widget_option( $widget_id, $option, $default=NULL ) {

        $opts = self::get_dashboard_widget_options($widget_id);

        //If widget opts dont exist, return false
        if ( ! $opts )
            return false;

        //Otherwise fetch the option or use default
        if ( isset( $opts[$option] ) && ! empty($opts[$option]) )
            return $opts[$option];
        else
            return ( isset($default) ) ? $default : false;

    }

    /**
     * Saves an array of options for a single dashboard widget to the database.
     * Can also be used to define default values for a widget.
     *
     * @param string $widget_id The name of the widget being updated
     * @param array $args An associative array of options being saved.
     * @param bool $add_only If true, options will not be added if widget options already exist
     */
    public static function update_dashboard_widget_options( $widget_id , $args=array(), $add_only=false )
    {
        //Fetch ALL dashboard widget options from the db...
        $opts = get_option( 'dashboard_widget_options' );

        //Get just our widget's options, or set empty array
        $w_opts = ( isset( $opts[$widget_id] ) ) ? $opts[$widget_id] : array();

        if ( $add_only ) {
            //Flesh out any missing options (existing ones overwrite new ones)
            $opts[$widget_id] = array_merge($args,$w_opts);
        }
        else {
            //Merge new options with existing ones, and add it back to the widgets array
            $opts[$widget_id] = array_merge($w_opts,$args);
        }

        //Save the entire widgets array back to the db
        return update_option('dashboard_widget_options', $opts);
    }

}

?>