<?php

function wdesk_user()
{
    if (isset($_POST['wdesk-user-recover'])) {
        (isset($_POST['email']) ? $email = sanitize_email($_POST['email']) : $email = '');
        wdesk_helper_recover_password($email);
    }
    if (isset($_POST['wdesk-user-register']) || isset($_POST['wdesk-user-update'])) {
        global $wpdb;
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
		$users = $wpdb->get_results("SELECT 1 FROM wdesk_users WHERE email = $email;");
		if (isset($users[0]) && $users[0] >= 1 && isset($_POST['wdesk-user-register'])) {
			echo "<script>alert('" . __('Email already in use', 'wdesk') . "')</script>";
			return 1;
		} else {
			if (isset($_POST['id'])) {
				$id = sanitize_text_field($_POST['id']);
				$wpdb->replace(
					'wdesk_users',
					array(
						'id' => $id,
						'name' => $name,
						'email' => $email,
						'password' => $_COOKIE["wdesk-user-password"]                        
					)
				);
				echo "<script>alert('" . __('User updated', 'wdesk') . "')</script>";
				return 0;
			} else {
				$password = sha1(base64_encode($_POST["password"]));
				$wpdb->insert(
					'wdesk_users',
					array(
						'name' => $name,
						'email' => $email,
						'password' => $password
					)
				);
				echo "<script>alert('" . __('Registered successfully', 'wdesk') . "')</script>";
				setcookie("wdesk-user-email", $email, time() + 3600);
				setcookie("wdesk-user-password", $password, time() + 3600);
				header("refresh: 1");
				$subject = __('Registered successfully', 'wdesk');
				$message = __('Acess the helpdesk with your email and password', 'wdesk');
				wdesk_helper_send_mail($email, $subject, $message);
				return 0;
			}
			echo "<script>alert('" . __('User does not exist', 'wdesk') . "')</script>";
			return 1;
		}
    }
    if (isset($_POST['wdesk-user-login'])) {
        global $wpdb;
        $email = sanitize_email($_POST['email']);
        $password = sha1(base64_encode($_POST['password']));
        $result = $wpdb->get_results("SELECT * FROM `wdesk_users` WHERE email = '$email' AND password = '$password'");
        if (!empty($result)) {
            setcookie("wdesk-user-email", $email, time() + 3600);
            setcookie("wdesk-user-password", $password, time() + 3600);
            header("refresh: 1");
        }
        else {
            echo '<script>alert("' . __('Password incorrect or user does not exist', 'wdesk') . '");</script>';
        }
    }
    if (isset($_POST['wdesk-user-logout'])) {
        unset($_COOKIE['wdesk-user-email']);
        unset($_COOKIE['wdesk-user-password']);
        setcookie('wdesk-user-email', null, -1); 
        setcookie('wdesk-user-password', null, -1); 
    }
}

function wdesk_ticket()
{
	if (isset($_POST['wdesk-ticket-status'])) {
        global $wpdb;
		$wpdb->update(
			'wdesk_tickets',
			array(
				'status' => sanitize_text_field($_POST['status']),
				'agent' => sanitize_text_field($_POST['agent']),
				'department' => sanitize_text_field($_POST['department']),
			), array(
				'id' => $_POST['ticket'],
			)
		);	
	}
	if (isset($_POST['wdesk-ticket-notify'])) {
		wdesk_helper_notify_user(sanitize_text_field($_POST['ticket']));
	}
	if (isset($_POST['wdesk-ticket-close'])) {
		global $wpdb;	
		$wpdb->update(
			'wdesk_tickets',
			array(
				'status' => 'Closed',
			), array(
				'id' => sanitize_text_field($_POST['ticket']),
			)
		);
	}
    if (isset($_POST['wdesk-ticket-new'])) {
        global $wpdb;
		$file = isset($_FILES['file']) && $_FILES['file']['error'] == 0 ? wdesk_helper_save_file($_FILES['file']) : "";
		$thread = [[sanitize_textarea_field($_POST['thread']), sanitize_text_field($_POST['thread-user']), $file]];
		$thread = serialize($thread);
		$user = sanitize_text_field($_POST['user']);
		$tickets = $wpdb->get_results("SELECT * FROM `wdesk_tickets` WHERE user = '$user' and status != 'Closed'");
		if(count($tickets) <= 0) {
			$wpdb->insert(
				'wdesk_tickets',
				array(
					'subject' => sanitize_text_field($_POST['subject']),
					'thread' => $thread,
					'user' => $user,
					'status' => 'Open',
					'department' => sanitize_text_field($_POST['department'])
				)
			);
		} else {
			echo "<script>alert('" . __('Your already have a ticket open, wait until you ticket is solved or close it to create another', 'wdesk') . "')</script>";
		}
	}
    if (isset($_POST['wdesk-ticket-update'])) {
        global $wpdb;
		$id = sanitize_text_field($_POST['ticket']);
		$tickets = $wpdb->get_results("SELECT * FROM `wdesk_tickets` WHERE id = '$id'");
		$thread = $tickets[0]->thread;
		$thread = unserialize($thread);
		$file = isset($_FILES['file']) && $_FILES['file']['error'] == 0 ? wdesk_helper_save_file($_FILES['file']) : "";
		array_push($thread, [sanitize_textarea_field($_POST['thread']), sanitize_text_field($_POST['thread-user']), $file]);
		$thread = serialize($thread);
		$wpdb->update(
			'wdesk_tickets',
			array(
				'subject' => sanitize_text_field($_POST['subject']),
				'thread' => $thread,
				'user' => sanitize_text_field($_POST['user'])                     
			), array (
				'id' => sanitize_text_field($_POST['ticket']),
			)
		);
		wdesk_helper_notify_user($id);
		wdesk_helper_notify_agent($id);
	}
}

