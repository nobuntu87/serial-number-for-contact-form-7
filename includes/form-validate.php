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
			$validity = false;
		}

		// ------------------------------------
		// エラーメッセージ登録
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
			);
		}

		// ------------------------------------
		// (追加)オプション値検証
		// ------------------------------------

		$valid_func = sprintf( '%s\Form_Validate::validate_%s'
			, __NAMESPACE__ , strval( $key )
		);

		if ( Utility::function_exists( $valid_func ) ) {
			if ( !$valid_func( $value, $message ) ) {
				$validity = false;
			}
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
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_prefix( $value, &$message = null )
	{
		// false:事前検証(入力パターン検証)で無効時
		$validity = empty( $message ) ? true : false ;

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		// 追加の検証なし

		// ------------------------------------
		// エラーメッセージ登録 (追加/上書き)
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Contains invalid characters or Too many characters.', _TEXT_DOMAIN )
			);
		}

		// ------------------------------------

		return $validity;
	}

	/**
	 * オプション値の検証を行う。(表示桁数)
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_digits( $value, &$message = null )
	{
		// false:事前検証(入力パターン検証)で無効時
		$validity = empty( $message ) ? true : false ;

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		// 追加の検証なし

		// ------------------------------------
		// エラーメッセージ登録 (追加/上書き)
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ( ' . __( '1 digit integer. 1~9', _TEXT_DOMAIN ) . ' )'
			);
		}

		// ------------------------------------

		return $validity;
	}

	/**
	 * オプション値の検証を行う。(メールカウント)
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_count( $value, &$message = null )
	{
		// false:事前検証(入力パターン検証)で無効時
		$validity = empty( $message ) ? true : false ;

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		// 追加の検証なし

		// ------------------------------------
		// エラーメッセージ登録 (追加/上書き)
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ( ' . __( 'Up to 5 digits integer. 0~99999', _TEXT_DOMAIN ) . ' )'
			);
		}

		// ------------------------------------

		return $validity;
	}

	/**
	 * オプション値の検証を行う。(デイリーカウント)
	 *
	 * @param string $key オプションキー
	 * @param mixed $value オプション値
	 * @param string|null $message エラーメッセージ
	 * @return boolean 有効性の検証結果を返す。(true:有効/false:無効)
	 */
	public static function validate_daycount( $value, &$message = null )
	{
		// false:事前検証(入力パターン検証)で無効時
		$validity = empty( $message ) ? true : false ;

		// ------------------------------------
		// オプション値検証
		// ------------------------------------

		// 追加の検証なし

		// ------------------------------------
		// エラーメッセージ登録 (追加/上書き)
		// ------------------------------------

		if ( !$validity ) {
			$message = sprintf( ''
				. __( 'Input value is invalid.', _TEXT_DOMAIN )
				. ' ( ' . __( 'Up to 5 digits integer. 0~99999', _TEXT_DOMAIN ) . ' )'
			);
		}

		// ------------------------------------

		return $validity;
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

  // ========================================================

}
