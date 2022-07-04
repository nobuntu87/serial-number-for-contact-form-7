<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/functions.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/serial-number.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/submission.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/mail-tag.php';

if ( is_admin() ) {
	require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/admin.php';
}


/**
 * アクションフック設定
 */
add_action( 'init', 'nt_wpcf7sn_init', 10, 0 );
add_action( 'admin_init', 'nt_wpcf7sn_upgrade', 10, 0 );


class NT_WPCF7SN
{
	
	/**
	 * コンタクトフォームのオプションをセットアップする。
	 * 
	 * デフォルト値で初期化しDBに保存する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @return mixed[] コンタクトフォームのオプションを返す。
	 */
	public function setup_form_options( $form_id ) {
		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_value = [];

		// 全てのオプションを設定
		foreach( NT_WPCF7SN_FORM_OPTION as $key => $value ) {
			$default = $value['default'];

			// 変数型の変換
			$type = $value['type'];
			switch ( $type ) {
				case 'integer' :
					$default = intval( $default );
					break;
				case 'string' :
					$default = strval( $default );
					break;
				default :
			}
			
			$option_value[$key] = $default;
		}

		update_option( $option_name, $option_value );

		return $option_value;
	}

	/**
	 * コンタクトフォームのオプションを取得する。
	 * 
	 * DBに存在しない場合は初期化し新規追加する。
	 * 
	 * @param int $form_id コンタクトフォームID
	 * @return mixed[] コンタクトフォームのオプションを返す。
	 */
	public function get_form_options( $form_id ) {
		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;
		
		$option_value = get_option( $option_name );

		// DBに存在しない場合はセットアップ
		if ( false === $option_value ) {
			return self::setup_form_options( $form_id );
		}

		// 変数型の変換
		foreach( $option_value as $key => $value ) {
			$type = NT_WPCF7SN_FORM_OPTION[$key]['type'];
			switch ( $type ) {
				case 'integer' :
					$option_value[$key] = intval( $value );
					break;
				case 'string' :
					$option_value[$key] = strval( $value );
					break;
				default :
			}
		}

		return $option_value;
	}

	/**
	 * コンタクトフォームのオプションを取得する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @param string $name オプション名
	 * @return mixed コンタクトフォームのオプションを返す。
	 *               オプション名が定義されていない場合はfalseを返す。
	 */
	public function get_form_option( $form_id, $name ) {
		if ( ! isset( NT_WPCF7SN_FORM_OPTION[$name] ) ) {
			return false;
		}

		$option_value = self::get_form_options( $form_id );

		if ( isset( $option_value[$name] ) ) {
			return $option_value[$name];
		} else {
			return NT_WPCF7SN_FORM_OPTION[$name]['default'];
		}
	}

	/**
	 * コンタクトフォームのオプションを更新する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @param string $name オプション名
	 * @param mixed $value オプション値
	 * @return bool オプション名が定義されていない場合はfalseを返す。
	 */
	public function update_form_option( $form_id, $name, $value ) {
		if ( ! isset( NT_WPCF7SN_FORM_OPTION[$name] ) ) {
			return false;
		}

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_value = self::get_form_options( $form_id );

		// 変数型の変換
		$type = NT_WPCF7SN_FORM_OPTION[$name]['type'];
		switch ( $type ) {
			case 'integer' :
				$value = intval( $value );
				break;
			case 'string' :
				$value = strval( $value );
				break;
			default :
		}

		$option_value = array_merge( $option_value, array( $name => $value ) );

		update_option( $option_name, $option_value );
	}

	/**
	 * コンタクトフォームのオプションを削除する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @param string $name オプション名
	 * @return void
	 */
	public function delete_form_option( $form_id, $name ) {
		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_value = self::get_form_options( $form_id );

		unset( $option_value[$name] );

		update_option( $option_name, $option_value );
	}

