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

$enabled_GENERAL = array('level', 'user_id', 'ip', 'page', 'action', 'request_uri', 'REQUEST_METHOD', 'User-Agent', 'status', 'title', 'excerpt', 'content', 'id');

$skip_ANY = array('testcookie', 'redirect_to');

$i_fieldnum = 0;

$val = '';
$event_data = false;

if(isset($_GET) && isset($_GET['code']) && !empty($_GET['code']))
{
    $sw_wal_log_generate_events_list = sw_wal_log_generate_events_list();

    if(!empty($sw_wal_log_generate_events_list['sw_code_'.sanitize_text_field(wmvc_xss_clean($_GET['code']))])){
        $event_data = $sw_wal_log_generate_events_list['sw_code_'.sanitize_text_field(wmvc_xss_clean($_GET['code']))];
    }

}

?>


<div class="wrap winterlock_wrap">

<h1><?php echo __('Security / Control','winter-activity-log'); ?></h1>

<form method="post" action="">

<?php //dump($_POST); ?>
<div class="winterlock_wrap">
<?php 

$form->messages();

if(isset($_GET['is_updated']))
{
  echo '<p class="alert alert-success">'.__('Successfuly saved', 'wmvc_win').'</p>';
}

?>
</div>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Control description','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">
        <?php
          $default_i = '';

          if(isset($_GET['subfunction']) && $_GET['subfunction']=='hide')
          {
            $default_i = __('Hide log','winter-activity-log');
          }
          elseif(isset($_GET['subfunction']) && $_GET['subfunction']=='block')
          {
            $default_i = __('Control log','winter-activity-log');
          }
        ?>
        <div class="form-group">
          <label for="inputTitle"><?php echo __('Title','winter-activity-log'); ?></label>
          <input name="title" type="text" class="form-control" id="inputTitle" value="<?php echo esc_attr(wmvc_show_data('title', $db_data, $default_i)); ?>" placeholder="<?php echo __('Title','winter-activity-log'); ?>">
        </div>

        <?php
          $default_i = '';

          if(isset($_GET['subfunction']) && $_GET['subfunction']=='hide')
          {
            $default_i = __('Hide log by criteria','winter-activity-log').' '.(isset($log_data->request_uri)?$log_data->request_uri:'');
          }
          elseif(isset($_GET['subfunction']) && $_GET['subfunction']=='block')
          {
            $default_i = __('Control log by criteria','winter-activity-log').' '.(isset($log_data->request_uri)?$log_data->request_uri:'');
          }
          if($event_data){
              $default_i = '#'.$event_data['code'].' '.$event_data['description'];
          }
        ?>
        <div class="form-group">
          <label for="inputDescription"><?php echo __('Description','winter-activity-log'); ?></label>
          <input name="description" type="text" class="form-control" id="inputDescription" value="<?php echo esc_attr(wmvc_show_data('description', $db_data, $default_i)); ?>" placeholder="<?php echo __('Description','winter-activity-log'); ?>">
        </div>

        </div>
    </div>
</div>


