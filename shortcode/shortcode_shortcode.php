<?php 
session_start();
function wdesk_shortcode() {
    $return = '';
    if (isset($_GET['otp'])) {
    	$return .= wdesk_shortcode_otp(sanitize_text_field($_GET['otp']));
    }
    elseif (isset($_GET['ticket']) && isset($_GET['token'])) {
    	$return .= wdesk_shortcode_ticket_guest(sanitize_text_field($_GET['ticket']), sanitize_text_field($_GET['token']));
    }
    elseif (isset($_GET['ticket'])) {
    	$return .= wdesk_shortcode_ticket();
    }
    else {
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
	        $return .= wdesk_shortcode_tickets($user);
			$return .= wdesk_shortcode_ticket($user);
			$return .= wdesk_shortcode_profile($user);
			$return .= wdesk_shortcode_new_ticket($user);
	    } else {
	        $return .= wdesk_shortcode_login();
	        $return .= wdesk_shortcode_guest();
	    }  
    }
    $return .= wdesk_shortcode_script_masks();
    $return .= wdesk_shortcode_style();
    return $return;
}

function wdesk_shortcode_otp($otp) {
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

function wdesk_shortcode_login() {
    $return = '';
    $return .= '
        <div id="wdesk-shortcode-login">	
			<div style="display: flex; gap: 20px; flex-wrap: wrap; flex-direction: row;">
				<div style="flex-grow: 1;">
					<h1 style="color: #1a447a; flex-grow: 1;">' . __('Guest log-in', 'wdesk') . '</h1>
					<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
						document.getElementById(`wdesk-shortcode-login`).style.display = `none`;
						document.getElementById(`wdesk-shortcode-guest`).style.display = `block`;
					})();return false;" value="' . __('Send ticket as a guest', 'wdesk') . '" />
					<h1 style="color: #1a447a; flex-grow: 1;">' . __('Sign-in', 'wdesk') . '</h1>
					<form method="post">  
						<div style="display: flex; flex-direction: column;">
							<label>' . __('Email', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
							<input required type="email" name="email" placeholder="' . __('Valid email adress', 'wdesk') . '" />
							<br>
							<label>' . __('Name', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
							<input required type="text" name="name" placeholder="' . __('Full name', 'wdesk') . '" />
							<br>
							<label>' . __('Password', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
							<input onchange="confirmPassword();" type="password" required name="password" id="password" placeholder="' . __('A strong password', 'wdesk') . '" />
							<br>
							<label>' . __('Confirm your password', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
							<input onchange="confirmPassword();" type="password" required name="password-confirm" id="password-confirm" placeholder="' . __('Repeat your password', 'wdesk') . '" />
							<br>
							<input required type="submit" class="button action" id="wdesk-user-register" name="wdesk-user-register" value="' . __('Register', 'wdesk') . '" />
						</div>
					</form>
				</div>
				<div style="flex-grow: .5;">
					<h1 style="color: #1a447a; flex-grow: 1;">' . __('Log-in', 'wdesk') . '</h1>
					<form method="post">
						<div style="display: flex; flex-direction: column;">
							<label>' . __('Email', 'wdesk') . '</label>
							<input type="text" required name="email" placeholder="' . __('Valid email adress', 'wdesk') . '" />
							<br>
							<label>' . __('Password', 'wdesk') . '</label>
							<input type="password" required name="password" id="password" placeholder="' . __('A strong password', 'wdesk') . '" />
							<br>
							<input type="submit" required class="button action" name="wdesk-user-login" value="' . __('Log-in', 'wdesk') . '" />
						</div>
					</form>
					<h1 style="color: #1a447a;">' . __('Forgot your password', 'wdesk') . '?</h1>
					<form method="post" style="display: flex; flex-direction: column;">
						<input type="text" required name="email" placeholder="' . __('Last email used to acess', 'wdesk') . '" style="margin-bottom: 15px;"/>
						<input type="submit" required class="button action" name="wdesk-user-recover" value="' . __('Send email with the password', 'wdesk') . '" />
					</form>
				</div>
			</div>
        </div>
    ';
    return $return;
}

