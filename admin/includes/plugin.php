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
	public static function set_plugin_action_links( $actions, $plugin_file )
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

	/**
	 * プラグインのメタ情報を設定する。
	 * 
	 * [Filter Hook] plugin_row_meta
	 *
	 * @param string[] $plugin_meta メタ情報
	 * @param string $plugin_file プラグイン名 : {plugin-name}\{main-file.php}
	 * @return void
	 */
	public static function set_plugin_meta_info( $plugin_meta, $plugin_file )
	{
		// 自プラグイン判別
		if ( _PLUGIN_BASENAME != $plugin_file ) { return $plugin_meta; }

		// ------------------------------------
		// 依存関係チェック：Contact Form 7 プラグイン
		// ------------------------------------

		// [ContactForm7] 無効化の場合
		if ( !NT_WPCF7SN::is_active_wpcf7() ) {

			$iframe_url = Utility::get_plugin_iframe_url( _EXTERNAL_PLUGIN['wpcf7']['slug'] );

			$plugin_link = sprintf( ''
				. '<a href="%s" class="%s" data-title="%s">%s</a>'
				, esc_url( $iframe_url )
				, esc_attr( 'thickbox open-plugin-details-modal' )
				, esc_attr( _EXTERNAL_PLUGIN['wpcf7']['name'] )
				, esc_attr( _EXTERNAL_PLUGIN['wpcf7']['name'] )
			);

			$message = sprintf( __( ''
				. '<strong>Required the %s plugin to work.</strong>'
				. ' Please install and activate the plugin first.'
				, _TEXT_DOMAIN )
				, $plugin_link
			);

			$meta_info = sprintf( ''
				. '<div class="%s"><p>%s</p></div>'
				, esc_attr( 'notice notice-warning notice-alt inline' )
				, $message
			);

			// 先頭に登録
			array_unshift( $plugin_meta, wp_kses_post( $meta_info ) );
		}

		// ------------------------------------

		return $plugin_meta;
	}

}
