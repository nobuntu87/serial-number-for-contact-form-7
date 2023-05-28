<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

$prefix = 'nt_wpcf7sn';

// ------------------------------------
// 関連データベース削除
// ------------------------------------

global $wpdb;

$wpdb_options = $wpdb->get_results( sprintf( ''
	. 'SELECT * FROM %s'
	. '  WHERE 1 = 1 AND option_name like \'%s_%%\''
	. '  ORDER BY option_name'
	, $wpdb->options
	, $prefix
), ARRAY_A );

if ( is_array( $wpdb_options ) ) {
	foreach( $wpdb_options as $wpdb_option ) {
		delete_option( $wpdb_option['option_name'] );
	}
}
