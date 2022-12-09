<?php
function wdesk_shortcode_component_ticket() {
	$return = '';
	if (isset($_GET['ticket'])) {
		global $wpdb;
		// Get ticket settings
		$wdesk_max_thread = get_option('wdesk_max_thread');
		//
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
			<textarea required type="text" name="thread" id="thread" placeholder="' . __('Describe your case', 'wdesk') . '" maxlength="' . $wdesk_max_thread . '" value="" style="height: 170px;"></textarea>
			<br>
			<input type="file" name="file" />
			<br>
			<input style="width: 100%;" type="submit" class="button action" name="wdesk-ticket-update" value="' . __('Send', 'wdesk') . '">
		</form>	
		';
	}
	return $return;
}
?>