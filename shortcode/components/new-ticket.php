<?php
function wdesk_shortcode_component_new_ticket($users) {
	global $wpdb;
	// Get ticket settings
	$wdesk_max_subject 	= get_option('wdesk_max_subject');
	$wdesk_max_thread 	= get_option('wdesk_max_thread');
	//
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
				<input type="text" name="subject" value="" placeholder="' . __('Ticket subject', 'wdesk') . '" maxlength="' . $wdesk_max_subject . '" required />
				<br>
				<label>' . __('Description', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<textarea type="text" name="thread" placeholder="' . __('Ticket thread start', 'wdesk') . '" maxlength="' . $wdesk_max_thread . '" value="" style="height: 170px;" required></textarea>
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
?>