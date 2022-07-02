<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * アクションフック設定
 */
add_action( 'wpcf7_submit', 'nt_wpcf7sn_submit', 10, 1 );

/**
 * フィルターフック設定
 */
add_filter( 'wpcf7_posted_data', 'nt_wpcf7sn_posted_data', 10, 1 );


/**
 * メール送信後の処理を行う。
 *
 * @param mixed[] $contact_form コンタクトフォーム情報
 * @return void
 */
function nt_wpcf7sn_submit( $contact_form ) {
	$form_id = intval( $contact_form->id );
	nt_wpcf7sn_increment_count( $form_id );
}


/**
 * POSTデータを作成する。
 * 
 * POSTデータにシリアル番号を追加する。
 *
 * @param mixed[] $posted_data POSTデータ
 * @return mixed[] POSTデータを返す。
 */
function nt_wpcf7sn_posted_data( $posted_data ) {
	if ( class_exists( 'WPCF7_Submission' ) ) {

		$submission = WPCF7_Submission::get_instance();
		
		$contact_form = $submission->get_contact_form();
		$form_id = intval( $contact_form->id );

		$count = NT_WPCF7SN::get_form_option( 'count', $form_id );
		$serial_num = NT_WPCF7SN_Serial_Number::get_serial_number( $form_id, $count + 1 );

		$posted_data[NT_WPCF7SN_POST_FIELD] = $serial_num;
	}

	return $posted_data;
}


/**
 * カウント値を増加する。
 *
 * @param int $form_id コンタクトフォームID
 * @return void
 */
function nt_wpcf7sn_increment_count( $form_id ) {
	$count = NT_WPCF7SN::get_form_option( 'count', $form_id );
	NT_WPCF7SN::update_form_option( 'count', $form_id, $count + 1 );
}
