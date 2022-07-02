<?php
if (!defined('ABSPATH')) exit;


class NT_WPCF7SN_Serial_Number {

	/**
	 * シリアル番号を取得する。
	 * 
	 * カウント値が未指定の場合は現在の設定値を使用する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @param int $count メールカウント (Default:false)
	 * @return string シリアル番号を返す。
	 */
	public function get_serial_number( $form_id, $count = false ) {
		$serial_num = '';

		$type = intval( get_option( 'nt_wpcf7sn_type_' . $form_id ) );
		$prefix = get_option( 'nt_wpcf7sn_prefix_' . $form_id );
		$digits = intval( get_option( 'nt_wpcf7sn_digits_' . $form_id ) );
		$separator = get_option( 'nt_wpcf7sn_separator_' . $form_id );
		$year2dig = get_option( 'nt_wpcf7sn_year2dig_' . $form_id );
		$nocount = get_option( 'nt_wpcf7sn_nocount_' . $form_id );

		if ( false === $count ) {
			$count = intval( get_option( 'nt_wpcf7sn_count_' . $form_id ) );
		}

		$num = self::count_digits( $count, $digits );
		$sep = ( $separator == 'yes' ? '-' : '' );

		switch( $type ) {
			case 0: // 通し番号
				$serial_num = $num;
				break;
			case 1: // タイムスタンプ (UNIX時間)
				$time = self::get_unix_timestamp();
				$serial_num = $time . $sep . $num;
				break;
			case 2: // タイムスタンプ (年月日)
				$format = ( $year2dig == 'yes' ? 'ymd' : 'Ymd' );
				$date = self::get_date_timestamp( $format );
				$serial_num = $date . $sep . $num;
				break;
			case 3: // タイムスタンプ (年月日+時分秒)
				$format = ( $year2dig == 'yes' ? 'ymd' : 'Ymd' );
				$date = self::get_date_timestamp( $format );
				$time = self::get_date_timestamp( 'His' );
				$serial_num = $date . $sep . $time . $sep . $num;
				break;
			case 4: // ユニークID (英数字)
				$id = self::get_unique_id( $count );
				$serial_num = ( $nocount == 'yes' ? $id : $id . $sep . $num );
				break;
		}

		return $prefix . $serial_num;
	}

	/**
	 * タイムスタンプ(UNIX時間)を取得する
	 *
	 * @return string タイムスタンプを返す。
	 */
	private function get_unix_timestamp() {
		$timestamp = '';

		$microtime = microtime( true );
		$timestamp = sprintf( '%d', $microtime );

		return $timestamp;
	}

	/**
	 * タイムスタンプ(日付)を取得する
	 *
	 * @return string タイムスタンプを返す。
	 */
	private function get_date_timestamp( $format ) {
		$timestamp = '';

		$timestamp = date( $format );

		return $timestamp;
	}

	/**
	 * ユニークIDを取得する。
	 *
	 * @param int $count メールカウント
	 * @return string ユニークIDを返す。
	 */
	private function get_unique_id( $count ) {
		$unique_id = '';

		// タイムコード (UNIX時間を基準に桁数を減らすため起点時刻を変更)
		$microtime = microtime( true );
		$microtime -= strtotime( '2022/01/01 00:00:00' );
		$microtime = sprintf( '%.4f', $microtime );

		// 乱数 (00~99)
		$randum = sprintf( '%02d', mt_rand( 0, 99 ) );

		// ユニークIDの算出値を作成 (逆順変換)
		$basecode = $microtime . $count . $randum;
		$basecode = strrev( $basecode );

		// 10進数を36進数[0-9/a-z]に変換 (大文字変換)
		$unique_id = base_convert( $basecode, 10, 36 );
		$unique_id = strtoupper( $unique_id );

		return $unique_id;
	}

	/**
	 * カウント値を桁数表示に変換する。
	 *
	 * @param int $count メールカウント
	 * @param int $digits 表示桁数
	 * @return string 桁数表示のカウント値を返す。
	 */
	private function count_digits( $count, $digits ) {
		if ( $digits == 0 ) {
			return sprintf( "%d", $count );
		} else {
			return sprintf( "%0" . $digits . "d", $count );
		}
	}

}
