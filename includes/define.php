<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ========================================================
// プラグイン定義
// ========================================================

// ------------------------------------
// プラグイン設定
// ------------------------------------

define( __NAMESPACE__ . '\_VERSION', '1.1.0' );

define( __NAMESPACE__ . '\_REQUIRED_WP_VERSION', '5.9' );

define( __NAMESPACE__ . '\_TEXT_DOMAIN', 'serial-number-for-contact-form-7' );

define( __NAMESPACE__ . '\_MAIN_FILE', 'wpcf7-serial-number.php' );

define( __NAMESPACE__ . '\_PREFIX', array(
	'-' => 'nt-wpcf7sn',
	'_' => 'nt_wpcf7sn',
) );

define( __NAMESPACE__ . '\_EXTERNAL_PLUGIN', array(
	'wpcf7' => array(
		'name'     => 'Contact Form 7',
		'slug'     => 'contact-form-7',
		'basename' => 'contact-form-7/wp-contact-form-7.php',
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

define( __NAMESPACE__ . '\_MAIL_TAG_PREFIX', '_serial_number_' );

define( __NAMESPACE__ . '\_FORM_OPTIONS', array(
	'id'        => array(
		'key'     => 'form_id',
		'default' => '',
		'pattern' => '^\d+$'
	),
	'tag'       => array(
		'key'     => 'mail_tag',
		'default' => '',
		'pattern' => '^\[' . _MAIL_TAG_PREFIX . '\d+\]$'
	),
	'type'      => array(
		'key'     => 'type',
		'default' => 0,
		'pattern' => '^[0-4]$'
	),
	'prefix'    => array(
		'key'     => 'prefix',
		'default' => '',
		'pattern' => '^(?!.*[\\\"&\'<>])\S{0,10}$'
	),
	'digits'    => array(
		'key'     => 'digits',
		'default' => 1,
		'pattern' => '^[1-9]$'
	),
	'separator' => array(
		'key'     => 'separator',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	'year2dig'  => array(
		'key'     => 'year2dig',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	'nocount'   => array(
		'key'     => 'nocount',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	'dayreset'  => array(
		'key'     => 'dayreset',
		'default' => 'no',
		'pattern' => '^(no|yes)$'
	),
	'count'     => array(
		'key'     => 'count',
		'default' => 0,
		'pattern' => '^[0-9]{1,5}$'
	),
	'daycount'  => array(
		'key'     => 'daycount',
		'default' => 0,
		'pattern' => '^[0-9]{1,5}$'
	),
) );

// ------------------------------------
// ライブラリ設定
// ------------------------------------

// WordPress Library「Admin Menu」

define( __NAMESPACE__ . '\_LIB_ADMIN_MENU_VERSION', '2_2_0' );

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
