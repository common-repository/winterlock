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

                if(function_exists('socket_create'))
                {
                    echo __('OK: Function socket_create exists on server','winter-activity-log').'<br />';
                }
                else
                {
                    echo '<span style="color:red;">'.__('ERROR: Function socket_create not found o nserver, contact your server admin','winter-activity-log').'</span><br />';
                }

                $PAPERTRAIL_HOSTNAME = $db_data->host;
                $PAPERTRAIL_PORT = $db_data->port;
                $message = 'Test message, '.date('l jS \of F Y h:i:s A');
                $component = $db_data->component;
                $program = $db_data->program_name;

                if(function_exists('socket_create'))
                {
                    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                    foreach(explode("\n", $message) as $line) {
                        $syslog_message = "<22>" . date('M d H:i:s ') . $program . ' ' . $component . ': ' . $line;
                        $ret = socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, $PAPERTRAIL_HOSTNAME, $PAPERTRAIL_PORT);
                
                        if($ret === FALSE)
                        {
                            echo '<span style="color:red;">'.__('ERROR: ') . esc_html(socket_strerror(socket_last_error($sock))).'</span><br />';
                        }
                        else
                        {
                            echo '<span style="color:green;">'.__('OK: Message sent successfuly: ','winter-activity-log').esc_html($message).'</span><br />';
                        }
                        	
                    }
                    socket_close($sock);
                }

                
                echo '</pre>';

            ?>

            <div class="alert alert-warning" role="alert"><?php echo __('If you doesn\'t receive message, very usual server blocking such request, so check with server admin to open this port for socket connection: ', 'winter-activity-log').esc_html($PAPERTRAIL_HOSTNAME).':'.esc_html($PAPERTRAIL_PORT); ?></div>


            <a href="<?php menu_page_url( 'wal_cloudintegration', true ); ?>&function=cloud_edit&id=<?php echo esc_attr($db_data->idcloud); ?>" class="btn btn-info"><?php echo __('Back to Cloud Integration Edit','winter-activity-log'); ?></a>



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
