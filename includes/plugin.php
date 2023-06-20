<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// プラグイン制御クラス：NT_WPCF7SN
// ============================================================================

class NT_WPCF7SN {

  // ========================================================
  // 初期化
  // ========================================================

	/**
	 * プラグインの初期化を行う。
	 *
	 * @return void
	 */
	public static function init_plugin()
	{
		// コンタクトフォーム設定の初期化
		Form_Option::init_options();
	}

  // ========================================================
  // オプション
  // ========================================================

	/**
	* オプション値を取得する。
	*
	* @param string $key オプションキー
	* @param string $default デフォルト値
	* @return string オプション値を返す。
	*/
	public static function get_option( $key, $default )
	{
		$key = strval( $key );
		$default = strval( $default );

		// オプション値を取得
		$option_value = Utility::get_wpdb_option( _OPTION_NAME );

		// 存在しない場合はデフォルト値で初期化 (NG：未定義/NULL)
		if ( !array_key_exists( $key, $option_value ) || !isset( $option_value[$key] ) ) {
			SELF::update_option( $key, $default );
			return $default;
		}

		return strval( $option_value[$key] );
	}

	/**
	* オプション値を更新する。
	*
	* @param string $key オプションキー
	* @param string $key_value オプション値
	* @return void
	*/
	public static function update_option( $key, $key_value )
	{
		$key = strval( $key );
		$key_value = strval( $key_value );

		// オプション値を取得
		$option_value = Utility::get_wpdb_option( _OPTION_NAME );

		// オプション値をマージ
		$option_value = array_merge( $option_value, array( $key => $key_value ) );

		// オプション値を更新
		Utility::update_wpdb_option( _OPTION_NAME, $option_value );
	}

  // ========================================================
  // デイリーリセット
  // ========================================================

	/**
	 * デイリーリセット機能が動作可能か確認する。
	 *
	 * @return boolean 動作対応結果を返す。(true:対応/false:未対応)
	 */
	public static function is_working_dayreset()
	{
		// ------------------------------------
		// 依存関係チェック：クラス
		// ------------------------------------

		// DateTime (PHP >= 5.2.0)
		if ( !class_exists( '\DateTime' ) ) { return false; }

		// DateTimeZone (PHP >= 5.2.0)
		if ( !class_exists( '\DateTimeZone' ) ) { return false; }

		// ------------------------------------

		return true;
	}

	/**
	 * デイリーリセットの実行チェックを行う。
	 *
	 * @return void
	 */
	public static function check_reset_count()
	{
		// リセット機能の動作確認
		if ( !SELF::is_working_dayreset() ) { return; }

		// ------------------------------------

		$timezone = new \DateTimeZone( 'UTC' );

		$datetime_format = 'Y-m-d H:i:s P';

		// ------------------------------------
		// 現在時刻 取得
		// ------------------------------------

		$now_time = new \DateTime( '', $timezone );

		// ------------------------------------
		// 最終リセット時刻 取得/判定
		// ------------------------------------

		$reset_timestamp = NT_WPCF7SN::get_option(
			'last_dayreset_time',
			$now_time->format( $datetime_format )
		);

		// ------------------------------------
		// リセット基準時刻 設定
		// ------------------------------------

		$reset_basetime = new \DateTime( $reset_timestamp );

		// リセット基準時刻を補正
		$reset_basetime->setTimeZone( $timezone );
		$reset_basetime->setTime( 0, 0 );

		// ------------------------------------
		// リセット実行 判定
		// ------------------------------------

		$reset_diff = $reset_basetime->diff( $now_time );

		// 1日以上経過でリセット実行
		if ( 1 <= intval( $reset_diff->format( '%a' ) ) ) {

			Form_Option::reset_daily_mail_count();

			NT_WPCF7SN::update_option(
				'last_dayreset_time',
				$now_time->format( $datetime_format )
			);

		}
	}

  // ========================================================
  // WordPressフック
  // ========================================================

   // ------------------------------------
   // アクションフック
   // ------------------------------------

	/**
	 * オプション更新の完了時の処理を行う。
	 * 
	 * [Action Hook] updated_option
	 *
	 * @param string $option_name オプション名
	 * @param mixed[] $old_value オプション値 (更新前)
	 * @param mixed[] $new_value オプション値 (更新後)
	 * @return void
	 */
	public static function updated_option( $option_name, $old_value, $new_value )
	{
		// オプション名判別
		if ( 1 !== preg_match( _ADMIN_MENU_REGEX['option_name'], $option_name, $matches ) ) {
			return;
		}

		// コンタクトフォーム設定の初期化
		Form_Option::init_option( strval( $matches['form_id'] ) );
	}

  // ========================================================

}
