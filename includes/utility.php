<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ========================================================
// ファイル読み込み
// ========================================================

include_once( ABSPATH . 'wp-load.php' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( ABSPATH . 'wp-admin/includes/template.php' );

// ============================================================================
// プラグイン用ユーティリティクラス：Utility
// ============================================================================

class Utility {

	/**
	 * Contact Form 7 プラグインの投稿情報を取得する。
	 *
	 * @return WP_Post[] Contact Form 7 の投稿オブジェクトを返す。
	 */
	public static function get_wpcf7_posts()
	{
		return get_posts( array(
			'post_type'      => 'wpcf7_contact_form',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'offset'         => 0,
		) );
	}

	/**
	 * WordPress データベースのオプション情報を取得する。
	 *
	 * @param string $pattern オプション名の検索パターン
	 * @return mixed[] WordPress データベースのオプション情報を返す。
	 */
	public static function get_wpdb_options( $pattern )
	{
		global $wpdb;

		return $wpdb->get_results( sprintf( ''
			. 'SELECT * FROM %s'
			. '  WHERE 1 = 1 AND option_name like \'%s\''
			. '  ORDER BY option_name'
			, $wpdb->options
			, $pattern
		), ARRAY_A );
	}

	/**
	 * プラグインが有効化されているか確認する。
	 *
	 * @param string $basename プラグイン名 : {plugin-name}\{main-file.php}
	 * @return boolean 有効化状態を返す。(true:有効/false:無効)
	 */
	public static function is_active_plugin( $basename )
	{
		if ( function_exists( 'is_plugin_active' ) ) {
			return is_plugin_active( $basename );
		} else {
			return false;
		}
	}

	/**
	 * プラグインページの埋め込みURLを取得する。
	 *
	 * @param string $plugin_slug プラグインスラッグ名
	 * @return void
	 */
	public static function get_plugin_iframe_url( $plugin_slug )
	{
		return esc_url( add_query_arg(
			array(
				'tab'       => 'plugin-information',
				'plugin'    => $plugin_slug,
				'TB_iframe' => 'true',
				'width'     => '600',
				'height'    => '550',
			),
			admin_url( 'plugin-install.php' )
		) );
	}

	/**
	 * 管理画面にメッセージを通知する。
	 *
	 * @param string $slug スラッグ名
	 * @param string $code 識別コード名 (HTMLのid属性)
	 * @param string $message メッセージ
	 * @param string $type メッセージ種別 (error/success/warning/info) [error]
	 * @return void
	 */
	public static function notice_admin_message( $slug, $code, $message, $type = 'error' )
	{
		if ( !in_array( $type, [ 'error', 'success', 'warning', 'info' ] ) ) { return; }

		// メッセージ設定
		add_settings_error( $slug, $code, $message, $type );

		// メッセージ表示
		settings_errors( $slug );
	}

	/**
	 * 文字列のエスケープ/エンコード処理を行う。
	 *
	 * @param string $string 文字列
	 * @return string エスケープ処理した文字列を返す。
	 */
	public static function esc_encode( $string )
	{
		if ( !is_string( $string ) ) { return $string; }

		// エンコード
		$string = htmlspecialchars( $string, ENT_QUOTES, 'UTF-8' );

		// エスケープ
		$string = addslashes( $string );

		return $string;
	}

	/**
	 * 文字列のアンエスケープ/デコード処理を行う。
	 *
	 * @param string $string 文字列
	 * @return string アンエスケープ処理した文字列を返す。
	 */
	public static function unesc_decode( $string )
	{
		if ( !is_string( $string ) ) { return $string; }

		// アンエスケープ
		$string = stripslashes( $string );

		// デコード
		$string = htmlspecialchars_decode( $string, ENT_QUOTES );

		return $string;
	}

	/**
	 * ディレクトリのURIを取得する。
	 *
	 * @param string $path ディレクトリパス (絶対パス)
	 * @return string ディレクトリのURIを返す。
	 */
	public static function get_uri( $path )
	{
		return preg_replace(
			array( '/^.+[\/\\\]wp-content[\/\\\]/', '/[\/\\\]/' ),
			array( content_url() . '/', '/' ),
			$path
		);
	}

}
