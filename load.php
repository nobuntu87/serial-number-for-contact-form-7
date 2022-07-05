<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/functions.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/form-options.php';
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
add_action( 'activate_' . NT_WPCF7SN_PLUGIN_BASENAME, 'nt_wpcf7sn_install', 10, 0 );
add_action( 'wpcf7_after_create', 'nt_wpcf7sn_create_form', 10, 1 );
add_action( 'delete_post', 'nt_wpcf7sn_delete_form', 10, 2 );


class NT_WPCF7SN {
	
	/**
	 * プラグインのオプションを取得する。
	 * 
	 * DBに存在しない場合デフォルト値で新規作成する。
	 *
	 * @param string $name オプション名
	 * @param mixed $default デフォルト値 (オプション/デフォルト:false)
	 * @return mixed プラグインのオプション値を返す。
	 */
	public function get_option( $name, $default = false ) {
		$option = get_option( NT_WPCF7SN_PREFIX['_'] );

		// DBに存在しない場合はデフォルト値で新規作成
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
 * プラグインの初期化処理を行う。
 * 
 * タイムゾーンを設定する。
 * 
 * @return void
 */
function nt_wpcf7sn_init() {
	// タイムゾーン設定
	nt_wpcf7sn_set_timezone();
}


/**
 * プラグインのアップグレード処理を行う。
 * 
 * バージョン番号が変化した場合にアップグレード処理を行う。
 * コンタクトフォームのオプションの整合性チェックを行う。
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

	// コンタクトフォームのオプションをチェック
	NT_WPCF7SN_Form_Options::check_all_options();
}


/**
 * プラグインが初めて有効化された時のインストール処理を行う。
 * 
 * コンタクトフォームのオプションの初期化を行う。
 *
 * @return void
 */
function nt_wpcf7sn_install() {
	if ( get_option( NT_WPCF7SN_PREFIX['_'] ) ) {
		return;
	}

	// コンタクトフォームのオプションを初期化
	NT_WPCF7SN_Form_Options::setup_all_options();

	// アップグレード処理
	nt_wpcf7sn_upgrade();
}


/**
 * コンタクトフォームが新規追加された時の処理を行う。
 * 
 * 対象のコンタクトフォームのオプションを作成する。
 *
 * @param mixed $wpcf7_object クラスオブジェクト (WPCF7_ContactForm)
 * @return void
 */
function nt_wpcf7sn_create_form( $wpcf7_object ) {
	$form_id = intval(  $wpcf7_object->__get( 'id' ) );

	// コンタクトフォームのオプションを初期化
	NT_WPCF7SN_Form_Options::setup_options( $form_id );
}


/**
 * コンタクトフォームが削除された時の処理を行う。
 * 
 * 対象のコンタクトフォームのオプションを削除する。
 *
 * @param int $post_id Post ID
 * @param mixed $post_data Postオブジェクト (WP_Post)
 * @return void
 */
function nt_wpcf7sn_delete_form( $post_id, $post_data ) {
	if ( 'wpcf7_contact_form' != $post_data->post_type ) {
		return;
	}

	$form_id = intval( $post_data->ID );

	$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

	delete_option( $option_name );
}
