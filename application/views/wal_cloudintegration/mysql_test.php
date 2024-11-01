<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://listing-themes.com/
 * @since      1.0.0
 *
 * @package    Winter_Activity_Log
 * @subpackage Winter_Activity_Log/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap winterlock_wrap">

<h1><?php echo __('Test Cloud details','winter-activity-log'); ?></h1>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Testing results','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body form-horizontal">

            <?php //dump($db_data); ?>

            <?php

                echo '<pre>';

                if(function_exists('mysqli_connect'))
                {
                    echo __('OK: Function mysqli_connect exists on server','winter-activity-log').'<br />';
                }
                else
                {
                    echo '<span style="color:red;">'.__('ERROR: Function mysqli_connect not found o nserver, contact your server admin','winter-activity-log').'</span><br />';
                }


                $servername = $db_data->host.':'.$db_data->port;
                $username = $db_data->database_username;
                $password = $db_data->database_password;
                $dbname = $db_data->database_name;

                global $wpdb;
            
                // Create connection
                $conn = mysqli_connect($servername, $username, $password, $dbname);
            
                // Check connection
                if (!$conn) {
                    echo '<span style="color:red;">'."Connection failed: " . mysqli_connect_error().'</span><br />';
                }
                else
                {
                    // sql to create table
                
                    $table_name = $db_data->database_tablename;
                
                    $charset_collate = $wpdb->get_charset_collate();
                
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
                        
                    if ($conn->query($sql) === TRUE) {
                        echo '<span style="color:green;">'."Table ".esc_html($table_name)." created successfully".'</span><br />';
                    } else {
                        echo '<span style="color:red;">'."Error creating table: " . esc_html($conn->error).'</span><br />';
                    }

                    $sql = "INSERT INTO $table_name (description)
                            VALUES ('test for database conenction')";
            
                    if ($conn->query($sql) === TRUE) {
                        echo '<span style="color:green;">'."New record created successfully".'</span><br />';
                    } else {
                        echo '<span style="color:red;">'."Error: " . esc_html($sql) . "<br>" . esc_html($conn->error).'</span><br />';
                    }

                }

                $conn->close(); 
                
                echo '</pre>';

            ?>

            <a href="<?php menu_page_url( 'wal_cloudintegration', true ); ?>&function=mysql_edit&id=<?php echo esc_attr($db_data->idcloud); ?>" class="btn btn-info"><?php echo __('Back to MySQL Integration Edit','winter-activity-log'); ?></a>



        </div>
    </div>
</div>





</div>


<?php

wp_enqueue_style('winter-activity-log_basic_wrapper');

?>
<script>

// Generate table
jQuery(document).ready(function($) {


    
});

</script>

<style>


</style>



<?php $this->view('general/footer', $data); ?>
