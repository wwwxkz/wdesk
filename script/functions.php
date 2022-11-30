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
		// Is the email or provider in the blocklist?
		$provider = substr($email, strpos($email, '@') + 1);
		$blocked_emails = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_settings_emails` WHERE email = %s", $email));
		$blocked_providers = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_settings_email_providers` WHERE provider = %s", $provider));
		if (count($blocked_providers) <= 0 && count($blocked_emails) <= 0) {
			// Is the email being used?
			$users = $wpdb->get_results($wpdb->prepare("SELECT 1 FROM wdesk_users WHERE email = %s;", $email));
			if (isset($users[0]) && $users[0] >= 1 && isset($_POST['wdesk-user-register'])) {
				echo "<script>alert('" . __('Email already in use', 'wdesk') . "')</script>";
				return 1;
			} else {
				// Update existing user
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
				// Create new user
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
		} else {
			echo "<script>alert('" . __('Your personal email or provider is in our blocklist', 'wdesk') . "')</script>";
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
	// Notify user manually
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
        $user_email = sanitize_email($_POST['user-email']);
		$thread_user = sanitize_text_field($_POST['thread-user']);
		// Is provider or email in the blocklist?
		$provider = substr($user_email, strpos($user_email, '@') + 1);
		$blocked_emails = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_settings_emails` WHERE email = %s", $user_email));
		$blocked_providers = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_settings_email_providers` WHERE provider = %s", $provider));
		if (count($blocked_providers) <= 0 && count($blocked_emails) <= 0) {
			// This user_email already has tickets?
			$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE user_email = %s and status != 'Closed'", $user_email));
			if (count($tickets) <= 0) {
				// Unique ticket id (token)
				$token = uniqid();
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
				// Success, update last_update and notify user
				$wpdb->query($wpdb->prepare("UPDATE `wdesk_tickets` SET `last_update`= NOW() WHERE `id` = %s", $wpdb->insert_id));
				wdesk_helper_notify_user($token);
			} else {
				echo "<script>alert('" . __('Your already have a ticket open, wait until you ticket is solved or close it to create another', 'wdesk') . "')</script>";
			}
		} else {
			echo "<script>alert('" . __('Your personal email or provider is in the blocklist', 'wdesk') . "')</script>";
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
	if (isset($_POST['wdesk-ticket-tag-add'])) {
        global $wpdb;
		$ticket_id = sanitize_text_field($_POST['ticket']);
		$tag = sanitize_text_field($_POST['tag']);
		$wpdb->update(
			'wdesk_tickets',
			array(
				'tag' => $tag,
			), array(
				'id' => $ticket_id,
			)
		);
	}
	if (isset($_POST['wdesk-ticket-tag-remove'])) {
	  global $wpdb;
		$ticket_id = sanitize_text_field($_POST['ticket']);
		$wpdb->update(
			'wdesk_tickets',
			array(
				'tag' => '',
			), array(
				'id' => $ticket_id,
			)
		);
	}
	// Same as update, but with note flag set to 1
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
		// Update related agents in related DB
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

function wdesk_tag()
{
	if (isset($_POST['wdesk-tag-new'])) {
        global $wpdb;
		$wpdb->insert(
			'wdesk_tags',
			array(
				'name' => sanitize_text_field($_POST['name']),
				'color' => sanitize_text_field($_POST['color'])
			)
		);
	}
	if (isset($_POST['wdesk-tag-update'])) {
        global $wpdb;
		$tag_id = sanitize_text_field($_POST['id']);
		$wpdb->update(
			'wdesk_tags',
			array(
				'name' => sanitize_text_field($_POST['name']),
				'color' => sanitize_text_field($_POST['color'])
			), array(
				'id' => $tag_id,
			)
		);
	}
	if (isset($_POST['wdesk-tag-delete'])) {
        global $wpdb;
		$tag_id = sanitize_text_field($_POST['id']);
		$wpdb->delete(
			'wdesk_tags',
			array(
				'id' => $tag_id,
			)
		);
	}
}

function wdesk_setting()
{
	// Update all 3 settings values together
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
		$wpdb->update(
			'wdesk_settings',
			array(
				'value' => sanitize_text_field($_POST['date-format']),
			), array(
				'id' => 3,
			)
		);
	}
	if (isset($_POST['wdesk-setting-email-add'])) {
        global $wpdb;
		$wpdb->insert(
			'wdesk_settings_emails',
			array(
				'email' => sanitize_text_field($_POST['email']),
			)
		);
	}
	if (isset($_POST['wdesk-setting-email-delete'])) {
        global $wpdb;
		$wpdb->delete(
			'wdesk_settings_emails',
			array(
				'id' => sanitize_text_field($_POST['id']),
			)
		);
	}
	if (isset($_POST['wdesk-setting-email-provider-add'])) {
        global $wpdb;
		$wpdb->insert(
			'wdesk_settings_email_providers',
			array(
				'provider' => sanitize_text_field($_POST['provider']),
			)
		);
	}
	if (isset($_POST['wdesk-setting-email-provider-delete'])) {
        global $wpdb;
		$wpdb->delete(
			'wdesk_settings_email_providers',
			array(
				'id' => sanitize_text_field($_POST['id']),
			)
		);
	}
}
?>