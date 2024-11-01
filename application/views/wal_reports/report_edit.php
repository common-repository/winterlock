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

<h1><?php echo __('Add/Edit report','winter-activity-log'); ?></h1>

<form method="post" action="">

<?php //dump($_POST); ?>
<div class="winterlock_wrap">
<?php 

$form->messages();

if(isset($_GET['is_updated']))
{
  echo '<p class="alert alert-success">'.__('Successfuly saved', 'wmvc_win').'</p>';
}

if(isset($_GET['subfunc']) && $_GET['subfunc'] == 'sendemail')
{
    $print = $this->report_m->report_sendemail($_GET['id']);

    echo '<p class="alert alert-warning">'.wp_kses_post($print).'</p>';
}

//dump($db_data);

?>
</div>


<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Report details','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body form-horizontal">

        <?php
          $default_i = '';
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputreport_name"><?php echo __('Report name','winter-activity-log'); ?>*</label>
            <div class="col-sm-10">
                <input name="report_name" type="text" class="form-control" id="inputreport_name" value="<?php echo esc_attr(wmvc_show_data('report_name', $db_data, $default_i)); ?>" placeholder="<?php echo __('Report name','winter-activity-log'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputreport_email"><?php echo __('Report email','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="report_email" type="text" class="form-control" id="inputreport_email" value="<?php echo esc_attr(wmvc_show_data('report_email', $db_data, $default_i)); ?>" placeholder="<?php echo __('Report email','winter-activity-log'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputscheduling_period"><?php echo __('Scheduling period','winter-activity-log'); ?></label>
            <div class="col-sm-10">

            <?php 
            
            //wmvc_show_data('report_email', $db_data, $default_i);
            $options_array = array(
                '0' => __('Scheduling disabled','winter-activity-log'),
                '1' => __('Daily','winter-activity-log'),
                '7' => __('7 days','winter-activity-log'),
                '30' => __('30 days','winter-activity-log'),
                '180' => __('180 days','winter-activity-log'),
                '365' => __('365 days','winter-activity-log')
              );

            $allowed_html = array(
                'input' => array(
                    'type'      => array(),
                    'name'      => array(),
                    'id'     => array(),
                    'value'     => array(),
                    'checked'   => array()
                ),
                'label' => array(),
                'div' => array(
                    'class'      => array(),
                ),
            );
            
            echo wp_kses(wmvc_select_radio('scheduling_period', $options_array, wmvc_show_data('scheduling_period', $db_data, $default_i)), $allowed_html); 
            ?>

            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputformat"><?php echo __('Format','winter-activity-log'); ?></label>
            <div class="col-sm-10">
            <?php 
            
            //wmvc_show_data('report_email', $db_data, $default_i);
            $options_array = array(
                'csv' => __('CSV (like excel)','winter-activity-log'),
                'html' => __('HTML','winter-activity-log'),
                'json' => __('JSON','winter-activity-log'),
                'xml' => __('XML','winter-activity-log')
              );
            
            echo wp_kses(wmvc_select_radio('format', $options_array, wmvc_show_data('format', $db_data, 'csv')), $allowed_html); 
            ?>
            </div>
        </div>
        </div>
    </div>
</div>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Report conditions','winter-activity-log'); ?></h3>
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
            <label class="col-sm-2 control-label" for="inputby_description"><?php echo __('By description','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <input name="by_description" type="text" class="form-control" id="inputby_description" value="<?php echo esc_attr(wmvc_show_data('by_description', $db_data, $default_i)); ?>" placeholder="<?php echo __('By description','winter-activity-log'); ?>">
                <p><em><?php echo __('Part of description','sw_win'); ?></em></p>
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

        <?php
          $default_i = '';
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputdate_start"><?php echo __('Start date','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                <div id="datetimepicker-from" class="input-group date datetimepicker">
                    <input value="<?php 

                    echo esc_attr(wmvc_show_data('date_start', $db_data, $default_i)); 
                    
                    ?>" id="date_start" name="date_start" type="text" class="form-control" placeholder="<?php echo __('Start date','winter-activity-log'); ?>">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                
                <p><em><?php echo __('This should be empty in case of scheduling report','sw_win'); ?></em></p>
            </div>
        </div>

        <?php
          $default_i = '';
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputdate_end"><?php echo __('End date','winter-activity-log'); ?></label>
            <div class="col-sm-10">
                
                <div id="datetimepicker-to" class="input-group date datetimepicker">
                    <input value="<?php 

                    echo esc_attr(wmvc_show_data('date_end', $db_data, $default_i)); 
                    
                    ?>" id="date_end" name="date_end" type="text" class="form-control" placeholder="<?php echo __('End date','winter-activity-log'); ?>">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                
                <p><em><?php echo __('This should be empty in case of scheduling report','sw_win'); ?></em></p>
            </div>
        </div>

        <div class="form-group <?php if ( !winteractivitylog()->is_plan_or_trial('standard') ) echo 'wal-pro'; ?>">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success"><?php echo __('Save report','winter-activity-log'); ?></button>

                <?php if(isset($_GET['id']) && $_GET['id'] != 0): ?>
                    <a target="_blank" href="<?php echo admin_url("admin.php?page=wal_reports&function=report_download&id=".wmvc_xss_clean($_GET['id'])); ?>" class="btn btn-info"><?php echo __('Download report','winter-activity-log'); ?></a>
                    <a id="send_email" href="<?php echo admin_url("admin.php?page=wal_reports&function=report_edit&subfunc=sendemail&id=".wmvc_xss_clean($_GET['id'])); ?>" class="btn btn-warning"><?php echo __('Send email','winter-activity-log'); ?></a>
                    <?php 
                    $last_sent = wmvc_show_data('date_sent', $db_data, '');
                    
                    if(!empty($last_sent))
                        echo '('.__('Last sent:','sw_win').' '.esc_html(wmvc_show_data('date_sent', $db_data, '')).')'; 
                    ?>

                <?php endif; ?>
            </div>
        </div>
        </div>
    </div>

    <div class="alert alert-info" role="alert"><?php echo __('Before download or sending email you need to save report', 'winter-activity-log'); ?></div>


</div>



</div>
</form>


<?php

wp_enqueue_style('winter-activity-log_basic_wrapper');

?>
<script>

// Generate table
jQuery(document).ready(function($) {
	if (jQuery('.datetimepicker').length) {
		jQuery('.datetimepicker').datetimepicker({
			format: 'YYYY-MM-DD HH:mm:ss',
			useCurrent: 'hour',
			//hour : '12',
			stepping: 30,
            locale:'<?php echo get_user_locale();?>'
		});

	}

    $('a#send_email').on('click', function()
    {

        if($('#inputreport_email').val() == '')
        {
            alert('<?php echo __('Please enter email','sw_win'); ?>');
            return false;
        }

    });





    
});

</script>

<style>


</style>



<?php

wp_enqueue_script( 'datetime-picker-moment' );
wp_enqueue_script( 'datetime-picker-bootstrap' );
wp_enqueue_style( 'datetime-picker-css' );

?>

<?php $this->view('general/footer', $data); ?>
