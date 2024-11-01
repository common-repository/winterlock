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

<?php

$str_limit = 100;

$options = '';
$options.= wmvc_btn_delete(admin_url("admin.php?page=wal_history&function=remove&history_id=".intval($form_data->idhistory))).' ';

?>


<div class="wrap winterlock_wrap">

<h1><?php echo __('Activity log','winter-activity-log'); ?></h1>

<?php if($popup == 'ajax'): ?>
<br />
<?php endif; ?>

<div class="winterlock_wrap">
    <div class="clearfix">
        <div class="pull-right right-options">
            <?php echo wp_kses_post($options); ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Saved data','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">

            <?php if(is_object($form_data)): ?>
            <div>  
                <table class="table table-striped">
                <thead> <tr> <th><?php echo __('Variable name','winter-activity-log'); ?></th> <th><?php echo __('Value','winter-activity-log'); ?></th> </tr> </thead>
                <?php 
                $form_data_array = (array) $form_data;
                foreach($form_data_array as $key=>$val): ?>
                <?php if(strpos($key, 'data') !== FALSE)continue; ?>
                <?php if(strpos($key, 'is_favourite') !== FALSE)continue; ?>
                    <tr><th scope="row"><?php echo esc_html($key); ?></th><td>
                    <?php 
                    
                    if($key == 'description')
                    {
                        echo esc_html($val); 
                    }
                    else
                    {
                        echo wp_kses_post(wmvc_character_limiter(strip_tags(wmvc_xss_clean($val)), $str_limit)); 
                    }
                    
                    ?></td></tr>
                <?php endforeach; ?>
                </table>    
            </div>
            <?php endif; ?>

            <?php //dump(unserialize($form_data->request_data)); ?>

            <?php //dump(unserialize($form_data->header_data)); ?>

            <?php //dump(unserialize($form_data->other_data)); ?>

        </div>
    </div>
