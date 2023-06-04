<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// シリアル番号操作クラス：Serial_Number
// ============================================================================

class Serial_Number {

  // ========================================================
  // シリアル番号
  // ========================================================

	/**
	 * シリアル番号を取得する。
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @param int|string $count メールカウント
	 * @return void
	 */
	public static function get_serial_number( $form_id, $count = '' )
	{
		$form_id = strval( $form_id );
		$count = strval( $count );

		// ------------------------------------
		// コンタクトフォーム設定取得
		// ------------------------------------

		$form_option = Form_Option::get_option( $form_id );

		// ------------------------------------
		// メールカウント設定
		// ------------------------------------

		if ( empty( $count ) || !is_numeric( $count ) ) {
			$count = strval( Form_Option::get_mail_count( $form_id ) );
		}

		// ------------------------------------
		// シリアル番号生成
		// ------------------------------------

		$serial_number = '';

		switch( strval( $form_option['type'] ) ) {
			case '1': // タイムスタンプ (UNIX時間)
				$serial_number = SELF::create_snum_unixtime( $count, $form_option );
				break;
			case '2': // タイムスタンプ (年月日)
				$serial_number = SELF::create_snum_date( $count, $form_option );
				break;
			case '3': // タイムスタンプ (年月日+時分秒)
				$serial_number = SELF::create_snum_datetime( $count, $form_option );
				break;
			case '4': // ユニークID (英数字)
				$serial_number = SELF::create_snum_unique( $count, $form_option );
				break;
			default: // 通し番号
				$serial_number = SELF::create_snum_number( $count, $form_option );
				break;
		}

		// ------------------------------------

		return strval( $form_option['prefix'] . $serial_number );
	}

   // ------------------------------------
   // シリアル番号生成
   // ------------------------------------

