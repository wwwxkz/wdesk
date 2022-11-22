<?php 

session_start();

// Components
include_once('components/guest.php');
include_once('components/styles/main.php');

function wdesk_shortcode_guest() {
    $return = '';
	$return .= wdesk_shortcode_component_guest();
    $return .= wdesk_shortcode_component_style_main();
    return $return;
}

?>