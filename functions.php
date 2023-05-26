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

// オプション初期化
add_action(
	'init',
	__NAMESPACE__ . '\Form_Option::init_options',
	10, 0
);

// デイリーリセット実行確認
add_action(
	'init',
	__NAMESPACE__ . '\NT_WPCF7SN::check_reset_count',
	11, 0
);
add_action(
	'nt_wpcf7sn_check_reset_count',
	__NAMESPACE__ . '\NT_WPCF7SN::check_reset_count',
	10, 0
);