	/**
	 * シリアル番号を生成する。(通し番号)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private static function create_snum_number( $count, $option_value )
	{
		$digits = $option_value['digits'];

		// ------------------------------------
		// シリアル番号生成
		// ------------------------------------

		// 連番
		$serial_number = sprintf( '%s'
			, SELF::convert_num_digits( $count, $digits )
		);

		// ------------------------------------

		return strval( $serial_number );
	}

	/**
	 * シリアル番号を生成する。(UNIX時間)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private static function create_snum_unixtime( $count, $option_value )
	{
		$separator = $option_value['separator'] === 'yes' ? '-' : '';
		$digits = $option_value['digits'];
		$nocount = $option_value['nocount'] === 'yes' ? true : false;

		// ------------------------------------
		// UNIX時間設定
		// ------------------------------------

		$unixtime = '';
		$microtime = '';

		SELF::get_unixtime( $unixtime, $microtime );

		switch( strval( $option_value['unixtime_type'] ) ) {
			case '1': // ミリ秒 (ms)
				$microtime = substr( $microtime, 0, 3 );
				break;
			case '2': // マイクロ秒 (μs)
				$microtime = substr( $microtime, 0, 6 );
				break;
			default: // 秒 (s)
				$microtime = '';
				break;
		}

		// ------------------------------------
		// シリアル番号生成
		// ------------------------------------

		$serial_number = sprintf( '%s'
			, strval( $unixtime )
		);

		if ( !empty( $microtime ) ) {
			$serial_number .= sprintf( '%s%s'
				, $separator
				, strval( $microtime )
			);
		}

		if ( !$nocount ) {
			$serial_number .= sprintf( '%s%s'
				, $separator
				, SELF::convert_num_digits( $count, $digits )
			);
		}

		// ------------------------------------

		return strval( $serial_number );
	}

	/**
	 * シリアル番号を生成する。(年月日)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private static function create_snum_date( $count, $option_value )
	{
		$separator = $option_value['separator'] === 'yes' ? '-' : '';
		$digits = $option_value['digits'];
		$format = $option_value['year2dig'] === 'yes' ? 'ymd' : 'Ymd';

		// ------------------------------------
		// シリアル番号生成
		// ------------------------------------

		$serial_number = sprintf( '%s%s%s'
			, SELF::get_timestamp( $format )
			, $separator
			, SELF::convert_num_digits( $count, $digits )
		);

		// ------------------------------------

		return strval( $serial_number );
	}

	/**
	 * シリアル番号を生成する。(年月日+時分秒)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private static function create_snum_datetime( $count, $option_value )
	{
		$separator = $option_value['separator'] === 'yes' ? '-' : '';
		$digits = $option_value['digits'];
		$format = $option_value['year2dig'] === 'yes' ? 'ymd' : 'Ymd';

		// ------------------------------------
		// シリアル番号生成
		// ------------------------------------

		$serial_number = sprintf( '%s%s%s%s%s'
			, SELF::get_timestamp( $format )
			, $separator
			, SELF::get_timestamp( 'His' )
			, $separator
			, SELF::convert_num_digits( $count, $digits )
		);

		// ------------------------------------

		return strval( $serial_number );
	}

	/**
	 * シリアル番号を生成する。(ユニークID)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private static function create_snum_unique( $count, $option_value )
	{
		$separator = $option_value['separator'] === 'yes' ? '-' : '';
		$digits = $option_value['digits'];
		$nocount = $option_value['nocount'] === 'yes' ? true : false;

		// ------------------------------------
		// シリアル番号生成
		// ------------------------------------

		$serial_number = sprintf( '%s'
			, SELF::create_unique_id( $count )
		);

		if ( !$nocount ) {
			$serial_number .= sprintf( '%s%s'
				, $separator
				, SELF::convert_num_digits( $count, $digits )
			);
		}

		// ------------------------------------

		return strval( $serial_number );
	}

  // ========================================================

	/**
	 * ユニークIDを生成する。
	 *
	 * @param int|string $count メールカウント
	 * @return string ユニークIDを返す。
	 */
	private static function create_unique_id( $count )
	{
		$unique_id = '';

		// タイムコード (UNIX時間を基準に桁数を減らすため起点時刻を変更)
		$microtime = microtime( true );
		$microtime -= strtotime( '2022/01/01 00:00:00' );
		$microtime = sprintf( '%.4f', $microtime );

		// 乱数 (00~99)
		$randum = sprintf( '%02d', mt_rand( 0, 99 ) );

		// ユニークIDの算出値を作成 (逆順変換)
		$basecode = $microtime . intval( $count ) . $randum;
		$basecode = strrev( $basecode );

		// 10進数を36進数[0-9/a-z]に変換 (大文字変換)
		$unique_id = base_convert( $basecode, 10, 36 );
		$unique_id = strtoupper( $unique_id );

		return strval( $unique_id );
	}

	/**
	 * 数値の表示桁数を変換する。
	 *
	 * @param int|string $number 数値
	 * @param int|string $digits 表示桁数
	 * @return string 数値の文字列を返す。
	 */
	private static function convert_num_digits( $number, $digits )
	{
		if ( intval( $digits ) > 0 ) {
			return sprintf( '%0' . strval( $digits ) . 'd', intval( $number ) );
		} else {
			return sprintf( '%d', intval( $number ) );
		}
	}

	/**
	 * タイムスタンプを取得する。
	 *
	 * @param string $format 表示フォーマット
	 * @return string タイムスタンプを返す。
	 */
	private static function get_timestamp( $format )
	{
		return date_i18n( $format );
	}

	/**
	 * UNIX時間を取得する。
	 *
	 * @param string $unixtime UNIX時間
	 * @param string $microtime マイクロ秒
	 * @return void
	 */
	private static function get_unixtime( &$unixtime, &$microtime )
	{
		list( $usec, $sec ) = explode( ' ', microtime() );
		$unixtime = strval( $sec );
		$microtime = strval( explode( '.', $usec )[1] );
	}

  // ========================================================

}
