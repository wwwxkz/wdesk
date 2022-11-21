<?php

function wdesk_tags() {
?>
	<div style="display: flex; margin-top: 15px; padding: 0; flex-direction: column; justify-content: space-between;">
	<h2><?php _e('Tags', 'wdesk') ?></h2>
		<div style="display: flex; flex-direction: row;">
			<?php 
				global $wpdb;
				$tags = $wpdb->get_results("SELECT * FROM `wdesk_tags`");
				foreach ($tags as $tag) { ?>
					<table class="wp-list-table widefat fixed striped table-view-list" style="width: 400px; height: -webkit-fill-available;">
						<thead>
							<tr>
								<th><?php _e('Tag', 'wdesk') ?> <?php echo esc_textarea($tag->id) ?></th>
							</tr>
						</thead>
						<tbody>
							<form method="post">
								<input type="hidden" name="id" value="<?php echo esc_textarea($tag->id) ?>"/>
								<tr><th><?php _e('Name', 'wdesk') ?>: <br><input type="text" name="name" placeholder="<?php _e('Tag name', 'wdesk') ?>" value="<?php echo esc_textarea($tag->name) ?>" style="padding: 0 8px; margin: 0;"/></th></tr>
								<tr><th><?php _e('Color', 'wdesk') ?>: <br><input type="color" name="color" placeholder="<?php _e('Color code', 'wdesk') ?>" value="<?php echo esc_textarea($tag->color) ?>" style="padding: 0 8px; margin: 0;"/></th></tr>
								<tr>
									<th>
										<input type="submit" class="button-primary action" name="wdesk-tag-update" value="<?php _e('Update', 'wdesk') ?>" />
										<input type="submit" class="button action" 		   name="wdesk-tag-delete" value="<?php _e('Delete', 'wdesk') ?>" />
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
						<th><?php _e('New tag', 'wdesk') ?></th>
					</tr>
				</thead>
				<tbody>
					<form method="post">
						<tr><th><?php _e('Name', 'wdesk') ?>: <br><input type="text" name="name" placeholder="<?php _e('Tag name', 'wdesk') ?>" style="padding: 0 8px; margin: 0;"/></th></tr>
						<tr><th><?php _e('Color', 'wdesk') ?>: <br><input type="color" name="color" placeholder="<?php _e('Color code', 'wdesk') ?>" style="padding: 0 8px; margin: 0;"/></th></tr>
						<tr>
							<th>
								<input type="submit" class="button action" name="wdesk-tag-new" value="<?php _e('Create', 'wdesk') ?>" />
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