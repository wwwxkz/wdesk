<?php
function wdesk_helper_send_mail($to, $subject, $message) 
{
	global $wpdb;
	$wdesk_sender = get_option('wdesk_sender');
    $headers[] = "From: $wdesk_sender";
    wp_mail($to, $subject, $message, $headers);
}

function wdesk_helper_notify_user($ticket_id) 
{
	global $wpdb;
	$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE id = %s", $ticket_id));
	if (isset($tickets[0]->token) && 
		isset($tickets[0]->user_email) && 
		$tickets[0]->user_email != ""
	) {
		$subject = __('Ticket update', 'wdesk');
		$wdesk_url = get_option('wdesk_url');
		$token = $tickets[0]->token;
		$message = __("Access the helpdesk by using your email and password or using the url", 'wdesk') . " $wdesk_url?ticket=$ticket_id&token=$token";
		wdesk_helper_send_mail($tickets[0]->user_email, $subject, $message);
	}
}

function wdesk_helper_notify_agent($ticket_id) 
{
	global $wpdb;
	// Email message
	$subject = __('Ticket', 'wdesk') . " $ticket_id " . __('was updated', 'wdesk');
	$wdesk_url = get_option('wdesk_url');
	$message = __('Ticket', 'wdesk') . "$ticket_id." . __("Access the helpdesk by using the url") . " $wdesk_url";
	// Get ticket info
	$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE id = %s", $ticket_id));
	// Agent not assigned, use department email
	$agent = get_user_by('id', $tickets[0]->agent);
	if (!$agent) {
		$departments = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_departments` WHERE id = %s", $tickets[0]->department));
		wdesk_helper_send_mail($departments[0]->email, $subject, $message);
		return;
	}
	// Notify agent if it's email is set
	(isset($agent->user_email) && $agent->user_email != "") ? wdesk_helper_send_mail($agent->user_email, $subject, $message) : '';
}

function wdesk_helper_recover_password($email)
{
    global $wpdb;
    $users = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_users` WHERE email = %s;", $email));
    if (isset($users[0])) {
    	$otp = uniqid();
		$wpdb->update(
			'wdesk_users',
			array(
				// Set OTP code to be used as a password alternative while it is not reset after a scheduled time
				'otp' => $otp,
			), array(
				'id' => $users[0]->id,
			)
		);
		// Send email with website recover url and OTP code
		$wdesk_url = get_option('wdesk_url');
		$wdesk_sender = get_option('wdesk_sender');
		$subject =  __('Recover your helpdesk access password', 'wdesk');
		$message = __("Access $wdesk_url?recover=$otp to reset your password", 'wdesk');
		$headers[] = "From: $wdesk_sender";
		wp_mail($email, $subject, $message, $headers);
    }
    echo '<script>alert("' . __('If your user is found in the database we will send you a email with an OTP code and URL to reset your password. If not, sign-in or submit a ticket as a guest', 'wdesk') . '")</script>';
}

function wdesk_helper_save_file($file)
{
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    $uploaded_file = wp_handle_upload($file, array('test_form' => false));
    if ($uploaded_file) {
		// Returns url to be saved in the DB
        return $uploaded_file['url'];
    }
}

function wdesk_helper_download_ticket_csv($ticket_id) {
	global $wpdb;
    // Set filename
    $file = __('Ticket', 'wdesk') . '-' . $ticket_id;
    // Get thread of the ticket
    $csv = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets_threads` WHERE ticket_id = %s", $ticket_id));
    // Set header .csv
	$header['0'] = __('ID', 'wdesk');
	$header['1'] = __('Ticket', 'wdesk') . __('ID', 'wdesk');
	$header['2'] = __('Created', 'wdesk');
    $header['3'] = __('Text', 'wdesk');
    $header['4'] = __('Note', 'wdesk');
    $header['5'] = __('File', 'wdesk');
    $header['6'] = __('Username', 'wdesk');
    // Serialize object to .csv
    $output = '"' . implode('";"', $header) . '";' . "\n";
    foreach ($csv as $row) {
        $output .= '"' . implode('";"', (array) $row) . '";' . "\n";
    }
    $output .= "\n";
    // Output as .csv
    $filename = $file . "_" . date("Y-m-d_H-i", time()) . ".csv";
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"" . $filename . ".csv\";");
    header("Content-Transfer-Encoding: binary");
    print $output;
    exit;
}
?>