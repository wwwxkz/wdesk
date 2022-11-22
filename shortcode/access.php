<?php 

session_start();

// Log-in Sign-in
include_once('components/log-in.php');
include_once('components/sign-in.php');

// Recover
include_once('components/recover.php');

// Tickets panel
include_once('components/profile.php');
include_once('components/ticket.php');
include_once('components/tickets.php');
include_once('components/new-ticket.php');

// Scripts
include_once('components/styles/main.php');

// Styles
include_once('components/scripts/masks.php');


function wdesk_shortcode_access() {
    $return = '';
	
	// Tickets panel
	if (isset($_SESSION["wdesk-user-email"]) && isset($_SESSION["wdesk-user-password"])) {
		global $wpdb;
		$email = sanitize_email($_SESSION["wdesk-user-email"]);
		$password = $_SESSION["wdesk-user-password"];
		$user = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_users` WHERE email = %s AND password = %s", array($email, $password)));
		if(empty($user)) {
			echo '<script>alert("' . __('This user does not exist', 'wdesk') . '")</script>';
			session_destroy();
			header("refresh: 1");
		}
		$return .= wdesk_shortcode_component_tickets($user);
		$return .= wdesk_shortcode_component_ticket($user);
		$return .= wdesk_shortcode_component_profile($user);
		$return .= wdesk_shortcode_component_new_ticket($user);
	} else {
		$return .= wdesk_shortcode_component_sign_in();
		$return .= wdesk_shortcode_component_log_in();
		$return .= wdesk_shortcode_component_recover();
	}  
  
	// Commom scripts
    $return .= wdesk_shortcode_component_script_masks();
    $return .= wdesk_shortcode_component_style_main();
	
    return $return;
}

?>