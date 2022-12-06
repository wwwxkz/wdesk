<?php

/*
Plugin Name: 	wdesk
Description: 	Plugin developed to track inquiries in a Helpdesk platform inside Wordpress
Version: 		0.5
Author: 		Marcelo Rodrigues Campos
Author URI: 	https://github.com/wwwxkz
Text Domain:	wdesk
Domain Path:	/languages
*/

define('WDESK_LOCAL', plugin_dir_path(__FILE__));

// Backend script
require_once(WDESK_LOCAL . 'script/script.php');

// Shortcode
require_once(WDESK_LOCAL . 'shortcode/shortcode.php');

// Admin
require_once(WDESK_LOCAL . 'admin/tags/tags.php');
require_once(WDESK_LOCAL . 'admin/tickets/tickets.php');
require_once(WDESK_LOCAL . 'admin/reports/reports.php');
require_once(WDESK_LOCAL . 'admin/settings/settings.php');
require_once(WDESK_LOCAL . 'admin/departments/departments.php');


add_action( 'plugins_loaded', 'wdesk_init' );

function wdesk_init() {
	add_action('admin_menu', 'wdesk');
	add_action('wdesk_cron_hook', 'wdesk_cron');
	// Load internationalizations
	load_plugin_textdomain('wdesk', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Register admin pages
function wdesk() {
	add_menu_page( 'Helpdesk', 'Helpdesk', 'manage_options', 'helpdesk', 'helpdesk', 'dashicons-editor-ul', 10 );
	add_submenu_page( 'helpdesk', __('Tickets', 'wdesk'), __('Tickets', 'wdesk'), 'read', 'wdesk_tickets', 'wdesk_tickets' );
	if (current_user_can('administrator')) {
		add_submenu_page( 'helpdesk', __('Departments', 'wdesk'), __('Departments', 'wdesk'), 'read', 'wdesk_departments', 'wdesk_departments' );
		add_submenu_page( 'helpdesk', __('Tags', 'wdesk'), __('Tags', 'wdesk'), 'read', 'wdesk_tags', 'wdesk_tags' );
		add_submenu_page( 'helpdesk', __('Reports', 'wdesk'), __('Reports', 'wdesk'), 'read', 'wdesk_reports', 'wdesk_reports' );
		add_submenu_page( 'helpdesk', __('Settings', 'wdesk'), __('Settings', 'wdesk'), 'read', 'wdesk_settings', 'wdesk_settings' );
	}
	// Remove repeated first page
	remove_submenu_page('helpdesk','helpdesk');
}		

// Registers activation, deactivation, and uninstall hook

register_activation_hook(__FILE__, 'wdesk_activation');
function wdesk_activation() {
	global $wpdb;
	
	// Users
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
	
	// Departments
	$table2 = "wdesk_departments"; 
	$charset_collate2 = $wpdb->get_charset_collate();
	$sql2 = "CREATE TABLE $table2 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		email tinytext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate2;";
	dbDelta($sql2);
	
	// Agents
	$table3 = "wdesk_departments_agents"; 
	$charset_collate3 = $wpdb->get_charset_collate();
	$sql3 = "CREATE TABLE $table3 (
		department_id mediumint(9) NOT NULL,
		agent_id mediumint(9) NOT NULL,
		FOREIGN KEY (department_id) REFERENCES wdesk_departments(id)
	) $charset_collate3;";
	dbDelta($sql3);
	
	// Tags
	$table4 = "wdesk_tags"; 
	$charset_collate4 = $wpdb->get_charset_collate();
	$sql4 = "CREATE TABLE $table4 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		color varchar(10) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate4;";
	dbDelta($sql4);
	
	// Tickets
	$table5 = "wdesk_tickets"; 
	$charset_collate5 = $wpdb->get_charset_collate();
	$sql5 = "CREATE TABLE $table5 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		created timestamp NOT NULL default CURRENT_TIMESTAMP,
		subject tinytext NOT NULL,
		status varchar(255),
		agent varchar(255),
		department varchar(255),
		tag varchar(255),
		user_email varchar(255) NOT NULL,
		user_name varchar(255) NOT NULL,
		last_update timestamp NOT NULL default CURRENT_TIMESTAMP,
		token varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate5;";
	dbDelta($sql5);
	
	// Threads
	$table6 = "wdesk_tickets_threads"; 
	$charset_collate6 = $wpdb->get_charset_collate();
	$sql6 = "CREATE TABLE $table6 (
		id mediumint(9) NOT NULL AUTO_INCREMENT, 
		ticket_id mediumint(9) NOT NULL,
		created timestamp NOT NULL default CURRENT_TIMESTAMP,
		text varchar(255) NOT NULL,
		note tinyint(1) DEFAULT 0,
		file varchar(255) NOT NULL,
		user_name varchar(255) NOT NULL,
		FOREIGN KEY (ticket_id) REFERENCES wdesk_tickets(id),
		UNIQUE KEY id (id)
	) $charset_collate6;";
	dbDelta($sql6);
	
	// Settings
	$table7 = "wdesk_settings"; 
	$charset_collate7 = $wpdb->get_charset_collate();
	$sql7 = "CREATE TABLE $table7 (
		id mediumint(9) NOT NULL,
		setting tinytext NOT NULL,
		value tinytext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate7;";
	dbDelta($sql7);
	
	// Default settings
	$wpdb->replace($table7, array(
		'id' => 0,
		'setting' => 'Helpdesk name',
		'value' => 'ExemCompany'
	));
	$wpdb->replace($table7, array(
		'id' => 1,
		'setting' => 'Sender email',
		'value' => 'email@example.com'
	));
	$wpdb->replace($table7, array(
		'id' => 2,
		'setting' => 'Helpdesk url',
		'value' => 'https://www.wordpress.org/'
	));
	$wpdb->replace($table7, array(
		'id' => 3,
		'setting' => 'Date format',
		'value' => 'd-m-Y H:i:s'
	));
	$wpdb->replace($table7, array(
		'id' => 4,
		'setting' => 'Max subject',
		'value' => '180'
	));
	$wpdb->replace($table7, array(
		'id' => 5,
		'setting' => 'Max thread',
		'value' => '2800'
	));
		
	// Settings emails
	$table8 = "wdesk_settings_emails"; 
	$charset_collate8 = $wpdb->get_charset_collate();
	$sql8 = "CREATE TABLE $table8 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		email tinytext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate8;";
	dbDelta($sql8);
	
	// Settings email providers
	$table9 = "wdesk_settings_email_providers"; 
	$charset_collate9 = $wpdb->get_charset_collate();
	$sql9 = "CREATE TABLE $table9 (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		provider tinytext NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate9;";
	dbDelta($sql9);

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
	$wpdb->query("DROP TABLE IF EXISTS wdesk_tags;");
   	$wpdb->query("DROP TABLE IF EXISTS wdesk_tickets;");
	$wpdb->query("DROP TABLE IF EXISTS wdesk_tickets_threads;");
   	$wpdb->query("DROP TABLE IF EXISTS wdesk_settings;");
}

// Setting up crons
 
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
	// Delete all users OTP every 5 minutes
	$wpdb->query("UPDATE `wdesk_users` SET `otp` = NULL WHERE `otp` IS NOT NULL; ");
	// Close inactive tickets
	$wpdb->query("UPDATE `wdesk_tickets` SET `status`='Closed' WHERE last_update < (NOW() - INTERVAL 1 MONTH); ");
}

if (!wp_next_scheduled('wdesk_cron_hook')) {
	wp_schedule_event(time(), 'five_minutes', 'wdesk_cron_hook');
}

?>