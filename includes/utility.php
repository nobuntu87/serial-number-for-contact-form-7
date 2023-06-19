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

  // ========================================================
  // オプション
  // ========================================================

	/**
	* オプション値を取得する。
	*
	* @param string $option_name オプション名
	* @return mixed[] オプション値を返す。
	*/
	public static function get_wpdb_option( $option_name )
	{
		// ------------------------------------
		// オプション取得
		// ------------------------------------

		// WordPressデータベース取得
		$option_value = get_option( $option_name );
		if ( false === $option_value ) { return []; }

		// ------------------------------------
		// デコード処理
		// ------------------------------------

		// JSON形式の文字列をデコード
		$option_value = @json_decode( $option_value, true );

		// アンエスケープ・デコード
		if ( is_array( $option_value ) ) {
			$option_value = array_map(
				array( __NAMESPACE__ . '\Utility', 'unesc_decode' ),
				$option_value
			);
		}

		// ------------------------------------

		return $option_value;
	}

	/**
	 * オプション値を更新する。
	 *
	 * @param string $option_name オプション名
	 * @param mixed[] $option_value オプション値
	 * @return void
	 */
	public static function update_wpdb_option( $option_name, $option_value )
	{
		// ------------------------------------
		// エンコード処理
		// ------------------------------------

		// エスケープ・エンコード
		if ( is_array( $option_value ) ) {
			$option_value = array_map(
				array( __NAMESPACE__ . '\Utility', 'esc_encode' ),
				$option_value
			);
		}

		// JSON形式の文字列にエンコード
		$option_value = @json_encode( $option_value );

		// ------------------------------------
		// オプション更新
		// ------------------------------------

		// WordPressデータベース更新
		update_option( $option_name, $option_value );
	}

   // ------------------------------------
   // エンコード/デコード
   // ------------------------------------

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

  // ========================================================
  // ユーティリティ
  // ========================================================

	/**
	 * WordPress データベースのオプション情報を取得する。
	 * 
	 * [include] wp-load.php
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
	 * [include] wp-admin/includes/plugin.php
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
	 * [include] wp-admin/includes/template.php
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
	 * オプションを削除する。
	 *
	 * @param string $option_name オプション名
	 * @return boolean 削除結果を返す。(true:成功or該当なし/false:失敗)
	 */
	public static function delete_option( $option_name )
	{
		if ( false !== get_option( $option_name ) ) {
			return delete_option( $option_name );
		}
		return true;
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

	/**
	 * 配列要素を更新する。
	 * 
	 * 元配列に存在するデータのみ更新(上書き)を行う。
	 *
	 * @param mixed[] $dst コピー先の配列
	 * @param mixed[] $src コピー元の配列
	 * @return mixed[] 更新した配列を返す。
	 */
	public static function array_update( $dst, $src )
	{
		if ( !is_array( $dst ) || !is_array( $src ) ) { return $dst; }

		foreach( $dst as $key => $value ) {
			if ( array_key_exists( $key, $src ) ) {
				$dst[$key] = $src[$key];
			}
		}

		return $dst;
	}

	/**
	 * 関数が存在するかチェックする。
	 *
	 * @param string $function 関数名の文字列 : {function} / {class}::{function}
	 * @return boolean チェック結果を返す。(true:有り/false:無し)
	 */
	public static function function_exists( $function )
	{
		// ------------------------------------
		// function_exists
		// ------------------------------------

		$pattern = '/^(?P<function>[^:\/\\\]+)$/';
		if ( 1 === preg_match( $pattern, $function, $matches ) ) {
			return function_exists( $matches['function'] );
		}

		// ------------------------------------
		// method_exists
		// ------------------------------------
		
		$pattern = '/^(?P<class>.+)::(?P<function>.+)$/';
		if ( 1 === preg_match( $pattern, $function, $matches ) ) {
			return method_exists( $matches['class'], $matches['function'] );
		}

		// ------------------------------------

		return false;
	}

  // ========================================================
  // ユーティリティ：Contact Form 7 プラグイン
  // ========================================================

	/**
	 * Contact Form 7 プラグインが有効化されているか確認する。
	 *
	 * @return boolean 有効化状態を返す。(true:有効/false:無効)
	 */
	public static function is_active_wpcf7()
	{
		return SELF::is_active_plugin(
			_EXTERNAL_PLUGIN['wpcf7']['basename']
		);
	}

	/**
	 * Contact Form 7 プラグインの投稿情報を取得する。
	 *
	 * @return WP_Post[] Contact Form 7 の投稿オブジェクトを返す。
	 */
	public static function get_wpcf7_posts()
	{
		return get_posts( array(
			'post_type'      => _EXTERNAL_PLUGIN['wpcf7']['post_type'],
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'offset'         => 0,
		) );
	}

   // ------------------------------------
   // WPCF7_Submission クラス
   // ------------------------------------

	/**
	 * Contact Form 7 プラグインの WPCF7_Submission クラスオブジェクトを取得する。
	 *
	 * @return WPCF7_Submission|null WPCF7_Submission クラスオブジェクトを返す。
	 */
	public static function get_wpcf7_submission()
	{
		if ( !class_exists( '\WPCF7_Submission' ) ) { return null; }
		return \WPCF7_Submission::get_instance();
	}

	/**
	 * Contact Form 7 プラグインの WPCF7_ContactForm クラスオブジェクトを取得する。
	 *
	 * @return WPCF7_ContactForm|null WPCF7_ContactForm クラスオブジェクトを返す。
	 */
	public static function get_wpcf7_submission_contact_form()
	{
		$submission = SELF::get_wpcf7_submission();
		if ( !$submission ) { return null; }
		return $submission->get_contact_form();
	}

	/**
	 * Contact Form 7 プラグインの送信データを取得する。
	 *
	 * @param string $name 送信データのフィールド名。
	 * @return mixed[]|string|null 送信データを返す。
	 */
	public static function get_wpcf7_submission_posted_data( $name = '' )
	{
		$submission = SELF::get_wpcf7_submission();
		if ( !$submission ) { return null; }
		return $submission->get_posted_data( strval( $name ) );
	}

   // ------------------------------------
   // WPCF7_ContactForm クラス
   // ------------------------------------

	/**
	 * Contact Form 7 プラグインの WPCF7_ContactForm オブジェクトを取得する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return WPCF7_ContactForm|null WPCF7_ContactForm オブジェクトを返す。
	 */
	public static function get_wpcf7_contact_form( $form_id )
	{
		if ( !class_exists( '\WPCF7_ContactForm' ) ) { return null; }
		return \WPCF7_ContactForm::get_instance( intval( $form_id ) );
	}

	/**
	 * Contact Form 7 プラグインのプロパティ設定を取得する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @param string $name プロパティのフィールド名。
	 * @return mixed[]|string|null プロパティ設定を返す。
	 */
	public static function get_wpcf7_contact_form_property( $form_id, $name )
	{
		$contact_form = SELF::get_wpcf7_contact_form( $form_id );
		if ( !$contact_form ) { return null; }
		return $contact_form->prop( strval( $name ) );
	}

  // ========================================================

}
