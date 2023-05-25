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

	/**
	 * コンタクトフォーム設定の初期化を行う。
	 *
	 * @return void
	 */
	public static function init_options()
	{
		// ------------------------------------
		// コンタクトフォーム設定の整合性チェック
		// ------------------------------------

		SELF::check_options_integrity();

		// ------------------------------------
		// コンタクトフォーム設定値の整合性チェック
		// ------------------------------------

		foreach ( SELF::get_all_options() as $form_id => $form_option ) {

			$option_value = SELF::check_option_value_integrity( $form_option );

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
	 * グローバルオプションの初期化を行う。
	 *
	 * @return void
	 */
	public static function init_global_options()
	{
		// ------------------------------------
		// グローバルオプション初期化
		// ------------------------------------

		$GLOBALS['_NT_WPCF7SN']['form'] = [];

		foreach ( Utility::get_wpcf7_posts() as $wpcf7_post ) {

			$form_id = strval( $wpcf7_post->ID );
			
			$GLOBALS['_NT_WPCF7SN']['form'] += array(
				$form_id => SELF::get_default_value( $form_id )
			);

		}

		// ------------------------------------
		// グローバルオプション更新
		// ------------------------------------

		foreach ( SELF::get_all_options() as $form_id => $form_option ) {

			// グローバルオプション更新
			$GLOBALS['_NT_WPCF7SN']['form'][$form_id] = Utility::array_update(
				$GLOBALS['_NT_WPCF7SN']['form'][$form_id], $form_option
			);

		}
	}

	/**
	 * コンタクトフォーム設定の整合性チェックを行う。(全数)
	 *
	 * @return void
	 */
	public static function check_options_integrity()
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
		foreach ( SELF::get_all_options() as $form_option ) {
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
	 * コンタクトフォーム設定値の整合性チェックを行う。
	 *
	 * @param mixed[] $option_value オプション値
	 * @return void mixed[] コンタクトフォーム設定値を返す。
	 */
	public static function check_option_value_integrity( $option_value )
	{
		if ( !is_array( $option_value ) ) { return []; }

		// ------------------------------------
		// オプションキー取得
		// ------------------------------------
		
		// [ContactForm7] オプション定義キー取得
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
		// 不足オプション値追加
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

		return $option_value;
	}

	/**
	 * コンタクトフォーム設定を取得する。(全数)
	 *
	 * @return void mixed[] コンタクトフォーム設定を返す。
	 */
	public static function get_all_options()
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
	 * コンタクトフォーム設定の既定値を取得する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return void mixed[] コンタクトフォーム設定値を返す。
	 */
	public static function get_default_value( $form_id )
	{
		$default_value = [];

		// 定義から既定値を生成
		foreach ( _FORM_OPTIONS as $item => $option ) {
			$default_value += array(
				$option['key'] => strval( $option['default'] )
			);
		}

		$default_value['form_id'] = strval( $form_id );

		$default_value['mail_tag'] = sprintf( '[%s%s]'
			, _MAIL_TAG_PREFIX , strval( $form_id )
		);

		return $default_value;
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

}
