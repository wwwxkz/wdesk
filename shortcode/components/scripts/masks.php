<?php
function wdesk_shortcode_component_script_masks() {
    $return = '';
    $return .= '
    <script>
        function confirmPassword() {
            if (document.querySelectorAll("#password")[0].value === document.getElementById("password-confirm").value) {
                document.getElementById("wdesk-user-register").disabled = false;
                document.getElementById("wdesk-user-register").value = "' . __('Register', 'wdesk') . '";
            } else {
                document.getElementById("wdesk-user-register").disabled = true;
                document.getElementById("wdesk-user-register").value = "' . __('Password are not equal', 'wdesk') . '";
            }
        } 
    </script>
    ';
    return $return;
}
?>

