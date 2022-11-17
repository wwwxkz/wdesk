<?php

/*
Plugin Name: 	wdesk
Description: 	Plugin developed to track inquiries in a Helpdesk platform inside Wordpress
Version: 		0.3
Author: 		Marcelo Rodrigues Campos
Author URI: 	https://github.com/wwwxkz
Text Domain:	wdesk
Domain Path:	/languages
*/

define('WDESK_LOCAL', plugin_dir_path(__FILE__));
require_once(WDESK_LOCAL . 'script/script.php');
require_once(WDESK_LOCAL . 'shortcode/shortcode.php');
require_once(WDESK_LOCAL . 'admin/tickets/tickets.php');
require_once(WDESK_LOCAL . 'admin/departments/departments.php');
require_once(WDESK_LOCAL . 'admin/settings/settings.php');

add_action( 'plugins_loaded', 'wdesk_init' );

function wdesk_init() {
	add_action('admin_menu', 'wdesk');
	add_action('wdesk_cron_hook', 'wdesk_cron');
	load_plugin_textdomain('wdesk', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

function wdesk() {
	add_menu_page( 'Helpdesk', 'Helpdesk', 'manage_options', 'helpdesk', 'helpdesk', 'dashicons-editor-ul', 10 );
	add_submenu_page( 'helpdesk', __('Tickets', 'wdesk'), __('Tickets', 'wdesk'), 'read', 'wdesk_tickets', 'wdesk_tickets' );
	if (current_user_can('administrator')) {
		add_submenu_page( 'helpdesk', __('Departments', 'wdesk'), __('Departments', 'wdesk'), 'read', 'wdesk_departments', 'wdesk_departments' );
		add_submenu_page( 'helpdesk', __('Settings', 'wdesk'), __('Settings', 'wdesk'), 'read', 'wdesk_settings', 'wdesk_settings' );
	}
	remove_submenu_page('helpdesk','helpdesk');
}		

register_activation_hook(__FILE__, 'wdesk_activation');
function wdesk_activation() {
	global $wpdb;
	$table1 = "wdesk_users"; 
	$charset_collate1 = $wpdb->get_charset_collate();
	$sql1 = "CREATE TABLE $table1 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		created timestamp NOT NULL default CURRENT_TIMESTAMP,
		email varchar(255) NOT NULL,
		name varchar(255) NOT NULL,
		password varchar(255) NOT NULL,
		otp varchar(255),
		UNIQUE KEY id (id)
	) $charset_collate1;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql1);
	$table2 = "wdesk_departments"; 
	$charset_collate2 = $wpdb->get_charset_collate();
	$sql2 = "CREATE TABLE $table2 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate2;";
	dbDelta($sql2);
	$table3 = "wdesk_departments_agents"; 
	$charset_collate3 = $wpdb->get_charset_collate();
	$sql3 = "CREATE TABLE $table3 (
		department_id mediumint(9) NOT NULL,
		agent_id mediumint(9) NOT NULL,
		FOREIGN KEY (department_id) REFERENCES wdesk_departments(id)
	) $charset_collate3;";
	dbDelta($sql3);
	$table4 = "wdesk_tickets"; 
	$charset_collate4 = $wpdb->get_charset_collate();
	$sql4 = "CREATE TABLE $table4 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		created timestamp NOT NULL default CURRENT_TIMESTAMP,
		subject tinytext NOT NULL,
		status varchar(255),
		agent varchar(255),
		department varchar(255),
		user_email varchar(255) NOT NULL,
		user_name varchar(255) NOT NULL,
		token varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate4;";
	dbDelta($sql4);
	$table5 = "wdesk_tickets_threads"; 
	$charset_collate5 = $wpdb->get_charset_collate();
	$sql5 = "CREATE TABLE $table5 (
		id mediumint(9) NOT NULL AUTO_INCREMENT, 
		ticket_id mediumint(9) NOT NULL,
		created timestamp NOT NULL default CURRENT_TIMESTAMP,
		text varchar(255) NOT NULL,
		note tinyint(1) DEFAULT 0,
		file varchar(255) NOT NULL,
		user_name varchar(255) NOT NULL,
		FOREIGN KEY (ticket_id) REFERENCES wdesk_tickets(id),
		UNIQUE KEY id (id)
	) $charset_collate5;";
	dbDelta($sql5);
	$table6 = "wdesk_settings"; 
	$charset_collate6 = $wpdb->get_charset_collate();
	$sql6 = "CREATE TABLE $table6 (
		id mediumint(9) NOT NULL,
		setting tinytext NOT NULL,
		value tinytext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate6;";
	dbDelta($sql6);
	$wpdb->replace($table6, array(
		'id' => 0,
		'setting' => 'Helpdesk name',
		'value' => 'ExemCompany'
	));
	$wpdb->replace($table6, array(
		'id' => 1,
		'setting' => 'Sender email',
		'value' => 'email@example.com'
	));
	$wpdb->replace($table6, array(
		'id' => 2,
		'setting' => 'Helpdesk url',
		'value' => 'https://www.wordpress.org/'
	));
}

register_deactivation_hook(__FILE__, 'wdesk_deactivation');
function wdesk_deactivation() {
	$timestamp = wp_next_scheduled('wdesk_cron_hook');
	wp_unschedule_event($timestamp, 'wdesk_cron_hook');
}

register_uninstall_hook(__FILE__, 'wdesk_uninstall');
function wdesk_uninstall() {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS wdesk_users;");
   	$wpdb->query("DROP TABLE IF EXISTS wdesk_departments;");
	$wpdb->query("DROP TABLE IF EXISTS wdesk_departments_agents;");
   	$wpdb->query("DROP TABLE IF EXISTS wdesk_tickets;");
	$wpdb->query("DROP TABLE IF EXISTS wdesk_tickets_threads;");
   	$wpdb->query("DROP TABLE IF EXISTS wdesk_settings;");
}

add_filter('cron_schedules', 'wdesk_cron_interval');
function wdesk_cron_interval($schedules) { 
    $schedules['five_minutes'] = array(
        'interval' => 300,
        'display'  => esc_html__('Every Five Minutes') 
    );
    return $schedules;
}

function wdesk_cron() {
	global $wpdb;
	$wpdb->query("UPDATE `wdesk_users` SET `otp` = NULL WHERE `otp` IS NOT NULL; ");
}

if (!wp_next_scheduled('wdesk_cron_hook')) {
	wp_schedule_event(time(), 'five_minutes', 'wdesk_cron_hook');
}

?>