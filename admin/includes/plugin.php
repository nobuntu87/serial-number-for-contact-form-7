<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// プラグイン制御クラス：NT_WPCF7SN_Admin
// ============================================================================

class NT_WPCF7SN_Admin {

  // ========================================================
  // 初期化
  // ========================================================

	/**
	 * プラグインの初期化を行う。
	 *
	 * @return void
	 */
	public static function init_plugin()
	{
		// バージョン比較
		SELF::compare_plugin_version();

		// 動作環境チェック
		SELF::check_system_requirements();

		// 依存関係チェック
		SELF::check_dependent_plugin();
	}

   // ------------------------------------
   // バージョン比較
   // ------------------------------------

	/**
	* プラグインのバージョン比較処理を行う。
	*
	* @return void
	*/
	private function compare_plugin_version()
	{
		$new_version = _VERSION;
		$old_version = NT_WPCF7SN::get_option( 'version', '0.0.0' );

		// バージョン比較
		switch ( version_compare( $new_version, $old_version ) ) {
			// ダウングレード [ new < old ]
			case -1:
				// 処理なし
				break;
			// アップグレード [ new > old ]
			case 1:
				// 処理なし
				break;
			// 同一バージョン [ new = old ]
			default:
				// 処理なし
				return;
		}

		// バージョン更新
		NT_WPCF7SN::update_option( 'version', $new_version );
	}

   // ------------------------------------
   // 環境チェック
   // ------------------------------------

	/**
	 * プラグインの動作環境チェックを行う。
	 *
	 * @return void
	 */
	private function check_system_requirements()
	{
		// ------------------------------------
		// 動作環境チェック：WordPress 要求バージョン
		// ------------------------------------

		$now_version = get_bloginfo( 'version' );
		$req_version = _REQUIRED_WP_VERSION;

		// バージョン比較 [ now < req ]
		if ( version_compare( $now_version, $req_version, '<' ) ) {

			$message = sprintf( __( ''
				. 'Serial Number for Contact Form 7 %1$s requires WordPress %2$s or higher.'
				. ' Please <a href="%3$s">update WordPress</a> first.'
				, _TEXT_DOMAIN )
				, _VERSION
				, _REQUIRED_WP_VERSION
				, esc_url( admin_url( 'update-core.php' ) )
			);

			// 管理画面に通知
			$notice_slug = _PREFIX['-'] . '-required-wp-version-error';
			Utility::notice_admin_message(
				$notice_slug, '', $message, 'error'
			);
		}
	}

	/**
	 * プラグインの依存関係チェックを行う。
	 *
	 * @return void
	 */
	private function check_dependent_plugin()
	{
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
				. 'Serial Number for Contact Form 7 requires %s to work.'
				. ' Please install and activate the plugin first.'
				, _TEXT_DOMAIN )
				, $plugin_link
			);

			// 管理画面に通知
			$notice_slug = _PREFIX['-'] . '-dependent-plugin-error-wpcf7';
			Utility::notice_admin_message(
				$notice_slug, '', $message, 'error'
			);
		}
	}

  // ========================================================
  // WordPressフック
  // ========================================================

   // ------------------------------------
   // アクションフック
   // ------------------------------------

	/**
	 * プラグインのインストール処理を行う。
	 * 
	 * [Action Hook] activate_{$plugin}
	 *
	 * @return void
	 */
	public static function installed_plugin()
	{
		// オプション初期化
		NT_WPCF7SN::update_plugin_option( [] );
	}

	/**
	 * 管理画面のスクリプトを読み込む。
	 * 
	 * [Action Hook] admin_enqueue_scripts
	 *
	 * @param string $hook_suffix 管理画面の接尾辞
	 * @return void
	 */
	public static function enqueue_admin_scripts( $hook_suffix )
	{
		// ------------------------------------
		// プラグイン設定画面
		// ------------------------------------

		if ( 1 === preg_match( _ADMIN_MENU_REGEX['page_suffix'], $hook_suffix ) ) {

			// メニューページ用CSS
			wp_enqueue_style(
				_PREFIX['-'] . '-admin',
				Utility::get_uri( _ADMIN_CSS_DIR ) . '/style.css',
				array(),
				_VERSION, 'all'
			);

		}
	}

	/**
	 * オプション更新後の処理を行う。
	 * 
	 * [Action Hook] updated_option
	 *
	 * @param string $option_name オプション名
	 * @param mixed[] $old_value オプション値 (更新前)
	 * @param mixed[] $new_value オプション値 (更新後)
	 * @return void
	 */
	public static function updated_option( $option_name, $old_value, $new_value )
	{
		// オプション名判別
		if ( 1 !== preg_match( _ADMIN_MENU_REGEX['option_name'], $option_name, $matches ) ) {
			return;
		}

		// グローバルオプション初期化
		Form_Option::init_global_option( strval( $matches['form_id'] ) );
	}

   // ------------------------------------
   // フィルターフック
   // ------------------------------------

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

		// [ContactForm7] 有効化の場合
		if ( NT_WPCF7SN::is_active_wpcf7() ) {

			$action_link = sprintf( ''
				. '<a href="%s">%s</a>'
				, esc_url( menu_page_url( _PREFIX['-'], false ) )
				, esc_html( __( 'Settings', _TEXT_DOMAIN ) )
			);

			// 先頭に登録
			array_unshift( $actions, wp_kses_post( $action_link ) );
		}

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
				. 'This plugin requires %s to work.'
				. ' Please install and activate the plugin first.'
				, _TEXT_DOMAIN )
				, $plugin_link
			);

			$meta_info = sprintf( ''
				. '<div class="%s"><p><strong>%s</strong></p></div>'
				, esc_attr( 'notice notice-warning notice-alt inline' )
				, $message
			);

			// 先頭に登録
			array_unshift( $plugin_meta, wp_kses_post( $meta_info ) );
		}

		// ------------------------------------

		return $plugin_meta;
	}

  // ========================================================

}