function wdesk_shortcode_guest() {
	global $wpdb;
	$return = '';
	$return .= '
        <div id="wdesk-shortcode-guest" style="display: none;">	
			<h1 style="color: #1a447a; flex-grow: 1;">' . __('Ticket', 'wdesk') . '</h1>
			<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
				document.getElementById(`wdesk-shortcode-login`).style.display = `block`;
				document.getElementById(`wdesk-shortcode-guest`).style.display = `none`;
			})();return false;" value="' . __('Log-in', 'wdesk') . '" />
			<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
				<label>' . __('Email', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input type="text" name="user-email" placeholder="' . __('Valid email adress', 'wdesk') . '" required />
				<br>
				<label>' . __('Name', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input type="text" name="thread-user" placeholder="' . __('Full name', 'wdesk') . '" required />
				<br>
				<div style="display: flex; flex-direction: column;">
					<label>' . __('Department', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
					';
					$departments = $wpdb->get_results("SELECT * FROM `wdesk_departments`");
					$return .= '<select name="department">';
						foreach ($departments as $department) {
							$return .= '<option value="' . $department->id . '">' . $department->name . '</option>';
						}
					$return .= '</select><br>';
					$return .= '
					<label>' . __('Subject', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
					<input type="text" name="subject" value="" placeholder="' . __('Ticket subject', 'wdesk') . '" required />
					<br>
					<label>' . __('Description', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
					<textarea type="text" name="thread" placeholder="' . __('Ticket thread start', 'wdesk') . '" style="height: 170px;" required></textarea>
					<br>
					<input type="file" name="file" />
					<br>
					<input style="width: 100%;" type="submit" class="button action" name="wdesk-ticket-new" value="' . __('Send', 'wdesk') . '">
				</div>	
			</form>
		</div>
	';
	return $return;
}

