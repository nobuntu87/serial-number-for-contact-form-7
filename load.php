<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/functions.php';
require_once NT_WPCF7SN_PLUGIN_DIR . '/includes/options.php';
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
	NT_WPCF7SN_Option::check_form_options();
}


/**
 * プラグインが初めて有効化された時のインストール処理を行う。
 * 
 * フォームオプションの初期化を行う。
 *
 * @return void
 */
function nt_wpcf7sn_install() {
	if ( get_option( NT_WPCF7SN_PREFIX['_'] ) ) {
		return;
	}

	NT_WPCF7SN_Option::setup_all_form_options();

	nt_wpcf7sn_upgrade();
}


/**
 * コンタクトフォームが生成された時の処理を行う。
 * 
 * 対象のフォームオプションを作成する。
 *
 * @param mixed $wpcf7_object クラスオブジェクト (WPCF7_ContactForm)
 * @return void
 */
function nt_wpcf7sn_create_form( $wpcf7_object ) {

}


/**
 * コンタクトフォームが削除された時の処理を行う。
 * 
 * 対象のフォームオプションを削除する。
 *
 * @param int $post_id PostID
 * @param mixed $post_data Postオブジェクト (WP_Post)
 * @return void
 */
function nt_wpcf7sn_delete_form( $post_id, $post_data ) {

}
