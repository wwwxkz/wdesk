<?php
function wdesk_shortcode_component_style_main() {
    $return = '';
    $return .= '
    <style>
		label {
			margin-bottom: 15px;
		}
		.wp-container-1 {
			flex-basis: 0 !important;
		}
		input, select, textarea {
			border-radius: 0px !important;
			border-color: #cac6c6 !important;
		}
		table {
			border-collapse: collapse;
			width: 100%;
		}
		table td, table th {
			text-align: left;
			border: 1px solid #ddd;
			padding: 8px;
		}
		table tr:hover {
			background-color: #ddd;
		}	
		form, p {
			margin: 0;
			padding: 0;	
		}
    </style>
    ';
    return $return;
}
?>