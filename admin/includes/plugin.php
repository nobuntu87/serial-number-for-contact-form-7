<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// プラグイン制御クラス：NT_WPCF7SN_Admin
// ============================================================================

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

	/**
	 * プラグインのアクションリンクを設定する。
	 * 
	 * [Filter Hook] plugin_action_links
	 *
	 * @param string[] $actions アクションリンク
	 * @param string $plugin_file プラグイン名 : {plugin-name}\{main-file.php}
	 * @return void アクションリンクを返す。
	 */
	function set_plugin_action_links( $actions, $plugin_file )
	{
		// 自プラグイン判別
		if ( _PLUGIN_BASENAME != $plugin_file ) { return $actions; }
		
		// ------------------------------------
		// プラグイン設定ページ登録
		// ------------------------------------

		$action_link = sprintf( ''
			. '<a href="%s">%s</a>'
			, esc_url( menu_page_url( _PREFIX['-'], false ) )
			, esc_html( __( 'Settings', _TEXT_DOMAIN ) )
		);

		// 先頭に登録
		array_unshift( $actions, wp_kses_post( $action_link ) );

		// ------------------------------------

		return $actions;
	}

}
