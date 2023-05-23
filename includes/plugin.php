<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

class NT_WPCF7SN {

	/**
	 * Contact Form 7 プラグインが有効化されているか確認する。
	 *
	 * @return boolean 有効化状態を返す。(true:有効/false:無効)
	 */
	public static function is_active_wpcf7()
	{
		return Utility::is_active_plugin(
			_EXTERNAL_PLUGIN['wpcf7']['basename']
		);
	}

	/**
	* プラグインのオプション値を取得する。
	*
	* @param string $key オプションキー
	* @param mixed $default デフォルト値
	* @return mixed オプション値を返す。
	*/
	public static function get_option( $key, $default )
	{
		// オプション値を取得
		$option_value = SELF::get_plugin_option();

		// 存在しない場合はデフォルト値で初期化 (NG：未定義/NULL)
		if ( !array_key_exists( $key, $option_value ) || !isset( $option_value[$key] ) ) {
			SELF::update_option( $key, $default );
			return $default;
		}

		return $option_value[$key];
	}

	/**
	* プラグインのオプション値を更新する。
	*
	* @param string $key オプションキー
	* @param mixed $value オプション値
	* @return void
	*/
	public static function update_option( $key, $value )
	{
		// オプション値を取得
		$option_value = SELF::get_plugin_option();

		// オプション値をマージ
		$option_value = array_merge( $option_value, array( $key => $value ) );

		// オプション値を更新
		SELF::update_plugin_option( $option_value );
	}

	/**
	* プラグインのオプション値を取得する。
	*
	* @return mixed[] オプション値を返す。
	*/
	public static function get_plugin_option()
	{
		$option_name = sprintf( "%s_conf" , _PREFIX['_'] );

		// WordPressデータベース取得
		$option_value = get_option( $option_name );
		if ( false === $option_value ) { return []; }

		// ------------------------------------
		// デコード処理
		// ------------------------------------

		// JSON形式の文字列をデコード
		$option_value = @json_decode( $option_value, true );

		// アンエスケープ・デコード
		if ( is_array( $option_value ) ) {
			$option_value = array_map(
				array( __NAMESPACE__ . '\Utility', 'unesc_decode' ),
				$option_value
			);
		}

		// ------------------------------------

		return $option_value;
	}

	/**
	* プラグインのオプション値を更新する。
	*
	* @param mixed[] $option_value オプション値
	* @return void
	*/
	public static function update_plugin_option( $option_value )
	{
		$option_name = sprintf( "%s_conf" , _PREFIX['_'] );

		// ------------------------------------
		// エンコード処理
		// ------------------------------------

		// エスケープ・エンコード
		if ( is_array( $option_value ) ) {
			$option_value = array_map(
				array( __NAMESPACE__ . '\Utility', 'esc_encode' ),
				$option_value
			);
		}

		// JSON形式の文字列にエンコード
		$option_value = @json_encode( $option_value );

		// ------------------------------------

		// WordPressデータベース更新
		update_option( $option_name, $option_value );
	}

}
