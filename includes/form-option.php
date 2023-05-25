<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// コンタクトフォーム設定操作クラス：Form_Option
// ============================================================================

class Form_Option {

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
