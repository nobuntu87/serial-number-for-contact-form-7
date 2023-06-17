<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ========================================================
// プラグイン定義
// ========================================================

// ------------------------------------
// ディレクトリ設定
// ------------------------------------

define( __NAMESPACE__ . '\_LIBRARY_DIR',  __DIR__ . '/../library' );
define( __NAMESPACE__ . '\_INCLUDE_DIR',  __DIR__ . '/../includes' );
define( __NAMESPACE__ . '\_FUNCTION_DIR', __DIR__ . '/../functions' );
define( __NAMESPACE__ . '\_ADMIN_DIR',    __DIR__ . '/../admin' );

// ========================================================
// ファイル読み込み
// ========================================================

// ------------------------------------
// ライブラリ
// ------------------------------------

require_once( _LIBRARY_DIR . '/wplib-admin-menu/wplib-admin-menu.php' );

// ------------------------------------
// 共通ファイル
// ------------------------------------

require_once( _INCLUDE_DIR . '/define.php' );
require_once( _INCLUDE_DIR . '/utility.php' );
require_once( _INCLUDE_DIR . '/plugin.php' );
require_once( _INCLUDE_DIR . '/form-option.php' );
require_once( _INCLUDE_DIR . '/form-validate.php' );

require_once( _FUNCTION_DIR . '/serial-number.php' );
require_once( _FUNCTION_DIR . '/submission.php' );
require_once( _FUNCTION_DIR . '/mail-tag.php' );
require_once( _FUNCTION_DIR . '/rest-api.php' );

// ------------------------------------
// 管理ファイル
// ------------------------------------

if ( is_admin() ) {
	require_once( _ADMIN_DIR . '/functions.php' );
}