function wdesk_shortcode_tickets($users) {
	global $wpdb;
	$email = $users[0]->email;
	$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE user_email = %s", $email));
    $return = '';
    $return .= '
		<div id="wdesk-shortcode-tickets">
			<div style="display: flex; flex-direction: row; justify-content: space-between;">
				<h1 style="color: #1a447a; margin-bottom: 40px;">' . __('Tickets', 'wdesk') . '</h1>
				<div>
					<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
						document.getElementById(`wdesk-shortcode-tickets`).style.display = `none`; 
						document.getElementById(`wdesk-shortcode-profile`).style.display = `none`; 
						document.getElementById(`wdesk-shortcode-new`).style.display = `block`;
					})();return false;" value="' . __('New', 'wdesk') . '" />
					<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
						document.getElementById(`wdesk-shortcode-tickets`).style.display = `none`;
						document.getElementById(`wdesk-shortcode-profile`).style.display = `block`;
						document.getElementById(`wdesk-shortcode-new`).style.new = `none`; 
					})();return false;" value="' . __('Profile', 'wdesk') . '" />
				</div>
			</div>
			<table>
				<thead>
					<tr>						
						<th scope="col">#</th>
						<th scope="col">' . __('Status', 'wdesk') . '</th>
						<th scope="col">' . __('Created', 'wdesk') . '</th>
						<th scope="col">' . __('Subject', 'wdesk') . '</th>
						<th scope="col">' . __('Department', 'wdesk') . '</th>
						<th scope="col">' . __('Agent', 'wdesk') . '</th>
						<th scope="col">' . __('Actions', 'wdesk') . '</th>
					</tr>
				</thead>
				<tbody>';
				foreach ($tickets as $ticket) {
					$agent = get_user_by('id', $ticket->agent);
					$agent = (isset($agent->display_name)) ? $agent->display_name : '';
					if ($ticket->status != "Closed") {
						$id = $ticket->department;
						$department = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_departments` WHERE id = %s", $id));
						$return .= '
							<tr>
								<th><a href="?ticket=' . $ticket->token . '"><p>' 	. $ticket->id . '</p></a></th>
								<th><p>';
								if ($ticket->status == 'Open') {
									$return .= __('Open', 'wdesk');
								} elseif ($ticket->status == 'Waiting user') {
									$return .= __('Waiting user', 'wdesk');
								} elseif ($ticket->status == 'Waiting agent') {
									$return .= __('Waiting agent', 'wdesk');
								} elseif ($ticket->status == 'Closed') {
									$return .= __('Closed', 'wdesk');
								}
								$return .= '
								</p></th>	
								<th><p>' 									. $ticket->created . '</p></th>
								<th><a href="?ticket=' . $ticket->id . '"><p>' 	. $ticket->subject . '</p></a></th>
								<th><p>' 									. $department[0]->name . '</p></th>
								<th><p>' 									. $agent . '</p></th>
								<th>
									<form method="post" enctype="multipart/form-data">
										<input type="hidden" name="ticket" value="' . $ticket->id . '" />
										<input type="submit" name="wdesk-ticket-close" value="' . __('Close', 'wdesk') . '" />
									</form>
								</th>
							</tr>
						';
					}
				}
				$return .= '
				</tbody>
			</table>
		</div>
        ';
    return $return;    
}

function wdesk_shortcode_ticket() {
	$return = '';
	if (isset($_GET['ticket'])) {
		global $wpdb;
		$ticket = sanitize_text_field($_GET['ticket']);
		$ticket = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE id = %s", $ticket));
		$return .= '
		<script>
			document.getElementById(`wdesk-shortcode-tickets`).style.display = `none`; 
			document.getElementById(`wdesk-shortcode-profile`).style.display = `none`; 
			document.getElementById(`wdesk-shortcode-new`).style.display = `none`;
		</script>
		';
		$return .= '
		<div style="display: flex; flex-direction: row; justify-content: space-between;">
			<h1 style="color: #1a447a; margin-bottom: 40px;">' . __('Ticket', 'wdesk') . ' ' . $ticket[0]->id . '</h1>
			<div style="display: flex; flex-direction: row;">
				<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
					 window.location.replace(location.pathname);
				})();return false;" value="' . __('Tickets', 'wdesk') . '" />
				&nbsp;
				<form method="post" style="margin-bottom: 40px; margin-top: 20px;">
					<input type="hidden" name="ticket" value="' . $ticket[0]->id . '" />
					<input type="submit" name="wdesk-ticket-close" value="' . __('Close', 'wdesk') . '" style="height: 100%;" />
				</form>
			</div>
		</div>
		';
		$return .= '
		<table>
		<thead>
			<tr>
				<th colspan="100%">' . $ticket[0]->subject . '</th>
				<th colspan="1">' . __('User', 'wdesk') . '</th>
				<th colspan="1">' . __('File', 'wdesk') . '</th>
			</tr>
		</thead>
		';
		$ticket_id = $ticket[0]->id;
		$thread = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets_threads` WHERE ticket_id = %s AND note = 0", $ticket_id));
		$return .= '<tbody>';
		foreach ($thread as $response) {
			$return .= '
			<tr>
				<th colspan="100%">' . $response->text . '</th>
				<th colspan="1">' . $response->user_name . '</th>
				<th colspan="1">';
				if (isset($response->file) && $response->file != '') {
					$return .= '<a href="' . $response->file . '">' . __('Download', 'wdesk') . '</a>';
				}
				$return .= '
				</th>
			</tr>
			';
		}
		$return .= '
		</tbody>
		</table>
		';
		$return .= '
		<br>
		<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
			<input type="hidden" name="ticket" value="' . $ticket[0]->id . '"/>
			<input type="hidden" name="subject" value="' . $ticket[0]->subject . '"/>
			<input type="hidden" name="thread-user" value="' . $ticket[0]->user_name . '" />
			<textarea required type="text" name="thread" id="thread" placeholder="' . __('Describe your case', 'wdesk') . '" value="" style="height: 170px;"></textarea>
			<br>
			<input type="file" name="file" />
			<br>
			<input style="width: 100%;" type="submit" class="button action" name="wdesk-ticket-update" value="' . __('Send', 'wdesk') . '">
		</form>	
		';
	}
	return $return;
}

