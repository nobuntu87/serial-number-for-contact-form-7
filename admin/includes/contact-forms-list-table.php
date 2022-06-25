<?php
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class NT_WPCF7SN_Contact_Forms_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct( array(
			'singular' => 'post',
			'plural'   => 'posts',
			'ajax'     => false,
		) );
	}

	public function get_columns() {
		$columns = array(
			'title'    => __( 'タイトル' ),
			'mail_tag' => __( 'メールタグ' ),
			'setting'  => __( '設定' ),
			'count'    => __( 'カウント' ),
		);
		return $columns;
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$data = $this->get_contact_form_items();
		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => count( $data ),
			'total_pages' => 0,
			'per_page'    => 0,
		) );
	}

	public function get_contact_form_items(){
		$data = get_posts( array(
			'post_type'   => 'wpcf7_contact_form',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'offset' => 0,
		) );
		return $data;
	}

	public function column_title( $item ) {

	}

	public function column_mail_tag( $item ) {

	}

	public function column_setting( $item ) {

	}

	public function column_count( $item ) {

	}
	
}
