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

<h1><?php echo __('Add/Edit Cloud details','winter-activity-log'); ?></h1>

<form method="post" action="">

<?php //dump($_POST); ?>
<div class="winterlock_wrap">
<?php 

$form->messages();

if(isset($_GET['is_updated']))
{
  echo '<p class="alert alert-success">'.__('Successfuly saved, please test connection because of possible server restrictions', 'wmvc_win').'</p>';
}

if(isset($_GET['subfunc']) && $_GET['subfunc'] == 'sendemail')
{
    $print = $this->report_m->report_sendemail($report->idreport);

    echo '<p class="alert alert-warning">'.esc_html($print).'</p>';
}

//dump($db_data);

?>
</div>


<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Cloud details','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body form-horizontal">

        <?php
          $default_i = '';
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputitle"><?php echo __('Title','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="title" type="text" class="form-control" id="inputtitle" value="<?php echo esc_attr(wmvc_show_data('title', $db_data, $default_i)); ?>" placeholder="<?php echo __('Title','winter-activity-log'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputcomponent"><?php echo __('Component','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="component" type="text" class="form-control" id="inputcomponent" value="<?php echo esc_attr(wmvc_show_data('component', $db_data, $default_i)); ?>" placeholder="<?php echo __('Component','winter-activity-log'); ?>">
                <p><em><?php echo __('Papertrail will categorize it by this value, enter anything','sw_win'); ?></em></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputprogram_name"><?php echo __('Program name','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="program_name" type="text" class="form-control" id="inputprogram_name" value="<?php echo esc_attr(wmvc_show_data('program_name', $db_data, $default_i)); ?>" placeholder="<?php echo __('Program name','winter-activity-log'); ?>">
                <p><em><?php echo __('Papertrail will categorize it by this value, enter anything but wihout spaces','sw_win'); ?></em></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputhost"><?php echo __('Host','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="host" type="text" class="form-control" id="inputhost" value="<?php echo esc_attr(wmvc_show_data('host', $db_data, $default_i)); ?>" placeholder="<?php echo __('Host','winter-activity-log'); ?>">
                <p><em><?php echo __('Can be found in Papartrail app, log destination settings','sw_win'); ?></em></p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputport"><?php echo __('Port','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="port" type="text" class="form-control" id="inputport" value="<?php echo esc_attr(wmvc_show_data('port', $db_data, $default_i)); ?>" placeholder="<?php echo __('Port','winter-activity-log'); ?>">
                <p><em><?php echo __('Can be found in Papartrail app, log destination settings','sw_win'); ?></em></p>
            </div>
        </div>

        </div>
    </div>
</div>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Logging conditions','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body form-horizontal">

        <?php
          $default_i = '';
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputby_user"><?php echo __('By user','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="by_user" type="text" class="form-control" id="inputby_user" value="<?php echo esc_attr(wmvc_show_data('by_user', $db_data, $default_i)); ?>" placeholder="<?php echo __('By user','winter-activity-log'); ?>">
                <p><em><?php echo __('User ID or username','sw_win'); ?></em></p>
            </div>
        </div>

        <?php
          $default_i = '';
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputby_ip"><?php echo __('By IP','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="by_ip" type="text" class="form-control" id="inputby_ip" value="<?php echo esc_attr(wmvc_show_data('by_ip', $db_data, $default_i)); ?>" placeholder="<?php echo __('By IP','winter-activity-log'); ?>">
            </div>
        </div>

        <?php
          $default_i = '';
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputrbylevel"><?php echo __('By level','winter-activity-log'); ?></label>
            <div class="col-sm-10">

            <?php

                $levels_array = $levels = wmvc_show_data('level', $db_data, $default_i);

                if(!is_array($levels))
                    $levels_array = explode(',', $levels);

            ?>

                <div class="checkbox">
                <label>
                    <input name="level[]" type="checkbox" value="1" <?php echo in_array('1', $levels_array)?'checked':''; ?> />
                    1 <em><?php echo __('Most basic activities log, like when someone open some page','winter-activity-log'); ?></em>
                </label>
                </div>
                <div class="checkbox">
                <label>
                    <input name="level[]" type="checkbox" value="2" <?php echo in_array('2', $levels_array)?'checked':''; ?> />
                    2 <em><?php echo __('Something is sent in POST via ajax, sometimes this mean change in database','winter-activity-log'); ?></em>
                </label>
                </div>
                <div class="checkbox">
                <label>
                    <input name="level[]" type="checkbox" value="3" <?php echo in_array('3', $levels_array)?'checked':''; ?> />
                    3 <em><?php echo __('Something general is sent in POST to regular page, mostly this mean change in database','winter-activity-log'); ?></em>
                </label>
                </div>
                <div class="checkbox">
                <label>
                    <input name="level[]" type="checkbox" value="4" <?php echo in_array('4', $levels_array)?'checked':''; ?> />
                    4 <em><?php echo __('Editing known contents like post, page or similar','winter-activity-log'); ?></em>
                </label>
                </div>
                <div class="checkbox">
                <label>
                    <input name="level[]" type="checkbox" value="5" <?php echo in_array('5', $levels_array)?'checked':''; ?> />
                    5 <em><?php echo __('Critical tasks like FAILED login','winter-activity-log'); ?></em>
                </label>
                </div>
            </div>
        </div>

        <?php
          $default_i = '';
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputrequest_uri"><?php echo __('By request uri','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="request_uri" type="text" class="form-control" id="inputrequest_uri" value="<?php echo esc_attr(wmvc_show_data('request_uri', $db_data, $default_i)); ?>" placeholder="<?php echo __('By request uri','winter-activity-log'); ?>">
            </div>
        </div>

        <div class="form-group <?php if ( !winteractivitylog()->is_plan_or_trial('premium') ) echo 'wal-pro'; ?>">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success"><?php echo __('Save cloud config','winter-activity-log'); ?></button>

                <?php if(!is_null($db_data)): ?>
                <a href="<?php menu_page_url( 'wal_cloudintegration', true ); ?>&function=cloud_test&id=<?php echo esc_attr($db_data->idcloud); ?>" class="btn btn-info"><?php echo __('Test connection','winter-activity-log'); ?></a>
                <?php endif; ?>
   
            </div>
        </div>
        </div>
    </div>

    <div class="alert alert-info" role="alert"><?php echo __('Mod php sockets must be enabled on your server php.ini configuration for papertrail cloud logging, and you need to check with your hosting provider if sockets are enabled for your server', 'winter-activity-log'); ?></div>


</div>



</div>
</form>


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
