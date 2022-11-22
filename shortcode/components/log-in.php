<?php
function wdesk_shortcode_component_log_in() {
    $return = '';
    $return .= '
        <div id="wdesk-shortcode-log-in">	
			<h1 style="color: #1a447a; flex-grow: 1;">' . __('Log-in', 'wdesk') . '</h1>
			<form method="post">
				<div style="display: flex; flex-direction: column;">
					<label>' . __('Email', 'wdesk') . '</label>
					<input type="text" required name="email" placeholder="' . __('Valid email adress', 'wdesk') . '" />
					<br>
					<label>' . __('Password', 'wdesk') . '</label>
					<input type="password" required name="password" id="password" placeholder="' . __('A strong password', 'wdesk') . '" />
					<br>
					<input type="submit" required class="button action" name="wdesk-user-login" value="' . __('Log-in', 'wdesk') . '" />
				</div>
			</form>
        </div>
    ';
    return $return;
}
?>