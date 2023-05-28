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
	private function create_snum_number( $count, $option_value )
	{
		$digits = $option_value['digits'];

		return sprintf( '%s'
			, SELF::convert_num_digits( $count, $digits )
		);
	}

	/**
	 * シリアル番号を生成する。(UNIX時間)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private function create_snum_unixtime( $count, $option_value )
	{
		$separator = $option_value['separator'] === 'yes' ? '-' : '';
		$digits = $option_value['digits'];

		return sprintf( '%s%s%s'
			, SELF::get_timestamp( 'U' )
			, $separator
			, SELF::convert_num_digits( $count, $digits )
		);
	}

	/**
	 * シリアル番号を生成する。(年月日)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private function create_snum_date( $count, $option_value )
	{
		$separator = $option_value['separator'] === 'yes' ? '-' : '';
		$digits = $option_value['digits'];
		$format = $option_value['year2dig'] === 'yes' ? 'ymd' : 'Ymd';

		return sprintf( '%s%s%s'
			, SELF::get_timestamp( $format )
			, $separator
			, SELF::convert_num_digits( $count, $digits )
		);
	}

	/**
	 * シリアル番号を生成する。(年月日+時分秒)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private function create_snum_datetime( $count, $option_value )
	{
		$separator = $option_value['separator'] === 'yes' ? '-' : '';
		$digits = $option_value['digits'];
		$format = $option_value['year2dig'] === 'yes' ? 'ymd' : 'Ymd';

		return sprintf( '%s%s%s%s%s'
			, SELF::get_timestamp( $format )
			, $separator
			, SELF::get_timestamp( 'His' )
			, $separator
			, SELF::convert_num_digits( $count, $digits )
		);
	}

	/**
	 * シリアル番号を生成する。(ユニークID)
	 *
	 * @param int|string $count メールカウント
	 * @param mixed[] $option_value オプション値
	 * @return string シリアル番号を返す。
	 */
	private function create_snum_unique( $count, $option_value )
	{
		$separator = $option_value['separator'] === 'yes' ? '-' : '';
		$digits = $option_value['digits'];

		if ( $option_value['nocount'] === 'yes' ) {
			return sprintf( '%s'
				, SELF::create_unique_id( $count )
			);
		}
		else {
			return sprintf( '%s%s%s'
				, SELF::create_unique_id( $count )
				, $separator
				, SELF::convert_num_digits( $count, $digits )
			);
		}
	}

  // ========================================================

	/**
	 * ユニークIDを生成する。
	 *
	 * @param int|string $count メールカウント
	 * @return string ユニークIDを返す。
	 */
	private function create_unique_id( $count )
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
	private function convert_num_digits( $number, $digits )
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
	private function get_timestamp( $format )
	{
		return date_i18n( $format );
	}

  // ========================================================

}
