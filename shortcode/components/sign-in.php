<?php
function wdesk_shortcode_component_sign_in() {
    $return = '';
    $return .= '
        <div id="wdesk-shortcode-sign-in">	
			<h1 style="color: #1a447a; flex-grow: 1;">' . __('Sign-in', 'wdesk') . '</h1>
			<form method="post">  
				<div style="display: flex; flex-direction: column;">
					<label>' . __('Email', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
					<input required type="email" name="email" placeholder="' . __('Valid email adress', 'wdesk') . '" />
					<br>
					<label>' . __('Name', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
					<input required type="text" name="name" placeholder="' . __('Full name', 'wdesk') . '" />
					<br>
					<label>' . __('Password', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
					<input onchange="confirmPassword();" type="password" required name="password" id="password" placeholder="' . __('A strong password', 'wdesk') . '" />
					<br>
					<label>' . __('Confirm your password', 'wdesk') . ' <a style="color: #FF0000;">*</a></label>
					<input onchange="confirmPassword();" type="password" required name="password-confirm" id="password-confirm" placeholder="' . __('Repeat your password', 'wdesk') . '" />
					<br>
					<input required type="submit" class="button action" id="wdesk-user-register" name="wdesk-user-register" value="' . __('Register', 'wdesk') . '" />
				</div>
			</form>
        </div>
    ';
    return $return;
}
?>