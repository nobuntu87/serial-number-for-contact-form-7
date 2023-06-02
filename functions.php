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

// プラグイン初期化
add_action(
	'init',
	__NAMESPACE__ . '\NT_WPCF7SN::init_plugin',
	10, 0
);

// オプション更新完了
add_action(
	'updated_option',
	__NAMESPACE__ . '\NT_WPCF7SN::updated_option',
	10, 3
);

// デイリーリセット実行チェック
add_action(
	'init',
	__NAMESPACE__ . '\NT_WPCF7SN::check_reset_count',
	11, 0
);

// ========================================================
// プラグインフック設定
// ========================================================

// ------------------------------------
// アクションフック
// ------------------------------------

// デイリーリセット実行チェック
add_action(
	'nt_wpcf7sn_check_reset_count',
	__NAMESPACE__ . '\NT_WPCF7SN::check_reset_count',
	10, 0
);

// ========================================================
// Contact Form 7 プラグインフック設定
// ========================================================

// ------------------------------------
// アクションフック
// ------------------------------------

// [ContactForm7] メール送信成功
add_action(
	'wpcf7_mail_sent',
	__NAMESPACE__ . '\Submission::sent_mail_success',
	11, 1
);

// [ContactForm7] メール送信失敗
add_action(
	'wpcf7_mail_failed',
	__NAMESPACE__ . '\Submission::sent_mail_failed',
	11, 1
);

// ------------------------------------
// フィルターフック
// ------------------------------------

// [ContactForm7] フォーム入力データ編集
add_filter(
	'wpcf7_posted_data',
	__NAMESPACE__ . '\Submission::edit_wpcf7_post_data',
	11, 1
);

// [ContactForm7] 送信結果メッセージ編集
add_filter(
	'wpcf7_display_message',
	__NAMESPACE__ . '\Submission::edit_wpcf7_display_message',
	11, 2
);

// [ContactForm7] メールタグの変換
add_filter(
	'wpcf7_special_mail_tags',
	__NAMESPACE__ . '\Mail_Tag::convert_mail_tags',
	11, 2
);

// [ContactForm7] REST API
add_filter(
	'wpcf7_refill_response',
	__NAMESPACE__ . '\REST_Controller::set_dom_api_response',
	11, 1
);
add_filter(
	'wpcf7_feedback_response',
	__NAMESPACE__ . '\REST_Controller::set_dom_api_response',
	11, 1
);
