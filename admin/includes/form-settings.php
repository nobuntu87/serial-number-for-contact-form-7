<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * アクションフック設定
 */
add_action( 'admin_init', 'nt_wpcf7sn_register_setting', 10, 0 );


/**
 * 設定項目と無害化用コールバックを登録する。
 *
 * @return void
 */
function nt_wpcf7sn_register_setting() {

}


/**
 * コンタクトフォームのオプションのサニタイズ処理を行う。
 *
 * @param mixed[] $options 入力されたオプション値
 * @return $options サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_options( $options ) {

}
