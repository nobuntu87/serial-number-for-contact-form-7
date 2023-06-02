<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// グローバルオプション定義
// ============================================================================

$_NT_WPCF7SN['form'] = [];

// ============================================================================
// コンタクトフォーム設定操作クラス：Form_Option
// ============================================================================

class Form_Option {

  // ========================================================
  // 初期化
  // ========================================================

	/**
	 * オプションを初期化する。(全数)
	 *
	 * @return void
	 */
	public static function init_options()
	{
		// ------------------------------------
		// 整合性チェック
		// ------------------------------------

		// 全体の設定チェック
		SELF::check_options_integrity();

		// 個別の設定チェック
		foreach ( SELF::get_options() as $form_id => $form_option ) {

			$form_id = strval( $form_id );
			$option_value = SELF::check_option_integrity( $form_option );

			// 変更がある場合は更新
			if ( $form_option !== $option_value ) {
				SELF::update_option( $form_id, $option_value );
			}

		}

		// ------------------------------------
		// バリテーション
		// ------------------------------------

		foreach ( SELF::get_options() as $form_id => $form_option ) {

			$form_id = strval( $form_id );
			$option_value = SELF::check_option_validity( $form_id, $form_option );

			// 変更がある場合は更新
			if ( $form_option !== $option_value ) {
				SELF::update_option( $form_id, $option_value );
			}

		}

		// ------------------------------------
		// グローバルオプション設定
		// ------------------------------------

		SELF::init_global_options();
	}

	/**
	 * オプションを初期化する。(個別)
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return void
	 */
	public static function init_option( $form_id )
	{
		// ------------------------------------
		// コンタクトフォーム設定の整合性チェック
		// ------------------------------------

		// 処理不要

		// ------------------------------------
		// コンタクトフォーム設定のバリテーション
		// ------------------------------------

		// 処理不要

		// ------------------------------------
		// グローバルオプション設定
		// ------------------------------------

		SELF::init_global_option( strval( $form_id ) );
	}

   // ------------------------------------
   // 整合性チェック
   // ------------------------------------

	/**
	 * コンタクトフォーム設定の整合性チェックを行う。(全数)
	 *
	 * @return void
	 */
	private static function check_options_integrity()
	{
		// ------------------------------------
		// コンタクトフォームID取得
		// ------------------------------------
		
		// [ContactForm7] コンタクトフォームID取得
		$wpcf7_form_ids = [];
		foreach ( Utility::get_wpcf7_posts() as $wpcf7_post ) {
			$wpcf7_form_ids[] = strval( $wpcf7_post->ID );
		}

		// [SerialNumber] コンタクトフォームID取得
		$wpcf7sn_form_ids = [];
		foreach ( SELF::get_options() as $form_option ) {
			$wpcf7sn_form_ids[] = strval( $form_option['form_id'] );
		}

		// ------------------------------------
		// 不要オプション削除
		// - - - - - - - - - - - - - - - - - -
		//   [CF7:無] / [CF7SN:有]
		// ------------------------------------

		foreach ( $wpcf7sn_form_ids as $wpcf7sn_form_id ) {
			if ( !in_array( $wpcf7sn_form_id, $wpcf7_form_ids ) ) {

				// 不要オプション削除
				SELF::delete_option( $wpcf7sn_form_id );

			}
		}

		// ------------------------------------
		// 不足オプション追加
		// - - - - - - - - - - - - - - - - - -
		//   [CF7:有] / [CF7SN:無]
		// ------------------------------------

		foreach ( $wpcf7_form_ids as $wpcf7_form_id ) {
			if ( !in_array( $wpcf7_form_id, $wpcf7sn_form_ids ) ) {

				// 不足オプション追加 (既定値で初期化)
				SELF::update_option(
					$wpcf7_form_id,
					SELF::get_default_value( $wpcf7_form_id )
				);

			}
		}
	}

