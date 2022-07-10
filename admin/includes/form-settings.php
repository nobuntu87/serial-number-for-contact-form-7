<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * アクションフック設定
 */
add_action( 'admin_init', 'nt_wpcf7sn_register_setting', 10, 0 );


/**
 * 設定項目とサニタイズ用コールバックを登録する。
 * 
 * Settings API / register_setting()
 *
 * @return void
 */
function nt_wpcf7sn_register_setting() {
	$wpcf7_posts = nt_wpcf7sn_get_posts_wpcf7();

	foreach( $wpcf7_posts as $wpcf7_post ) {
		$form_id = intval( $wpcf7_post->ID );
	
		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;
	
		// 設定項目と無害化用コールバックを登録
		register_setting(
			$option_name,
			$option_name,
			'nt_wpcf7sn_sanitize_form_options'
		);
	}
}


/**
 * コンタクトフォームのオプションのサニタイズ処理を行う。
 *
 * @param mixed[] $options 入力されたオプション値
 * @return mixed[] サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_options( $options ) {
	foreach( $option as $key => $value ) {
		switch ( $key ) {
			case 'type' :
			case 'count' :
			case 'digits' :
			case 'prefix' :
			case 'separator' :
			case 'year2dig' :
			case 'nocount' :
			default :
		}
	}
	
	return $options;
}
