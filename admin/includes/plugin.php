<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

class NT_WPCF7SN_Admin {

	/**
	* プラグインのバージョン比較処理を行う。
	*
	* @return void
	*/
	public static function compare_plugin_version()
	{
		$new_version = _VERSION;
		$old_version = SELF::get_option( 'version', '0.0.0' );

		// バージョン比較
		switch ( version_compare( $new_version, $old_version ) ) {
			// ------------------------------------
			// ダウングレード
			// ------------------------------------
			case -1:
				// 処理なし
				break;
			// ------------------------------------
			// アップグレード
			// ------------------------------------
			case 1:
				// 処理なし
				break;
			// ------------------------------------
			// 同一バージョン
			// ------------------------------------
			default:
				// 処理なし
				return;
		}

		// バージョン更新
		SELF::update_option( 'version', $new_version );
	}

}
