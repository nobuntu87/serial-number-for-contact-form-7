<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/serial-number.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/submission.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/mail-tag.php';


if ( is_admin() ) {
	require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/admin.php';
}


add_action( 'init', 'nt_wpcf7sn_init' );


function nt_wpcf7sn_init() {
	$timezone = get_option( 'timezone_string', 'UTC' );
	date_default_timezone_set( $timezone );
}
