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
