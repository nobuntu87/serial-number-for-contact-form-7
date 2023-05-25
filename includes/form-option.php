<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// コンタクトフォーム設定操作クラス：Form_Option
// ============================================================================

class Form_Option {

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
