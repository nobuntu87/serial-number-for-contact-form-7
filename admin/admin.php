<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/includes/contact-forms-list-table.php';


/**
 * アクションフック設定
 */
add_action( 'admin_menu', 'nt_wpcf7sn_admin_menu' );
add_action( 'admin_enqueue_scripts', 'nt_wpcf7sn_admin_enqueue_scripts' );

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
	$output = ''
	. '<div class="wrap">'
	. '  <h2> ' . __( 'Contact Form 7 Serial Number Addon', NT_WPCF7SN_TEXT_DOMAIN ) . '</h2>'
	. '</div>';
	
	echo trim( $output );

	$list_table = new NT_WPCF7SN_Contact_Forms_List_Table();
	$list_table->prepare_items();
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
 * @param string[] $links プラグインのアクションリンク
 * @param string $file プラグインの相対パス
 * @return string[] プラグインのアクションリンクを返す。
 */
function nt_wpcf7sn_plugin_action_links( $links, $file ) {

	if ( $file == NT_WPCF7SN_PLUGIN_BASENAME ) {
		$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=' . NT_WPCF7SN_PREFIX['-'] . '">' . __( 'Settings', NT_WPCF7SN_TEXT_DOMAIN ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}
