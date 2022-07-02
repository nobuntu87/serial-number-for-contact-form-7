<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/serial-number.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/submission.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/mail-tag.php';

if ( is_admin() ) {
	require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/admin.php';
}


/**
 * アクションフック設定
 */
add_action( 'init', 'nt_wpcf7sn_init' );


class NT_WPCF7SN
{
	
	/**
	 * オプション(コンタクトフォーム設定)を取得する。
	 * 
	 * オプションが未設定の場合はデフォルト値で初期化を行う。
	 *
	 * @param string $name オプション名
	 * @param int $form_id コンタクトフォームID
	 * @return string|int|bool オプションの設定値を返す。
	 *                         オプションが未設定の場合はデフォルト値を返す。
	 *                         オプション名が定義されていない場合はFALSEを返す。
	 */
	public function get_form_option( $name, $form_id ) {
		if ( ! isset( NT_WPCF7SN_FORM_OPTION[$name] ) ) {
			return false;
		}
	
		$key = NT_WPCF7SN_FORM_OPTION[$name]['key'] . $form_id;
		$default = NT_WPCF7SN_FORM_OPTION[$name]['default'];

		// DBからオプション値を取得
		$option = get_option( $key );
		if ( false === $option ) {
			update_option( $key, $default );
			return $default;
		}

		// 変数型の変換
		$type = NT_WPCF7SN_FORM_OPTION[$name]['type'];
		switch ( $type ) {
			case 'int' :
				return intval( $option );
			case 'string' :
				return strval( $option );
			default :
				return $option;
		}
	}

	/**
	 * オプション(コンタクトフォーム全設定)を取得する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @return string[] コンタクトフォームの全設定を返す。
	 */
	public function get_form_options( $form_id ) {
		$form_options = [];
		
		// 全てのオプション値を取得
		foreach( NT_WPCF7SN_FORM_OPTION as $key => $value ) {
			$form_options[$key] = self::get_form_option( $key, $form_id );
		}

		return $form_options;
	}

	/**
	 * オプション(コンタクトフォーム設定)を更新する。
	 *
	 * @param string $name オプション名
	 * @param int $form_id コンタクトフォームID
	 * @param mixed $option オプション値
	 * @return bool オプション名が定義されていない場合はFALSEを返す。
	 */
	public function update_form_option( $name, $form_id, $option ) {
		if ( ! isset( NT_WPCF7SN_FORM_OPTION[$name] ) ) {
			return false;
		}

		$key = NT_WPCF7SN_FORM_OPTION[$name]['key'] . $form_id;

		// 変数型の変換
		$type = NT_WPCF7SN_FORM_OPTION[$name]['type'];
		switch ( $type ) {
			case 'int' :
				$option = intval( $option );
				break;
			case 'string' :
				$option = strval( $option );
				break;
			default :
				break;
		}

		// DBにオプション値を設定
		update_option( $key, $option );
	}

}


/**
 * プラグインを初期化する。
 * 
 * @return void
 */
function nt_wpcf7sn_init() {
	$timezone = get_option( 'timezone_string', 'UTC' );
	date_default_timezone_set( $timezone );
}
