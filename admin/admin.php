<?php
if ( ! defined( 'ABSPATH' ) ) exit;


require_once NT_WPCF7SN_PLUGIN_DIR . '/admin/includes/contact-forms-list-table.php';


add_action( 'admin_menu', 'nt_wpcf7sn_admin_menu' );
add_action( 'admin_enqueue_scripts', 'nt_wpcf7sn_admin_enqueue_scripts' );


function nt_wpcf7sn_admin_menu() {
	add_options_page(
		__( 'Contact Form 7 Serial Number Addon', NT_WPCF7SN_TEXT_DOMAIN ),
		__( 'CF7 Serial Number', NT_WPCF7SN_TEXT_DOMAIN ),
		'manage_options',
		'nt-wpcf7sn',
		'nt_wpcf7sn_admin_management_page'
	);
}


function nt_wpcf7sn_admin_management_page() {
	$output = ''
	. '<div class="wrap">'
	. '  <h2> ' . __( 'Contact Form 7 Serial Number Addon', NT_WPCF7SN_TEXT_DOMAIN ) . '</h2>'
	. '</div>';
	
	echo trim( $output );

	$list_table = new NT_WPCF7SN_Contact_Forms_List_Table();
	$list_table->prepare_items();
	$list_table->display();
}


function nt_wpcf7sn_admin_enqueue_scripts( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'nt-wpcf7sn' ) ) {
		return;
	}

	wp_enqueue_style(
		NT_WPCF7SN_TEXT_DOMAIN . '-admin',
		NT_WPCF7SN_PLUGIN_URL . '/admin/css/style.css',
		array(),
		NT_WPCF7SN_VERSION, 'all'
	);
}
