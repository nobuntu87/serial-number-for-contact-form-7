<?php
if ( ! defined( 'ABSPATH' ) ) exit;


require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/includes/contact-forms-list-table.php';


add_action( 'admin_menu', 'nt_wpcf7sn_admin_menu' );


function nt_wpcf7sn_admin_menu() {
	add_options_page(
		__( 'Contact Form 7 Serial Number Addon' ),
		__( 'CF7 Serial Number' ),
		'manage_options',
		'wpcf7sn',
		'nt_wpcf7sn_admin_management_page'
	);
}


function nt_wpcf7sn_admin_management_page() {
	$output = ''
	. '<div class="wrap">'
	. '  <h2>Contact Form 7 Serial Number Addon</h2>'
	. '</div>';
	
	echo trim( $output );

	$list_table = new NT_WPCF7SN_Contact_Forms_List_Table();
	$list_table->prepare_items();
	$list_table->display();
}
