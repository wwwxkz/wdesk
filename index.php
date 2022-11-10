<?php

/*
Plugin Name: 	wdesk
Description: 	Plugin developed to track inquiries in a Helpdesk platform inside Wordpress
Version: 		0.1
Author: 		Marcelo Rodrigues Campos
Author URI: 	https://github.com/wwwxkz
Text Domain:	wdesk
Domain Path:	/languages
*/

define('WDESK_LOCAL', plugin_dir_path(__FILE__));
require_once(WDESK_LOCAL . 'functions/frontend/frontend.php');
require_once(WDESK_LOCAL . 'functions/tickets/tickets.php');
require_once(WDESK_LOCAL . 'functions/departments/departments.php');
require_once(WDESK_LOCAL . 'functions/settings/settings.php');

add_action( 'plugins_loaded', 'wdesk_init' );

function wdesk_init()
{
	add_action('admin_menu', 'wdesk');
	load_plugin_textdomain('wdesk', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

function wdesk(){
	add_menu_page( 'Helpdesk', 'Helpdesk', 'manage_options', 'helpdesk', 'helpdesk', 'dashicons-editor-ul', 10 );
	add_submenu_page( 'helpdesk', __('Tickets', 'wdesk'), __('Tickets', 'wdesk'), 'read', 'wdesk_tickets', 'wdesk_tickets' );
	add_submenu_page( 'helpdesk', __('Departments', 'wdesk'), __('Departments', 'wdesk'), 'read', 'wdesk_departments', 'wdesk_departments' );
	add_submenu_page( 'helpdesk', __('Settings', 'wdesk'), __('Settings', 'wdesk'), 'read', 'wdesk_settings', 'wdesk_settings' );
	remove_submenu_page('helpdesk','helpdesk');
}		

register_activation_hook(__FILE__, 'wdesk_activation');
function wdesk_activation(){
	global $wpdb;
	$table1 = "wdesk_users"; 
	$charset_collate1 = $wpdb->get_charset_collate();
	$sql1 = "CREATE TABLE $table1 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		created timestamp NOT NULL default CURRENT_TIMESTAMP,
		name tinytext NOT NULL,
		email varchar(255) NOT NULL,
		password varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate1;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql1);
	$table2 = "wdesk_departments"; 
	$charset_collate2 = $wpdb->get_charset_collate();
	$sql2 = "CREATE TABLE $table2 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		agents varchar(255),
		UNIQUE KEY id (id)
	) $charset_collate2;";
	dbDelta($sql2);
	$table3 = "wdesk_tickets"; 
	$charset_collate3 = $wpdb->get_charset_collate();
	$sql3 = "CREATE TABLE $table3 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		created timestamp NOT NULL default CURRENT_TIMESTAMP,
		subject tinytext NOT NULL,
		thread mediumtext NOT NULL,
		status varchar(255),
		agent varchar(255),
		department varchar(255),
		user varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate3;";
	dbDelta($sql3);
	$table4 = "wdesk_settings"; 
	$charset_collate4 = $wpdb->get_charset_collate();
	$sql4 = "CREATE TABLE $table4 (
		id mediumint(9) NOT NULL,
		setting tinytext NOT NULL,
		value tinytext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate4;";
	dbDelta($sql4);
	$wpdb->insert($table4, array(
		'id' => 0,
		'setting' => 'Helpdesk name',
		'value' => 'ExemCompany'
	));
	$wpdb->insert($table4, array(
		'id' => 1,
		'setting' => 'Sender email',
		'value' => 'email@example.com'
	));
}

?>


