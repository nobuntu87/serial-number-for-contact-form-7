<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// コンタクトフォーム送信制御クラス：Serial_Number
// ============================================================================

class Submission {

  // ========================================================
  // Contact Form 7 プラグインフック設定
  // ========================================================

   // ------------------------------------
   // アクションフック
   // ------------------------------------

	/**
	 * メール送信の成功時の処理を行う。
	 * 
	 * [Action Hook] wpcf7_mail_sent
	 *
	 * @param mixed[] $contact_form コンタクトフォーム情報
	 * @return void
	 */
	public static function sent_mail_success( $contact_form )
	{
		$form_id = strval( $contact_form->id );

		// ------------------------------------
		// メールカウント条件判定
		// ------------------------------------

		// デモモード：カウント停止 [demo_mode: on]
		if ( $contact_form->in_demo_mode() ) { return; }

		// ------------------------------------

		// メールカウント増加
		Form_Option::increment_mail_count( $form_id );
	}

	/**
	 * メール送信の失敗時の処理を行う。
	 * 
	 * [Action Hook] wpcf7_mail_sent
	 *
	 * @param mixed[] $contact_form コンタクトフォーム情報
	 * @return void
	 */
	public static function sent_mail_failed( $contact_form )
	{
		$form_id = strval( $contact_form->id );

		// ------------------------------------
		// メールカウント条件判定
		// ------------------------------------

		// デモモード：カウント停止 [demo_mode: on]
		if ( $contact_form->in_demo_mode() ) { return; }

		// 送信失敗：カウント条件判定
		if ( 'yes' === $GLOBALS['_NT_WPCF7SN'][$form_id]['13'] ) { return; }

		// ------------------------------------

		// メールカウント増加
		Form_Option::increment_mail_count( $form_id );
	}

   // ------------------------------------
   // フィルターフック
   // ------------------------------------

	/**
	 * 送信メールのPOSTデータを編集する。
	 * 
	 * [Filter Hook] wpcf7_posted_data
	 *
	 * @param mixed[] $posted_data POSTデータ
	 * @return void POSTデータを返す。
	 */
	public static function edit_wpcf7_post_data( $posted_data )
	{
		// ------------------------------------
		// コンタクトフォーム取得
		// ------------------------------------

		// コンタクトフォーム設定取得
		$contact_form = Utility::get_wpcf7_submission_contact_form();
		if ( !$contact_form ) { return $posted_data; }

		$form_id = strval( $contact_form->id );

		// ------------------------------------
		// シリアル番号設定
		// ------------------------------------

		// デイリーリセット確認
		do_action( 'nt_wpcf7sn_check_reset_count' );

		// シリアル番号を新規フィールドに追加
		$posted_data[_POST_FIELD] = Serial_Number::get_serial_number(
			$form_id,
			intval( Form_Option::get_mail_count( $form_id ) ) + 1
		);

		// ------------------------------------

		return $posted_data;
	}

	/**
	 * 送信結果メッセージを編集する。
	 * 
	 * [Filter Hook] wpcf7_display_message
	 *
	 * @param string $message 表示メッセージ
	 * @param string $status 送信結果ステータス
	 * @return string 表示メッセージを返す。
	 */
	public static function edit_wpcf7_display_message( $message, $status )
	{
		// ------------------------------------
		// コンタクトフォーム取得
		// ------------------------------------

		// コンタクトフォーム設定取得
		$contact_form = Utility::get_wpcf7_submission_contact_form();
		if ( !$contact_form ) { return $message; }

		$form_id = strval( $contact_form->id );

		// ------------------------------------
		// メール送信 成功
		// ------------------------------------

		if ( 'mail_sent_ok' === strval( $status ) ) {

			// ------------------------------------
			// メッセージ追加判定
			// ------------------------------------

			// 送信結果メッセージ非表示
			if ( 'yes' === $GLOBALS['_NT_WPCF7SN'][$form_id]['14'] ) { return $message; }

			// ------------------------------------
			// シリアル番号取得
			// ------------------------------------

			// コンタクトフォーム設定取得 (シリアル番号)
			$serial_num = Utility::get_wpcf7_submission_posted_data( _POST_FIELD );
			if ( empty( $serial_num ) ) { return $message; }

			// ------------------------------------
			// 表示メッセージ設定
			// ------------------------------------

			$message .= sprintf( ''
				. ' ( %s%s%s )'
				, $GLOBALS['_NT_WPCF7SN'][$form_id]['15']
				, empty( $GLOBALS['_NT_WPCF7SN'][$form_id]['15'] ) ? '' : ' '
				, $serial_num
			);

		}

		// ------------------------------------

		return $message;
	}

  // ========================================================

}
