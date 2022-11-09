<?php
require_once(WDESK_LOCAL . 'functions/frontend/script.php');
require_once(WDESK_LOCAL . 'functions/frontend/shortcode.php');
add_shortcode('wdesk', 'wdesk_shortcode');
add_action('admin_menu', 'wdesk_user');
add_action('template_redirect', 'wdesk_user');
add_action('admin_menu', 'wdesk_ticket');
add_action('template_redirect', 'wdesk_ticket');
add_action('admin_menu', 'wdesk_department');
add_action('template_redirect', 'wdesk_department');
?>