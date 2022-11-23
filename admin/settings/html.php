<?php

function wdesk_settings() {
	global $wpdb;
	$settings = $wpdb->get_results("SELECT * FROM `wdesk_settings`");
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
								<input type="text" name="name" placeholder="<?php _e('Helpdesk name', 'wdesk') ?>" value="<?php echo esc_html($settings[0]->value) ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<?php _e('Sender', 'wdesk') ?>: <br>
								<input type="text" name="email" placeholder="<?php _e('Sender email', 'wdesk') ?>" value="<?php echo esc_html($settings[1]->value) ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<?php _e('URL', 'wdesk') ?>: <br>
								<input type="text" name="url" placeholder="<?php _e('Helpdesk url', 'wdesk') ?>" value="<?php echo esc_html($settings[2]->value) ?>" style="padding: 0 8px; margin: 0;"/>
							</th>
						</tr>
						<tr>
							<th>
								<?php _e('Date', 'wdesk') ?>: <br>
								<select name="date-format">
								  <option value="d-m-Y" <?php echo ($settings[3]->value == "d-m-Y") ? 'selected' : '' ?>>d-m-Y</option>
								  <option value="m-d-Y" <?php echo ($settings[3]->value == "m-d-Y") ? 'selected' : '' ?>>m-d-Y</option>
								  <option value="Y-m-d" <?php echo ($settings[3]->value == "Y-m-d") ? 'selected' : '' ?>>Y-m-d</option>
								  <option value="d/m/Y" <?php echo ($settings[3]->value == "d/m/Y") ? 'selected' : '' ?>>d/m/Y</option>
								  <option value="m/d/Y" <?php echo ($settings[3]->value == "m/d/Y") ? 'selected' : '' ?>>m/d/Y</option>
								  <option value="Y/m/d" <?php echo ($settings[3]->value == "Y/m/d") ? 'selected' : '' ?>>Y/m/d</option>
								  <option value="d-m-Y H:i:s" <?php echo ($settings[3]->value == "d-m-Y H:i:s") ? 'selected' : '' ?>>d-m-Y H:i:s</option>
								  <option value="m-d-Y H:i:s" <?php echo ($settings[3]->value == "m-d-Y H:i:s") ? 'selected' : '' ?>>m-d-Y H:i:s</option>
								  <option value="Y-m-d H:i:s" <?php echo ($settings[3]->value == "Y-m-d H:i:s") ? 'selected' : '' ?>>Y-m-d H:i:s</option>
								  <option value="d/m/Y H:i:s" <?php echo ($settings[3]->value == "d/m/Y H:i:s") ? 'selected' : '' ?>>d/m/Y H:i:s</option>
								  <option value="m/d/Y H:i:s" <?php echo ($settings[3]->value == "m/d/Y H:i:s") ? 'selected' : '' ?>>m/d/Y H:i:s</option>
								  <option value="Y/m/d H:i:s" <?php echo ($settings[3]->value == "Y/m/d H:i:s") ? 'selected' : '' ?>>Y/m/d H:i:s</option>
								</select>
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
		</div>
	</div>
<?php
}
?>