function wdesk_department()
{
	if (isset($_POST['wdesk-department-new'])) {
        global $wpdb;
		$wpdb->insert(
			'wdesk_departments',
			array(
				'name' => sanitize_text_field($_POST['name']),
			)
		);
	}
	if (isset($_POST['wdesk-department-update'])) {
        global $wpdb;
		$wpdb->update(
			'wdesk_departments',
			array(
				'name' => sanitize_text_field($_POST['name']),
				'agents' => (isset($_POST['agents'])) ? serialize($_POST['agents']) : array(),
			), array(
				'id' => sanitize_text_field($_POST['id']),
			)
		);
	}
	if (isset($_POST['wdesk-departments-delete'])) {
        global $wpdb;
		$wpdb->delete(
			'wdesk_departments',
			array(
				'id' => sanitize_text_field($_POST['id']),
			)
		);
	}
}

function wdesk_setting()
{
	if (isset($_POST['wdesk-setting-update'])) {
        global $wpdb;
		$wpdb->update(
			'wdesk_settings',
			array(
				'value' => sanitize_text_field($_POST['name']),
			), array(
				'id' => 0,
			)
		);
		$wpdb->update(
			'wdesk_settings',
			array(
				'value' => sanitize_email($_POST['email']),
			), array(
				'id' => 1,
			)
		);
	}
}

function wdesk_helper_send_mail($to, $subject, $message) 
{
	global $wpdb;
	$settings = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_settings`"));
    $headers[] = "From: $settings[1]->value";
    wp_mail($to, $subject, $message, $headers);
}

function wdesk_helper_recover_password($to)
{
    global $wpdb;
    $settings = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_settings`"));
    $users = $wpdb->get_results("SELECT * FROM `wdesk_users` WHERE email = '$to';");
    if (isset($users[0])) {
        $password = $users[0]->password;
		$subject =  __('Recover your helpdesk access password', 'wdesk');
		$message = __('Your password is: ', 'wdesk') . $password;
		$headers[] = "From: $settings[1]->value";
		wp_mail($to, $subject, $message, $headers);
    }
    echo '<script>alert("' . __('If your user is found in the database we will send you a email with its password. If not, sign-in as usual to access the helpdesk', 'wdesk') . '")</script>';
}

function wdesk_helper_notify_user($ticket_id) 
{
	global $wpdb;
	$id = $ticket_id;
	$tickets = $wpdb->get_results("SELECT * FROM `wdesk_tickets` WHERE id = '$id'");
	$id = $tickets[0]->user;
	$user = $wpdb->get_results("SELECT * FROM `wdesk_users` WHERE id = '$id'");
	$subject = __('Ticket update', 'wdesk');
	$message = __('Access the helpdesk by using your email and password', 'wdesk');
	(isset($user[0]->email) && $user[0]->email != "") ? wdesk_helper_send_mail($user[0]->email, $subject, $message) : '';
}

function wdesk_helper_notify_agent($ticket_id) 
{
	global $wpdb;
	$id = $ticket_id;
	$tickets = $wpdb->get_results("SELECT * FROM `wdesk_tickets` WHERE id = '$id'");
	$agent = get_user_by('id', $tickets[0]->agent);
	$subject = __('Ticket', 'wdesk') . " $id " . __('was updated', 'wdesk');
	$message = __('Ticket', 'wdesk') . " $id ";
	(isset($agent->user_email) && $agent->user_email != "") ? wdesk_helper_send_mail($agent->user_email, $subject, $message) : '';
}

function wdesk_helper_save_file($file)
{
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    $uploaded_file = wp_handle_upload($file, array('test_form' => false));
    if ($uploaded_file) {
        return $uploaded_file['url'];
    }
}