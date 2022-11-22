<?php
function wdesk_shortcode_component_otp($otp) {
	$return = '';
	global $wpdb;
	$users = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_users` WHERE `otp` = %s;", $otp));
	if(isset($users[0])) {
		$return .= wdesk_shortcode_profile($users);
		$return .= '
		<script>
			document.getElementById(`wdesk-shortcode-profile`).style.display = `block`;
		</script>
		';
	} else {
		$return .= '<h1 style="color: #1a447a; flex-grow: 1;">' . __('Invalid OTP code', 'wdesk') . '</h1>';
	}
	return $return;
}
?>