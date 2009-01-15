<?php
// If uninstall/delete not called from WordPress then exit
if( ! defined('ABSPATH') && ! defined('WP_UNINSTALL_PLUGIN') )
	exit();

// Delete shadowbox option from options table and drop the options table
global $wpdb;
$wpdb->query('DROP TABLE wp_favorites');
delete_option('dynamic_favorites_limit');
?>
