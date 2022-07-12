<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * フィルターフック設定
 */
add_filter( 'wpcf7_special_mail_tags', 'nt_wpcf7sn_special_mail_tags', 10, 2 );


/**
 * メールタグを変換する。
 *
 * @param string $output メールタグの変換結果
 * @param string $mail_tag メールタグ名
 * @return string メールタグの変換結果を返す。
 */
function nt_wpcf7sn_special_mail_tags( $output, $mail_tag ) {
	if ( 1 != preg_match( '/^' . NT_WPCF7SN_MAIL_TAG . '[0-9]+$/', $mail_tag ) ) {
		return $output;
	}

	// メールタグからコンタクトフォームIDを抽出
	preg_match( '/(?P<id>[0-9]+)$/', $mail_tag, $match );
	$form_id = intval( $match['id'] );

	if ( class_exists( 'WPCF7_Submission' ) ) {
		$submission = WPCF7_Submission::get_instance();
		if ( ! $submission ) {
			return $output;
		}

		$contact_form = $submission->get_contact_form();

		// POSTデータからシリアル番号を取得
		if ( $form_id == intval( $contact_form->id ) ) {
			$output = $submission->get_posted_data( NT_WPCF7SN_POST_FIELD );
		}
	}

	return $output;
}
