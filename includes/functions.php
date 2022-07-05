<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * タイムゾーンを設定する。
 * 
 * タイムゾーンが未指定の場合はWordPress設定を使用する。
 *
 * @param string $timezone_id タイムゾーンID (オプション/デフォルト:false)
 * @return bool タイムゾーンの設定結果true/falseを返す。
 */
function nt_wpcf7sn_set_timezone( $timezone_id = false ) {
	if ( false === $timezone_id ) {
		$timezone_id = get_option( 'timezone_string', 'UTC' );
	}

	return date_default_timezone_set( $timezone_id );
}
