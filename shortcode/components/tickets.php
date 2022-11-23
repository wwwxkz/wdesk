<?php
function wdesk_shortcode_component_tickets($users) {
	global $wpdb;
	$email = $users[0]->email;
	$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE user_email = %s", $email));
	$settings = $wpdb->get_results("SELECT * FROM `wdesk_settings`");
	$date_format = $settings[3]->value;
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
						<th scope="col">' . __('Last update', 'wdesk') . '</th>
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
								<th><a href="?ticket=' . $ticket->id . '"><p>' 	. $ticket->id . '</p></a></th>
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
								<th><p>' . date($date_format, strtotime($ticket->created)) . '</p></th>
								<th><p>' . date($date_format, strtotime($ticket->last_update)) . '</p></th>
								<th><a href="?ticket=' . $ticket->id . '"><p>' 	. $ticket->subject . '</p></a></th>
								<th><p>' . $department[0]->name . '</p></th>
								<th><p>' . $agent . '</p></th>
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
?>