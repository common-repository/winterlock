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

<?php 
$def_col_level = 0;
$def_col_date = 0;
$def_col_avatar = 0;
$def_col_user = 0;
$def_col_ip = 0;
$def_col_description = 0;
$def_col_select = 1;
$def_cols = [
    'level',
    'date',
    'avatar',
    'user',
    'ip',
    'description'
];
foreach ( $def_cols as $def_column ) {
    if ( sw_wal_log_is_visible_table_column( $def_column ) ) {
        $s_plus = false;
        foreach ( $def_cols as $col ) {
            if ( $col == $def_column ) {
                $s_plus = true;
            }
            if ( $s_plus ) {
                ${'def_col_' . $col}++;
            }
        }
        $def_col_select++;
    }
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap winterlock_wrap">

<h1>
    <?php 
echo __( 'Activity log', 'winter-activity-log' );
?>
    <?php 
if ( get_option( 'wal_checkbox_disable_hints', '0' ) == '0' ) {
    ?>
        <a href="#popup_tutorial" id="popup_tutorial" class="page-title-action pull-right"><i class="fa fa-video-camera"></i>&nbsp;&nbsp;<?php 
    echo __( 'Need help? Check Video tutorials!', 'winter-activity-log' );
    ?></a>
    <?php 
}
?>
</h1>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading flex">
            <h3 class="panel-title"><?php 
echo __( 'Logged data', 'winter-activity-log' );
?></h3>
        </div>
        <div class="panel-body">
            <!-- Data Table -->
            <div class="box box-without-bottom-padding">
                <div class="tableWrap dataTable table-responsive js-select">
                    <table id="din-table" class="table table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th data-priority="1" width="40px">#</th>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'level' ) ) {
    ?>
                                <th data-priority="2" width="40px"><?php 
    echo __( 'Level', 'winter-activity-log' );
    ?></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'date' ) ) {
    ?>
                                    <th data-priority="4"><?php 
    echo __( 'Date', 'winter-activity-log' );
    ?></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'avatar' ) ) {
    ?>
                                    <th data-priority="2"><?php 
    echo __( 'Avatar', 'winter-activity-log' );
    ?></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'user' ) ) {
    ?>
                                    <th data-priority="2"><?php 
    echo __( 'User', 'winter-activity-log' );
    ?></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'ip' ) ) {
    ?>
                                    <th><?php 
    echo __( 'IP', 'winter-activity-log' );
    ?></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'description' ) ) {
    ?>
                                    <th><?php 
    echo __( 'Description', 'winter-activity-log' );
    ?></th>
                                <?php 
}
?>
                                <th data-priority="3" width="100px"></th>
                                <th width="10px"><input type="checkbox" class="selectAll" name="selectAll" value="all"></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th><input type="text" name="filter_id" class="dinamic_par"  placeholder="<?php 
echo __( 'Filter #', 'winter-activity-log' );
?>" /></th>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'level' ) ) {
    ?>
                                <th><input type="text" name="filter_level" class="dinamic_par" placeholder="<?php 
    echo __( 'Filter Level', 'winter-activity-log' );
    ?>" /></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'date' ) ) {
    ?>
                                <th><input type="text" id="filter_date" name="filter_date" class="dinamic_par" placeholder="<?php 
    echo __( 'Filter Date From', 'winter-activity-log' );
    ?>" /></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'avatar' ) ) {
    ?>
                                <th></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'user' ) ) {
    ?>
                                <th><input type="text" id="filter_user" name="filter_user" value="<?php 
    echo esc_attr( wmvc_show_data( 'filter_user', $_GET, '' ) );
    ?>" class="dinamic_par" placeholder="<?php 
    echo __( 'Filter User', 'winter-activity-log' );
    ?>" /></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'ip' ) ) {
    ?>
                                <th><input type="text" name="filter_ip" class="dinamic_par" placeholder="<?php 
    echo __( 'Filter IP', 'winter-activity-log' );
    ?>" /></th>
                                <?php 
}
?>
                                <?php 
