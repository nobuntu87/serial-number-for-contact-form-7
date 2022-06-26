<?php
if ( ! defined( 'ABSPATH' ) ) exit;


if ( is_admin() ) {
	require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/admin.php';
}


add_action( 'init', 'nt_wpcf7sn_init' );


function nt_wpcf7sn_init() {
	$timezone = get_option( 'timezone_string', 'UTC' );
	date_default_timezone_set( $timezone );
}
