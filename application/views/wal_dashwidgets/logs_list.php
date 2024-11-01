<?php if(wal_access_allowed('winterlock_view')): ?>

<script>
</script>

<style>
    
    .winterlock_wrap #din-table_wrapper .row
{
    margin:0px;
}

.winterlock_wrap .dataTable div.dataTables_wrapper label
{
    width:100%;
    padding:10px 0px;
}

.dataTable div.dataTables_wrapper div.dataTables_filter input
{
    display:inline-block;
    width:65%;
    margin: 0 10px;
}

.dataTable div.dataTables_wrapper div.dataTables_length select
{
    display:inline-block;
    width:100px;
    margin: 0 10px;
}

.dataTable td.control
{
    color:#337AB7;
    display:table-cell !important;
    font-weight: bold;
}

.dataTable th.control
{
    display:table-cell !important;
}

.dash_widget .winterlock_wrap .table > tbody > tr > td, .dash_widget .winterlock_wrap .table > tbody > tr > th, 
.dash_widget .winterlock_wrap .table > tfoot > tr > td, .dash_widget .winterlock_wrap .table > tfoot > tr > th, 
.dash_widget .winterlock_wrap .table > thead > tr > td, .dash_widget .winterlock_wrap .table > thead > tr > th {
    vertical-align: middle;
}

table.dataTable tbody > tr.odd.selected, table.dataTable tbody > tr > .odd.selected {
    background-color: #B0BED9;
}

.winterlock_wrap table.dataTable tbody td.select-checkbox::before, 
.winterlock_wrap table.dataTable tbody td.select-checkbox::after, 
.winterlock_wrap table.dataTable tbody th.select-checkbox::before, 
.winterlock_wrap table.dataTable tbody th.select-checkbox::after {
    display: block;
    position: absolute;
    /*top: 2.5em;*/
    top:50%;
    left: 50%;
    width: 12px;
    height: 12px;
    box-sizing: border-box;
}

.winterlock_wrap a#bulk_remove:hover,
.winterlock_wrap a#bulk_remove:focus {
    text-decoration: none;
}

tfoot input{
    width:100%;
    min-width:70px;
}

.dash_widget img.avatar
{
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

.wal-system-icon{
    width: 50px;
    font-size: 50px;
    height: 50px;
}

.dashicons.wal-system-icon.dashicons-before::before {
    display: inline-block;
    font-family: dashicons;
    transition: color .1s ease-in;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    width: 50px;
    font-size: 50px;
    height: 50px;
}

.dash_widget td a.btn
{
    margin-bottom:2px;
}

.dataTable  ul {
    margin-top: 0;
    margin-bottom: 0;
}   

.dataTable  ul li {
    margin: 0;
    padding: 7px 0px;
}
    
#din-table.table:not(.collapsed) .details-control {
    display: none;
}  
    
    
#din-table.table:not(.collapsed) .details-control {
    display: none;
}  
    
table.dataTable thead th.sorting:after,
.dataTables_wrapper>.row:last-child,
.dataTables_wrapper>.row:first-child {
    display: none !important;
}


.dataTable .table:not(.collapsed) .details-control,
#din-table.table:not(.collapsed) .details-control {
    display: none;
} 


</style>
<div class="dash_widget dash_widget_latest_logs">

<?php if(wmvc_count($logs) == 0): ?>
<div class="winterlock_wrap">
    <div class="alert alert-warning" role="alert"><?php echo __('Logs not found', 'winter-activity-log'); ?></div>
</div>
<?php else: ?>

