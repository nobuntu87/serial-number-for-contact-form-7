<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ========================================================
// プラグイン定義
// ========================================================

// ------------------------------------
// プラグイン設定
// ------------------------------------

define( __NAMESPACE__ . '\_VERSION', '2.4.0' );

define( __NAMESPACE__ . '\_REQUIRED_WP_VERSION', '6.0' );

define( __NAMESPACE__ . '\_TEXT_DOMAIN', 'serial-number-for-contact-form-7' );

define( __NAMESPACE__ . '\_MAIN_FILE', 'wpcf7-serial-number.php' );

define( __NAMESPACE__ . '\_PREFIX', array(
	'-' => 'nt-wpcf7sn',
	'_' => 'nt_wpcf7sn',
) );

define( __NAMESPACE__ . '\_EXTERNAL_PLUGIN', array(
	'wpcf7' => array(
		'name'      => 'Contact Form 7',
		'slug'      => 'contact-form-7',
		'basename'  => 'contact-form-7/wp-contact-form-7.php',
		'menu_slug' => 'wpcf7',
		'post_type' => 'wpcf7_contact_form',
	),
) );

// ------------------------------------
// パス設定
// - - - - - - - - - - - - - - - - - -
// _PLUGIN_DIR      : (root) ~\wp-content\plugins\{plugin-name}
// _PLUGIN_URL      : (http) ~/wp-content/themes/{plugin-name}
// - - - - - - - - - - - - - - - - - -
// _PLUGIN          : (root) ~\wp-content\themes\{plugin-name}\{main-file.php}
// _PLUGIN_BASENAME : {plugin-name}\{main-file.php}
// _PLUGIN_NAME     : {plugin-name}
// ------------------------------------

define( __NAMESPACE__ . '\_PLUGIN_DIR', untrailingslashit( dirname( __DIR__ ) ) );
define( __NAMESPACE__ . '\_PLUGIN_URL', untrailingslashit( plugins_url( '', __DIR__ ) ) );

define( __NAMESPACE__ . '\_PLUGIN', _PLUGIN_DIR . '\\' . _MAIN_FILE );
define( __NAMESPACE__ . '\_PLUGIN_BASENAME', plugin_basename( _PLUGIN ) );
define( __NAMESPACE__ . '\_PLUGIN_NAME', trim( dirname( _PLUGIN_BASENAME ), '/' ) );

// ------------------------------------
// オプション設定
// ------------------------------------

define( __NAMESPACE__ . '\_OPTION_NAME', sprintf( '%s_conf', _PREFIX['_'] ) );

// ------------------------------------
// ライブラリ設定
// ------------------------------------

// WordPress Library「Admin Menu」

define( __NAMESPACE__ . '\_LIB_ADMIN_MENU_VERSION', '2_8_1' );

class_alias(
	'_Nt\WpLib\AdminMenu\v' . _LIB_ADMIN_MENU_VERSION . '\Admin_Menu_Base',
	__NAMESPACE__ . '\Admin_Menu_Base'
);

class_alias(
	'_Nt\WpLib\AdminMenu\v' . _LIB_ADMIN_MENU_VERSION . '\Library_Utility',
	__NAMESPACE__ . '\Admin_Menu_Util'
);

define( __NAMESPACE__ . '\_ADMIN_MENU_SLUG', _PREFIX['-'] );
define( __NAMESPACE__ . '\_ADMIN_MENU_TAB_PREFIX', 'wpcf7-form-' );

define( __NAMESPACE__ . '\_ADMIN_MENU_REGEX', array(
	'page_suffix' => sprintf( '/_page_%s$/'
		, _ADMIN_MENU_SLUG
	),
	'option_name' => sprintf( '/^%s_%s_%s(?P<form_id>\d+)_conf$/'
		, _PREFIX['_'] , _ADMIN_MENU_SLUG , _ADMIN_MENU_TAB_PREFIX
	),
	'tab_slug'    => sprintf( '/^%s(?P<form_id>\d+)$/'
		, _ADMIN_MENU_TAB_PREFIX
	),
) );

// ========================================================
// グローバルオプション定義
// ========================================================

$_NT_WPCF7SN = [];

// ========================================================
// オプション定義
// ========================================================

define( __NAMESPACE__ . '\_MAIL_TAG_PREFIX', '_serial_number_' );

define( __NAMESPACE__ . '\_MAIL_TAG_REGEX', '/^' . _MAIL_TAG_PREFIX . '(?P<form_id>\d+)$/' );

define( __NAMESPACE__ . '\_POST_FIELD', 'serial-number' );

// {global_key} > {option_info}
define( __NAMESPACE__ . '\_FORM_OPTIONS', array(
	// コンタクトフォームID
	'01' => array(
		'key'     => 'form_id',
		'default' => '',
		'pattern' => '^\d+$'
	),
	// メールタグ
	'02' => array(
		'key'     => 'mail_tag',
		'default' => '',
		'pattern' => '^\[' . _MAIL_TAG_PREFIX . '\d+\]$'
	),
	// シリアル番号 表示形式
	'03' => array(
		'key'     => 'type',
		'default' => 0,
		'pattern' => '^[0-4]$'
	),
	// プレフィックス
	'04' => array(
		'key'     => 'prefix',
		'default' => '',
		'pattern' => '^\S*$'
	),
	// カウンター桁数
	'05' => array(
		'key'     => 'digits',
		'default' => 1,
		'pattern' => '^[1-9]$'
	),
	// 区切り文字表示
	'06' => array(
		'key'     => 'separator',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	// 西暦2桁表示
	'07' => array(
		'key'     => 'year2dig',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	// カウンター非表示
	'08' => array(
		'key'     => 'nocount',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	// UNIX時間 表示形式
	'09' => array(
		'key'     => 'unixtime_type',
		'default' => 0,
		'pattern' => '^[0-2]$'
	),
	// デイリーカウンター使用
	'10' => array(
		'key'     => 'dayreset',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	// メールカウント
	'11' => array(
		'key'     => 'count',
		'default' => 0,
		'pattern' => '^[0-9]{1,5}$'
	),
	// デイリーカウント
	'12' => array(
		'key'     => 'daycount',
		'default' => 0,
		'pattern' => '^[0-9]{1,5}$'
	),
	// カウント増加無効(送信失敗)
	'13' => array(
		'key'     => 'nocount_mail_failed',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	// 送信結果メッセージ非表示
	'14' => array(
		'key'     => 'hide_sent_msg',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	// 送信結果メッセージ
	'15' => array(
		'key'     => 'sent_msg',
		'default' => __( 'Receipt No :', _TEXT_DOMAIN ),
		'pattern' => '^.*$'
	),
) );
