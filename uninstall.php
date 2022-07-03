<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

/**
 * プラグインの削除を行う。
 * 
 * プラグイン関連のオプションを削除する。
 *
 * @return void
 */
function nt_wpcf7sn_delete_plugin() {
	global $wpdb;

	// プラグインオプション削除
	delete_option( 'nt_wpcf7sn' );

	// コンタクトフォームオプション削除
	$options = $wpdb->get_results( "
		SELECT * FROM $wpdb->options
		WHERE option_name like 'nt_wpcf7sn_%'
	" );
	
	foreach ( $options as $option ) {
		delete_option( $option->option_name );
	}
}


if ( ! defined( 'NT_WPCF7SN_VERSION' ) ) {
	nt_wpcf7sn_delete_plugin();
}