if ( sw_wal_log_is_visible_table_column( 'description' ) ) {
    ?>
                                <th><input type="text" name="filter_description" class="dinamic_par" placeholder="<?php 
    echo __( 'Filter Description', 'winter-activity-log' );
    ?>" /></th>
                                <?php 
}
?>
                                <th colspan="2">
                                    <div class="winterlock_save_search_filter">
                                        <div class="winterlock_save_search_filter_btn">
                                            <a href="#" class="btn btn_save"><?php 
echo __( 'Save', 'winter-activity-log' );
?></a>
                                            <a href="#" class="btn-toggle"><i class="fa fa-angle-down"></i></a>
                                        </div>
                                        <ul class="winterlock_list_filters">
                                        </ul>
                                    </div>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="form-inline">
                <div class="checkbox  <?php 
if ( !winteractivitylog()->is_plan_or_trial( 'standard' ) ) {
    echo 'wal-pro';
}
?>">
                    <label>
                        <input id="wal_live_monitoring" type="checkbox"> <?php 
echo __( 'Live monitoring enable (autorefresh each 10 sec)', 'winter-activity-log' );
?>
                    </label>
                </div>
                
                <div class="footer-btns">
                    <a href="<?php 
echo admin_url( "admin.php?page=wal_reports&function=report_edit" );
?>" class="btn btn-warning pull-right"><i class="fa fa-download"></i>&nbsp;&nbsp;<?php 
echo __( 'Export/Generate Report', 'winter-activity-log' );
?></a>
                    <a href="#bulk_remove-form" id="bulk_remove" class="btn btn-danger pull-right popup-with-form"><i class="fa fa-remove"></i>&nbsp;&nbsp;<?php 
echo __( 'Bulk remove', 'winter-activity-log' );
?><i class="fa fa-spinner fa-spin fa-custom-ajax-indicator-opc ajax-indicator-masking hidden_opacity"></i></a>
                    <a href="#clear_filters" id="clear_filters" class="btn btn-danger pull-right "><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php 
echo __( 'Clear all filters', 'winter-activity-log' );
?></a>
                </div>
            </div>
        </div>
    </div>

    <?php 
?>
    
</div>
</div>


<?php 
wp_enqueue_style( 'winter-activity-log_basic_wrapper' );
wp_enqueue_script( 'datatables' );
wp_enqueue_script( 'dataTables-responsive' );
wp_enqueue_script( 'dataTables-select' );
wp_enqueue_style( 'dataTables-select' );
?>

<script>
 
var wal_timer_live_monitoring;
var temp_change = '';

