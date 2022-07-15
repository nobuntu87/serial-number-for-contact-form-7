<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/includes/form-settings.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/includes/contact-forms-list-table.php';


/**
 * アクションフック設定
 */
add_action( 'admin_menu', 'nt_wpcf7sn_admin_menu', 11, 0 );
add_action( 'admin_enqueue_scripts', 'nt_wpcf7sn_admin_enqueue_scripts', 10, 1 );
add_action( 'nt_wpcf7sn_admin_warnings', 'nt_wpcf7sn_wp_version_error', 10, 0 );

/**
 * フィルターフック設定
 */
add_filter( 'plugin_action_links', 'nt_wpcf7sn_plugin_action_links', 10, 2 );
add_filter( 
	'set_screen_option_' . NT_WPCF7SN_FORM_OPTION_SCREEN['per_page']['option'] ,
	'nt_wpcf7sn_set_screen_option', 10, 3
);


/**
 * 管理メニューを設定する。
 * 
 * @return void
 */
function nt_wpcf7sn_admin_menu() {
	$hook = add_submenu_page(
		'wpcf7',
		__( 'Serial Number for Contact Form 7', NT_WPCF7SN_TEXT_DOMAIN ),
		__( 'Serial Number Settings', NT_WPCF7SN_TEXT_DOMAIN ),
		'manage_options',
		NT_WPCF7SN_PREFIX['-'],
		'nt_wpcf7sn_admin_management_page'
	);

	add_action( 'load-' . $hook, 'nt_wpcf7sn_load_admin_management_page', 10, 0 );
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
	
	$output_before = ''
	. '<div class="wrap">'
	. '  <h2>' . esc_html( __( 'Serial Number for Contact Form 7', NT_WPCF7SN_TEXT_DOMAIN ) ) . '</h2>'
	. '  <p>' . esc_html( __( 'Copy and paste the mail-tag anywhere in the mail template.', NT_WPCF7SN_TEXT_DOMAIN ) ) . '</p>';

	$output_after = ''
	. '</div>';

	echo wp_kses( trim( $output_before ), NT_WPCF7SN_ALLOWED_HTML );

	// テーブルリスト表示
	$list_table->display();

	echo wp_kses( trim( $output_after ), NT_WPCF7SN_ALLOWED_HTML );
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
 * @param string[] $actions アクションリンク
 * @param string $plugin_file プラグインの相対パス
 * @return string[] アクションリンクを返す。
 */
function nt_wpcf7sn_plugin_action_links( $actions, $plugin_file ) {
	if ( NT_WPCF7SN_PLUGIN_BASENAME != $plugin_file ) {
		return $actions;
	}

	$page_url = menu_page_url( NT_WPCF7SN_PREFIX['-'], false );
	$settings_link = '<a href="' . esc_url( $page_url) . '">' . esc_html( __( 'Settings', NT_WPCF7SN_TEXT_DOMAIN ) ) . '</a>';
	
	// 先頭に追加
	array_unshift(
		$actions,
		wp_kses( $settings_link, NT_WPCF7SN_ALLOWED_HTML )
	);

	return $actions;
}


/**
 * 管理ページ読み込み時の処理を行う。
 * 
 * 表示オプションの設定を行う。
 *
 * @return void
 */
function nt_wpcf7sn_load_admin_management_page() {
	add_screen_option( 'per_page', array(
		'default' => NT_WPCF7SN_FORM_OPTION_SCREEN['per_page']['default'],
		'option'  => NT_WPCF7SN_FORM_OPTION_SCREEN['per_page']['option'],
	) );
}


/**
 * 表示オプションの設定時の処理を行う。
 * 
 * @param mixed $screen_option 
 * @param string $option オプション名
 * @param int $value オプション値
 * @return int ページ毎の表示数を返す。
 */
function nt_wpcf7sn_set_screen_option( $screen_option, $option, $value ) {
	return $value;
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
			'<strong>Serial Number for Contact Form 7 %1$s requires WordPress %2$s or higher.</strong>'
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
	
	echo wp_kses( trim( $output ), NT_WPCF7SN_ALLOWED_HTML );
}
