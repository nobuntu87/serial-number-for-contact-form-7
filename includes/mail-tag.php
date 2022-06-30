<?php
if ( ! defined( 'ABSPATH' ) ) exit;


add_filter( 'wpcf7_special_mail_tags', 'nt_wpcf7sn_special_mail_tags', 10, 2 );


function nt_wpcf7sn_special_mail_tags( $output, $name ) {
	
	if ( preg_match( '/^_serial_number_[0-9]+$/', $name ) ) {

		// メールタグからIDを取得
		preg_match('/(?P<id>[0-9]+)$/', $name, $match);
		$form_id = intval( $match['id'] );

		if ( class_exists( 'WPCF7_Submission' ) ) {

			$submission = WPCF7_Submission::get_instance();
			
			$contact_form = $submission->get_contact_form();

			// シリアル番号を取得
			if ( $form_id == intval( $contact_form->id ) ) {
				$output = $submission->get_posted_data( 'serial-number' );
			}

		}

	}

	return $output;
}