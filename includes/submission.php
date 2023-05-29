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
		// メールカウント増加
		Form_Option::increment_mail_count( strval( $contact_form->id ) );
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

		if ( !class_exists( 'WPCF7_Submission' ) ) { return $posted_data; }

		// インスタンス取得
		$submission = \WPCF7_Submission::get_instance();
		if ( !$submission ) { return $posted_data; }

		// コンタクトフォーム設定取得
		$contact_form = $submission->get_contact_form();
		$form_id = strval( $contact_form->id );

		// ------------------------------------
		// シリアル番号設定
		// ------------------------------------

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
	 * @param string $message 表示メッセージ
	 * @param string $status 送信結果ステータス
	 * @return string 表示メッセージを返す。
	 */
	public static function edit_wpcf7_display_message( $message, $status )
	{
		// ------------------------------------
		// メール送信 成功
		// ------------------------------------

		if ( 'mail_sent_ok' === strval( $status ) ) {

			// ------------------------------------
			// コンタクトフォーム取得
			// ------------------------------------

			if ( !class_exists( 'WPCF7_Submission' ) ) { return $message; }

			// インスタンス取得
			$submission = \WPCF7_Submission::get_instance();
			if ( !$submission ) { return $message; }

			// コンタクトフォーム設定取得 (シリアル番号)
			$serial_num = $submission->get_posted_data( _POST_FIELD );
			if ( empty( $serial_num ) ) { return $message; }

			// ------------------------------------
			// 表示メッセージ設定
			// ------------------------------------

			$message .= sprintf( '' 
				. '( ' . __( 'Receipt No', _TEXT_DOMAIN ) . ' : %s )'
				, strval( $serial_num )
			);

		}

		// ------------------------------------

		return $message;
	}

  // ========================================================

}
