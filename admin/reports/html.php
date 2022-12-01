<?php

function wdesk_reports() {
?>
	<div style="display: flex; margin-top: 15px; padding: 0; flex-direction: column; justify-content: space-between;">
	<h2><?php _e('Reports', 'wdesk') ?></h2>
		<div style="display: flex; flex-direction: row;">
		<?php 
			global $wpdb;
			$users = get_users();
			foreach ($users as $user) {
				$tickets = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wdesk_tickets` WHERE agent = %s", $user->id));
				// Map departments and tags with respective name
				$departments = $wpdb->get_results("SELECT * FROM `wdesk_departments`");
				$tags = $wpdb->get_results("SELECT * FROM `wdesk_tags`");
				$departments_map = array();
				foreach ($departments as $department) {
					$departments_map[$department->id] = $department->name;
				}
				$tags_map = array();
				foreach ($tags as $tag) {
					$tags_map[$tag->id] = $tag->name;
				}
				// Number of tickets of the same tag, department
				$tags_number = array();
				$departments_number = array();
				foreach ($tickets as $ticket) {
					// Tag
					if (isset($tags_number[$ticket->tag])) {
						$tags_number[$ticket->tag] += 1;
					} else {
						$tags_number[$ticket->tag] = 1;
					}
					// Department
					if (isset($departments_number[$ticket->department])) {
						$departments_number[$ticket->department] += 1;
					} else {
						$departments_number[$ticket->department] = 1;
					}
				}
				?>
				<table class="wp-list-table widefat fixed striped table-view-list" style="width: 400px; height: -webkit-fill-available;">
					<thead>
						<tr>
							<th><?php echo esc_textarea($user->display_name) ?></th>
						</tr>
					</thead>
					<tbody>
						<form method="post">
							<input type="hidden" name="id" value="<?php echo esc_textarea($user->id) ?>"/>
							<tr>
								<th>
									<?php echo __('Number of tickets', 'wdesk') . ': ' . count($tickets); ?>
								</th>
							</tr>
							<tr>
								<th>
									Departments: <br>
									<?php 
									foreach ($departments_number as $key=>$department) {
										echo '# ' . $departments_map[$key] . ': ' . $department . '<br>'; 
									}
									?>
								</th>
							</tr>
							<tr>
								<th>
									Tags: <br>
									<?php 
									foreach ($tags_number as $key=>$tag) {
										echo '# ' . $tags_map[$key] . ': ' . $tag . '<br>';
									}
									?>
								</th>
							</tr>
						</form>
					</tbody>
				</table>
				&nbsp;
			<?php
			}
			?>
		</div>
	</div>
<?php
}
?>