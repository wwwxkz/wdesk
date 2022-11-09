<?php 

function wdesk_shortcode() 
{
    $return = '';
    if (isset($_COOKIE["wdesk-user-email"]) && isset($_COOKIE["wdesk-user-password"])) {
        global $wpdb;
        $email = $_COOKIE["wdesk-user-email"];
        $password = $_COOKIE["wdesk-user-password"];
        $user = $wpdb->get_results ("SELECT * FROM `wdesk_users` WHERE email = '$email' AND password = '$password'");
        if(empty($user)) {
            echo '<script>alert("' . __('This user does not exist', 'wdesk') . '")</script>';
			unset($_COOKIE['wdesk-user-email']);
			unset($_COOKIE['wdesk-user-password']);
			setcookie('wdesk-user-email', null, -1); 
			setcookie('wdesk-user-password', null, -1); 
			header("Refresh:0");
        }
        $return .= wdesk_shortcode_tickets($user);
		$return .= wdesk_shortcode_ticket($user);
		$return .= wdesk_shortcode_profile($user);
		$return .= wdesk_shortcode_new_ticket($user);
    } else {
        $return .= wdesk_shortcode_login();
    }  
    $return .= wdesk_shortcode_script_masks();
    $return .= wdesk_shortcode_style();
    return $return;
}

function wdesk_shortcode_login() 
{
    $return = '';
    $return .= '
        <div id="wdesk-login">	
			<div style="display: flex; gap: 20px; flex-wrap: wrap; flex-direction: row;">
				<div style="flex-grow: 1;">
					<h1 style="color: #1a447a; flex-grow: 1;">' . __('Sign-in', 'wdesk') . '</h1>
					<form method="post" enctype="multipart/form-data">  
						<div style="display: flex; flex-direction: column;">
							<label>' . __('Email', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
							<input required type="email" name="email" placeholder="' . __('Valid email adress', 'wdesk') . '" />
							<br>
							<label>' . __('Name', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
							<input required type="text" name="name" id="name" placeholder="' . __('Full name', 'wdesk') . '" />
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
				<div style="flex-grow: 1;">
					<h1 style="color: #1a447a; flex-grow: 1;">' . __('Log-in', 'wdesk') . '</h1>
					<form method="post" enctype="multipart/form-data">
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
					<!--
					<h1 style="color: #1a447a;">' . __('Forgot your password', 'wdesk') . '?</h1>
					<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
						<input type="text" required name="email" placeholder="' . __('Last email used to acess', 'wdesk') . '" style="margin-bottom: 15px;"/>
						<input type="submit" required class="button action" name="wdesk-user-recover" value="' . __('Send email with the password', 'wdesk') . '" />
					</form>
					 -->
				</div>
			</div>
        </div>
    ';
    return $return;
}

function wdesk_shortcode_tickets($users) {
	global $wpdb;
	$id = $users[0]->id;
	$tickets = $wpdb->get_results("SELECT * FROM `wdesk_tickets` WHERE user = '$id'");
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
						$department = $wpdb->get_results ("SELECT * FROM `wdesk_departments` WHERE id = '$id'");
						$return .= '
							<tr>
								<th><a href="?id=' . $ticket->id . '"><p>' 	. $ticket->id . '</p></a></th>
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
								<th><a href="?id=' . $ticket->id . '"><p>' 	. $ticket->subject . '</p></a></th>
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

function wdesk_shortcode_ticket($users) {
	$return = '';
	if (isset($_GET['id'])) {
		global $wpdb;
		$id = $_GET['id'];
		$ticket = $wpdb->get_results ("SELECT * FROM `wdesk_tickets` WHERE id = '$id'");
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
			<div>
				<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
					 window.location.replace(location.pathname);
				})();return false;" value="' . __('Tickets', 'wdesk') . '" />
				<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
					alert(`Por enquanto nada, sorry`);
				})();return false;" value="' . __('Close', 'wdesk') . '" />
			</div>
		</div>
		';
		$return .= '
		<table>
		<thead>
			<tr>
				<th colspan="2">' . $ticket[0]->subject . '</th>
			</tr>
		</thead>
		';
		$thread = unserialize($ticket[0]->thread);
		$return .= '<tbody>';
		foreach ($thread as $res) {
			$return .= '
			<tr>
				<th>' . $res[0] . '</th>
				<th>' . __('User', 'wdesk') . ': ' . $res[1] . '</th>
			</tr>
			';
		}
		$return .= '
		</tbody>
		</table>
		';
		$return .= '
		<h1 style="color: #1a447a; margin-bottom: 40px;">' . __('Answer ticket', 'wdesk') . '</h1>
		<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
			<input type="hidden" name="ticket" value="' . $ticket[0]->id . '"/>
			<input type="hidden" name="subject" value="' . $ticket[0]->subject . '"/>
			<input type="hidden" name="user" value="' . $users[0]->id . '" />
			<input type="hidden" name="thread-user" value="'. $users[0]->name .'">
			<textarea required type="text" name="thread" id="thread" placeholder="' . __('Describe your case', 'wdesk') . '" value="" style="height: 170px;"></textarea>
			<br>
			<input style="width: 100%;" type="submit" class="button action" name="wdesk-ticket-update" value="' . __('Send', 'wdesk') . '">
		</form>	
		';
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
		<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
			<input type="hidden" name="id" value="'. $users[0]->id .'">
			<input type="hidden" name="password" value="'. $users[0]->password .'">
			<div style="display: flex; flex-direction: column;">
				<label>' . __('Name', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="text" name="name" id="name" value="'. $users[0]->name .'" placeholder="' . __('Full name', 'wdesk') . '" />
				<br>
				<label>' . __('Email', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="email" name="email" value="' . $users[0]->email. '" placeholder="' . __('Valid email adress', 'wdesk') . '" />
				<br>
				<label>' . __('New password', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="password" name="password" id="password" placeholder="' . __('A strong password', 'wdesk') . '" value=""/>
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
			<input type="hidden" name="user" value="'. $users[0]->id .'">
			<input type="hidden" name="thread-user" value="'. $users[0]->name .'">
			<div style="display: flex; flex-direction: column;">
				<label>' . __('Department', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				';
				$departments = $wpdb->get_results ("SELECT * FROM `wdesk_departments`");
				$return .= '<select name="department">';
					foreach ($departments as $department) {
						$return .= '<option value="' . $department->id . '">' . $department->name . '</option>';
					}
				$return .= '</select><br>';
				$return .= '
				<label>' . __('Subject', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="text" name="subject" id="subject" value="" placeholder="' . __('Ticket subject', 'wdesk') . '" />
				<br>
				<label>' . __('Description', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<textarea required type="text" name="thread" id="thread" placeholder="' . __('Ticket thread start', 'wdesk') . '" value="" style="height: 170px;"></textarea>
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