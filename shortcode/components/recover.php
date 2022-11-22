<?php
function wdesk_shortcode_component_recover() {
    $return = '';
    $return .= '
        <div id="wdesk-shortcode-login">	
			<h1 style="color: #1a447a;">' . __('Forgot your password', 'wdesk') . '?</h1>
			<form method="post" style="display: flex; flex-direction: column;">
				<input type="text" required name="email" placeholder="' . __('Last email used to acess', 'wdesk') . '" style="margin-bottom: 15px;"/>
				<input type="submit" required class="button action" name="wdesk-user-recover" value="' . __('Send email with the password', 'wdesk') . '" />
			</form>
        </div>
    ';
    return $return;
}
?>