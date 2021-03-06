<?php
if ( !defined( 'ABSPATH' ) ) exit;

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
	// POSTデータからコンタクトフォーム情報を取得
	$wpcf7_posts = nt_wpcf7sn_get_posts_wpcf7();

	// 全てのコンタクトフォームのオプションを設定
	foreach( $wpcf7_posts as $wpcf7_post ) {
		$form_id = intval( $wpcf7_post->ID );
	
		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;
	
		// 設定項目とサニタイズ用コールバックを登録
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
 * エラー時は元設定に戻すか補正を行う。
 *
 * @param mixed[] $options 入力されたオプション値
 * @return mixed[] サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_options( $options ) {
	$form_id = intval( $options['id'] );

	foreach( $options as $key => $value ) {
		switch ( $key ) {
			case 'type' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_type( $form_id, $value );
				break;
			case 'count' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_count( $form_id, $value );
				break;
			case 'daycount' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_daycount( $form_id, $value );
				break;
			case 'digits' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_digits( $form_id, $value );
				break;
			case 'prefix' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_prefix( $form_id, $value );
				break;
			case 'separator' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_checkbox( $form_id, $value, $key );
				break;
			case 'year2dig' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_checkbox( $form_id, $value, $key );
				break;
			case 'nocount' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_checkbox( $form_id, $value, $key );
				break;
			case 'dayreset' :
				$options[$key] = nt_wpcf7sn_sanitize_form_option_checkbox( $form_id, $value, $key );
				break;
			default :
				// 未定義の設定項目を削除
				unset( $options[$key] );
				break;
		}
	}
	
	return $options;
}


/**
 * コンタクトフォームのオプションのサニタイズ処理を行う。
 *
 * @param int $form_id コンタクトフォームID
 * @param mixed $value 入力されたオプション値
 * @return mixed サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_option_type( $form_id, $value ) {
	$form_id = intval( $form_id );
	$name = 'type';
	
	// 型変換・エスケープ処理
	$value = NT_WPCF7SN_Form_Options::cast_type_option( $name, esc_attr( $value ) );

	// フォーマットチェック
	if ( 1 !== preg_match( '/' . NT_WPCF7SN_FORM_OPTION[$name]['pattern'] . '/', $value ) ) {
		// 規定外の入力は元値に補正
		return NT_WPCF7SN_Form_Options::get_option( $form_id, $name );
	}

	return $value;
}


/**
 * コンタクトフォームのオプションのサニタイズ処理を行う。
 *
 * @param int $form_id コンタクトフォームID
 * @param mixed $value 入力されたオプション値
 * @return mixed サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_option_count( $form_id, $value ) {
	$form_id = intval( $form_id );
	$name = 'count';

	// 型変換・エスケープ処理
	$value = NT_WPCF7SN_Form_Options::cast_type_option( $name, esc_attr( $value ) );

	// フォーマットチェック
	if ( 1 !== preg_match( '/' . NT_WPCF7SN_FORM_OPTION[$name]['pattern'] . '/', $value ) ) {
		// 規定外の入力はエラー表示
		$message = sprintf( '[id:%s] '.
			__( 'Current Count', NT_WPCF7SN_TEXT_DOMAIN ) . ' : ' .
			__( 'Input value is invalid.', NT_WPCF7SN_TEXT_DOMAIN ) . ' ' .
			__( 'Input range (%s)', NT_WPCF7SN_TEXT_DOMAIN ) . ' : ' .
			__( 'Input [%s]', NT_WPCF7SN_TEXT_DOMAIN ),
			esc_html( $form_id ),
			__( 'Up to 5 digits integer. 0~99999', NT_WPCF7SN_TEXT_DOMAIN ),
			esc_html( $value )
		);

		add_settings_error(
			NT_WPCF7SN_FORM_OPTION_NAME . $form_id,
			NT_WPCF7SN_FORM_OPTION_NAME . $form_id,
			esc_html( $message ),
			'error'
		);

		// 規定外の入力は元値に補正
		return NT_WPCF7SN_Form_Options::get_option( $form_id, $name );
	}

	return $value;
}


/**
 * コンタクトフォームのオプションのサニタイズ処理を行う。
 *
 * @param int $form_id コンタクトフォームID
 * @param mixed $value 入力されたオプション値
 * @return mixed サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_option_daycount( $form_id, $value ) {
	$form_id = intval( $form_id );
	$name = 'daycount';

	// 型変換・エスケープ処理
	$value = NT_WPCF7SN_Form_Options::cast_type_option( $name, esc_attr( $value ) );

	// フォーマットチェック
	if ( 1 !== preg_match( '/' . NT_WPCF7SN_FORM_OPTION[$name]['pattern'] . '/', $value ) ) {
		// 規定外の入力はエラー表示
		$message = sprintf( '[id:%s] '.
			__( 'Daily Count', NT_WPCF7SN_TEXT_DOMAIN ) . ' : ' .
			__( 'Input value is invalid.', NT_WPCF7SN_TEXT_DOMAIN ) . ' ' .
			__( 'Input range (%s)', NT_WPCF7SN_TEXT_DOMAIN ) . ' : ' .
			__( 'Input [%s]', NT_WPCF7SN_TEXT_DOMAIN ),
			esc_html( $form_id ),
			__( 'Up to 5 digits integer. 0~99999', NT_WPCF7SN_TEXT_DOMAIN ),
			esc_html( $value )
		);

		add_settings_error(
			NT_WPCF7SN_FORM_OPTION_NAME . $form_id,
			NT_WPCF7SN_FORM_OPTION_NAME . $form_id,
			esc_html( $message ),
			'error'
		);

		// 規定外の入力は元値に補正
		return NT_WPCF7SN_Form_Options::get_option( $form_id, $name );
	}

	return $value;
}


/**
 * コンタクトフォームのオプションのサニタイズ処理を行う。
 *
 * @param int $form_id コンタクトフォームID
 * @param mixed $value 入力されたオプション値
 * @return mixed サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_option_digits( $form_id, $value ) {
	$form_id = intval( $form_id );
	$name = 'digits';

	// 型変換・エスケープ処理
	$value = NT_WPCF7SN_Form_Options::cast_type_option( $name, esc_attr( $value ) );

	// フォーマットチェック
	if ( 1 !== preg_match( '/' . NT_WPCF7SN_FORM_OPTION[$name]['pattern'] . '/', $value ) ) {
		// 規定外の入力はエラー表示
		$message = sprintf( '[id:%s] '.
			__( 'Digits', NT_WPCF7SN_TEXT_DOMAIN ) . ' : ' .
			__( 'Input value is invalid.', NT_WPCF7SN_TEXT_DOMAIN ) . ' ' .
			__( 'Input range (%s)', NT_WPCF7SN_TEXT_DOMAIN ) . ' : ' .
			__( 'Input [%s]', NT_WPCF7SN_TEXT_DOMAIN ),
			esc_html( $form_id ),
			__( '1 digit integer. 1~9', NT_WPCF7SN_TEXT_DOMAIN ),
			esc_html( $value )
		);

		add_settings_error(
			NT_WPCF7SN_FORM_OPTION_NAME . $form_id,
			NT_WPCF7SN_FORM_OPTION_NAME . $form_id,
			esc_html( $message ),
			'error'
		);

		// 規定外の入力は元値に補正
		return NT_WPCF7SN_Form_Options::get_option( $form_id, $name );
	}

	return $value;
}


/**
 * コンタクトフォームのオプションのサニタイズ処理を行う。
 *
 * @param int $form_id コンタクトフォームID
 * @param mixed $value 入力されたオプション値
 * @return mixed サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_option_prefix( $form_id, $value ) {
	$form_id = intval( $form_id );
	$name = 'prefix';

	// 型変換・エスケープ処理
	$value = NT_WPCF7SN_Form_Options::cast_type_option( $name, esc_attr( $value ) );

	// フォーマットチェック
	if ( 1 !== preg_match( '/' . NT_WPCF7SN_FORM_OPTION[$name]['pattern'] . '/u', $value ) ) {
		// 規定外の入力はエラー表示
		$message = sprintf( '[id:%s] '.
			__( 'Prefix', NT_WPCF7SN_TEXT_DOMAIN ) . ' : ' .
			__( 'Input value is invalid.', NT_WPCF7SN_TEXT_DOMAIN ) . ' ' .
			__( 'Input range (%s)', NT_WPCF7SN_TEXT_DOMAIN ) . ' : ' .
			__( 'Input [%s]', NT_WPCF7SN_TEXT_DOMAIN ),
			esc_html( $form_id ),
			__( 'Within 10 characters. Unusable \\"&\'<>', NT_WPCF7SN_TEXT_DOMAIN ),
			esc_html( $value )
		);

		add_settings_error(
			NT_WPCF7SN_FORM_OPTION_NAME . $form_id,
			NT_WPCF7SN_FORM_OPTION_NAME . $form_id,
			esc_html( $message ),
			'error'
		);

		// 規定外の入力は元値に補正
		return NT_WPCF7SN_Form_Options::get_option( $form_id, $name );
	}

	return $value;
}


/**
 * コンタクトフォームのオプションのサニタイズ処理を行う。
 *
 * @param int $form_id コンタクトフォームID
 * @param mixed $value 入力されたオプション値
 * @param string $name オプション名
 * @return mixed サニタイズ処理したオプション値を返す。
 */
function nt_wpcf7sn_sanitize_form_option_checkbox( $form_id, $value, $name ) {
	$form_id = intval( $form_id );
	
	// 型変換・エスケープ処理
	$value = NT_WPCF7SN_Form_Options::cast_type_option( $name, esc_attr( $value ) );

	// フォーマットチェック
	if ( 1 !== preg_match( '/' . NT_WPCF7SN_FORM_OPTION[$name]['pattern'] . '/', $value ) ) {
		// 規定外の入力は元値に補正
		return NT_WPCF7SN_Form_Options::get_option( $form_id, $name );
	}

	return $value;
}
