<?php
if ( ! defined( 'ABSPATH' ) ) exit;


add_filter( 'wpcf7_posted_data', 'nt_wpcf7sn_posted_data', 10, 1 );


function nt_wpcf7sn_posted_data( $posted_data ) {

	if ( class_exists( 'WPCF7_Submission' ) ) {

		$submission = WPCF7_Submission::get_instance();
		
		$contact_form = $submission->get_contact_form();
		$form_id = intval( $contact_form->id );

		$count = intval( get_option( 'nt_wpcf7sn_count_' . $form_id ) );

		$posted_data['serial-number'] = NT_WPCF7SN_Serial_Number::get_serial_number( $form_id, $count + 1 );
	}

	return $posted_data;
}