<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('If Request meet rules:','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body control-container">

<?php if( (!isset($_POST) || count($_POST) == 0) && is_object($log_data)): ?>
            
            <?php if(is_object($log_data)): ?>
            <div>  

               <?php 
                $log_data_array = (array) $log_data;
                foreach($log_data_array as $key=>$val): ?>
                <?php if(strpos($key, 'data') !== FALSE || !in_array($key, $enabled_GENERAL) || empty($val))continue;$i_fieldnum++; 
                
                if($val == 'DISABLED' || $val == 'DISABLED_BY_SECURITY')
                  $val = '';
                
                ?>

<div class="form-inline control-box">
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>
    <select class="form-control" name="<?php echo 'control_type_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="POST">POST</option>
          <option value="GET">GET</option>
          <option value="HEADER">HEADER</option>
          <option value="GENERAL" selected>GENERAL</option>
    </select>

    </div>

    <div class="form-group">
        <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
        <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="<?php echo esc_attr($key); ?>" />
    </div>
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>

    <select class="form-control" name="<?php echo 'control_operator_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="CONTAINS" selected>CONTAINS</option>
          <option value="NOT_CONTAINS">NOT_CONTAINS</option>
    </select>

  </div>

  <div class="form-group">
    <label><?php echo __('Value:','winter-activity-log'); ?> </label>
    <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_value_'.$i_fieldnum, $db_data, $val)); ?>" />
  </div>
  <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
  <hr />
</div>

<?php endforeach; ?>

<?php
    $log_data_array = unserialize($log_data->request_data);

    $request_data = unserialize($log_data->other_data);

    if(isset($log_data_array['REQUEST_METHOD']))
        $request_data['REQUEST_METHOD'] = $log_data_array['REQUEST_METHOD'];

    if(isset($log_data_array['BODY']))
        $request_data['BODY'] = $log_data_array['BODY'];

    if(isset($request_data['BODY']))
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
    <?php 
    $log_data_array = $request_data;
    foreach($log_data_array as $key=>$val): ?>
    <?php if(strpos($key, 'data') !== FALSE || !in_array($key, $enabled_GENERAL))continue;$i_fieldnum++; 
    
    if($val == 'DISABLED' || $val == 'DISABLED_BY_SECURITY')
      $val = '';
    
    ?>
<div class="form-inline control-box">
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>
    <select class="form-control" name="<?php echo 'control_type_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="POST">POST</option>
          <option value="GET">GET</option>
          <option value="HEADER">HEADER</option>
          <option value="GENERAL" selected>GENERAL</option>
    </select>

    </div>

    <div class="form-group">
        <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
        <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="<?php echo esc_attr($key); ?>" />
    </div>
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>

    <select class="form-control" name="<?php echo 'control_operator_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="CONTAINS" selected>CONTAINS</option>
          <option value="NOT_CONTAINS">NOT_CONTAINS</option>
    </select>

  </div>

  <div class="form-group">
    <label><?php echo __('Value:','winter-activity-log'); ?> </label>
    <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_value_'.$i_fieldnum, $db_data, $val)); ?>" />
  </div>
  <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
  <hr />
</div>
    <?php endforeach; ?>

</div>
<?php endif; ?>

<?php
    $request_data = unserialize($log_data->request_data);
?>

<?php if(is_array($request_data['GET'])): ?>
<div>  

   <?php 
    $log_data_array = $request_data['GET'];
    foreach($log_data_array as $key=>$val): ?>
    <?php if(strpos($key, 'data') !== FALSE)continue;$i_fieldnum++; 
    
    if($val == 'DISABLED' || $val == 'DISABLED_BY_SECURITY')
    $val = '';

    ?>
<div class="form-inline control-box">
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>
    <select class="form-control" name="<?php echo 'control_type_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="POST">POST</option>
          <option value="GET" selected>GET</option>
          <option value="HEADER">HEADER</option>
          <option value="GENERAL">GENERAL</option>
    </select>

    </div>

    <div class="form-group">
        <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
        <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="<?php echo esc_attr($key); ?>" />
    </div>
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>

    <select class="form-control" name="<?php echo 'control_operator_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="CONTAINS" selected>CONTAINS</option>
          <option value="NOT_CONTAINS">NOT_CONTAINS</option>
    </select>

  </div>

  <div class="form-group">
    <label><?php echo __('Value:','winter-activity-log'); ?> </label>
    <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_value_'.$i_fieldnum, $db_data, $val)); ?>" />
  </div>
  <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
  <hr />
</div>
    <?php endforeach; ?>

</div>
<?php endif; ?>

<?php
    $request_data = unserialize($log_data->request_data);
?>

<?php if(is_array($request_data['POST'])): ?>
<div>  
   <?php 
    $log_data_array = $request_data['POST'];
    foreach($log_data_array as $key=>$val): ?>
    <?php if(strpos($key, 'data') !== FALSE || in_array($key, $skip_ANY))continue;$i_fieldnum++; 
    
    if($val == 'DISABLED' || $val == 'DISABLED_BY_SECURITY')
    $val = '';

    if(is_array($val))$val = '';
    
    ?>

<div class="form-inline control-box">
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>
    <select class="form-control" name="<?php echo 'control_type_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="POST" selected>POST</option>
          <option value="GET">GET</option>
          <option value="HEADER">HEADER</option>
          <option value="GENERAL">GENERAL</option>
    </select>

    </div>

    <div class="form-group">
        <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
        <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="<?php echo esc_attr($key); ?>" />
    </div>
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>

    <select class="form-control" name="<?php echo 'control_operator_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="CONTAINS" selected>CONTAINS</option>
          <option value="NOT_CONTAINS">NOT_CONTAINS</option>
    </select>

  </div>

  <div class="form-group">
    <label><?php echo __('Value:','winter-activity-log'); ?> </label>
    <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_value_'.$i_fieldnum, $db_data, $val)); ?>" />
  </div>
  <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
  <hr />
</div>

    <?php endforeach; ?>

</div>
<?php endif; ?>



<?php
    $request_data = unserialize($log_data->header_data)
?>

<?php if(is_array($request_data)): ?>
<div>  

   <?php 
    $log_data_array = $request_data;
    foreach($log_data_array as $key=>$val): ?>
    <?php if(strpos($key, 'data') !== FALSE || !in_array($key, $enabled_GENERAL))continue;$i_fieldnum++; ?>
<div class="form-inline control-box">
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>
    <select class="form-control" name="<?php echo 'control_type_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="POST">POST</option>
          <option value="GET">GET</option>
          <option value="HEADER" selected>HEADER</option>
          <option value="GENERAL">GENERAL</option>
    </select>

    </div>

    <div class="form-group">
        <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
        <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="<?php echo esc_attr($key); ?>" />
    </div>
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>

    <select class="form-control" name="<?php echo 'control_operator_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="CONTAINS" selected>CONTAINS</option>
          <option value="NOT_CONTAINS">NOT_CONTAINS</option>
    </select>

  </div>

  <div class="form-group">
    <label><?php echo __('Value:','winter-activity-log'); ?> </label>
    <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_value_'.$i_fieldnum, $db_data, $val)); ?>" />
  </div>
  <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
  <hr />
</div>
    <?php endforeach; ?>



</div>
<?php endif; ?>

            </div>
            <?php endif; ?>

<?php elseif( (!isset($_POST) || count($_POST) == 0) && is_array($db_data) ): ?>


<?php

//dump($db_data);

foreach($db_data as $key=>$post_val): 

if( substr($key, 0, strlen('control_type_')) != 'control_type_' )continue;

$i_fieldnum = substr($key, -1, 1);

if(!is_numeric($i_fieldnum))continue;

if(empty(wmvc_show_data('control_type_'.$i_fieldnum, $db_data, $val)))continue;

?>

<div class="form-inline control-box">
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>

    <?php 

    $options_array = array(
      '' => '',
      'POST' => 'POST',
      'GET' => 'GET',
      'HEADER' => 'HEADER',
      'GENERAL' => 'GENERAL'
    );
    
    echo wp_kses_post(wmvc_select_option('control_type_'.$i_fieldnum, $options_array, wmvc_show_data('control_type_'.$i_fieldnum, $db_data, $val), ' class="form-control" '));
    
    ?>

    </div>

    <div class="form-group">
        <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
        <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_parameter_'.$i_fieldnum, $db_data, $val)); ?>" />
    </div>
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>
    <?php 

    $options_array = array(
      '' => '',
      'CONTAINS' => 'CONTAINS',
      'NOT_CONTAINS' => 'NOT_CONTAINS'
    );
    
    echo wp_kses_post(wmvc_select_option('control_operator_'.$i_fieldnum, $options_array, wmvc_show_data('control_operator_'.$i_fieldnum, $db_data, $val), ' class="form-control" '));
    
    ?>
  </div>

  <div class="form-group">
    <label><?php echo __('Value:','winter-activity-log'); ?> </label>
    <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_value_'.$i_fieldnum, $db_data, $val)); ?>" />
  </div>
  <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
  <hr />
</div>

<?php endforeach; ?>

<?php elseif( isset($_POST) && count($_POST) > 0 ): ?>

<?php 

/*

 ["control_type_1"]=>
  string(4) "POST"
  ["control_parameter_1"]=>
  string(5) "level"
  ["control_operator_1"]=>
  string(8) "CONTAINS"
  ["control_value_1"]=>
  string(1) "3"
  ["control_type_2"]=>
  string(7) "GENERAL"

*/

foreach($_POST as $key=>$post_val): 

if( substr($key, 0, strlen('control_type_')) != 'control_type_' )continue;

$i_fieldnum = substr(sanitize_text_field($key), -1, 1);

if(!is_numeric($i_fieldnum))continue;

if(empty(wmvc_show_data('control_type_'.$i_fieldnum, $db_data, $val)))continue;

?>

<div class="form-inline control-box">
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>

    <?php 

    $options_array = array(
      '' => '',
      'POST' => 'POST',
      'GET' => 'GET',
      'HEADER' => 'HEADER',
      'GENERAL' => 'GENERAL'
    );
    
    echo wp_kses_post(wmvc_select_option('control_type_'.$i_fieldnum, $options_array, wmvc_show_data('control_type_'.$i_fieldnum, $db_data, $val), ' class="form-control" '));
    
    ?>

    </div>

    <div class="form-group">
        <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
        <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_parameter_'.$i_fieldnum, $db_data, $val)); ?>" />
    </div>
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>
    <?php 

    $options_array = array(
      '' => '',
      'CONTAINS' => 'CONTAINS',
      'NOT_CONTAINS' => 'NOT_CONTAINS'
    );
    
    echo wp_kses_post(wmvc_select_option('control_operator_'.$i_fieldnum, $options_array, wmvc_show_data('control_operator_'.$i_fieldnum, $db_data, $val), ' class="form-control" '));
    
    ?>
  </div>

  <div class="form-group">
    <label><?php echo __('Value:','winter-activity-log'); ?> </label>
    <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_value_'.$i_fieldnum, $db_data, $val)); ?>" />
  </div>
  <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
  <hr />
</div>

<?php endforeach; ?>
<?php endif;?>  
            
            
<?php if(!empty($event_data['requests'])):?>
    <?php foreach($event_data['requests'] as $request):?>
    <?php 
        $options_array = array(
          '' => '',
          'POST' => 'POST',
          'GET' => 'GET',
          'HEADER' => 'HEADER',
          'GENERAL' => 'GENERAL'
        ); 

        $options_array_contain = array(
            '' => '',
            'CONTAINS' => 'CONTAINS',
            'NOT_CONTAINS' => 'NOT_CONTAINS'
          );
        ?>
        <?php
        $i_fieldnum++;   
        ?>
        <div class="form-inline control-box">
            <div class="form-group">
              <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>

            <?php 
                echo wp_kses_post(wmvc_select_option('control_type_'.$i_fieldnum, $options_array, wmvc_show_data('control_type_'.$i_fieldnum, $db_data, $request['type']), 'class="form-control" '));
            ?>
            </div>

              <div class="form-group">
                  <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
                  <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_parameter_'.$i_fieldnum, $db_data, $request['parameter'])); ?>" />
              </div>

            <div class="form-group">
              <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>
              <?php 
              echo wp_kses_post(wmvc_select_option('control_operator_'.$i_fieldnum, $options_array_contain, wmvc_show_data('control_operator_'.$i_fieldnum, $db_data, $request['operator']), ' class="form-control" '));
              ?>
            </div>

            <div class="form-group">
              <label><?php echo __('Value:','winter-activity-log'); ?> </label>
              <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="<?php echo wp_kses_post(wmvc_show_data('control_value_'.$i_fieldnum, $db_data, $request['value'])); ?>" />
            </div>
            <button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
            <hr />
          </div>             
    <?php endforeach;?>
<?php endif;?>         
            
<?php
$i_fieldnum++;
?>


<div class="form-inline control-box control-box-new">
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Type:','winter-activity-log'); ?> </label>
    <select class="form-control" name="<?php echo 'control_type_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="POST">POST</option>
          <option value="GET">GET</option>
          <option value="HEADER">HEADER</option>
          <option value="GENERAL">GENERAL</option>
    </select>

    </div>

    <div class="form-group">
        <label for="exampleInputName2"><?php echo __('Parameter:','winter-activity-log'); ?> </label>
        <input type="text" class="form-control" name="<?php echo 'control_parameter_'.intval($i_fieldnum); ?>" value="" />
    </div>
  <div class="form-group">
    <label for="exampleInputName2"><?php echo __('Operator:','winter-activity-log'); ?> </label>

    <select class="form-control" name="<?php echo 'control_operator_'.intval($i_fieldnum); ?>">
          <option value=""></option>
          <option value="CONTAINS">CONTAINS</option>
          <option value="NOT_CONTAINS">NOT_CONTAINS</option>
    </select>

  </div>

  <div class="form-group">
    <label><?php echo __('Value:','winter-activity-log'); ?> </label>
    <input type="text" class="form-control" name="<?php echo 'control_value_'.intval($i_fieldnum); ?>" value="" />
  </div>
  <button type="button" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i></button>
  <hr />
</div>

    <h3 class="panel-title"><?php echo __('Operation','winter-activity-log'); ?>:</h3>
    <p></p>
    <p><b><?php echo __('Parameter "request_uri"','winter-activity-log'); ?></b> - <?php echo __('Will do action if parameter Request URL/link contain or not contain value, depend on operator','winter-activity-log'); ?></p>
    <p><b><?php echo __('Operator "CONTAINS/NOT_CONTAINS"','winter-activity-log'); ?></b> - <?php echo __('CONTAINS means that Parameter must contain value, NOT_CONTAINS means that parameter should not contain value','winter-activity-log'); ?></p>



            <?php //dump($log_data_array); ?>

            <?php //dump(unserialize($log_data->request_data)); ?>

            <?php //dump(unserialize($log_data->header_data)); ?>

            <?php //dump(unserialize($log_data->other_data)); ?>

        </div>
    </div>
</div>




<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Then:','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">

        <div>

          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist" id="myTabs">
            <li role="presentation" class="active"><a href="#email-alert" aria-controls="email-alert" role="tab" data-toggle="tab"><?php echo __('Email alert','winter-activity-log'); ?></a></li>
            <li role="presentation"><a href="#sms-alert" aria-controls="sms-alert" role="tab" data-toggle="tab"><?php echo __('SMS alert','winter-activity-log'); ?></a></li>
            <li role="presentation"><a href="#block-access" aria-controls="block-access" role="tab" data-toggle="tab"><?php echo __('Block access','winter-activity-log'); ?></a></li>
            <li role="presentation"><a href="#disable-log" aria-controls="disable-log" role="tab" data-toggle="tab"><?php echo __('Disable log','winter-activity-log'); ?></a></li>
          </ul>

          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="email-alert">

            <div class="form-part  <?php if ( !winteractivitylog()->is_plan_or_trial('lite') ) echo 'wal-pro'; ?>" >
              <div class="checkbox">
                <label>
                  <input name="is_email_enabled" type="checkbox" value="1" <?php echo wmvc_show_data('is_email_enabled', $db_data, '')=='1'?'checked':''; ?>> <?php echo __('Enable email alert','winter-activity-log'); ?>
                </label>
              </div>

              <div class="form-group">
                <label for="inputEmail"><?php echo __('Email address','winter-activity-log'); ?></label>
                <input name="email" type="text" class="form-control" id="inputEmail" value="<?php echo esc_attr(wmvc_show_data('email', $db_data, '')); ?>" placeholder="<?php echo __('Email','winter-activity-log'); ?>">
              </div>

            </div>

            </div>

            <div role="tabpanel" class="tab-pane" id="sms-alert">

            <div class="form-part  <?php if ( !winteractivitylog()->is_plan_or_trial('premium') ) echo 'wal-pro'; ?>" >
              <div class="checkbox">
                <label>
                  <input name="is_sms_enabled" type="checkbox" value="1" <?php echo wmvc_show_data('is_sms_enabled', $db_data, '')=='1'?'checked':''; ?>> <?php echo __('Enable SMS alert','winter-activity-log'); ?>
                </label>
              </div>

              <div class="form-group">
                <label for="inputPhone"><?php echo __('Phone number for SMS','winter-activity-log'); ?></label>
                <input name="phone" type="text" class="form-control" id="inputPhone" value="<?php echo esc_attr(wmvc_show_data('phone', $db_data, '')); ?>" placeholder="<?php echo __('Phone number for SMS','winter-activity-log'); ?>">
              </div>

                <div class="alert alert-danger" role="alert">
                  <?php echo __('Use full phone number with country code like +385981234567', 'winter-activity-log'); ?><br />
                </div>
             


            </div>

            </div>

            <div role="tabpanel" class="tab-pane" id="block-access">

            <div class="form-part  <?php if ( !winteractivitylog()->is_plan_or_trial('lite') ) echo 'wal-pro'; ?>" >
              <div class="checkbox">
                <label>
                  <input name="is_block_enabled" type="checkbox" value="1" <?php echo wmvc_show_data('is_block_enabled', $db_data, '')=='1'?'checked':''; ?>> <?php echo __('Block access','winter-activity-log'); ?>
                </label>
              </div>

            </div>

            <div class="alert alert-danger" role="alert">
              <?php echo __('This may block your access to website', 'winter-activity-log'); ?><br />
              <?php echo __('Save this link to txt file on your computer, will help you to unblock your website in such cases:', 'winter-activity-log'); ?><br />
              <?php echo get_home_url().'?wal_unblock='.md5(AUTH_KEY.'wal'); ?><br />
              <a href="<?php menu_page_url( 'wal_controlsecurity', true ); ?>&function=wal_unblock_download"><?php echo __('Or download here', 'winter-activity-log'); ?></a>
            </div>

            </div>
            <div role="tabpanel" class="tab-pane" id="disable-log">

            <div class="form-part" >
              <div class="checkbox">
                <label>
                  <input name="is_skip" type="checkbox" value="1" <?php echo wmvc_show_data('is_skip', $db_data, (isset($_GET['subfunction']) && $_GET['subfunction']=='hide')?'1':'')=='1'?'checked':''; ?>> <?php echo __('Skip/Disable log','winter-activity-log'); ?>
                </label>
              </div>

            </div>

            </div>
          </div>

          <button type="submit" class="btn btn-success"><?php echo __('Save control rules','winter-activity-log'); ?></button>

          </div>

        </div>
    </div>
</div>


<?php

wp_enqueue_style('winter-activity-log_basic_wrapper');

?>

<style>

.winterlock_wrap table tr th[scope="row"]{

}

</style>

<script>

// Generate table
jQuery(document).ready(function($) {

  refresh_events();

  $('#myTabs a').click(function (e) {
    e.preventDefault();
    $(this).closest('.nav-tabs').find('li').removeClass('active');
    $(this).parent().addClass('active');
    $(this).closest('.panel-body').find('.tab-content > .tab-pane').removeClass('active').parent().find('.tab-pane'+$(this).attr('href')).addClass('active');
    return false;
  })

});

function refresh_events()
{
  jQuery('.control-box .btn').unbind();

  jQuery('.control-box .btn-danger').click(function(){
    jQuery(this).parent().remove();

    return false;
  });

  jQuery('.control-box .btn-success').click(function(){
    jQuery(this).parent().clone().appendTo( ".control-container" );
    jQuery(this).removeClass('btn-success');
    jQuery(this).addClass('btn-danger');
    jQuery(this).find('i').removeClass('glyphicon-plus');
    jQuery(this).find('i').addClass('glyphicon-remove');

    refresh_events();

    return false;
  });
}


</script>
</form>

<div class="winterlock_wrap">
  <div class="alert alert-info" role="alert"><a target="_blank" href="https://wordpress.org/plugins/wp-mail-smtp/"><?php echo __('Email sending may not work on some servers, for such cases use SMTP configuration with plugin like WP Mail SMTP by WPForms', 'winter-activity-log'); ?></a></div>
  <div class="alert alert-info" role="alert"><a target="_blank" href="https://www.clickatell.com/"><?php echo __('For SMS alert you need to configure clickatell service api in settings, use One API for integration', 'winter-activity-log'); ?></a></div>

</div>

</div>

<?php $this->view('general/footer', $data); ?>