	/**
	 * コンタクトフォーム設定値の整合性チェックを行う。(個別)
	 *
	 * @param mixed[] $option_value オプション値
	 * @return void mixed[] コンタクトフォーム設定値を返す。
	 */
	private static function check_option_integrity( $option_value )
	{
		if ( !is_array( $option_value ) ) { return []; }

		// ------------------------------------
		// オプションキー取得
		// ------------------------------------
		
		// オプション定義のキー取得
		$define_keys = [];
		foreach ( _FORM_OPTIONS as $item => $option ) {
			$define_keys[] = strval( $option['key'] );
		}

		// ------------------------------------
		// 不要オプション値削除
		// - - - - - - - - - - - - - - - - - -
		//   [定義:無] / [設定値:有]
		// ------------------------------------

		foreach ( $option_value as $key => $value ) {
			if ( !in_array( $key, $define_keys ) ) {

				// 不要オプション値削除
				unset( $option_value[$key] );

			}
		}

		// ------------------------------------
		// 不足オプション値追加 (既定値)
		// - - - - - - - - - - - - - - - - - -
		//   [定義:有] / [設定値:無]
		// ------------------------------------

		foreach ( _FORM_OPTIONS as $item => $option ) {
			if ( !array_key_exists( $option['key'], $option_value ) ) {

				// 不足オプション値追加 (既定値で初期化)
				$option_value += array(
					$option['key'] => strval( $option['default'] )
				);

			}
		}

		// ------------------------------------
		// リセット機能チェック
		// ------------------------------------

		// リセット機能が未対応の場合
		if ( !NT_WPCF7SN::is_working_dayreset() ) {

			$option_value['daycount'] = strval( _FORM_OPTIONS['daycount']['default'] );
			$option_value['dayreset'] = strval( _FORM_OPTIONS['dayreset']['default'] );

		}

		// ------------------------------------

		return $option_value;
	}


   // ------------------------------------
   // バリテーション
   // ------------------------------------

	/**
	 * コンタクトフォーム設定値の妥当性チェックを行う。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @param mixed[] $option_value オプション値
	 * @return void mixed[] コンタクトフォーム設定値を返す。
	 */
	private static function check_option_validity( $form_id, $option_value )
	{
		$form_id = strval( $form_id );

		$default_value = SELF::get_default_value( $form_id );

		// ------------------------------------
		// コンタクトフォーム設定
		// ------------------------------------

		$option_value['form_id'] = $default_value['form_id'];

		$option_value['mail_tag'] = $default_value['mail_tag'];

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		foreach ( $option_value as $key => $value ) {
			if ( !Form_Validate::validate_option( $key, $value ) ) {
				$option_value[$key] = $default_value[$key];
			}
		}

		// ------------------------------------

		return $option_value;
	}

   // ------------------------------------
   // グローバルオプション
   // ------------------------------------

	/**
	 * グローバルオプションの初期化を行う。(全数)
	 *
	 * @return void
	 */
	private static function init_global_options()
	{
		$GLOBALS['_NT_WPCF7SN']['form'] = [];
		foreach ( Utility::get_wpcf7_posts() as $wpcf7_post ) {
			SELF::init_global_option( strval( $wpcf7_post->ID ) );
		}
	}

	/**
	 * グローバルオプションの初期化を行う。(個別)
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return void
	 */
	private static function init_global_option( $form_id )
	{
		$form_id = strval( $form_id );

		$GLOBALS['_NT_WPCF7SN']['form'][$form_id] = [];
		$GLOBALS['_NT_WPCF7SN']['form'][$form_id] = Utility::array_update(
			SELF::get_default_value( $form_id ),
			SELF::get_option( $form_id )
		);
	}

  // ========================================================
  // コンタクトフォーム設定
  // ========================================================

	/**
	 * コンタクトフォーム設定を取得する。(全数)
	 *
	 * @return void mixed[] コンタクトフォーム設定を返す。
	 */
	public static function get_options()
	{
		$form_options = [];

		// ------------------------------------
		// データベース全数取得
		// ------------------------------------

		$wpdb_options = Utility::get_wpdb_options( sprintf( '%s_%s_%s%%'
			, _PREFIX['_']
			, _ADMIN_MENU_SLUG
			, _ADMIN_MENU_TAB_PREFIX
		) );

		if ( !is_array( $wpdb_options ) ) { return []; }

		// ------------------------------------
		// コンタクトフォーム設定変換
		// ------------------------------------

		foreach( $wpdb_options as $wpdb_option ) {

			$option_value = $wpdb_option['option_value'];

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

			$form_options += array(
				$option_value['form_id'] => $option_value
			);

		}

		// ------------------------------------

		return $form_options;
	}

