<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ========================================================
// ファイル読み込み
// ========================================================

require_once( __DIR__ . '/includes/load.php' );

// ========================================================
// WordPressフック設定
// ========================================================

// ------------------------------------
// アクションフック
// ------------------------------------

// プラグイン有効化
add_action(
	'activate_' . _PLUGIN_BASENAME,
	__NAMESPACE__ . '\NT_WPCF7SN_Admin::installed_plugin',
	10, 0
);

// プラグイン初期化
add_action(
	'init',
	__NAMESPACE__ . '\NT_WPCF7SN_Admin::init_plugin',
	10, 0
);

// ------------------------------------
// フィルターフック
// ------------------------------------

// アクションリンク設定
add_filter(
	'plugin_action_links',
	__NAMESPACE__ . '\NT_WPCF7SN_Admin::set_plugin_action_links',
	10, 2
);

// メタ情報設定
add_filter(
	'plugin_row_meta',
	__NAMESPACE__ . '\NT_WPCF7SN_Admin::set_plugin_meta_info',
	10, 2
);
