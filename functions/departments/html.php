<?php

function wdesk_departments() {
	if (current_user_can('administrator')) { ?>
	<div style="display: flex; margin-top: 15px; padding: 0; flex-direction: column; justify-content: space-between;">
	<h2><?php _e('Departments', 'wdesk') ?></h2>
		<div style="display: flex; flex-direction: row;">
			<?php 
				global $wpdb;
				$departments = $wpdb->get_results("SELECT * FROM `wdesk_departments`");
				foreach ($departments as $department) { ?>
					<table class="wp-list-table widefat fixed striped table-view-list" style="width: 400px; height: -webkit-fill-available;">
						<thead>
							<tr>
								<th><?php _e('Department', 'wdesk') ?> <?php echo $department->id ?></th>
							</tr>
						</thead>
						<tbody>
							<form method="post">
								<input type="hidden" name="id" value="<?php echo $department->id ?>"/>
								<tr><th><?php _e('Name', 'wdesk') ?>: <br><input type="text" name="name" placeholder="<?php _e('Department name', 'wdesk') ?>" value="<?php echo $department->name ?>" style="padding: 0 8px; margin: 0;"/></th></tr>
								<tr>
									<th>
										Agents
										<?php 
										$agents = get_users();
										$department_agents = unserialize($department->agents);
										$department_agents = (isset($department_agents) && is_array($department_agents)) ? $department_agents : array();
										foreach ($agents as $agent) {
											?>
											<div>
											<?php
											if (in_array($agent->id, $department_agents)) { ?>
												<input type="checkbox" name="agents[]" value="<?php echo $agent->id ?>" style="margin: 3px 0px 0px 0px;" checked />
											<?php
											} else {
											?>
												<input type="checkbox" name="agents[]" value="<?php echo $agent->id ?>" style="margin: 3px 0px 0px 0px;" />
											<?php
											}
											?>
												<label for="<?php echo $agent->id ?>"><?php echo $agent->display_name ?></label>
											</div>
										<?php
										}
										?>
									</th>
								</tr>
								<tr>
									<th>
										<input type="submit" class="button-primary action" name="wdesk-department-update" value="<?php _e('Update', 'wdesk') ?>" />
										<input type="submit" class="button action" 		   name="wdesk-department-delete" value="<?php _e('Delete', 'wdesk') ?>" />
									</th>
								</tr>
							</form>
						</tbody>
					</table>
					&nbsp;
				<?php
				}
				?>
			<table class="wp-list-table widefat fixed striped table-view-list" style="width: 400px; height: -webkit-fill-available;">
				<thead>
					<tr>
						<th><?php _e('New department', 'wdesk') ?></th>
					</tr>
				</thead>
				<tbody>
					<form method="post">
						<tr><th><?php _e('Name', 'wdesk') ?>: <br><input type="text" name="name" placeholder="<?php _e('Department name', 'wdesk') ?>" style="padding: 0 8px; margin: 0;"/></th></tr>
						<tr>
							<th>
								<input type="submit" class="button action" name="wdesk-department-new" value="<?php _e('Create', 'wdesk') ?>" />
							</th>
						</tr>
					</form>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	} else {
		?>
		<div style="display: flex; margin-top: 15px; padding: 0; flex-direction: column; justify-content: space-between;">
		<h2><?php _e('You do not have acess to this page', 'wdesk') ?></h2>
		</div>
		<?php
	}
}

?>