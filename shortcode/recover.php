<?php 

session_start();

// Components
include_once('components/otp.php');
include_once('components/recover.php');

// Styles
include_once('components/styles/main.php');

function wdesk_shortcode_recover() {
    $return = '';
	// Link sent to email has ?otp= if clicked redirect to tickets/profile page with OTP temporary access
    if (isset($_GET['otp'])) {
    	$return .= wdesk_shortcode_component_otp(sanitize_text_field($_GET['otp']));
    } else {
		$return .= wdesk_shortcode_component_recover();
	}
	$return .= wdesk_shortcode_component_style_main();
    return $return;
}
?>