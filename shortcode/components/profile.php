<?php
function wdesk_shortcode_component_profile($users) {
	$return = '';
	$return .= '
	<div id="wdesk-shortcode-profile" style="display: none;">
		<div style="display: flex; flex-direction: row; justify-content: space-between;">
			<h1 style="color: #1a447a; margin-bottom: 40px;">' . __('User profile', 'wdesk') . '</h1>
			<input type="submit" style="margin-bottom: 40px; margin-top: 20px;" required class="button action" onclick="(function(){
				document.getElementById(`wdesk-shortcode-tickets`).style.display = `block`; 
				document.getElementById(`wdesk-shortcode-profile`).style.display = `none`; 
				document.getElementById(`wdesk-shortcode-new`).style.display = `none`;
			})();return false;" value="' . __('Tickets', 'wdesk') . '" />
		</div>
		<form method="post" style="display: flex; flex-direction: column;">
			<input type="hidden" name="id" value="'. $users[0]->id .'">
			<div style="display: flex; flex-direction: column;">
				<label>' . __('Email', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="email" name="email" value="' . $users[0]->email. '" placeholder="' . __('Valid email adress', 'wdesk') . '" />
				<br>
				<label>' . __('Name', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="text" name="name" value="' . $users[0]->name. '" placeholder="' . __('Full name', 'wdesk') . '" />
				<br>
				<label>' . __('New password', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
				<input required type="text" name="password" placeholder="' . __('A strong password', 'wdesk') . '" value=""/>
				<br>
				<input style="width: 100%;" type="submit" class="button action" name="wdesk-user-update" value="' . __('Save', 'wdesk') . '">
				<input style="color: white; background-color: indianred; margin-top: 10px;width: 100%;" type="submit" class="button action" name="wdesk-user-logout" value="' . __('Logout', 'wdesk') . '">
			</div>
		</form>
	</div>
	';
	return $return;
}
?>