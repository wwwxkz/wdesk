<?php 

session_start();

// Components
include_once('components/guest.php');
include_once('components/guest-ticket.php');
include_once('components/styles/main.php');

function wdesk_shortcode_guest() {
    $return = '';
	if (isset($_GET['ticket']) && isset($_GET['token'])) {
		$return .= wdesk_shortcode_component_guest_ticket();
	} else {
		$return .= wdesk_shortcode_component_guest();
	}
    $return .= wdesk_shortcode_component_style_main();
    return $return;
}

?>