</div>
<?php if(FALSE):?>
<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('GET data (Usualy part of address query string)','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">
            <?php
                $request_data = unserialize($form_data->request_data);

            ?>

            <?php if(is_array($request_data['GET'])): ?>
            <div>  
                <?php if(count($request_data['GET']) > 0): ?>
                <table class="table table-striped">
                <thead> <tr> <th><?php echo __('Variable name','winter-activity-log'); ?></th> <th><?php echo __('Value','winter-activity-log'); ?></th> </tr> </thead>
                <?php 
                $form_data_array = $request_data['GET'];
                foreach($form_data_array as $key=>$val): ?>
                <?php if(strpos($key, 'data') !== FALSE)continue; ?>
                    <tr><th scope="row"><?php echo esc_html($key); ?></th><td><?php echo wp_kses_post(wmvc_character_limiter(wmvc_xss_clean($val), $str_limit)); ?></td></tr>
                <?php endforeach; ?>
                </table>    
                <?php else: ?>
                <div class="alert alert-info"><?php echo __('No data','winter-activity-log'); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('POST data (Usualy part of form)','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">
            <?php
                $request_data = unserialize($form_data->request_data);

            ?>

            <?php if(is_array($request_data['POST'])): ?>
            <div>  
                <?php if(count($request_data['POST']) > 0): ?>
                <table class="table table-striped">
                <thead> <tr> <th><?php echo __('Variable name','winter-activity-log'); ?></th> <th><?php echo __('Value','winter-activity-log'); ?></th> </tr> </thead>
                <?php 
                $form_data_array = $request_data['POST'];
                foreach($form_data_array as $key=>$val): ?>
                <?php if(strpos($key, 'data') !== FALSE)continue; ?>
                    <?php if($key == 'newcontent'): ?>
                    
                    <tr><th scope="row"><?php echo esc_html($key); ?></th><td><?php echo wp_kses_post(htmlentities($val)); ?></td></tr>
                    
                    <?php else: ?>
                    
                    <tr><th scope="row"><?php echo esc_html($key); ?></th>
                    <td>
                        <?php echo wp_kses_post(wmvc_character_limiter(wmvc_xss_clean($val), 
                                                            $str_limit));
                        ?>
                    </td>
                </tr>
                    
                    <?php endif; ?>
                    <?php endforeach; ?>
                </table>    
                <?php else: ?>
                <div class="alert alert-info"><?php echo __('No data','winter-activity-log'); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('COOKIE data (Part of user browser)','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">
            <?php
                $request_data = unserialize($form_data->request_data);
            ?>

            <?php if(is_array($request_data['COOKIE'])): ?>
            <div>  
                <?php if(count($request_data['COOKIE']) > 0): ?>
                <table class="table table-striped">
                <thead> <tr> <th><?php echo __('Variable name','winter-activity-log'); ?></th> <th><?php echo __('Value','winter-activity-log'); ?></th> </tr> </thead>
                <?php 
                $form_data_array = $request_data['COOKIE'];
                foreach($form_data_array as $key=>$val): ?>
                <?php if(strpos($key, 'data') !== FALSE)continue; ?>
                    <tr><th scope="row"><?php echo esc_html($key); ?></th><td><?php echo wp_kses_post(wmvc_character_limiter(wmvc_xss_clean($val), $str_limit)); ?></td></tr>
                <?php endforeach; ?>
                </table>    
                <?php else: ?>
                <div class="alert alert-info"><?php echo __('No data','winter-activity-log'); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('HEADER data (Part of user request)','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">
            <?php
                $request_data = unserialize($form_data->header_data)
            ?>

            <?php if(is_array($request_data)): ?>
            <div>  
                <?php if(count($request_data) > 0): ?>
                <table class="table table-striped">
                <thead> <tr> <th><?php echo __('Variable name','winter-activity-log'); ?></th> <th><?php echo __('Value','winter-activity-log'); ?></th> </tr> </thead>
                <?php 
                $form_data_array = $request_data;
                foreach($form_data_array as $key=>$val): ?>
                <?php if(strpos($key, 'data') !== FALSE)continue; ?>
                    <tr><th scope="row"><?php echo esc_html($key); ?></th><td><?php echo wp_kses_post(wmvc_character_limiter(wmvc_xss_clean($val), $str_limit)); ?></td></tr>
                <?php endforeach; ?>
                </table>    
                <?php else: ?>
                <div class="alert alert-info"><?php echo __('No data','winter-activity-log'); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('OTHER data','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">
            <?php
                $form_data_array = unserialize($form_data->request_data);

                $request_data = unserialize($form_data->other_data);

                if(isset($form_data_array['REQUEST_METHOD']))
                    $request_data['REQUEST_METHOD'] = $form_data_array['REQUEST_METHOD'];

                if(isset($form_data_array['BODY']))
                    $request_data['BODY'] = $form_data_array['BODY'];

                if(is_array($request_data['BODY']))
                {
                    foreach($request_data['BODY'] as $key=>$val)
                    {
                        $request_data[$key] = $val;
                    }
                }
                
            ?>

            <?php if(is_array($request_data)): ?>
            <div>  
                <?php if(count($request_data) > 0): ?>
                <table class="table table-striped">
                <thead> <tr> <th><?php echo __('Variable name','winter-activity-log'); ?></th> <th><?php echo __('Value','winter-activity-log'); ?></th> </tr> </thead>
                <?php 
                $form_data_array = $request_data;
                foreach($form_data_array as $key=>$val): ?>
                <?php if(strpos($key, 'data') !== FALSE)continue; ?>
                    <tr><th scope="row"><?php echo esc_html($key); ?></th><td><?php echo wp_kses_post(wmvc_character_limiter(wmvc_xss_clean($val), $str_limit)); ?></td></tr>
                <?php endforeach; ?>
                </table>    
                <?php else: ?>
                <div class="alert alert-info"><?php echo __('No data','winter-activity-log'); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php endif;?>
<?php

$sw_log_generate_class = function ($str='') {
    $events = [
        'ajax_request'=>'Ajax request',
        'plugins'=>'Plugins',
        'login_data'=>'Login with data',
        'wp_options'=>'WP Options',
        ];
    foreach ($events as $key => $value) {
        if(stripos($str, $value) !==FALSE) {
            return $key;
        }
    }

    return '';
};

?>
<?php if(is_numeric($form_data->user_id) && $form_data->user_id > 0): ?>
<div class="winterlock_wrap">
    <div class="panel panel-default panel_log">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Track user by ID:','winter-activity-log'); ?> <?php echo wp_kses_post(strip_tags($form_data->user_info)); ?>, <?php echo __('Last 100 logs','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">
            <?php

                $this->db->limit(100);
                $log_data = $this->history_m->get_by(array('user_id'=>$form_data->user_id));

                $user_info = get_userdata($form_data->user_id);
                $avatar = '<span class="dashicons dashicons-before dashicons-admin-users wal-system-icon"></span>';
                if(isset($user_info->ID))
                {
                    $avatar = '<img class="avatar" src="'.esc_url( get_avatar_url( $user_info->ID ) ).'" />';
                }
                
            ?>

            <?php if(is_array($log_data)): ?>
            <div>  
                <?php if(count($log_data) > 0): ?>
                <table class="table table-striped table_log">
                <!--   
                <thead> 
                    <tr>
                        <th><?php echo __('Description','winter-activity-log'); ?></th>
                        <th><?php echo __('IP','winter-activity-log'); ?></th> 
                        <th><?php echo __('Date','winter-activity-log'); ?></th> 
                    </tr>
                </thead>
                -->
                <?php 
                foreach($log_data as $key=>$row): ?>
                    <tr>
                        <td class="event_td <?php echo esc_attr($sw_log_generate_class($row->description));?>">
                            <i class="icon_log" aria-hidden="true"></i>
                            <div class="event_log_description">
                            <a target="_blank" href="<?php echo admin_url("admin.php?page=winteractivitylog&function=edit_log&id=".intval($row->idhistory)); ?>"><?php echo wp_kses_post($row->description); ?></a>
                                <span class="log_alert">
                                    <span class="lga_thumbnail"><?php echo wp_kses_post($avatar);?></span>
                                    <span class="lga_content"><?php echo strip_tags($row->user_info); ?>, <?php echo wp_kses_post($row->description); ?>, <?php echo esc_html__('Request uri','winter-activity-log').' '.$row->request_uri;?></span>
                                    <span class="lga_icons"><i class="icon_log"></i></span>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php
                                $resolved_ip = resolve_ip($row->ip);
                                if(!empty($resolved_ip))
                                    $row->ip=$resolved_ip;
                            ?>
                            <?php echo wp_kses_post($row->ip);?>
                        </td>
                        <th scope="row"><a target="_blank" href="<?php echo admin_url("admin.php?page=winteractivitylog&function=edit_log&id=".$row->idhistory); ?>"><?php echo esc_html($row->date); ?></a></th>
                    </tr>
                <?php endforeach; ?>
                </table>    
                <?php else: ?>
                <div class="alert alert-info"><?php echo __('No data','winter-activity-log'); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php else: ?>
<div class="winterlock_wrap">
    <div class="panel panel-default panel_log">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Track user by IP:','winter-activity-log'); ?> <?php echo wp_kses_post(strip_tags($form_data->ip)); ?>, <?php echo __('Last 100 logs','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">
            <?php

                $this->db->limit(100);
                $log_data = $this->history_m->get_by(array('ip'=>$form_data->ip));
                $avatar = '<span class="dashicons dashicons-before dashicons-admin-users wal-system-icon"></span>';
                
            ?>
            
            <?php if(is_array($log_data)): ?>
            <div>  
                <?php if(count($log_data) > 0): ?>
                <table class="table table-striped table_log">
                <!--   
                <thead> 
                    <tr>
                        <th><?php echo __('Description','winter-activity-log'); ?></th>
                        <th><?php echo __('IP','winter-activity-log'); ?></th> 
                        <th><?php echo __('Date','winter-activity-log'); ?></th> 
                    </tr>
                </thead>
                -->
                <?php 
                foreach($log_data as $key=>$row): ?>
                    <tr>
                        <td class="event_td <?php echo esc_attr($sw_log_generate_class($row->description));?>">
                            <i class="icon_log" aria-hidden="true"></i>
                            <div class="event_log_description">
                                <?php echo wp_kses_post($row->description); ?>
                                <span class="log_alert">
                                    <span class="lga_thumbnail"><?php echo wp_kses_post($avatar);?></span>
                                    <span class="lga_content"><?php echo esc_html(strip_tags($row->user_info)); ?>, <?php echo wp_kses_post($row->description); ?>, <?php echo esc_html__('Request uri','winter-activity-log').' '.$row->request_uri;?></span>
                                    <span class="lga_icons"><i class="icon_log"></i></span>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php
                                $resolved_ip = resolve_ip($row->ip);
                                if(!empty($resolved_ip))
                                    $row->ip=$resolved_ip;
                            ?>
                            <?php echo wp_kses_post($row->ip);?>
                        </td>
                        <th scope="row"><a target="_blank" href="<?php echo admin_url("admin.php?page=winteractivitylog&function=edit_log&id=".$row->idhistory); ?>"><?php echo esc_html($row->date); ?></a></th>
                    </tr>
                <?php endforeach; ?>
                </table>    
                <?php else: ?>
                <div class="alert alert-info"><?php echo __('No data','winter-activity-log'); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php endif; ?>

<?php

wp_enqueue_style('winter-activity-log_basic_wrapper');

?>

<style>

.winterlock_wrap table tr th[scope="row"]{

}

.wrap.winterlock_wrap h1+.winterlock_wrap .right-options
{
    margin-top: -43px;
}

</style>

<script>

// Generate table
jQuery(document).ready(function($) {
    $('a.save_button').click(function(){
                    
        var save_object = $(this);

        // ajax to remove row
        $.post($(this).attr('href'), function( data ) {
            //console.log(data);
            if(save_object.find('i').hasClass('glyphicon-heart-empty'))
            {
                save_object.find('i').removeClass('glyphicon-heart-empty');
                save_object.find('i').addClass('glyphicon-heart');
            }
            else
            {
                save_object.find('i').removeClass('glyphicon-heart');
                save_object.find('i').addClass('glyphicon-heart-empty');
            }

        });

        return false;
    });

    $('a.action_confirm').click(function(){
        return confirm('<?php echo esc_js(__('Are you sure?', 'wmvc_win')); ?>');
    });
});


</script>
</div>

<?php if($popup == 'ajax'): ?>
<br />
<?php endif; ?>