<div class="winterlock_wrap">
	<div class="tableWrap dataTable js-select">
		<table class="table table-striped" style="width: 100%;" id="data_table">
                    <thead>
                        <tr>
                            <th data-priority="1"></th>
                            <th data-priority="1"><?php echo __('Date', 'winter-activity-log'); ?></th>
                            <th data-priority="2"><?php echo __('Avatar', 'winter-activity-log'); ?></th>
                            <th data-priority="2"><?php echo __('User', 'winter-activity-log'); ?></th>
                            <th data-priority="2" style=""><?php echo __('Description', 'winter-activity-log'); ?></th>
                            <th data-priority="3" style=""></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $key=>$row): ?>
                        <tr>
                            <td></td>
                            <td><?php echo esc_html(date(get_option('date_format').' '.get_option('time_format'), strtotime($row->date))); ?></td>
                            <td>
<?php

$avatar = '';

$user_info = get_userdata($row->user_id);

$user_agent = NULL;
$header_data = unserialize($row->header_data);
if(!empty($header_data["User-Agent"]))
{
    $user_agent = $header_data["User-Agent"];
}

if(empty($row->user_id))
{
    $row->user_id = '-';
}

if(isset($user_info->ID))
{
    $avatar= '<img class="avatar" src="'.esc_url( get_avatar_url( $user_info->ID ) ).'" />';
}
elseif(wal_visitor_type($row->page, $row->request_uri, $user_agent ) === 'system')
{
    $avatar = '<span class="dashicons dashicons-wordpress wal-system-icon"></span>';
}
elseif(wal_visitor_type($row->page, $row->request_uri, $user_agent ) === 'guest')
{
    $avatar  = '<span class="dashicons dashicons-before dashicons-admin-users wal-system-icon"></span>';
}
elseif(wal_visitor_type($row->page, $row->request_uri, $user_agent ) === 'unknown')
{
    $avatar  = '-';
}

?>
                            
                            
                            <?php echo wp_kses_post($avatar); ?></td>
                            <td><?php echo wp_kses_post($row->user_info); ?></td>
                            <td style=""><?php echo wp_kses_post($row->description); ?></td>
                            <td>
                                
                                    <?php echo wp_kses_post(wmvc_btn_block(admin_url("admin.php?page=wal_controlsecurity&function=control_log&subfunction=block&log_id=".$row->idlog))); ?>
                                    <?php echo wp_kses_post(wmvc_btn_open(admin_url("admin.php?page=winteractivitylog&function=edit_log&id=".$row->idlog))); ?>

                                    <?php if(false): ?>
                                    <div class="nav">
                                    <a href="<?php echo esc_url($url_lock); ?>" class="button button-primary" target="_blank"><span class="dashicons dashicons-search"></span></a>
                                    <a href="<?php echo esc_url($url_edit); ?>" class="button button-edit" target="_blank"><span class="dashicons dashicons-edit"></span></a>
                                    </div>
                                    <?php endif; ?>
                                
                            </td>
                        </tr>
                        <?php endforeach; ?>

                    </tbody>
            </table>
    </div>
</div>
<?php endif; ?>
<div class="winterlock_wrap">
<a class="btn btn-success" href="<?php echo admin_url("admin.php?page=winteractivitylog"); ?>" aria-label="<?php echo __('Manage logs', 'winter-activity-log'); ?>"><?php echo __('View all logs', 'winter-activity-log'); ?></a>
</div>
<style>

</style>

<?php else: ?>
<div class="winterlock_wrap">
    <div class="alert alert-warning" role="alert"><?php echo __('Logs features are not available for your account type', 'winter-activity-log'); ?></div>
</div>
<?php endif; ?>
</div>

<?php

wp_enqueue_style('winter-activity-log_basic_wrapper');
wp_enqueue_script( 'datatables' );
wp_enqueue_script( 'dataTables-responsive' );
wp_enqueue_script( 'dataTables-select' );

wp_enqueue_style( 'dataTables-select' );

?>

<script>
/* DataTable ini */
// Generate table
jQuery(document).ready(function($) {
    var table = $('#data_table').DataTable({
        'responsive': true,
        "searching": false,
        'columns': [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { data: "idlisting" },
            { data: "image_filename" },
            { data: "address"   },
            { data: "field_10"  },
            { data: "field_4"   },
        ]
    });
});
/* End DataTable ini */
</script>
