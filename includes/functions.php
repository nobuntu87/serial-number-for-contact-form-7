<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * タイムゾーンを設定する。
 * 
 * タイムゾーン未指定の場合はWordPressのタイムゾーン設定を使用する。
 *
 * @param string $timezone_id タイムゾーンID (オプション)
 * @return bool タイムゾーンの設定結果を返す。
 */
function nt_wpcf7sn_set_timezone( $timezone_id = false ) {
	if ( false === $timezone_id ) {
		$timezone_id = get_option( 'timezone_string', 'UTC' );
	}
	
	$result = date_default_timezone_set( $timezone_id );
	
	return $result;
}
