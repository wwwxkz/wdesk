<?php

function wdesk_settings() {
	global $wpdb;
	$wdesk_name 		= get_option('wdesk_name');
	$wdesk_sender 		= get_option('wdesk_sender');
	$wdesk_url 			= get_option('wdesk_url');
	$wdesk_date_format 	= get_option('wdesk_date_format');
	$wdesk_max_subject 	= get_option('wdesk_max_subject');
	$wdesk_max_thread 	= get_option('wdesk_max_thread');
	?>
	<div style="display: flex; margin-top: 15px; padding: 0; flex-direction: column; justify-content: space-between;">
	<h2><?php _e('Settings', 'wdesk') ?></h2>
		<div style="display: flex; flex-direction: row;">
			<table class="wp-list-table widefat fixed striped table-view-list" style="width: 400px; height: -webkit-fill-available;">
				<thead>
					<tr>
						<th><?php _e('General', 'wdesk') ?></th>
					</tr>
				</thead>
				<tbody>
					<form method="post">
						<tr>
							<th>
								<?php _e('Helpdesk', 'wdesk') ?>: <br>
								<input type="text" name="name" placeholder="<?php _e('Helpdesk name', 'wdesk') ?>" value="<?php echo esc_html($wdesk_name) ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<?php _e('Sender', 'wdesk') ?>: <br>
								<input type="text" name="email" placeholder="<?php _e('Sender email', 'wdesk') ?>" value="<?php echo esc_html($wdesk_sender) ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<?php _e('URL', 'wdesk') ?>: <br>
								<input type="text" name="url" placeholder="<?php _e('Helpdesk url', 'wdesk') ?>" value="<?php echo esc_html($wdesk_url) ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<?php _e('Date', 'wdesk') ?>: <br>
								<select name="date-format">
								  <option value="d-m-Y" <?php echo ($wdesk_date_format == "d-m-Y") ? 'selected' : '' ?>>d-m-Y</option>
								  <option value="m-d-Y" <?php echo ($wdesk_date_format == "m-d-Y") ? 'selected' : '' ?>>m-d-Y</option>
								  <option value="Y-m-d" <?php echo ($wdesk_date_format == "Y-m-d") ? 'selected' : '' ?>>Y-m-d</option>
								  <option value="d/m/Y" <?php echo ($wdesk_date_format == "d/m/Y") ? 'selected' : '' ?>>d/m/Y</option>
								  <option value="m/d/Y" <?php echo ($wdesk_date_format == "m/d/Y") ? 'selected' : '' ?>>m/d/Y</option>
								  <option value="Y/m/d" <?php echo ($wdesk_date_format == "Y/m/d") ? 'selected' : '' ?>>Y/m/d</option>
								  <option value="d-m-Y H:i:s" <?php echo ($wdesk_date_format == "d-m-Y H:i:s") ? 'selected' : '' ?>>d-m-Y H:i:s</option>
								  <option value="m-d-Y H:i:s" <?php echo ($wdesk_date_format == "m-d-Y H:i:s") ? 'selected' : '' ?>>m-d-Y H:i:s</option>
								  <option value="Y-m-d H:i:s" <?php echo ($wdesk_date_format == "Y-m-d H:i:s") ? 'selected' : '' ?>>Y-m-d H:i:s</option>
								  <option value="d/m/Y H:i:s" <?php echo ($wdesk_date_format == "d/m/Y H:i:s") ? 'selected' : '' ?>>d/m/Y H:i:s</option>
								  <option value="m/d/Y H:i:s" <?php echo ($wdesk_date_format == "m/d/Y H:i:s") ? 'selected' : '' ?>>m/d/Y H:i:s</option>
								  <option value="Y/m/d H:i:s" <?php echo ($wdesk_date_format == "Y/m/d H:i:s") ? 'selected' : '' ?>>Y/m/d H:i:s</option>
								</select>
							</th>
						</tr>
						<tr>
							<th>
								<?php _e('Max subject', 'wdesk') ?>: <br>
								<input type="number" name="subject" placeholder="<?php _e('Ex: 180', 'wdesk') ?>" value="<?php echo esc_html($wdesk_max_subject) ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<?php _e('Max thread', 'wdesk') ?>: <br>
								<input type="number" name="thread" placeholder="<?php _e('Ex: 2800', 'wdesk') ?>" value="<?php echo esc_html($wdesk_max_thread) ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<input type="submit" class="button action" name="wdesk-setting-update" value="<?php _e('Update', 'wdesk') ?>"/>							
							</th>
						</tr>
					</form>
				</tbody>
			</table>
			&nbsp;
			<table class="wp-list-table widefat fixed striped table-view-list" style="width: 400px; height: -webkit-fill-available;">
				<thead>
					<tr>
						<th><?php _e('Email blocklist', 'wdesk') ?></th>
					</tr>
				</thead>
				<tbody>
					<form method="post">
						<?php 
						$blocked_emails = $wpdb->get_results("SELECT * FROM `wdesk_blocklist_emails`");
						foreach ($blocked_emails as $blocked_email) {
							?>
							<tr>
								<th>
									<div style="display: flex; flex-direction: row; justify-content: space-between;">
										<input type="hidden" name="id" value="<?php echo esc_textarea($blocked_email->id) ?>" />
										<label><?php echo esc_textarea($blocked_email->email) ?></label>
										<input type="submit" class="button action" name="wdesk-setting-email-delete" value="<?php _e('Delete', 'wdesk') ?>"/>							
									</div>
								</th>
							</tr>
						<?php
						}
						?>
						<tr>
							<th>
								<?php _e('Email', 'wdesk') ?>: <br>
								<input type="text" name="email" placeholder="<?php _e('Full email', 'wdesk') ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<input type="submit" class="button action" name="wdesk-setting-email-add" value="<?php _e('Add', 'wdesk') ?>"/>							
							</th>
						</tr>
					</form>
				</tbody>
			</table>
			&nbsp;
			<table class="wp-list-table widefat fixed striped table-view-list" style="width: 400px; height: -webkit-fill-available;">
				<thead>
					<tr>
						<th><?php _e('Email provider blocklist', 'wdesk') ?></th>
					</tr>
				</thead>
				<tbody>
					<form method="post">
						<?php 
						$blocked_providers = $wpdb->get_results("SELECT * FROM `wdesk_blocklist_email_providers`");
						foreach ($blocked_providers as $blocked_provider) {
							?>
							<tr>
								<th>
									<div style="display: flex; flex-direction: row; justify-content: space-between;">
										<input type="hidden" name="id" value="<?php echo esc_textarea($blocked_provider->id) ?>" />
										<label><?php echo esc_textarea($blocked_provider->provider) ?></label>
										<input type="submit" class="button action" name="wdesk-setting-email-provider-delete" value="<?php _e('Delete', 'wdesk') ?>"/>							
									</div>
								</th>
							</tr>
						<?php
						}
						?>
						<tr>
							<th>
								<?php _e('Provider', 'wdesk') ?>: <br>
								<input type="text" name="provider" placeholder="<?php _e('Provider without @', 'wdesk') ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<input type="submit" class="button action" name="wdesk-setting-email-provider-add" value="<?php _e('Add', 'wdesk') ?>"/>							
							</th>
						</tr>
					</form>
				</tbody>
			</table>
		</div>
	</div>
<?php
}
?>