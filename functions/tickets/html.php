<?php

function wdesk_tickets()
{
    global $wpdb;
	if (isset($_GET['token'])) {
		$token = sanitize_text_field($_GET['token']);
		$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE token = '$token'"));
		?>
		<div style="display: flex; margin-top: 15px; padding: 0; flex-direction: row; justify-content: space-between;">
			<h2>Ticket <?php echo esc_textarea($tickets[0]->id) ?></h2>
		</div>
		<div style="display: flex; flex-direction: row;">
			<table class="wp-list-table widefat fixed striped table-view-list" style="height: -moz-available; height: -webkit-fill-available;">
				<thead>
					<tr>
						<th colspan="4"><?php echo esc_textarea($tickets[0]->subject) ?></th>
						<th><?php _e('User', 'wdesk') ?></th>
						<th><?php _e('File', 'wdesk') ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$thread = unserialize($tickets[0]->thread); 
				foreach ($thread as $res) {
					?>
					<tr>
						<th colspan="4"><?php echo esc_textarea($res[0]) ?></th>
						<th><?php echo esc_textarea($res[1]) ?></th>
						<th>
						<?php 
						if (isset($res[2]) && $res[2] != '') {
							?>
							<a href="<?php echo esc_textarea($res[2]) ?>"><?php _e('Download', 'wdesk') ?></a> 
							<?php
						} 
						?>
						</th>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			&nbsp;
			<table class="wp-list-table widefat fixed striped table-view-list" style="width: 400px; height: -webkit-fill-available;">
				<thead>
					<tr>
						<th><?php _e('Details', 'wdesk') ?></th>
					</tr>
				</thead>
				<tbody>
					<form method="post">
						<input type="hidden" name="ticket" value="<?php echo esc_textarea($tickets[0]->id) ?>" />
						<input type="hidden" name="token" value="<?php echo esc_textarea($tickets[0]->token) ?>" />
						<input type="hidden" name="status" value="<?php echo esc_textarea($tickets[0]->status) ?>" />
						<input type="hidden" name="agent" value="<?php echo esc_textarea($tickets[0]->agent) ?>" />
						<tr><th>ID: <?php echo esc_textarea($tickets[0]->id) ?></th></tr>
						<tr>
							<th>
								<label><?php _e('Status', 'wdesk') ?>:</label>
								<br>
								<select name="status">
									<option value=""><?php _e('Select', 'wdesk') ?>...</option>								
									<option value="Open" 			<?php echo ($tickets[0]->status == "Open") ? 'selected' : ''; ?>	 		><?php _e('Open', 'wdesk') ?></option>
									<option value="Waiting user" 	<?php echo ($tickets[0]->status == "Waiting user") ? 'selected' : ''; ?>	><?php _e('Waiting user', 'wdesk') ?></option>
									<option value="Waiting agent" 	<?php echo ($tickets[0]->status == "Waiting agent") ? 'selected' : ''; ?>	><?php _e('Waiting agent', 'wdesk') ?></option>
									<option value="Closed" 			<?php echo ($tickets[0]->status == "Closed") ? 'selected' : ''; ?>			><?php _e('Closed', 'wdesk') ?></option>
								</select>
							</th>
						</tr>
						<tr>
							<th>
								<?php 
								$departments = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_departments`"));
								$id = $tickets[0]->department;
								$ticket_department = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_departments` WHERE id = '$id'"));
								?>
								<label><?php _e('Department', 'wdesk') ?>:</label>
								<br>
								<select name="department">
									<option value=""><?php _e('Select', 'wdesk') ?>...</option>
									<?php foreach ($departments as $department) { ?>
										<option value="<?php echo esc_textarea($department->id) ?>" <?php echo ($department->id == $tickets[0]->department) ? 'selected' : ''; ?> ><?php echo $department->name ?></option>
									<?php } ?>
								</select>
							</th>
						</tr>
						<tr>
							<th>
								<?php 
								$agent = get_user_by('id', $tickets[0]->agent);
								?>
								<label><?php _e('Agent', 'wdesk') ?>:</label>
								<br>
								<select name="agent">
									<option value=""><?php _e('Select', 'wdesk') ?>...</option>
									<?php 
									$users = get_users();
									foreach ($users as $user) { ?>
										<option value="<?php echo esc_textarea($user->id) ?>" <?php echo ($user->id == $tickets[0]->agent) ? 'selected' : ''; ?> ><?php echo esc_textarea($user->display_name) ?></option>
										<?php } ?>
								</select>
							</th>
						</tr>
						<tr><th><?php _e('User', 'wdesk') ?>: <?php echo esc_textarea($tickets[0]->user_name) ?></th></tr>
						<tr><th><?php _e('Created', 'wdesk') ?>: <?php echo esc_textarea($tickets[0]->created) ?></th></tr>
						<tr>
							<th>
								<input type="submit" class="button action" name="wdesk-ticket-status" value="<?php _e('Update', 'wdesk') ?>" />
								<input type="submit" class="button action" name="wdesk-ticket-notify" value="<?php _e('Notify', 'wdesk') ?>" />
							</th>
						</tr>
						
					</form>
				</tbody>
			</table>
		</div>
		<h2><?php _e('Answer ticket', 'wdesk') ?></h2>
		<form method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column;">
			<input type="hidden" name="ticket" value="<?php echo esc_textarea($tickets[0]->id) ?>"/>
			<input type="hidden" name="subject" value="<?php echo esc_textarea($tickets[0]->subject) ?>"/>
			<input type="hidden" name="user-email" value="<?php echo esc_textarea($tickets[0]->user_email) ?>" />
			<input type="hidden" name="thread-user" value="<?php echo esc_textarea(wp_get_current_user()->display_name) ?>" />
			<textarea type="text" name="thread" id="thread" placeholder="<?php _e('Please, describe your problem', 'wdesk') ?>" value="" style="height: 170px;" required></textarea>
			<br>
			<input type="file" name="file" />
			<br>
			<input style="width: 100%;" type="submit" class="button action" name="wdesk-ticket-update" value="<?php _e('Send', 'wdesk') ?>">
		</form>	
	<?php
	} else {
		$sql = "SELECT * FROM wdesk_tickets";
		$total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(1) FROM (${sql}) AS combined_table"));
		$items_per_page = 20;
		$page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
		$offset = ( $page * $items_per_page ) - $items_per_page;
		$tickets = $wpdb->get_results($wpdb->prepare($sql . " ORDER BY id LIMIT ${offset}, ${items_per_page}"));
		?>
		<div style="float: left; margin-top: 15px; padding: 0;">
			<h2><?php _e('Tickets', 'wdesk') ?></h2>
			<table class="wp-list-table widefat fixed striped table-view-list">
				<thead>
					<tr>
						<th>#</th>
						<th><?php _e('Status', 'wdesk') ?></th>
						<th><?php _e('Created', 'wdesk') ?></th>
						<th><?php _e('Department', 'wdesk') ?></th>
						<th><?php _e('Subject', 'wdesk') ?></th>
						<th><?php _e('User', 'wdesk') ?></th>
						<th><?php _e('Agent', 'wdesk') ?></th>
						<th><?php _e('Actions', 'wdesk') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$departments = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_departments`"));
					$wp_user = wp_get_current_user();
					$wp_user_id = $wp_user->id;
					$wp_user_groups = array();
					foreach ($departments as $department) {
						if (in_array($wp_user_id, unserialize($department->agents))) {
							array_push($wp_user_groups, $department->id);
						}
					}
					foreach ($tickets as $i => $ticket) {
						if ($ticket->agent == '' ||
							$ticket->agent == " " ||
							$ticket->agent == get_current_user_id() ||
							in_array($ticket->department, $wp_user_groups) ||
							current_user_can('administrator')
						) {
							$agent = get_user_by('id', $ticket->agent);
							$agent = (isset($agent->display_name)) ? $agent->display_name : '';
							$id = $ticket->department;
							$department = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_departments` WHERE id = '$id'"));
							?>
								<tr>
									<th><a onclick="(function(){
										var searchParams = new URLSearchParams(window.location.search);
										searchParams.set(`token`, `<?php echo esc_textarea($ticket->token) ?>`);
										window.location.search = searchParams.toString();
									})();return false;"><?php echo esc_textarea($ticket->id) ?></a></th>
									<th>
										<?php
											if ($ticket->status == 'Open') {
												_e('Open', 'wdesk');
											} elseif ($ticket->status == 'Waiting user') {
												_e('Waiting user', 'wdesk');
											} elseif ($ticket->status == 'Waiting agent') {
												_e('Waiting agent', 'wdesk');
											} elseif ($ticket->status == 'Closed') {
												_e('Closed', 'wdesk');
											} else {
												echo '';
											}
										?>
									</th>
									<th><?php echo esc_textarea($ticket->created) ?></th>
									<th><?php echo (isset($department[0]->name)) ? $department[0]->name : '' ?></th>
									<th><a onclick="(function(){
										var searchParams = new URLSearchParams(window.location.search);
										searchParams.set(`token`, `<?php echo esc_textarea($ticket->token) ?>`);
										window.location.search = searchParams.toString();
									})();return false;"><?php echo esc_textarea($ticket->subject) ?></a></th>
									<th><?php echo esc_textarea($ticket->user_name) ?></th>
									<th><?php echo esc_textarea($agent) ?></th>
									<th>
										<form method="post">
											<input type="submit" name="<?php echo esc_textarea($ticket->id) ?>-ticket-delete" value="<?php _e('Delete', 'wdesk') ?>" class="button action">  
										</form>
									</th>
								</tr>
							<?php
								if (isset($_POST[$ticket->id . '-ticket-delete'])) {
									$wpdb->get_results($wpdb->prepare("DELETE FROM wdesk_tickets WHERE id=$ticket->id"));
									echo "<script>window.location.reload()</script>";
								}
							}
							?>
							<?php 
						}
						?>
					
				</tbody>
			</table>
			<?php
				echo paginate_links( array(
					'base' => add_query_arg( 'cpage', '%#%' ),
					'format' => '',
					'prev_text' => __('&laquo;'),
					'next_text' => __('&raquo;'),
					'total' => ceil($total / $items_per_page),
					'current' => $page
				));
			?>
		</div>
		<?php
		}
	}
?>