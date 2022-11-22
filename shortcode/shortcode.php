<?php
require_once(WDESK_LOCAL . 'shortcode/recover.php');
require_once(WDESK_LOCAL . 'shortcode/sign-in.php');
require_once(WDESK_LOCAL . 'shortcode/log-in.php');
require_once(WDESK_LOCAL . 'shortcode/access.php');
require_once(WDESK_LOCAL . 'shortcode/guest.php');
add_shortcode('wdesk_recover', 			'wdesk_shortcode_recover');
add_shortcode('wdesk_sign_in', 			'wdesk_shortcode_sign_in');
add_shortcode('wdesk_log_in', 			'wdesk_shortcode_log_in');
add_shortcode('wdesk_access', 			'wdesk_shortcode_access');
add_shortcode('wdesk_guest', 			'wdesk_shortcode_guest');
?>