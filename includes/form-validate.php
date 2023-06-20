<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// コンタクトフォーム設定検証クラス：Form_Validate
// ============================================================================

class Form_Validate {

  // ========================================================
  // オプション値検証
  // ========================================================

	/**
	 * オプション値の検証を行う。
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_option( $key, $value, &$message = null )
	{
		$validity = true;

		// ------------------------------------
		// 入力パターン検証
		// ------------------------------------

		if ( !SELF::is_match_pattern( strval( $key ), $value ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
			);
			$validity = false;
		}

		// ------------------------------------
		// (追加)オプション値検証
		// ------------------------------------

		$valid_func = sprintf( '%s::validate_%s'
			, __CLASS__ , strval( $key )
		);

		if ( Utility::function_exists( $valid_func ) ) {
			$validity = $valid_func( $value, $message );
		}

		// ------------------------------------

		return $validity;
	}

  // ========================================================
  // (追加)オプション値検証
  // ========================================================

	/**
	 * オプション値の検証を行う。(プレフィックス)
	 *
	 * @param string $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_prefix( $value, &$message = null )
	{
		// ------------------------------------
		// 入力パターン検証 (メッセージ変更)
		// ------------------------------------

		if ( !empty( $message ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . __( 'Contains invalid characters.', _TEXT_DOMAIN )
			);
			return false;
		}

		// ------------------------------------
		// 制御文字 検出
		// ------------------------------------

		if ( SELF::detect_control_characters( $value ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . __( 'Control characters are not allowed.', _TEXT_DOMAIN )
			);
			return false;
		}

		// ------------------------------------
		// HTMLタグ 検出
		// ------------------------------------

		if ( SELF::detect_html_tags( $value ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . __( 'HTML tags are not allowed.', _TEXT_DOMAIN )
			);
			return false;
		}

		// ------------------------------------

		return true;
	}

	/**
	 * オプション値の検証を行う。(表示桁数)
	 *
	 * @param int $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_digits( $value, &$message = null )
	{
		// ------------------------------------
		// 入力パターン検証 (メッセージ変更)
		// ------------------------------------

		if ( !empty( $message ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . '( ' . __( '1 digit integer : 1~9', _TEXT_DOMAIN ) . ' )'
			);
			return false;
		}

		// ------------------------------------

		return true;
	}

	/**
	 * オプション値の検証を行う。(メールカウント)
	 *
	 * @param int $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_count( $value, &$message = null )
	{
		// ------------------------------------
		// 入力パターン検証 (メッセージ変更)
		// ------------------------------------

		if ( !empty( $message ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . '( ' . __( 'Up to 5 digits integer : 0~99999', _TEXT_DOMAIN ) . ' )'
			);
			return false;
		}

		// ------------------------------------

		return true;
	}

	/**
	 * オプション値の検証を行う。(デイリーカウント)
	 *
	 * @param int $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_daycount( $value, &$message = null )
	{
		// ------------------------------------
		// 入力パターン検証 (メッセージ変更)
		// ------------------------------------

		if ( !empty( $message ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . '( ' . __( 'Up to 5 digits integer : 0~99999', _TEXT_DOMAIN ) . ' )'
			);
			return false;
		}

		// ------------------------------------

		return true;
	}

	/**
	 * オプション値の検証を行う。(送信結果メッセージ)
	 *
	 * @param string $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_sent_msg( $value, &$message = null )
	{
		// ------------------------------------
		// 入力パターン検証 (メッセージ変更)
		// ------------------------------------

		if ( !empty( $message ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . __( 'Contains invalid characters.', _TEXT_DOMAIN )
			);
			return false;
		}

		// ------------------------------------
		// 制御文字 検出
		// ------------------------------------

		if ( SELF::detect_control_characters( $value ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . __( 'Control characters are not allowed.', _TEXT_DOMAIN )
			);
			return false;
		}

		// ------------------------------------
		// HTMLタグ 検出
		// ------------------------------------

		if ( SELF::detect_html_tags( $value ) ) {
			// エラーメッセージ登録
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ' . __( 'HTML tags are not allowed.', _TEXT_DOMAIN )
			);
			return false;
		}

		// ------------------------------------

		return true;
	}

  // ========================================================

	/**
	 * 正規表現パターンと一致するかチェックする。
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @return boolean チェック結果を返す。(true:一致/false:不一致)
	 */
	private static function is_match_pattern( $key, $value )
	{
		$pattern = '';

		// ------------------------------------
		// 正規表現パターン取得
		// ------------------------------------

		foreach ( _FORM_OPTIONS as $global_key => $option ) {
			if ( $option['key'] === strval( $key ) ) {
				$pattern = '/' . $option['pattern'] . '/';
			}
		}

		if ( empty( $pattern ) ) { return false; }

		// ------------------------------------
		// 正規表現マッチング
		// ------------------------------------

		if ( 1 === preg_match( $pattern, $value ) ) {
			return true;
		}

		// ------------------------------------

		return false;
	}

	/**
	 * 制御文字が含まれるか検出を行う。
	 *
	 * @param string $value オプション値
	 * @param boolean $newline 改行コード(true:対象/false:対象外)
	 * @return boolean 検出結果を返す。(true:検出/false:未検出)
	 */
	private static function detect_control_characters( $value, $newline = true )
	{
		$pattern = $newline ? '/[\x00-\x1F\x7F]/' : '/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/';
		if ( 1 === preg_match( $pattern, $value ) ) { return true; }
		return false;
	}

	/**
	 * HTMLタグが含まれるか検出を行う。
	 *
	 * @param string $value オプション値
	 * @return boolean 検出結果を返す。(true:検出/false:未検出)
	 */
	private static function detect_html_tags( $value )
	{
		if ( $value != wp_strip_all_tags( $value ) ) { return true; }
		return false;
	}

  // ========================================================

}