	/**
	 * コンタクトフォーム設定の設定値を取得する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return mixed[] オプション値を返す。
	 */
	public static function get_option( $form_id )
	{
		return Admin_Menu_Util::get_option(
			Admin_Menu_Util::get_option_name(
				_PREFIX['_'],
				_ADMIN_MENU_SLUG,
				_ADMIN_MENU_TAB_PREFIX . strval( $form_id )
			)
		);
	}

	/**
	 * コンタクトフォーム設定の設定値を更新する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @param mixed[] $option_value オプション値
	 * @return void
	 */
	public static function update_option( $form_id, $option_value )
	{
		Admin_Menu_Util::update_option(
			Admin_Menu_Util::get_option_name(
				_PREFIX['_'],
				_ADMIN_MENU_SLUG,
				_ADMIN_MENU_TAB_PREFIX . strval( $form_id )
			),
			$option_value
		);
	}

	/**
	 * コンタクトフォーム設定を削除する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return void
	 */
	public static function delete_option( $form_id )
	{
		Utility::delete_option(
			Admin_Menu_Util::get_option_name(
				_PREFIX['_'],
				_ADMIN_MENU_SLUG,
				_ADMIN_MENU_TAB_PREFIX . strval( $form_id )
			)
		);
	}

	/**
	 * コンタクトフォーム設定の既定値を取得する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return void mixed[] コンタクトフォーム設定値を返す。
	 */
	private static function get_default_value( $form_id )
	{
		$form_id = strval( $form_id );

		$default_value = [];

		// 定義から既定値を生成
		foreach ( _FORM_OPTIONS as $item => $option ) {
			$default_value += array(
				strval( $option['key'] ) => strval( $option['default'] )
			);
		}

		$default_value['form_id'] = $form_id;

		$default_value['mail_tag'] = Mail_Tag::get_sn_mail_tag( $form_id );

		return $default_value;
	}

  // ========================================================
  // メールカウント
  // ========================================================

	/**
	 * メールカウントを取得する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return string メールカウント数を返す。
	 */
	public static function get_mail_count( $form_id )
	{
		$form_id = strval( $form_id );

		// コンタクトフォーム設定取得
		$form_option = SELF::get_option( $form_id );

		// デイリーカウント取得
		if ( 'yes' === $form_option['dayreset'] ) {
			return strval( $form_option['daycount'] );
		}
		// メールカウント取得
		else {
			return strval( $form_option['count'] );
		}
	}

	/**
	 * メールカウントを増加する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return void
	 */
	public static function increment_mail_count( $form_id )
	{
		$form_id = strval( $form_id );

		// コンタクトフォーム設定取得
		$form_option = SELF::get_option( $form_id );

		// カウント増加
		$form_option['count'] = strval( intval( $form_option['count'] ) + 1 );
		$form_option['daycount'] = strval( intval( $form_option['daycount'] ) + 1 );

		// コンタクトフォーム設定更新
		SELF::update_option( $form_id, $form_option );
	}

	/**
	 * デイリーメールカウントをリセットする。
	 *
	 * @return void
	 */
	public static function reset_daily_mail_count()
	{
		// コンタクトフォーム設定取得
		foreach ( SELF::get_options() as $form_id => $form_option ) {

			$form_id = strval( $form_id );

			// カウント初期化
			$form_option['daycount'] = strval( _FORM_OPTIONS['daycount']['default'] );

			// コンタクトフォーム設定更新
			SELF::update_option( $form_id, $form_option );

		}
	}

  // ========================================================
  // メールカウント条件設定
  // ========================================================

	/**
	 * メール送信失敗時にメールカウントを増加するか確認する。
	 * 
	 * [ContactForm7標準動作] カウント増加する
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return boolean カウント増加結果を返す。(true:増加する/false:増加しない)
	 */
	public static function is_increment_mail_failed( $form_id )
	{
		$form_id = strval( $form_id );

		// コンタクトフォーム設定取得
		$form_option = SELF::get_option( $form_id );

		// メールカウント条件判定
		if ( 'yes' === $form_option['nocount_mail_failed'] ) {
			return false;
		}

		return true;
	}

