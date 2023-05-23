<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ========================================================
// プラグイン定義
// ========================================================

// ------------------------------------
// ディレクトリ設定
// ------------------------------------

define( __NAMESPACE__ . '\_ADMIN_INCLUDE_DIR',  _ADMIN_DIR . '/includes' );
define( __NAMESPACE__ . '\_ADMIN_FUNCTION_DIR', _ADMIN_DIR . '/functions' );
define( __NAMESPACE__ . '\_ADMIN_CSS_DIR',      _ADMIN_DIR . '/css' );

// ========================================================
// ファイル読み込み
// ========================================================

// ------------------------------------
// 管理ファイル
// ------------------------------------

require_once( _ADMIN_INCLUDE_DIR . '/define.php' );
require_once( _ADMIN_INCLUDE_DIR . '/plugin.php' );

require_once( _ADMIN_FUNCTION_DIR . '/menu/admin-menu.php' );