// Generate table
jQuery(document).ready(function($) {
    var table;

    $('#filter_date').datetimepicker({
        format: 'YYYY-MM-DD',
        useCurrent: false,
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'top'
         },
         keepOpen: false,
         //debug: true
    });

    <?php 
if ( sw_wal_log_is_visible_table_column( 'date' ) ) {
    ?> 
    $("#filter_date").on("dp.change", function (e) {
        $("#filter_date").trigger('change');
        table.columns(<?php 
    $def_col_date;
    ?>).search( $('#filter_date').val() ).draw();
    });
    <?php 
}
?>
        
    /* winterlock_save_search_filter */
    function generate_json_filter()
    {
        var js_gen = '{';
        jQuery('.dinamic_par').each(function(){
            js_gen+= '"'+jQuery(this).attr('name')+'":"'+jQuery(this).val()+'",';
        });
        js_gen = js_gen.slice(0,-1);
        js_gen+= '}';

        return js_gen;
    }

    function sw_log_notify(text, type, popup_place) {
        var $ = jQuery;
        if(!$('.sw_log_notify-box').length) $('body').append('<div class="sw_log_notify-box"></div>')
        if(typeof text=="undefined") var text = 'Undefined text';
        if(typeof type=="undefined") var type = 'success';
        if(typeof popup_place=="undefined") var popup_place = $('.sw_log_notify-box');
        var el_class = '';
        var el_timer= 5000;
        switch(type){
            case "success" : el_class = "success";
                            break
            case "error" : el_class = "error";
                            break
            case "loading" : el_class = "loading";
                             el_timer = 2000;
                            break
            default : el_class = "success";
                            break
        }

        /* notify */
        var html = '';
        html = '<div class="sw_log_notify '+el_class+'">\n\
                       '+text+'\n\
               </div>';
        var notification = $(html).appendTo(popup_place).delay(100).queue(function () {
                            $(this).addClass('show')
                                setTimeout(function() {
                                    notification.removeClass('show')
                                    setTimeout(function() {
                                        notification.remove();
                                    }, 1000);     
                                }, el_timer);  
                            })
        /* end notify */
    }

    function reload_filters()
    {
        var $ = jQuery;
        var data = {
            "page": 'winteractivitylog',
            'function': 'filter_get',
            "action": 'winter_activity_log_action',
        };

        $.post('<?php 
echo esc_url( admin_url( 'admin-ajax.php' ) );
?>', data,
        function(data){
            var html ='';
            $('.winterlock_save_search_filter .winterlock_list_filters').html(html);
            if(data.success && data.results){
                $.each(data.results, function(key, value){
                     html +='<li><a href="#" class="btn-load-save" data-filter="">'+value.name+'<textarea class="hidden">'+value.filter_par+'</textarea></a><a href="#" data-fielderid="'+value.filterid+'" class="remove"><i class="fa fa-remove"></i></a></li>';
                    //$('.winterlock_save_search_filter.show .winterlock_list_filters').append(html).find('li').last().find('.btn-load-save').get(0).filter_par =value.filter_par;
                })
            }
            $('.winterlock_save_search_filter .winterlock_list_filters').html(html);
        }, "json").success(function(){
            reload_elements_filter();
        });
    }
    /* reload save elements with events */
    function reload_elements_filter(){
        var $ = jQuery;

        /* get filters elements */
        $('.winterlock_save_search_filter .winterlock_list_filters a.btn-load-save').
            off().on('click', function(e){
                e.preventDefault();
                var filter_par = JSON.parse($(this).find('textarea').val());
                jQuery('.dinamic_par').each(function(){
                    //sw_log_search
                    if(typeof filter_par[jQuery(this).attr('name')] != 'undefined'){
                        if(jQuery(this).attr('name')=='sw_log_search') {
                            table.search(filter_par[jQuery(this).attr('name')]);
                            table.draw();
                        } else {
                            jQuery(this).val(filter_par[jQuery(this).attr('name')]);
                        }
                    }
                }).trigger('change');

                setTimeout(function(){jQuery('.dinamic_par[name=\"sw_log_search\"]').trigger('change');},1500);

                sw_log_notify('<?php 
echo __( 'Loaded filter', 'winter-activity-log' );
?> '+$(this).contents()[0].textContent);
                $(this).closest('.winterlock_save_search_filter').removeClass('show');
        })

        $('.winterlock_save_search_filter .winterlock_list_filters a.remove').
            off().on('click', function(e){
                e.preventDefault();
                var title = $(this).parent().find('.btn-load-save').eq(0).contents()[0].textContent;
                var data = {
                    "page": 'winteractivitylog',
                    'function': 'filter_remove',
                    "action": 'winter_activity_log_action',
                    "filter_id": $(this).attr('data-fielderid') || '',
                };
                sw_log_notify('<?php 
echo __( 'Removing filter', 'winter-activity-log' );
?> '+title, 'loading');

                $.post('<?php 
echo esc_url( admin_url( 'admin-ajax.php' ) );
?>', data,
                function(data){

                }, "json").success(function(){
                    sw_log_notify('<?php 
echo __( 'Removed filter', 'winter-activity-log' );
?> '+title);
                    reload_filters();
                });
        })
    }
    
    $('.winterlock_save_search_filter .winterlock_save_search_filter_btn a.btn_save').on('click', function(e){
        e.preventDefault()
        var is_empty = true;
        $('.dinamic_par:not([name="sw_log_count"])').each(function(){
            if($(this).val() !='') is_empty = false;
        });
        if($('.dinamic_par[name="sw_log_count"]').val() != 10) 
             is_empty = false;
        
        if(is_empty) {
            sw_log_notify('<?php 
echo __( 'Fitlers are empty', 'winter-activity-log' );
?>', 'error');
            return false;   
        }
        
        
        $.confirm({
            boxWidth: '400px',
            useBootstrap: false,
            title: '<?php 
echo __( 'Save', 'winter-activity-log' );
?>',
            content: '' +
            '<form action="" class="winterlock_list_filters_form formName">' +
            '<div class="form-group">' +
            '<label><?php 
echo __( 'Filter name', 'winter-activity-log' );
?></label>' +
            '<input type="text" placeholder="<?php 
echo __( 'Filter name', 'winter-activity-log' );
?>" class="filter_name form-control" required />' +
            '</div>' +
            '</form>',
            buttons: {
                formSubmit: {
                    text: '<?php 
echo __( 'Save', 'winter-activity-log' );
?>',
                    btnClass: 'btn-blue',
                    action: function () {
                        var filter_name = this.$content.find('.filter_name').val();

                        var object_values = [];
                        $('.dinamic_par').each(function(){
                            object_values.push({name: $(this).attr('name'), value: $(this).val()});
                        });
                        var data = {
                            "page": 'winteractivitylog',
                            'function': 'filter_save',
                            "action": 'winter_activity_log_action',
                            "filter_name": filter_name,
                            "filter_param": generate_json_filter()
                        };

                        $.post('<?php 
echo esc_url( admin_url( 'admin-ajax.php' ) );
?>', data,
                        function(data){
                        }, "json").success(function(){
                            sw_log_notify('<?php 
echo __( 'Saved filter', 'winter-activity-log' );
?> '+filter_name);
                            reload_filters();
                        } );

                    }
                },
                cancel: {
                    text: '<?php 
echo __( 'Cancel', 'winter-activity-log' );
?>',
                    action: function () {
                    }
                }
            },
            onContentReady: function () {
                // bind to events
                var jc = this;
                this.$content.find('form').on('submit', function (e) {
                    // if the user submits the form by pressing enter in the field.
                    e.preventDefault();
                    jc.$$formSubmit.trigger('click'); // reference the button and click it
                });
            }
        });

    });
    
    $('.winterlock_save_search_filter .btn-toggle').on('click', function(e){
        e.preventDefault();
        var $filter_box = $(this).closest('.winterlock_save_search_filter');
        $filter_box.toggleClass('show');
        
    })
    
    $("html").on("click", function(){
        $(".winterlock_save_search_filter").removeClass("show");
    });
    
    $(".winterlock_save_search_filter").on("click", function(e) {
        e.stopPropagation();
    });
    
    reload_filters();
    /* end winterlock_save_search_filter */

    //$(".selectAll").unbind();

    $(".selectAll").on( "click", function(e) {
        if ($(this).is( ":checked" )) {
            table.rows(  ).select();        
            //$(this).attr('checked','checked');
        } else {
            table.rows(  ).deselect(); 
            //$(this).attr('checked','');
        }
        //return false;
    });

    /* <fs_premium_only> */

    $('#wal_live_monitoring').click(function() {
        if ($('#wal_live_monitoring').is(':checked')) {
            wal_timer_live_monitoring =  setInterval(function(){ table.ajax.reload(); }, 10000);
        }
        else
        {
            clearInterval(wal_timer_live_monitoring);
        }
    });
    
    /* </fs_premium_only> */

    $('#bulk_remove').click(function(){
        var count = table.rows( { selected: true } ).count();
        var load_indicator_opc = $('.fa-custom-ajax-indicator-opc');
        load_indicator_opc.removeClass('hidden_opacity');
        
        if(count == 0)
        {
            alert('<?php 
echo esc_attr__( 'Please select listings to remove', 'winter-activity-log' );
?>');
            load_indicator_opc.addClass('hidden_opacity');
            return false;
        }
        else
        {

            if(confirm('<?php 
esc_js( __( 'Are you sure?', 'winter-activity-log' ) );
?>'))
            {
                $('img#ajax-indicator-masking').show();

                var form_selected_listings = table.rows( { selected: true } );
                var ids = table.rows( { selected: true } ).data().pluck( 'idlog' ).toArray();

                // ajax to remove rows
                $.post('<?php 
menu_page_url( 'winteractivitylog', true );
?>&function=bulk_remove', { log_ids: ids }, function(data) {

                    $('img#ajax-indicator-masking').hide();

                    table.ajax.reload();

                }).success(function(){load_indicator_opc.addClass('hidden_opacity');});
            } else {
                load_indicator_opc.addClass('hidden_opacity');
            }
        }

        return false;
    });
    /* clear all filters*/
    $('#clear_filters').click(function(e){
        e.preventDefault();
        $('.dinamic_par:not([name="sw_log_count"]):not([name="sw_log_search"])').val('').trigger('change');
        $('.dinamic_par[name="sw_log_count"]').val('10').trigger('change');
        /*fix if set not date */
        jQuery('#filter_date').data("DateTimePicker").date(new Date());
        $('#filter_date').data("DateTimePicker").clear()
        table.search('');
        table.draw();
        return false;
    });

	if ($('#din-table').length) {

        sw_log_s_table_load_counter = 0;

        table = $('#din-table').DataTable({
            "ordering": true,
            "responsive": true,
            "processing": true,
            "serverSide": true,
            'ajax': {
                "url": ajaxurl,
                "type": "POST",
                "data": function ( d ) {

                    $(".selectAll").prop('checked', false);

                    return $.extend( {}, d, {
                        "page": 'winteractivitylog',
                        "function": 'datatable',
                        "action": 'winter_activity_log_action'
                    } );


                }
            },
            "language": {
                search: "<?php 
esc_js( __( 'Search', 'winter-activity-log' ) );
?>",
                searchPlaceholder: "<?php 
esc_js( __( 'Enter here filter tag for any column', 'winter-activity-log' ) );
?>"
            },
            "initComplete": function(settings, json) {
            },
            "fnDrawCallback": function (oSettings){

//                if(sw_log_s_table_load_counter == 0)
                {
                    sw_log_s_table_load_counter++;
                    <?php 
if ( sw_wal_log_is_visible_table_column( 'user' ) ) {
    ?>  
                    if($('#filter_user').val() != '')
                    setTimeout(function(){ table.columns(<?php 
    echo intval( $def_col_user );
    ?>).search( $('#filter_user').val() ).draw(); }, 1000);
                    <?php 
}
?>
                    
                }

                $('a.delete_button').click(function(){
                    
                    if(confirm('<?php 
esc_js( __( 'Are you sure?', 'winter-activity-log' ) );
?>'))
                    {
                       // ajax to remove row
                        $.post($(this).attr('href'), function( [] ) {
                            table.row($(this).parent()).remove().draw( false );
                        });
                    }

                   return false;
                });

                define_popup_trigers();

                $('a.save_button').click(function(){
                    
                    var save_object = $(this);

                    // ajax to remove row
                    $.post($(this).attr('href'), function( data ) {
                        //console.log(data);
                        //save_object.find('i').removeClass('glyphicon-heart-empty');
                        //save_object.find('i').addClass('glyphicon-heart');
                        table.row($(this).parent()).remove().draw( false );
                    });

                   return false;
                });
            
                if ( table.responsive.hasHidden() )
                {
                    jQuery('table.dataTable td.details-control,table.dataTable td.details-controled').addClass('details-control');
                }
                else
                {
                    jQuery('table.dataTable td.details-control').removeClass('details-control').addClass('details-controled');
                }
                jQuery('.dataTable div.dataTables_wrapper div.dataTables_filter input').addClass("dinamic_par").attr('name','sw_log_search');
                jQuery('.dataTable div.dataTables_wrapper div.dataTables_length select').addClass("dinamic_par").attr('name','sw_log_count');
                
            },
            'columns': [
                { data: "idlog" },
                <?php 
if ( sw_wal_log_is_visible_table_column( 'level' ) ) {
    ?> 
                    { data: "level" },
                <?php 
}
?>
                <?php 
if ( sw_wal_log_is_visible_table_column( 'date' ) ) {
    ?> 
                    { data: "date"   },
                <?php 
}
?>
                <?php 
if ( sw_wal_log_is_visible_table_column( 'avatar' ) ) {
    ?> 
                    { data: "avatar"  },
                <?php 
}
?>
                <?php 
if ( sw_wal_log_is_visible_table_column( 'user' ) ) {
    ?> 
                    { data: "user_info"  },
                <?php 
}
?>
                <?php 
if ( sw_wal_log_is_visible_table_column( 'ip' ) ) {
    ?> 
                    { data: "ip"   },
                <?php 
}
?>
                <?php 
if ( sw_wal_log_is_visible_table_column( 'description' ) ) {
    ?> 
                    { data: "description"},
                <?php 
}
?>
                
                { data: "edit"    },
                { data: "checkbox"  }
            ],
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -2 }
            ],
            responsive: {
                details: {
                    type: 'column',
                    target: 0
                }
            },
            order: [[ 0, 'desc' ]],
            columnDefs: [   
                            <?php 
if ( sw_wal_log_is_visible_table_column( 'avatar' ) ) {
    ?> 
                            {
                                targets: <?php 
    echo intval( $def_col_avatar );
    ?>,
                                orderable: false
                            },
                            <?php 
}
?>       
                            {
                                //className: 'control',
                                className: 'details-control',
                                orderable: true,
                                targets:   0
                            },
                            <?php 
if ( sw_wal_log_is_visible_table_column( 'user' ) ) {
    ?>        
                            {
                                //className: 'control',
                                //className: 'details-control',
                                orderable: false,
                                targets:   <?php 
    echo intval( $def_col_user );
    ?>
                            },
                            <?php 
}
?>        
                            <?php 
if ( sw_wal_log_is_visible_table_column( 'ip' ) ) {
    ?>     
                            {
                                targets: <?php 
    echo intval( $def_col_ip );
    ?>,
                                orderable: false,
                                defaultContent: '2',
                            },
                            <?php 
}
?>
                            <?php 
if ( sw_wal_log_is_visible_table_column( 'description' ) ) {
    ?> 
                            {
                                targets: <?php 
    echo intval( $def_col_description );
    ?>,
                                orderable: false
                            },
                            <?php 
}
?>        
                            {
                                targets: <?php 
echo intval( $def_col_select );
?>,
                                orderable: false
                            },
                            {
                                className: 'select-checkbox',
                                orderable: false,
                                defaultContent: '',
                                targets:   <?php 
echo intval( $def_col_select + 1 );
?>
                            }
            ],
            select: {
                style:    'multi',
                selector: 'td:last-child'
            },
			'oLanguage': {
				'oPaginate': {
					'sPrevious': '<i class="fa fa-angle-left"></i>',
					'sNext': '<i class="fa fa-angle-right"></i>'
				},
                'sSearch': "<?php 
esc_js( __( 'Search', 'winter-activity-log' ) );
?>",
                "sLengthMenu": "<?php 
esc_js( __( 'Show _MENU_ entries', 'winter-activity-log' ) );
?>",
                "sInfoEmpty": "<?php 
esc_js( __( 'Showing 0 to 0 of 0 entries', 'winter-activity-log' ) );
?>",
                "sInfo": "<?php 
esc_js( __( 'Showing _START_ to _END_ of _TOTAL_ entries', 'winter-activity-log' ) );
?>",
                "sEmptyTable": "<?php 
esc_js( __( 'No data available in table', 'winter-activity-log' ) );
?>",
			},
			'dom': "<'row'<'col-sm-7 col-md-5'f><'col-sm-5 col-md-6'l>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>"
		});
        
//		$('.js-select select:not(.basic-select)').select2({
//			minimumResultsForSearch: Infinity
//		});
        
        // Apply the search
        table.columns().every( function () {
            var that = this;
     
            $( 'input,select', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );

        } );

        if ($('#wal_live_monitoring').is(':checked')) {
            wal_timer_live_monitoring = setInterval(function(){ table.ajax.reload(); }, 10000);
        }

        table.on( 'responsive-resize', function ( e, datatable, columns ) {
                if ( datatable.responsive.hasHidden() )
                {
                    jQuery('table.dataTable td.details-control,table.dataTable td.details-controled').addClass('details-control');
                }
                else
                {
                    jQuery('table.dataTable td.details-control').removeClass('details-control').addClass('details-controled');
                }
        } ); 
	}

    // Add event listener for opening and closing details
    $('table.dataTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            //row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            //row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });

    function define_popup_trigers()
    {


        $('.popup-with-form-ajax').on('click',function(e){
            if($(window).width() > 700) {
                e.preventDefault();
                var url = $(this).attr('href');
                if(url.indexOf('?') == -1){
                    url +='?'
                } else {
                    url +='&'
                }
                url +='popup=ajax';
                $.confirm({
                    boxWidth: '90%',
                    useBootstrap: false,
                    title: false,
                    content: 'url:'+url,
                });
            }
        })

        if(false)
            $('.popup-with-form-ajax').magnificPopup({
                type: 'ajax',
                preloader: true,
                alignTop: true,
                closeOnContentClick: false,
                closeOnBgClick: false,
                overflowY: 'scroll', // as we know that popup content is tall we set scroll overflow by default to avoid jump
                ajax: {
                    settings:  { data: {
                            "popup": 'ajax',
                        } }, 
                    
                    // Ajax settings object that will extend default one - http://api.jquery.com/jQuery.ajax/#jQuery-ajax-settings
                    // For example:
                    // settings: {cache:false, async:false}

                    cursor: 'mfp-ajax-cur', // CSS class that will be added to body during the loading (adds "progress" cursor)
                    tError: '<a href="%url%">The content</a> could not be loaded.' //  Error message, can contain %curr% and %total% tags if gallery is enabled
                    },
                focus: '#inputStyle',
                                
                // When elemened is focused, some mobile browsers in some cases zoom in
                // It looks not nice, so we disable it:
                callbacks: {
                    beforeOpen: function() {
                        if($(window).width() < 700) {
                            this.st.focus = false;
                        } else {
                            this.st.focus = '#inputStyle';
                        }
                    },
                    
                    open: function() {
                        //var magnificPopup = $.magnificPopup.instance,
                        //cur = magnificPopup.st.el.parent();
                        //$('#inputRel').val(cur.attr('rel'));
                    }
                }
            });
    }

    $('#popup_tutorial').on('click', function(e){
        e.preventDefault();
        $.confirm({
            boxWidth: '600px',
            useBootstrap: false,
            closeIcon: true,
            draggable: true,
            backgroundDismiss: true, // this will just close the modal
            title: '<?php 
echo esc_html__( 'Tutorials', 'winter-activity-log' );
?>',
            content: '<div class="winterlock_wrap wl_popup_tutorial">' +
                '<div class="alert alert-warning" role="alert"><a target="_blank" href="https://www.youtube.com/watch?v=VWI1WvlQQa8&list=PL0MjUuUth-hVFykhkW_UN8fsoAYJVbhf2"><i class="fa fa-youtube-play" aria-hidden="true"></i> <?php 
echo __( 'How to use quick search filtering?', 'winter-activity-log' );
?></a></div>'+
                '<div class="alert alert-warning" role="alert"><a target="_blank" href="https://www.youtube.com/watch?v=v8jJcRkEfjI&list=PL0MjUuUth-hVFykhkW_UN8fsoAYJVbhf2"><i class="fa fa-youtube-play" aria-hidden="true"></i> <?php 
echo __( 'How to Live Montor with auto refresh and detect login users?', 'winter-activity-log' );
?></a></div>'+
                '<div class="alert alert-warning" role="alert"><a target="_blank" href="https://www.youtube.com/watch?v=utDuznzhsHg&list=PL0MjUuUth-hVFykhkW_UN8fsoAYJVbhf2"><i class="fa fa-youtube-play" aria-hidden="true"></i> <?php 
echo __( 'How to check Login History and Block Failed Login Attempts?', 'winter-activity-log' );
?></a></div>'+
                '<div class="alert alert-warning" role="alert"><a target="_blank" href="https://www.youtube.com/watch?v=bLG_HZrpEX0&list=PL0MjUuUth-hVFykhkW_UN8fsoAYJVbhf2"><i class="fa fa-youtube-play" aria-hidden="true"></i> <?php 
echo __( 'How to Block IP, Username, some words in title or any other criteria?', 'winter-activity-log' );
?></a></div>'+
                '<div class="alert alert-warning" role="alert"><a target="_blank" href="https://www.youtube.com/watch?v=v9dJ8F9ekVI&list=PL0MjUuUth-hVFykhkW_UN8fsoAYJVbhf2"><i class="fa fa-youtube-play" aria-hidden="true"></i> <?php 
echo __( 'How to Log Content Editing and check Revisions?', 'winter-activity-log' );
?></a></div>'+
                '<div class="alert alert-warning" role="alert"><a target="_blank" href="https://www.youtube.com/watch?v=rVwcz6pbBWM&list=PL0MjUuUth-hVFykhkW_UN8fsoAYJVbhf2"><i class="fa fa-youtube-play" aria-hidden="true"></i> <?php 
echo __( 'How to Detect File Changes?', 'winter-activity-log' );
?></a></div>'+
                '<iframe width="560" height="315" src="https://www.youtube.com/embed/bLG_HZrpEX0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'+
            '</div>',
            buttons: {
                cancel: {
                    text: '<?php 
echo esc_html__( 'Close', 'winter-activity-log' );
?>',
                }
            },
            onContentReady: function () {
                // bind to events
                var jc = this;
                this.$content.find('form').on('submit', function (e) {
                    // if the user submits the form by pressing enter in the field.
                    e.preventDefault();
                    jc.$$formSubmit.trigger('click'); // reference the button and click it
                });
            }
        });
    });

});

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

.winterlock_wrap .table > tbody > tr > td, .winterlock_wrap .table > tbody > tr > th, 
.winterlock_wrap .table > tfoot > tr > td, .winterlock_wrap .table > tfoot > tr > th, 
.winterlock_wrap .table > thead > tr > td, .winterlock_wrap .table > thead > tr > th {
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

img.avatar
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

/* sw_log_notify */

.sw_log_notify-box {
    position: fixed;
    right: 15px;
    bottom: 0;
    z-index: 100;
    
    position: fixed;
    z-index: 5000;
    bottom: 10px;
    right: 10px;
}

.sw_log_notify {
    position: relative;
    background: #fffffff7;
    padding: 12px 15px;
    border-radius: 15px;
    width: 250px;
    box-shadow: 0px 1px 0px 0.25px rgba(0, 0, 0, 0.07);
    -webkit-box-shadow: 0px 0 3px 2px rgba(0, 0, 0, 0.08);
    margin: 0;
    margin-bottom: 10px;
    font-size: 16px;
    
    background: #5cb811;
    background: rgba(92, 184, 17, 0.9);
    padding: 15px;
    border-radius: 4px;
    color: #fff;
    text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.5);
    
    -webkit-transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
    -moz-transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
    -ms-transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
    -o-transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
    transition: all 500ms cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.sw_log_notify.error  {
    margin: 0;
    margin-bottom: 10px;
    background: #cf2a0e;
    padding: 12px 15px;
}

.sw_log_notify.loading  {
    background: #5bc0de;
}

.sw_log_notify {
    display: block;
    margin-top: 10px;
    position: relative;
    opacity: 0;
    transform: translateX(120%);
}

.sw_log_notify.show {
    transform: translateX(0);
    opacity: 1;
}
    
/* end sw_log_notify */

.winterlock_wrap .dataTables_filter .form-control {
    height: 30px;
}


body .winterlock_wrap .table-responsive {
    overflow-x: visible;
}


body .datepicker table.table-condensed tbody > tr:hover > td:first-child, body .datepicker table.table-condensed tbody > tr.selected > td:first-child {
    border-left: 0px solid #fba56a;
    border-radius: 3px 0 0 3px;
}
body .datepicker table.table-condensed tbody > tr > td:first-child {
    border-left: 0px solid #ffff;
    border-radius: 3px 0 0 3px;
}

</style>

<?php 
$this->view( 'general/footer', $data );