  // ========================================================

}

// ============================================================================
// コンタクトフォーム設定検証クラス：Form_Validate
// ============================================================================

class Form_Validate {

  // ========================================================
  // オプション値検証
  // ========================================================

	/**
	 * オプション値の検証を行う。
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_option( $key, $value, &$message = null )
	{
		$validity = true;

		// ------------------------------------
		// 入力パターン検証
		// ------------------------------------

		if ( !SELF::is_match_pattern( strval( $key ), $value ) ) {
			$validity = false;
		}

		// ------------------------------------
		// エラーメッセージ登録
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
			);
		}

		// ------------------------------------
		// (追加)オプション値検証
		// ------------------------------------

		$valid_func = sprintf( '%s\Form_Validate::validate_%s'
			, __NAMESPACE__ , strval( $key )
		);

		if ( Utility::function_exists( $valid_func ) ) {
			if ( !$valid_func( $value, $message ) ) {
				$validity = false;
			}
		}

		// ------------------------------------

		return $validity;
	}

  // ========================================================
  // (追加)オプション値検証
  // ========================================================

	/**
	 * オプション値の検証を行う。(プレフィックス)
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_prefix( $value, &$message = null )
	{
		// false:事前検証(入力パターン検証)で無効時
		$validity = empty( $message ) ? true : false ;

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		// 追加の検証なし

		// ------------------------------------
		// エラーメッセージ登録 (追加/上書き)
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Contains invalid characters or Too many characters.', _TEXT_DOMAIN )
			);
		}

		// ------------------------------------

		return $validity;
	}

	/**
	 * オプション値の検証を行う。(表示桁数)
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_digits( $value, &$message = null )
	{
		// false:事前検証(入力パターン検証)で無効時
		$validity = empty( $message ) ? true : false ;

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		// 追加の検証なし

		// ------------------------------------
		// エラーメッセージ登録 (追加/上書き)
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ( ' . __( '1 digit integer. 1~9', _TEXT_DOMAIN ) . ' )'
			);
		}

		// ------------------------------------

		return $validity;
	}

	/**
	 * オプション値の検証を行う。(メールカウント)
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_count( $value, &$message = null )
	{
		// false:事前検証(入力パターン検証)で無効時
		$validity = empty( $message ) ? true : false ;

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		// 追加の検証なし

		// ------------------------------------
		// エラーメッセージ登録 (追加/上書き)
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ( ' . __( 'Up to 5 digits integer. 0~99999', _TEXT_DOMAIN ) . ' )'
			);
		}

		// ------------------------------------

		return $validity;
	}

	/**
	 * オプション値の検証を行う。(デイリーカウント)
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_daycount( $value, &$message = null )
	{
		// false:事前検証(入力パターン検証)で無効時
		$validity = empty( $message ) ? true : false ;

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		// 追加の検証なし

		// ------------------------------------
		// エラーメッセージ登録 (追加/上書き)
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ( ' . __( 'Up to 5 digits integer. 0~99999', _TEXT_DOMAIN ) . ' )'
			);
		}

		// ------------------------------------

		return $validity;
	}

  // ========================================================

	/**
	 * 正規表現パターンと一致するかチェックする。
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @return boolean チェック結果を返す。(true:一致/false:不一致)
	 */
	private static function is_match_pattern( $key, $value )
	{
		$pattern = '';

		// ------------------------------------
		// 正規表現パターン取得
		// ------------------------------------

		foreach ( _FORM_OPTIONS as $item => $option ) {
			if ( $option['key'] === strval( $key ) ) {
				$pattern = '/' . $option['pattern'] . '/';
			}
		}

		if ( empty( $pattern ) ) { return false; }

		// ------------------------------------
		// 正規表現マッチング
		// ------------------------------------

		if ( 1 === preg_match( $pattern, $value ) ) {
			return true;
		}

		// ------------------------------------

		return false;
	}

  // ========================================================

}
