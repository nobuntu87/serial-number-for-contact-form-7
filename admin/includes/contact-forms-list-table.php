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
		// カラムヘッダー設定
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// POSTデータからコンタクトフォーム情報を取得
		$data = nt_wpcf7sn_get_posts_wpcf7();

		// ページネーション設定
		$per_page = $this->get_items_per_page( NT_WPCF7SN_FORM_OPTION_SCREEN['per_page']['option'] );
		$current_page = $this->get_pagenum();
		$total_items = count( $data );

		// 表示データ設定
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items = $data;

		$this->set_pagination_args( array(
			'per_page'    => $per_page,
			'total_items' => $total_items,
			'total_pages' => ceil( $total_items / $per_page ),
		) );
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
		. '<div id="' . NT_WPCF7SN_PREFIX['_'] . '_mail_tag_' . esc_attr( $form_id ) . '"'
		. '     class="' . NT_WPCF7SN_PREFIX['_'] . '_mail_tag clearfix">'
		. '    <div class="item-box title">'
		. '      <h4 class="form-title">'
		. '        <span class="title">' . esc_html( $form_title ) . '</span>'
		. '        <span class="id">[id:' . esc_html( $form_id ) . ']</span>'
		. '      </h4>'
		. '    </div>'
		. '    <div class="item-box mail_tag">'
		. '      <div class="item text">'
		. '        <input type="text" readonly="readonly" onfocus="this.select();"'
		. '               value="' . esc_attr( $mail_tag ) . '"/>'
		. '      </div>'
		. '    </div>'
		. '</div>';

		return wp_kses( trim( $output ), NT_WPCF7SN_ALLOWED_HTML );
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

		$option = NT_WPCF7SN_Form_Options::get_options( $form_id );

		// サニタイズ処理時にコンタクトフォームIDの識別用に一時保持(設定時に削除される)
		$option['id'] = $form_id;

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_key = [];
		foreach( $option as $key => $value ) {
			$option_key[$key] = $option_name . '[' . $key . ']';
		}

		$page_options = '';
		foreach( $option as $key => $value ) {
			$page_options .= ''
			. '<input type="hidden" name="' . esc_attr( $option_key[$key] ) . '"'
			. '       value="' . esc_attr( $option[$key] ) . '"/>';
		}

		$serial_num = NT_WPCF7SN_Serial_Number::get_serial_number( $form_id, $option['count'] + 1 );

		$output .= ''
		. '<div id="' . NT_WPCF7SN_PREFIX['_'] . '_setting_' . esc_attr( $form_id ) . '"'
		. '     class="' . NT_WPCF7SN_PREFIX['_'] . '_setting clearfix">'
		. '  <form method="post" action="options.php">' . wp_nonce_field( 'update-options' )
		. '    <input type="hidden" name="action" value="update"/>'
		. '    <input type="hidden" name="page_options" value="' . esc_attr( $option_name ) . '"/>' . $page_options
		. '    <div class="item-box type">'
		. '      <h4 class="item-title">' . esc_html( __( 'Type', NT_WPCF7SN_TEXT_DOMAIN ) ) . '</h4>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . esc_attr( $option_key['type'] ) . '"'
		. '               value="0" ' . ( 0 == intval( $option['type'] ) ? 'checked' : '' ) . ' />'
		.         esc_html(  __( 'Serial Number', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '      </label></div>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . esc_attr( $option_key['type'] ) . '"'
		. '               value="1" ' . ( 1 == intval( $option['type'] ) ? 'checked' : '' ) . ' />'
		.          esc_html( __( 'Timestamp (UnixTime)', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '      </label></div>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . esc_attr( $option_key['type'] ) . '"'
		. '               value="2" ' . ( 2 == intval( $option['type'] ) ? 'checked' : '' ) . ' />'
		.          esc_html( __( 'Timestamp (Date)', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '      </label></div>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . esc_attr( $option_key['type'] ) . '"'
		. '               value="3" ' . ( 3 == intval( $option['type'] ) ? 'checked' : '' ) . ' />'
		.          esc_html( __( 'Timestamp (Date + Time)', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '      </label></div>'
		. '      <div class="item radio"><label>'
		. '        <input type="radio" name="' . esc_attr( $option_key['type'] ) . '"'
		. '               value="4" ' . ( 4 == intval( $option['type'] ) ? 'checked' : '' ) . ' />'
		.          esc_html( __( 'Unique ID', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '      </label></div>'
		. '    </div>'
		. '    <div class="item-box option">'
		. '      <h4 class="item-title">' . esc_html( __( 'Options', NT_WPCF7SN_TEXT_DOMAIN ) ) . '</h4>'
		. '      <div class="item text">' . esc_html( __( 'Prefix', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '        <input type="text" name="' . esc_attr( $option_key['prefix'] ) . '"'
		. '               value="' . esc_attr( $option['prefix'] ) . '"' 
		. '               size="15" maxlength="10" pattern="'. esc_attr( NT_WPCF7SN_FORM_OPTION['prefix']['pattern'] ) .'"/>'
		. '        <p class="pattern">(' . esc_html( __( 'Within 10 characters. Unusable \\"&\'<>', NT_WPCF7SN_TEXT_DOMAIN ) ) . ')</p>'
		. '      </div>'
		. '      <div class="item text">' . esc_html( __( 'Digits', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '        <input type="text" name="' . esc_attr( $option_key['digits'] ) . '"'
		. '                value="' . esc_attr( $option['digits'] ) . '"'
		. '                size="1" maxlength="1" pattern="'. esc_attr( NT_WPCF7SN_FORM_OPTION['digits']['pattern'] ) .'"/>'
		. '        <p class="pattern">(' . esc_html( __( '1 digit integer. 1~9', NT_WPCF7SN_TEXT_DOMAIN ) ) . ')</p>'
		. '      </div>'
		. '      <div class="item check"><label>'
		. '        <input type="hidden"   name="' . esc_attr( $option_key['separator'] ) . '"/>'
		. '        <input type="checkbox" name="' . esc_attr( $option_key['separator'] ) . '"'
		. '               value="yes" '. ( $option['separator'] == 'yes' ? 'checked' : '' ) . ' />'
		.          esc_html( __( 'Display the delimiter "-".', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '      </label></div>'
		. '      <div class="item check"><label>'
		. '        <input type="hidden"   name="' . esc_attr( $option_key['year2dig'] ) . '"/>'
		. '        <input type="checkbox" name="' . esc_attr( $option_key['year2dig'] ) . '"'
		. '               value="yes" '. ( $option['year2dig'] == 'yes' ? 'checked' : '' ) . ' />'
		.          esc_html( __( 'Omit the number of years to 2 digits.', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '      </label></div>'
		. '      <div class="item check"><label>'
		. '        <input type="hidden"   name="' . esc_attr( $option_key['nocount'] ) . '"/>'
		. '        <input type="checkbox" name="' . esc_attr( $option_key['nocount'] ) . '"'
		. '               value="yes" '. ( $option['nocount'] == 'yes' ? 'checked' : '' ) . ' />'
		.          esc_html( __( 'Don\'t display count with unique ID.', NT_WPCF7SN_TEXT_DOMAIN ) )
		. '      </label></div>'
		. '    </div>'
		. '    <div class="item-box update">'
		. '      <div class="item example">'
		. '        <span>' . esc_html( sprintf( __( 'Example [%1$s]', NT_WPCF7SN_TEXT_DOMAIN ), $serial_num ) ) . '</span>'
		. '      </div>'
		. '      <div class="item submit_button">'
		. '        <input type="submit" class="button-primary"'
		. '               value="' . esc_html( __( 'Settings', NT_WPCF7SN_TEXT_DOMAIN ) ) .'"/>'
		. '      </div>'
		. '    </div>'
		. '  </form>'
		. '</div>';

		return wp_kses( trim( $output ), NT_WPCF7SN_ALLOWED_HTML );
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

		$option = NT_WPCF7SN_Form_Options::get_options( $form_id );

		// サニタイズ処理時にコンタクトフォームIDの識別用に一時保持(設定時に削除される)
		$option['id'] = $form_id;

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_key = [];
		foreach( $option as $key => $value ) {
			$option_key[$key] = $option_name . '[' . $key . ']';
		}

		$page_options = '';
		foreach( $option as $key => $value ) {
			$page_options .= ''
			. '<input type="hidden" name="' . esc_attr( $option_key[$key] ) . '"'
			. '       value="' . esc_attr( $option[$key] ) . '"/>';
		}

		$output .= ''
		. '<div id="' . NT_WPCF7SN_PREFIX['_'] . '_count_' . esc_attr( $form_id ) . '"'
		. '     class="' . NT_WPCF7SN_PREFIX['_'] . '_count clearfix">'
		. '  <form method="post" action="options.php">' . wp_nonce_field( 'update-options' )
		. '    <input type="hidden" name="action" value="update"/>'
		. '    <input type="hidden" name="page_options" value="' . esc_attr( $option_name ) . '"/>' . $page_options
		. '    <div class="item-box count">'
		. '      <h4 class="item-title">' . esc_html( __( 'Current Count', NT_WPCF7SN_TEXT_DOMAIN ) ) . '</h4>'
		. '      <div class="item text">'
		. '        <input type="text" name="' . esc_attr( $option_key['count'] ) . '"'
		. '               value="' . esc_attr( $option['count'] ) . '"'
		. '               size="5" maxlength="5" pattern="'. esc_attr( NT_WPCF7SN_FORM_OPTION['count']['pattern'] ) .'"/>'
		. '      </div>'
		. '      <div class="item submit_button">'
		. '        <input type="submit" class="button-primary"'
		. '               value="' . esc_html( __( 'Change', NT_WPCF7SN_TEXT_DOMAIN ) ) .'"/>'
		. '      </div>'
		. '      <p class="pattern">(' . esc_html( __( 'Up to 5 digits integer. 0~99999', NT_WPCF7SN_TEXT_DOMAIN ) ) . ')</p>'
		. '    </div>'
		. '  </form>'
		. '</div>';

		return wp_kses( trim( $output ), NT_WPCF7SN_ALLOWED_HTML );
	}
	
}
