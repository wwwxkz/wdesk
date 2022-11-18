<?php
require_once(WDESK_LOCAL . 'script/helpers.php');
session_start();
function wdesk_user()
{
    if (isset($_POST['wdesk-user-recover'])) {
        (isset($_POST['email']) ? $email = sanitize_email($_POST['email']) : $email = '');
        wdesk_helper_recover_password($email);
    }
    if (isset($_POST['wdesk-user-register']) || isset($_POST['wdesk-user-update'])) {
        global $wpdb;
        $email = sanitize_email($_POST['email']);
        $name = sanitize_text_field($_POST['name']);
		$users = $wpdb->get_results($wpdb->prepare("SELECT 1 FROM wdesk_users WHERE email = %s;", $email));
		if (isset($users[0]) && $users[0] >= 1 && isset($_POST['wdesk-user-register'])) {
			echo "<script>alert('" . __('Email already in use', 'wdesk') . "')</script>";
			return 1;
		} else {
			if (isset($_POST['id'])) {
				$id = sanitize_text_field($_POST['id']);
				$password = sha1(base64_encode($_POST["password"]));
				$wpdb->replace(
					'wdesk_users',
					array(
						'id' => $id,
						'email' => $email,
						'name' => $name,
						'password' => $password                 
					)
				);
				$_SESSION['wdesk-user-email'] = $email;
				$_SESSION['wdesk-user-password'] = $password;
				echo "<script>alert('" . __('User updated', 'wdesk') . "')</script>";
				echo "<script>window.location = window.location.pathname</script>";
				return 0;
			} else {
				$password = sha1(base64_encode($_POST["password"]));
				$wpdb->insert(
					'wdesk_users',
					array(
						'email' => $email,
						'password' => $password
					)
				);
				echo "<script>alert('" . __('Registered successfully', 'wdesk') . "')</script>";
				$_SESSION['wdesk-user-email'] = $email;
				$_SESSION['wdesk-user-password'] = $password;
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
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_users` WHERE email = %s AND password = %s", array($email, $password)));
        if (!empty($result)) {
			$_SESSION['wdesk-user-email'] = $email;
			$_SESSION['wdesk-user-password'] = $password;
            header("refresh: 1");
        }
        else {
            echo '<script>alert("' . __('Password incorrect or user does not exist', 'wdesk') . '");</script>';
        }
    }
    if (isset($_POST['wdesk-user-logout'])) {
        session_destroy();
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
				'id' => sanitize_text_field($_POST['ticket']),
			)
		);	
	}
	if (isset($_POST['wdesk-ticket-notify'])) {
		wdesk_helper_notify_user(sanitize_text_field($_POST['token']));
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
        $user_email = sanitize_email($_POST['user-email']);
		$thread_user = sanitize_text_field($_POST['thread-user']);
		$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE user_email = %s and status != 'Closed'", $user_email));
		$token = uniqid();
		if(count($tickets) <= 0) {
			$wpdb->insert(
				'wdesk_tickets',
				array(
					'subject' => sanitize_text_field($_POST['subject']),
					'user_email' => $user_email,
					'user_name' => $thread_user,
					'token' => $token,
					'status' => 'Open',
					'department' => sanitize_text_field($_POST['department'])
				)
			);
			$text = sanitize_textarea_field($_POST['thread']);
			$file = isset($_FILES['file']) && $_FILES['file']['error'] == 0 ? wdesk_helper_save_file($_FILES['file']) : "";
			$wpdb->insert(
				'wdesk_tickets_threads',
				array(
					'ticket_id' => $wpdb->insert_id,
					'text' => $text,
					'file' => $file,
					'user_name' => $thread_user
				)
			);
			$wpdb->query($wpdb->prepare("UPDATE `wdesk_tickets` SET `last_update`= NOW() WHERE `id` = %s", $wpdb->insert_id));
			wdesk_helper_notify_user($token);
		} else {
			echo "<script>alert('" . __('Your already have a ticket open, wait until you ticket is solved or close it to create another', 'wdesk') . "')</script>";
		}
	}
    if (isset($_POST['wdesk-ticket-update'])) {
        global $wpdb;
		$ticket_id = sanitize_text_field($_POST['ticket']);
		$text = sanitize_textarea_field($_POST['thread']);		
		$user_name = sanitize_text_field($_POST['thread-user']);		
		$file = isset($_FILES['file']) && $_FILES['file']['error'] == 0 ? wdesk_helper_save_file($_FILES['file']) : "";
		$wpdb->insert(
			'wdesk_tickets_threads',
			array(
				'ticket_id' => $ticket_id,
				'text' => $text,
				'file' => $file,
				'user_name' => $user_name
			)
		);
		$wpdb->query($wpdb->prepare("UPDATE `wdesk_tickets` SET `last_update`= NOW() WHERE `id` = %s", $ticket_id));
		wdesk_helper_notify_user($ticket_id);
		wdesk_helper_notify_agent($ticket_id);
	}
	if (isset($_POST['wdesk-ticket-note'])) {
        global $wpdb;
		$ticket_id = sanitize_text_field($_POST['ticket']);
		$text = sanitize_textarea_field($_POST['thread']);		
		$user_name = sanitize_text_field($_POST['thread-user']);		
		$file = isset($_FILES['file']) && $_FILES['file']['error'] == 0 ? wdesk_helper_save_file($_FILES['file']) : "";
		$wpdb->insert(
			'wdesk_tickets_threads',
			array(
				'ticket_id' => $ticket_id,
				'text' => $text,
				'file' => $file,
				'note' => 1,
				'user_name' => $user_name
			)
		);
		$wpdb->query($wpdb->prepare("UPDATE `wdesk_tickets` SET `last_update`= NOW() WHERE `id` = %s", $ticket_id));
	}
	if (isset($_POST['wdesk-ticket-download'])) {
		wdesk_helper_download_ticket_csv(sanitize_text_field($_POST['ticket']));
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
		$department_id = sanitize_text_field($_POST['id']);
		$wpdb->query(
			$wpdb->prepare("DELETE FROM `wdesk_departments_agents` WHERE `department_id` = %s", $department_id)
		);
		$wpdb->update(
			'wdesk_departments',
			array(
				'name' => sanitize_text_field($_POST['name']),
			), array(
				'id' => $department_id,
			)
		);
		$agents = isset($_POST['agents']) ? (array) $_POST['agents'] : array();
		$agents = array_map('sanitize_text_field', $agents );
		$actual_agents = $wpdb->get_results($wpdb->prepare("SELECT agent_id FROM `wdesk_departments_agents` WHERE `department_id` = %s", $department_id));
		foreach ($agents as $agent) {	
			if (!in_array($agent, $actual_agents)) {
				$wpdb->insert(
					'wdesk_departments_agents',
					array(
						'department_id' => $department_id,
						'agent_id' => $agent,
					)
				);	
			}
		}
	}
	if (isset($_POST['wdesk-department-delete'])) {
        global $wpdb;
		$department_id = sanitize_text_field($_POST['id']);
		$wpdb->query(
			$wpdb->prepare("DELETE FROM `wdesk_departments_agents` WHERE `department_id` = %s", $department_id)
		);
		$wpdb->delete(
			'wdesk_departments',
			array(
				'id' => $department_id,
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
		$wpdb->update(
			'wdesk_settings',
			array(
				'value' => sanitize_text_field($_POST['url']),
			), array(
				'id' => 2,
			)
		);
	}
}
?>