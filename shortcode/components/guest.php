<?php
function wdesk_shortcode_component_guest() {
	global $wpdb;
	// Get ticket settings
	$wdesk_max_subject 	= get_option('wdesk_max_subject');
	$wdesk_max_thread 	= get_option('wdesk_max_thread');
	$return = '';
	$return .= '
        <div id="wdesk-shortcode-guest">	
			<h1 style="color: #1a447a; flex-grow: 1;">' . __('Ticket', 'wdesk') . '</h1>
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
					<input type="text" name="subject" value="" placeholder="' . __('Ticket subject', 'wdesk') . '" maxlength="' . $wdesk_max_subject . '" required />
					<br>
					<label>' . __('Description', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
					<textarea type="text" name="thread" placeholder="' . __('Ticket thread start', 'wdesk') . '" maxlength="' . $wdesk_max_thread . '" style="height: 170px;" required></textarea>
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