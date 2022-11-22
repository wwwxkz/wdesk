<?php
require_once(WDESK_LOCAL . 'script/functions.php');
// User
add_action('admin_menu', 'wdesk_user');
add_action('template_redirect', 'wdesk_user');
// Ticket
add_action('admin_menu', 'wdesk_ticket');
add_action('template_redirect', 'wdesk_ticket');
// Department
add_action('admin_menu', 'wdesk_department');
add_action('template_redirect', 'wdesk_department');
// Tag
add_action('admin_menu', 'wdesk_tag');
add_action('template_redirect', 'wdesk_tag');
// Setting
add_action('admin_menu', 'wdesk_setting');
add_action('template_redirect', 'wdesk_setting');
?>