function wdesk_shortcode_ticket_guest($ticket_id, $token) {
	global $wpdb;
	$ticket = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE id = %s AND token = %s", array($ticket_id, $token)));
	$return = '';
	if (isset($ticket[0])) {
		$return .= '
		<div style="display: flex; flex-direction: row; justify-content: space-between;">
			<h1 style="color: #1a447a; margin-bottom: 40px;">' . __('Ticket', 'wdesk') . ' ' . $ticket[0]->id . '</h1>
			<div style="display: flex; flex-direction: row;">
				<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
					 window.location.replace(location.pathname);
				})();return false;" value="' . __('Tickets', 'wdesk') . '" />
				&nbsp;
				<form method="post" style="margin-bottom: 40px; margin-top: 20px;">
					<input type="hidden" name="ticket" value="' . $ticket[0]->id . '" />
					<input type="submit" name="wdesk-ticket-close" value="' . __('Close', 'wdesk') . '" style="height: 100%;" />
				</form>
			</div>
		</div>
		';
		$return .= '
		<table>
		<thead>
			<tr>
				<th colspan="100%">' . $ticket[0]->subject . '</th>
				<th colspan="1">' . __('User', 'wdesk') . '</th>
				<th colspan="1">' . __('File', 'wdesk') . '</th>
			</tr>
		</thead>
		';
		$ticket_id = $ticket[0]->id;
		$thread = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets_threads` WHERE ticket_id = %s AND note = 0", $ticket_id));
		$return .= '<tbody>';
		foreach ($thread as $response) {
			$return .= '
			<tr>
				<th colspan="100%">' . $response->text . '</th>
				<th colspan="1">' . $response->user_name . '</th>
				<th colspan="1">';
				if (isset($response->file) && $response->file != '') {
					$return .= '<a href="' . $response->file . '">' . __('Download', 'wdesk') . '</a>';
				}
				$return .= '
				</th>
			</tr>
			';
		}
		$return .= '
		</tbody>
		</table>
		';
		$return .= '
		<br>
		<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
			<input type="hidden" name="ticket" value="' . $ticket[0]->id . '"/>
			<input type="hidden" name="subject" value="' . $ticket[0]->subject . '"/>
			<input type="hidden" name="thread-user" value="' . $ticket[0]->user_name . '" />
			<textarea required type="text" name="thread" id="thread" placeholder="' . __('Describe your case', 'wdesk') . '" value="" style="height: 170px;"></textarea>
			<br>
			<input type="file" name="file" />
			<br>
			<input style="width: 100%;" type="submit" class="button action" name="wdesk-ticket-update" value="' . __('Send', 'wdesk') . '">
		</form>	
		';
	} else {
		$return .= __('No ticket found', 'wdesk');
	}
	return $return;
}

function wdesk_shortcode_profile($users) {
	$return = '';
	$return .= '
	<div id="wdesk-shortcode-profile" style="display: none;">
		<div style="display: flex; flex-direction: row; justify-content: space-between;">
			<h1 style="color: #1a447a; margin-bottom: 40px;">' . __('User profile', 'wdesk') . '</h1>
			<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
				document.getElementById(`wdesk-shortcode-tickets`).style.display = `block`; 
				document.getElementById(`wdesk-shortcode-profile`).style.display = `none`; 
				document.getElementById(`wdesk-shortcode-new`).style.display = `none`;
			})();return false;" value="' . __('Tickets', 'wdesk') . '" />
		</div>
		<form method="post" style="display: flex; flex-direction: column;">
			<input type="hidden" name="id" value="'. $users[0]->id .'">
			<div style="display: flex; flex-direction: column;">
				<label>' . __('Email', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="email" name="email" value="' . $users[0]->email. '" placeholder="' . __('Valid email adress', 'wdesk') . '" />
				<br>
				<label>' . __('Name', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="text" name="name" value="' . $users[0]->name. '" placeholder="' . __('Full name', 'wdesk') . '" />
				<br>
				<label>' . __('New password', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="text" name="password" placeholder="' . __('A strong password', 'wdesk') . '" value=""/>
				<br>
				<input style="width: 100%;" type="submit" class="button action" name="wdesk-user-update" value="' . __('Save', 'wdesk') . '">
				<input style="color: white; background-color: indianred; margin-top: 10px;width: 100%;" type="submit" class="button action" name="wdesk-user-logout" value="' . __('Logout', 'wdesk') . '">
			</div>
		</form>
	</div>
	';
	return $return;
}

function wdesk_shortcode_new_ticket($users) {
	global $wpdb;
	$return = '';
	$return .= '
	<div id="wdesk-shortcode-new" style="display: none;">
		<div style="display: flex; flex-direction: row; justify-content: space-between;">
			<h1 style="color: #1a447a; margin-bottom: 40px;">' . __('New ticket', 'wdesk') . '</h1>
			<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
				document.getElementById(`wdesk-shortcode-tickets`).style.display = `block`; 
				document.getElementById(`wdesk-shortcode-profile`).style.display = `none`; 
				document.getElementById(`wdesk-shortcode-new`).style.display = `none`;
			})();return false;" value="' . __('Tickets', 'wdesk') . '" />
		</div>
		<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
			<input type="hidden" name="user-email" value="'. $users[0]->email .'">
			<input type="hidden" name="thread-user" value="'. $users[0]->name .'">
			<div style="display: flex; flex-direction: column;">
				<label>' . __('Department', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				';
				$departments = $wpdb->get_results("SELECT * FROM `wdesk_departments`");
				$return .= '<select name="department">';
					foreach ($departments as $department) {
						$return .= '<option value="' . $department->id . '">' . $department->name . '</option>';
					}
				$return .= '</select><br>';
				$return .= '
				<label>' . __('Subject', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input type="text" name="subject" value="" placeholder="' . __('Ticket subject', 'wdesk') . '" required />
				<br>
				<label>' . __('Description', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<textarea type="text" name="thread" placeholder="' . __('Ticket thread start', 'wdesk') . '" value="" style="height: 170px;" required></textarea>
				<br>
				<input type="file" name="file" />
				<br>
				<input style="width: 100%;" type="submit" class="button action" name="wdesk-ticket-new" value="' . __('Send', 'wdesk') . '">
			</div>	
		</form>
	</div>
	';
	return $return;
}

function wdesk_shortcode_script_masks() {
    $return = '';
    $return .= '
    <script>
        function confirmPassword() {
            if (document.querySelectorAll("#password")[0].value === document.getElementById("password-confirm").value) {
                document.getElementById("wdesk-user-register").disabled = false;
                document.getElementById("wdesk-user-register").value = "' . __('Register', 'wdesk') . '";
            } else {
                document.getElementById("wdesk-user-register").disabled = true;
                document.getElementById("wdesk-user-register").value = "' . __('Password are not equal', 'wdesk') . '";
            }
        } 
    </script>
    ';
    return $return;
}

function wdesk_shortcode_style() {
    $return = '';
    $return .= '
    <style>
		label {
			margin-bottom: 15px;
		}
		.wp-container-1 {
			flex-basis: 0 !important;
		}
		input, select, textarea {
			border-radius: 0px !important;
			border-color: #cac6c6 !important;
		}
		table {
			border-collapse: collapse;
			width: 100%;
		}
		table td, table th {
			text-align: left;
			border: 1px solid #ddd;
			padding: 8px;
		}
		table tr:hover {
			background-color: #ddd;
		}	
		form, p {
			margin: 0;
			padding: 0;	
		}
    </style>
    ';
    return $return;
}
