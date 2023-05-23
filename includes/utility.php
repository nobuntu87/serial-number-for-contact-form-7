<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class Utility {

	/**
	 * Contact Form 7 の投稿情報を取得する。
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

}
