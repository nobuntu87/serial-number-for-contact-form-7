<?php
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
if ( is_admin() ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}


/**
 * プラグインが有効化されているか確認する。
 * 
 * 管理画面のみ使用可能。
 *
 * @param string $plugin プラグイン名 (plugin-directory/plugin-file.php)
 * @return bool プラグインが有効化されている場合はtrueを返す。
 *              プラグインが有効化されていない場合はfalseを返す。
 *              メソッド使用不可(管理画面以外)の場合はfalseを返す。
 */
function nt_wpcf7sn_is_active_plugin( $plugin ) {
	if ( function_exists( 'is_plugin_active' ) ) {
		return is_plugin_active( $plugin );
	} else {
		return false;
	}
}


/**
 * プラグインページの埋め込みURLを取得する。
 * 
 * プラグインページURLに埋め込みクエリパラメータを付加する。
 *
 * @param string $plugin_slug プラグインスラッグ
 * @return string プラグインページの埋め込みURLを返す。
 */
function nt_wpcf7sn_get_plugin_iframe_url( $plugin_slug ) {
	return esc_url( add_query_arg(
		array(
			'tab'       => 'plugin-information',
			'plugin'    => $plugin_slug,
			'TB_iframe' => 'true',
			'width'     => '600',
			'height'    => '550',
		),
		admin_url( 'plugin-install.php' )
	) );
}
