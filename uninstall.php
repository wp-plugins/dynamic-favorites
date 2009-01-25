<?php
// If uninstall/delete not called from WordPress then exit
if( ! defined('ABSPATH') && ! defined('WP_UNINSTALL_PLUGIN') )
	exit();

// Delete options from options table and drop the favorites table
global $wpdb;
$wpdb->query('DROP TABLE ' . $wpdb->prefix . 'favorites');
delete_option('dynamic_favorites_limit');
?>
