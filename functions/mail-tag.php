<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// メールタグ制御クラス：Mail_Tag
// ============================================================================

class Mail_Tag {

  // ========================================================
  // メールタグ制御
  // ========================================================

	/**
	 * シリアル番号のメールタグを取得する
	 *
	 * @param int|string $form_id コンタクトフォームID
	 * @return string
	 */
	public static function get_sn_mail_tag( $form_id )
	{
		return sprintf( '[%s%s]'
			, _MAIL_TAG_PREFIX , strval( $form_id )
		);
	}

  // ========================================================
  // Contact Form 7 プラグインフック設定
  // ========================================================

   // ------------------------------------
   // フィルターフック
   // ------------------------------------

	/**
	 * メールタグを出力値に変換する。
	 * 
	 * [Filter Hook] wpcf7_special_mail_tags
	 *
	 * @param string $output メールタグの出力値
	 * @param string $mail_tag メールタグ
	 * @return string メールタグの出力値を返す。
	 */
	public static function convert_mail_tags( $output, $mail_tag )
	{
		// ------------------------------------
		// メールタグ判別
		// ------------------------------------

		if ( 1 !== preg_match( _MAIL_TAG_REGEX, $mail_tag, $matches ) ) {
			return $output;
		}

		// ------------------------------------
		// コンタクトフォーム取得
		// ------------------------------------

		if ( !class_exists( 'WPCF7_Submission' ) ) { return $output; }

		// インスタンス取得
		$submission = \WPCF7_Submission::get_instance();
		if ( !$submission ) { return $output; }

		// コンタクトフォーム設定取得
		$contact_form = $submission->get_contact_form();
		$form_id = strval( $contact_form->id );
		if ( $form_id !== strval( $matches['form_id'] ) ) { return $output; }

		// コンタクトフォーム設定取得 (シリアル番号)
		$serial_num = strval( $submission->get_posted_data( _POST_FIELD ) );
		if ( empty( $serial_num ) ) { return $output; }

		// ------------------------------------
		// メールタグ設定
		// ------------------------------------

		$output = $serial_num;

		// ------------------------------------

		return $output;
	}

  // ========================================================

}
