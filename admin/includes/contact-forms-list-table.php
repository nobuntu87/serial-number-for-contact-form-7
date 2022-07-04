<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ファイル読み込み
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class NT_WPCF7SN_Contact_Forms_List_Table extends WP_List_Table {

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		parent::__construct( array(
			'plural'   => NT_WPCF7SN_PREFIX['_'],
			'singular' => '',
			'ajax'     => false,
		) );
	}

	/**
	 * カラム情報を取得する。
	 * 
	 * テーブルに表示するカラム情報を定義する。
	 *
	 * @return string[] カラム情報を返す。
	 */
	public function get_columns() {
		$columns = array(
			'mail_tag' => __( 'Mail-Tag', NT_WPCF7SN_TEXT_DOMAIN ),
			'setting'  => __( 'Setting', NT_WPCF7SN_TEXT_DOMAIN ),
			'count'    => __( 'Count', NT_WPCF7SN_TEXT_DOMAIN ),
		);
		return $columns;
	}

	/**
	 * 表示するアイテムリストを作成する。
	 * 
	 * カラムヘッダーを設定する。
	 * 表示するアイテムリストを作成する。
	 * ページ割りを設定する。
	 *
	 * @return void
	 */
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

	/**
	 * 表示するアイテムデータを取得する。
	 * 
	 * POSTデータからコンタクトフォーム情報を取得する。
	 *
	 * @return mixed[] 取得したPOSTデータを返す。
	 */
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

	/**
	 * 「メールタグ」カラムの内容を表示する。
	 *
	 * @param mixed[] $item 表示するアイテム配列 (コンタクトフォーム情報)
	 * @return string 表示する出力文字列を返す。
	 */
	public function column_mail_tag( $item ) {
		$output = '';

		$form_id = intval( $item->ID );
		$form_title =  $item->post_title;

		$mail_tag = '[' . NT_WPCF7SN_MAIL_TAG . $form_id . ']';

		$output .= ''
		. '<div id="' . NT_WPCF7SN_PREFIX['_'] . '_mail_tag_' . $form_id . '" class="' . NT_WPCF7SN_PREFIX['_'] . '_mail_tag clearfix">'
		. '    <div class="item-box title">'
		. '      <h4 class="form-title">'
		. '        <span class="title">' . esc_attr( $form_title ) . '</span>'
		. '        <span class="id">[id:' . esc_attr( $form_id ) . ']</span>'
		. '      </h4>'
		. '    </div>'
		. '    <div class="item-box mail_tag">'
		. '      <div class="item text">'
		. '        <input type="text" readonly="readonly" onfocus="this.select();" value="' . esc_attr( $mail_tag ) . '"/>'
		. '      </div>'
		. '    </div>'
		. '</div>';

		return trim( $output );
	}

	/**
	 * 「設定」カラムの内容を表示する。
	 *
	 * @param mixed[] $item 表示するアイテム配列 (コンタクトフォーム情報)
	 * @return string 表示する出力文字列を返す。
	 */
	public function column_setting( $item ) {
		$output = '';

		$form_id = intval( $item->ID );

		$option = NT_WPCF7SN_Option::get_form_options( $form_id );

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_key = [];
		foreach( $option as $key => $value ) {
			$option_key[$key] = $option_name . '[' . $key . ']';
		}

		$page_options = '';
		foreach( $option as $key => $value ) {
			$page_options .= '<input type="hidden" name="' . $option_key[$key] . '" value="' . $option[$key] . '" />';
		}

		$serial_num = NT_WPCF7SN_Serial_Number::get_serial_number( $form_id, $option['count'] + 1 );

		$output .= ''
		. '<div id="' . NT_WPCF7SN_PREFIX['_'] . '_setting_' . $form_id . '" class="' . NT_WPCF7SN_PREFIX['_'] . '_setting clearfix">'
		. '  <form method="post" action="options.php">' . wp_nonce_field( 'update-options' )
		. '    <input type="hidden" name="action" value="update" />'
		. '    <input type="hidden" name="page_options" value="' . esc_attr( $option_name ) . '" />' . $page_options
		. '    <div class="item-box type">'
		. '      <h4 class="item-title">' . __( 'Type', NT_WPCF7SN_TEXT_DOMAIN ) . '</h4>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . $option_key['type'] . '"'
		. '               value="0" ' . ( $option['type'] == 0 ? 'checked' : '' ) . ' />'
		.          __( 'Serial Number', NT_WPCF7SN_TEXT_DOMAIN )
		. '      </label></div>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . $option_key['type'] . '"'
		. '               value="1" ' . ( $option['type'] == 1 ? 'checked' : '' ) . ' />'
		.          __( 'Timestamp (UnixTime)', NT_WPCF7SN_TEXT_DOMAIN )
		. '      </label></div>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . $option_key['type'] . '"'
		. '               value="2" ' . ( $option['type'] == 2 ? 'checked' : '' ) . ' />'
		.          __( 'Timestamp (Date)', NT_WPCF7SN_TEXT_DOMAIN )
		. '      </label></div>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . $option_key['type'] . '"'
		. '               value="3" ' . ( $option['type'] == 3 ? 'checked' : '' ) . ' />'
		.          __( 'Timestamp (Date + Time)', NT_WPCF7SN_TEXT_DOMAIN )
		. '      </label></div>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . $option_key['type'] . '"'
		. '               value="4" ' . ( $option['type'] == 4 ? 'checked' : '' ) . ' />'
		.          __( 'Unique ID', NT_WPCF7SN_TEXT_DOMAIN )
		. '      </label></div>'
		. '    </div>'
		. '    <div class="item-box option">'
		. '      <h4 class="item-title">' . __( 'Options', NT_WPCF7SN_TEXT_DOMAIN ) . '</h4>'
		. '      <div class="item text">' . __( 'Prefix', NT_WPCF7SN_TEXT_DOMAIN )
		. '        <input type="text" name="' . $option_key['prefix'] . '"'
		. '               value="' . $option['prefix'] . '" size="15" maxlength="10" />'
		. '        (' . __( 'Within 10 characters', NT_WPCF7SN_TEXT_DOMAIN ) . ')'
		. '      </div>'
		. '      <div class="item text">' . __( 'Digits', NT_WPCF7SN_TEXT_DOMAIN )
		. '        <input type="text" name="' . $option_key['digits'] . '"'
		. '                value="' . $option['digits'] . '" size="1" maxlength="1" pattern="[1-9]"/>'
		. '        (' . __( '1~9 digits', NT_WPCF7SN_TEXT_DOMAIN ) . ')'
		. '      </div>'
		. '      <div class="item check"><label>'
		. '        <input type="hidden"   name="' . $option_key['separator'] . '" />'
		. '        <input type="checkbox" name="' . $option_key['separator'] . '"'
		. '               value="yes" '. ( $option['separator'] == 'yes' ? 'checked' : '' ) . ' />'
		.          __( 'Display the delimiter "-".', NT_WPCF7SN_TEXT_DOMAIN )
		. '      </label></div>'
		. '      <div class="item check"><label>'
		. '        <input type="hidden"   name="' . $option_key['year2dig'] . '" />'
		. '        <input type="checkbox" name="' . $option_key['year2dig'] . '"'
		. '               value="yes" '. ( $option['year2dig'] == 'yes' ? 'checked' : '' ) . ' />'
		.          __( 'Omit the number of years to 2 digits.', NT_WPCF7SN_TEXT_DOMAIN )
		. '      </label></div>'
		. '      <div class="item check"><label>'
		. '        <input type="hidden"   name="' . $option_key['nocount'] . '" />'
		. '        <input type="checkbox" name="' . $option_key['nocount'] . '"'
		. '               value="yes" '. ( $option['nocount'] == 'yes' ? 'checked' : '' ) . ' />'
		.          __( 'Don\'t display count with unique ID.', NT_WPCF7SN_TEXT_DOMAIN )
		. '      </label></div>'
		. '    </div>'
		. '    <div class="item-box update">'
		. '      <div class="item example"><span>' . __( 'Example', NT_WPCF7SN_TEXT_DOMAIN ) . ' [' . $serial_num . ']</span></div>'
		. '      <div class="item submit_button">'
		. '        <input type="submit" class="button-primary" value="' . __( 'Settings', NT_WPCF7SN_TEXT_DOMAIN ) .'" />'
		. '      </div>'
		. '    </div>'
		. '  </form>'
		. '</div>';

		return trim( $output );
	}

	/**
	 * 「カウント」カラムの内容を表示する。
	 *
	 * @param mixed[] $item 表示するアイテム配列 (コンタクトフォーム情報)
	 * @return string 表示する出力文字列を返す。
	 */
	public function column_count( $item ) {
		$output = '';

		$form_id = intval( $item->ID );

		$option = NT_WPCF7SN_Option::get_form_options( $form_id );

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_key = [];
		foreach( $option as $key => $value ) {
			$option_key[$key] = $option_name . '[' . $key . ']';
		}

		$page_options = '';
		foreach( $option as $key => $value ) {
			$page_options .= '<input type="hidden" name="' . $option_key[$key] . '" value="' . $option[$key] . '" />';
		}

		$output .= ''
		. '<div id="' . NT_WPCF7SN_PREFIX['_'] . '_count_' . $form_id . '" class="' . NT_WPCF7SN_PREFIX['_'] . '_count clearfix">'
		. '  <form method="post" action="options.php">' . wp_nonce_field( 'update-options' )
		. '    <input type="hidden" name="action" value="update" />'
		. '    <input type="hidden" name="page_options" value="' . esc_attr( $option_name ) . '" />' . $page_options
		. '    <div class="item-box count">'
		. '      <h4 class="item-title">' . __( 'Current Count', NT_WPCF7SN_TEXT_DOMAIN ) . '</h4>'
		. '      <div class="item text">'
		. '        <input type="text" name="' . $option_key['count'] . '"'
		. '               value="' . $option['count'] . '" size="5" maxlength="5" pattern="[0-9]+"/>'
		. '      </div>'
		. '      <div class="item submit_button">'
		. '        <input type="submit" class="button-primary" value="' . __( 'Change', NT_WPCF7SN_TEXT_DOMAIN ) .'" />'
		. '      </div>'
		. '    </div>'
		. '  </form>'
		. '</div>';

		return trim( $output );
	}
	
}
