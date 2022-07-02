<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * フィルターフック設定
 */
add_filter( 'wpcf7_special_mail_tags', 'nt_wpcf7sn_special_mail_tags', 10, 2 );


/**
 * メールタグを変換する。
 *
 * @param string $output メールタグの出力文字列
 * @param string $mail_tag メールタグのタグ名
 * @return string メールタグの出力文字列を返す。
 */
function nt_wpcf7sn_special_mail_tags( $output, $name ) {
	
	if ( preg_match( '/^' . NT_WPCF7SN_MAIL_TAG . '[0-9]+$/', $name ) ) {

		// メールタグからIDを取得
		preg_match('/(?P<id>[0-9]+)$/', $name, $match);
		$form_id = intval( $match['id'] );

		if ( class_exists( 'WPCF7_Submission' ) ) {

			$submission = WPCF7_Submission::get_instance();
			
			$contact_form = $submission->get_contact_form();

			// シリアル番号を取得
			if ( $form_id == intval( $contact_form->id ) ) {
				$output = $submission->get_posted_data( NT_WPCF7SN_POST_FIELD );
			}

		}

	}

	return $output;
}