	/**
	 * コンタクトフォームのオプションの整合性をチェックする。
	 * 
	 * DBに存在するすべてのオプションをチェックする。
	 *
	 * @return void
	 */
	public function check_form_options() {
		global $wpdb;
	
		$options = $wpdb->get_results( "
			SELECT * FROM $wpdb->options
			WHERE option_name like '" . NT_WPCF7SN_FORM_OPTION_NAME . "%'
		" );
	
		foreach ( $options as $option ) {
			$option_name = $option->option_name;

			preg_match( '/(?P<id>[0-9]+)$/', $option_name, $match );
			$form_id = intval( $match['id'] );

			self::check_form_option( $form_id );
		}
	}

	/**
	 * コンタクトフォームのオプションの整合性をチェックする。
	 * 
	 * 不要(削除)オプションをチェックする。 (DB[有]/定義[無])
	 * 不足(追加)オプションをチェックする。 (DB[無]/定義[有])
	 *
	 * @param int $form_id コンタクトフォームID
	 * @return void
	 */
	public function check_form_option( $form_id ) {
		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_value = get_option( $option_name );

		// DBに存在しない場合は終了
		if ( false === $option_value ) {
			return;
		}

		// 整合性チェック：DB[有] / 定義[無] = 不要(削除オプション)
		foreach ( $option_value as $key => $value ) {
			if ( ! isset( NT_WPCF7SN_FORM_OPTION[$key] ) ) {
				self::delete_form_option( $form_id, $key );
			} else {
				// 変数型チェック
				$type = NT_WPCF7SN_FORM_OPTION[$key]['type'];
				if ( $type != gettype( $value ) ){
					self::update_form_option( $form_id, $key, $value );
				}
			}
		}

		// 整合性チェック：DB[無] / 定義[有] = 不足(追加オプション)
		foreach( NT_WPCF7SN_FORM_OPTION as $key => $value ) {
			if ( ! isset( $option_value[$key] ) ) {
				$default = $value['default'];
				self::update_form_option( $form_id, $key, $default );
			}
		}
	}

	/**
	 * プラグインのオプションを取得する。
	 * 
	 * DBに存在しない場合デフォルト値を設定する。
	 *
	 * @param string $name オプション名
	 * @param mixed $default デフォルト値 (オプション)
	 * @return mixed プラグインのオプションを返す。
	 */
	public function get_option( $name, $default = false ) {
		$option = get_option( NT_WPCF7SN_PREFIX['_'] );

		if ( false === $option ) {
			self::update_option( $name, $default );
			return $default;
		}

		if ( isset( $option[$name] ) ) {
			return $option[$name];
		} else {
			self::update_option( $name, $default );
			return $default;
		}
	}

	/**
	 * プラグインのオプションを更新する。
	 *
	 * @param string $name オプション名
	 * @param mixed $value オプション値
	 * @return void
	 */
	public function update_option( $name, $value ) {
		$option = get_option( NT_WPCF7SN_PREFIX['_'] );

		$option = ( false === $option ) ? array() : (array) $option;

		$option = array_merge( $option, array( $name => $value ) );

		update_option( NT_WPCF7SN_PREFIX['_'], $option );
	}

}


/**
 * プラグインを初期化する。
 * 
 * @return void
 */
function nt_wpcf7sn_init() {
	nt_wpcf7sn_set_timezone();
}


/**
 * プラグインのアップグレードを行う。
 * 
 * バージョン番号が変化した場合にアップグレード処理を行う。
 * オプションデータの整合性チェックを行う。
 *
 * @return void
 */
function nt_wpcf7sn_upgrade() {
	$old_ver = NT_WPCF7SN::get_option( 'version', '0' );
	$new_ver = NT_WPCF7SN_VERSION;

	if ( $old_ver == $new_ver ) {
		return;
	}

	// バージョン更新
	NT_WPCF7SN::update_option( 'version', $new_ver );

	// フォームオプションのチェック
	NT_WPCF7SN::check_form_options();
}
