<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/includes/contact-forms-list-table.php';


/**
 * アクションフック設定
 */
add_action( 'admin_menu', 'nt_wpcf7sn_admin_menu', 10, 0 );
add_action( 'admin_enqueue_scripts', 'nt_wpcf7sn_admin_enqueue_scripts', 10, 1 );
add_action( 'nt_wpcf7sn_admin_warnings', 'nt_wpcf7sn_wp_version_error', 10, 0 );

/**
 * フィルターフック設定
 */
add_filter( 'plugin_action_links', 'nt_wpcf7sn_plugin_action_links', 10, 2 );


/**
 * 管理メニューを設定する。
 * 
 * @return void
 */
function nt_wpcf7sn_admin_menu() {
	add_options_page(
		__( 'Contact Form 7 Serial Number Addon', NT_WPCF7SN_TEXT_DOMAIN ),
		__( 'CF7 Serial Number', NT_WPCF7SN_TEXT_DOMAIN ),
		'manage_options',
		NT_WPCF7SN_PREFIX['-'],
		'nt_wpcf7sn_admin_management_page'
	);
}


/**
 * 管理ページを表示する。
 *
 * @return void
 */
function nt_wpcf7sn_admin_management_page() {
	$output = '';

	do_action( 'nt_wpcf7sn_admin_warnings' );

	$list_table = new NT_WPCF7SN_Contact_Forms_List_Table();
	$list_table->prepare_items();
	
	$output = ''
	. '<div class="wrap">'
	. '  <h2> ' . __( 'Contact Form 7 Serial Number Addon', NT_WPCF7SN_TEXT_DOMAIN ) . '</h2>'
	. '</div>';
	
	echo trim( $output );

	$list_table->display();
}


/**
 * スクリプトを読み込む。
 * 
 * @param string $hook_suffix 管理画面の接尾辞
 * @return void
 */
function nt_wpcf7sn_admin_enqueue_scripts( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, NT_WPCF7SN_PREFIX['-'] ) ) {
		return;
	}

	wp_enqueue_style(
		NT_WPCF7SN_TEXT_DOMAIN . '-admin',
		NT_WPCF7SN_PLUGIN_URL . '/admin/css/style.css',
		array(),
		NT_WPCF7SN_VERSION, 'all'
	);
}


/**
 * プラグインメニューにアクションリンクを設定する。
 *
 * @param string[] $actions プラグインのアクションリンク
 * @param string $plugin_file プラグインの相対パス
 * @return string[] プラグインのアクションリンクを返す。
 */
function nt_wpcf7sn_plugin_action_links( $actions, $plugin_file ) {
	if ( NT_WPCF7SN_PLUGIN_BASENAME != $plugin_file ) {
		return $actions;
	}

	$page_url = admin_url( 'options-general.php?page=' ) . NT_WPCF7SN_PREFIX['-'];
	$settings_link = '<a href="' . $page_url . '">' . __( 'Settings', NT_WPCF7SN_TEXT_DOMAIN ) . '</a>';
	
	// 先頭に追加
	array_unshift( $actions, $settings_link );

	return $actions;
}


/**
 * WordPressバージョンのエラーメッセージを表示する。
 *
 * 要求バージョンを満たさない場合にエラーメッセージ表示する。
 * 
 * @return void
 */
function nt_wpcf7sn_wp_version_error() {
	$wp_current_ver = get_bloginfo( 'version' );
	$wp_require_ver = NT_WPCF7SN_REQUIRED_WP_VERSION;

	if ( ! version_compare( $wp_current_ver, $wp_require_ver, '<' ) ) {
		return;
	}

	$message = sprintf(
		__(
			'<strong>Contact Form 7 Serial Number Addon %1$s requires WordPress %2$s or higher.</strong>'
			. ' Please <a href="%3$s">update WordPress</a> first.'
			, NT_WPCF7SN_TEXT_DOMAIN
		),
		NT_WPCF7SN_VERSION,
		NT_WPCF7SN_REQUIRED_WP_VERSION,
		admin_url( 'update-core.php' )
	);

	$output = ''
	. '<div class="notice notice-warning">'
	. '  <p> ' . $message . '</p>'
	. '</div>';
	
	echo trim( $output );
}
