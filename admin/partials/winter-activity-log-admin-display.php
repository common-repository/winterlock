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
if(!empty($results))                        // Checking if $results have some values or not
{    
    echo "<table width='100%' border='0'>"; // Adding <table> and <tbody> tag outside foreach loop so that it wont create again and again
    echo "<tbody>";      
    foreach($results as $row){   
        $userip = $row->user_ip;               //putting the user_ip field value in variable to use it later in update query
        echo "<tr>";                           // Adding rows of table inside foreach loop
        echo "<td>" . esc_html($row->idlog) . "</td>";
        echo "<td>" . esc_html($row->level) . "</td>";
        echo "<td>" . esc_html(date(get_option('date_format'), strtotime($row->date))) . "</td>";

        $user_info = get_userdata(1);
        echo "<td>";
        echo esc_html($user_info->user_login) . ",";
        echo esc_html(implode(', ', $user_info->roles)) . ",";
        echo esc_html($user_info->ID);
        echo "</td>";

        echo "<td>" . wp_kses_post($row->ip) . "</td>";
        echo "<td>" . esc_html($row->request_uri) . "</td>";
        echo "<td>" . esc_html($row->page) . "</td>";
        echo "<td>" . esc_html($row->action) . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>"; 

}
?>