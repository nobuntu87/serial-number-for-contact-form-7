<?php
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class NT_WPCF7SN_Contact_Forms_List_Table extends WP_List_Table {

	public function __construct() {
		parent::__construct( array(
			'plural'   => 'nt-wpcf7sn',
			'singular' => '',
			'ajax'     => false,
		) );
	}

	public function get_columns() {
		$columns = array(
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

	public function column_mail_tag( $item ) {
		$output = '';

		$form_id = intval( $item->ID );

		$form_title =  $item->post_title;
		$mail_tag = sprintf( '[_serial_number_%1$d]', $form_id );

		$output .= ''
		. '<div id="nt_wpcf7sn_mail_tag_' . $form_id . '" class="nt_wpcf7sn_mail_tag clearfix">'
		. '    <div class="item-box title">'
		. '      <h4 class="form-title">'
		. '        <span class="title">' . esc_attr( $form_title ) . '</span>'
		. '        <span class="id">[ID' . esc_attr( $form_id ) . ']</span>'
		. '      </h4>'
		. '    </div>'
		. '    <div class="item-box mail_tag">'
		. '      <div class="item text"><input type="text" readonly="readonly" onfocus="this.select();" value="' . esc_attr( $mail_tag ) . '"/></div>'
		. '    </div>'
		. '</div>';

		return trim( $output );
	}

	public function column_setting( $item ) {
		$output = '';

		$form_id = intval( $item->ID );

		$type = intval( get_option( 'nt_wpcf7sn_type_' . $form_id, 0 ) );
		$digits = intval( get_option( 'nt_wpcf7sn_digits_' . $form_id, 0 ) );
		$prefix = get_option( 'nt_wpcf7sn_prefix_' . $form_id, '' );
		$separator = get_option( 'nt_wpcf7sn_separator_' . $form_id, '' );
		$year2dig = get_option( 'nt_wpcf7sn_year2dig_' . $form_id, '' );
		$nocount = get_option( 'nt_wpcf7sn_nocount_' . $form_id, '' );
		$count = intval( get_option( 'nt_wpcf7sn_count_' . $form_id, 1 ) );

		$option_name = implode( ',', array(
			'nt_wpcf7sn_type_' . $form_id,
			'nt_wpcf7sn_digits_' . $form_id,
			'nt_wpcf7sn_prefix_' . $form_id,
			'nt_wpcf7sn_separator_' . $form_id,
			'nt_wpcf7sn_year2dig_' . $form_id,
			'nt_wpcf7sn_nocount_' . $form_id
		) );

		$output .= ''
		. '<div id="nt_wpcf7sn_setting_' . $form_id . '" class="nt_wpcf7sn_setting clearfix">'
		. '  <form method="post" action="options.php">' . wp_nonce_field( 'update-options' )
		. '    <input type="hidden" name="action" value="update" />'
		. '    <input type="hidden" name="page_options" value="' . esc_attr( $option_name ) . '" />'
		. '    <div class="item-box type">'
		. '      <h4 class="item-title">表示パターン</h4>'
		. '      <div class="item radio"><label><input type="radio" name="nt_wpcf7sn_type_' . $form_id . '" value="0" ' . ( $type == 0 ? 'checked' : '' ) . ' />通し番号</label></div>'
		. '      <div class="item radio"><label><input type="radio" name="nt_wpcf7sn_type_' . $form_id . '" value="1" ' . ( $type == 1 ? 'checked' : '' ) . ' />タイムスタンプ (UNIX時間)</label></div>'
		. '      <div class="item radio"><label><input type="radio" name="nt_wpcf7sn_type_' . $form_id . '" value="2" ' . ( $type == 2 ? 'checked' : '' ) . ' />タイムスタンプ (年月日)</label></div>'
		. '      <div class="item radio"><label><input type="radio" name="nt_wpcf7sn_type_' . $form_id . '" value="3" ' . ( $type == 3 ? 'checked' : '' ) . ' />タイムスタンプ (年月日+時分秒)</label></div>'
		. '      <div class="item radio"><label><input type="radio" name="nt_wpcf7sn_type_' . $form_id . '" value="4" ' . ( $type == 4 ? 'checked' : '' ) . ' />ユニークID (英数字)</label></div>'
		. '    </div>'
		. '    <div class="item-box option">'
		. '      <h4 class="item-title">表示オプション</h4>'
		. '      <div class="item text">接頭語<input type="text" name="nt_wpcf7sn_prefix_' . $form_id . '" value="' . $prefix . '" size="15" maxlength="10" />(10文字以内)</div>'
		. '      <div class="item text">桁数<input type="text" name="nt_wpcf7sn_digits_' . $form_id . '" value="' . $digits . '" size="1" maxlength="1" pattern="[0-9]"/>(0~9桁)</div>'
		. '      <div class="item check"><label><input type="checkbox" name="nt_wpcf7sn_separator_' . $form_id . '" value="yes" '. ( $separator == 'yes' ? 'checked' : '' ) . ' />区切り文字「-」を表示する。</label></div>'
		. '      <div class="item check"><label><input type="checkbox" name="nt_wpcf7sn_year2dig_' . $form_id . '" value="yes" '. ( $year2dig == 'yes' ? 'checked' : '' ) . ' />西暦の年数を2桁に省略する。</label></div>'
		. '      <div class="item check"><label><input type="checkbox" name="nt_wpcf7sn_nocount_' . $form_id . '" value="yes" '. ( $nocount == 'yes' ? 'checked' : '' ) . ' />ユニークIDで連番を表示しない。</label></div>'
		. '    </div>'
		. '    <div class="item-box update">'
		. '      <div class="item example"><span>表示例 [' . NT_WPCF7SN_Serial_Number::get_serial_number( $form_id, $count+1 ) . ']</span></div>'
		. '      <div class="item submit_button"><input type="submit" class="button-primary" value="設定" /></div>'
		. '    </div>'
		. '  </form>'
		. '</div>';

		return trim( $output );
	}

	public function column_count( $item ) {
		$output = '';

		$form_id = intval( $item->ID );

		$count = intval( get_option( 'nt_wpcf7sn_count_' . $form_id, 1 ) );

		$option_name = implode( ',', array(
			'nt_wpcf7sn_count_' . $form_id
		) );

		$output .= ''
		. '<div id="nt_wpcf7sn_count_' . $form_id . '" class="nt_wpcf7sn_count clearfix">'
		. '  <form method="post" action="options.php">' . wp_nonce_field( 'update-options' )
		. '    <input type="hidden" name="action" value="update" />'
		. '    <input type="hidden" name="page_options" value="' . esc_attr( $option_name ) . '" />'
		. '    <div class="item-box count">'
		. '      <h4 class="item-title">現在のカウント</h4>'
		. '      <div class="item text"><input type="text" name="nt_wpcf7sn_count_' . $form_id . '" id="nt_wpcf7sn_count_' . $form_id . '" value="' . $count . '" size="5" maxlength="5" pattern="[0-9]+"/></div>'
		. '      <div class="item submit_button"><input type="submit" class="button-primary" value="変更" /></div>'
		. '    </div>'
		. '  </form>'
		. '</div>';

		return trim( $output );
	}
	
}
