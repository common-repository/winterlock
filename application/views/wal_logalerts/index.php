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

<h1><?php echo __('Log Alerts','winter-activity-log'); ?></h1>

<div class="winterlock_wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Log Alerts Rules Data','winter-activity-log'); ?></h3>
        </div>
        <div class="panel-body">

            <!-- Data Table -->
            <div class="box box-without-bottom-padding">
                <div class="tableWrap dataTable table-responsive js-select">
                    <table id="din-table" class="table table-striped" style="width: 100%;">
                        <thead>
                            <tr>
                                <th data-priority="1">#</th>
                                <th data-priority="2"><?php echo __('Title', 'winter-activity-log'); ?></th>
                                <th data-priority="4"><?php echo __('Date', 'winter-activity-log'); ?></th>
                                <th data-priority="3"><i class="glyphicon glyphicon-envelope"></i> <i class="glyphicon glyphicon-lock"></i> <i class="glyphicon glyphicon-eye-close"></i></th>
                                <th><?php echo __('Description', 'winter-activity-log'); ?></th>
                                <th data-priority="3"><i class="glyphicon glyphicon-search"></i></th>
                                <th><input type="checkbox" class="selectAll" name="selectAll" value="all"></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th><input type="text" placeholder="<?php echo __('Filter #', 'winter-activity-log'); ?>" /></th>
                                <th><input type="text" placeholder="<?php echo __('Filter Title', 'winter-activity-log'); ?>" /></th>
                                <th><input type="text" id="filter_date" placeholder="<?php echo __('Filter Date From', 'winter-activity-log'); ?>" /></th>
                                <th></th>
                                <th><input type="text" placeholder="<?php echo __('Filter Description', 'winter-activity-log'); ?>" /></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="footer-btns">
                <a href="#bulk_remove-form" id="bulk_remove" class="btn btn-danger pull-right popup-with-form"><i class="fa fa-remove"></i>&nbsp;&nbsp;<?php echo __('Bulk remove','winter-activity-log')?><i class="fa fa-spinner fa-spin fa-custom-ajax-indicator-opc ajax-indicator-masking hidden_opacity"></i></a>
                <a href="#clear_filters" id="clear_filters" class="btn btn-danger pull-right "><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo __('Clear all filters','winter-activity-log')?></a>
            </div>
        </div>
    </div>

    <div class="alert alert-info" role="alert"><?php echo __('Here you can see only alert log control rules which helps you to receive email on specific user actions', 'winter-activity-log'); ?></div>

</div>
</div>



<?php

wp_enqueue_style('winter-activity-log_basic_wrapper');
wp_enqueue_script( 'datatables' );
wp_enqueue_script( 'dataTables-responsive' );
wp_enqueue_script( 'dataTables-select' );

wp_enqueue_style( 'dataTables-select' );
?>
<script>

// Generate table
jQuery(document).ready(function($) {

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

    $("#filter_date").on("dp.change", function (e) {
        $("#filter_date").trigger('change');
        table.columns(2).search( $('#filter_date').val() ).draw();
    });

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


    $('#bulk_remove').click(function(){
        var count = table.rows( { selected: true } ).count();
        var load_indicator_opc = $('.fa-custom-ajax-indicator-opc');
        load_indicator_opc.removeClass('hidden_opacity');
        if(count == 0)
        {
            alert('<?php echo esc_attr__('Please select listings to remove', 'winter-activity-log'); ?>');
            load_indicator_opc.addClass('hidden_opacity');
            return false;
        }
        else
        {

            if(confirm('<?php esc_js(__('Are you sure?', 'winter-activity-log')); ?>'))
            {
                $('img#ajax-indicator-masking').show();

                var form_selected_listings = table.rows( { selected: true } );
                var ids = table.rows( { selected: true } ).data().pluck( 'idcontrol' ).toArray();

                // ajax to remove rows
                $.post('<?php menu_page_url( 'wal_controlsecurity', true ); ?>&function=bulk_remove', { log_ids: ids }, function(data) {

                    $('img#ajax-indicator-masking').hide();

                    table.ajax.reload();

                }).success(function(){load_indicator_opc.addClass('hidden_opacity');});
            } else {
                load_indicator_opc.addClass('hidden_opacity');
            }
        }

        return false;
    });


	if ($('#din-table').length) {

		var table = $('#din-table').DataTable({
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
                        "page": 'wal_logalerts',
                        "function": 'datatable',
                        "action": 'winter_activity_log_action'
                    } );
                }
            },
            "language": {
                search: "<?php esc_js(__('Search', 'winter-activity-log')); ?>",
                searchPlaceholder: "<?php esc_js(__('Enter here filter tag for any column', 'winter-activity-log')); ?>"
            },
            "fnDrawCallback": function (oSettings){
                $('a.delete_button').click(function(){
                    
                    if(confirm('<?php esc_js(__('Are you sure?', 'winter-activity-log')); ?>'))
                    {
                       // ajax to remove row
                        $.post($(this).attr('href'), function( [] ) {
                            table.row($(this).parent()).remove().draw( false );
                        });
                    }

                   return false;
                });

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
            },
            'columns': [
                { data: "idcontrol" },
                { data: "title" },
                { data: "date"   },
                { data: "icons"  },
                { data: "description"},
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
            columnDefs: [   {
                                targets: 3,
                                orderable: false
                            },
                            {
                                //className: 'control',
                                className: 'details-control',
                                orderable: true,
                                targets:   0
                            },
                            {
                                //className: 'control',
                                //className: 'details-control',
                                orderable: false,
                                targets:   4
                            },
                            {
                                targets: 5,
                                orderable: false
                            },
                            {
                                className: 'select-checkbox',
                                orderable: false,
                                defaultContent: '',
                                targets:   6
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
                'sSearch': "<?php esc_js(__('Search', 'winter-activity-log')); ?>",
                "sLengthMenu": "<?php esc_js(__('Show _MENU_ entries', 'winter-activity-log')); ?>",
                "sInfoEmpty": "<?php esc_js(__('Showing 0 to 0 of 0 entries', 'winter-activity-log')); ?>",
                "sInfo": "<?php esc_js( __('Showing _START_ to _END_ of _TOTAL_ entries', 'winter-activity-log')); ?>",
                "sEmptyTable": "<?php esc_js(__('No data available in table', 'winter-activity-log')); ?>",
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
        /* clear all filters*/
            $('#clear_filters').click(function(e){
                e.preventDefault();
                $('.dataTables_wrapper input:not([name="sw_log_count"]):not([name="sw_log_search"])').val('').trigger('change');
                $('.dataTables_wrapper .dataTables_length select').val('10').trigger('change');
                /*fix if set not date */
                if(jQuery('#filter_date').length){
                    jQuery('#filter_date').data("DateTimePicker").date(new Date());
                    $('#filter_date').data("DateTimePicker").clear()
                }
                table.search('');
                table.draw();
                return false;
            });
            table.on( 'responsive-resize', function ( e, datatable, columns ) {
                if ( datatable.responsive.hasHidden() )
                {
                    jQuery('table.dataTable td.details-control,table.dataTable td.details-controled').addClass('details-control');
                }
                else
                {
                    jQuery('table.dataTable td.details-control').removeClass('details-control').addClass('details-controled');
                }
            } )
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
