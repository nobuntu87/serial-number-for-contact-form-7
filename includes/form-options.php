<?php
if ( !defined( 'ABSPATH' ) ) exit;

class NT_WPCF7SN_Form_Options {

	/**
	 * コンタクトフォームのオプションをセットアップする。
	 * 
	 * DBに存在する全コンタクトフォームのオプションをセットアップする。
	 *
	 * @return void
	 */
	public function setup_all_options() {
		// POSTデータからコンタクトフォーム情報を取得
		$wpcf7_posts = nt_wpcf7sn_get_posts_wpcf7();

		// 全てのコンタクトフォームのオプションをセットアップ
		foreach( $wpcf7_posts as $wpcf7_post ) {
			$form_id = intval( $wpcf7_post->ID );
			self::setup_options( $form_id );
		}
	}

	/**
	 * コンタクトフォームのオプションをセットアップする。
	 * 
	 * デフォルト値で新規作成する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @return mixed[] コンタクトフォームのオプションを返す。
	 */
	public function setup_options( $form_id ) {
		$form_id = intval( $form_id );

		$option_value = [];

		// 全てのコンタクトフォームのオプションを設定
		foreach( NT_WPCF7SN_FORM_OPTION as $key => $value ) {
			$default = $value['default'];

			// 変数型の変換
			$default = self::cast_type_option( $key, $default );
			
			$option_value[$key] = $default;
		}

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		update_option( $option_name, $option_value );

		return $option_value;
	}

	/**
	 * DBからコンタクトフォームのオプションを取得する。
	 *
	 * @return mixed[] コンタクトフォームのオプションを返す。
	 */
	public function get_wpdb_options() {
		global $wpdb;

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . '%';

		$options = $wpdb->get_results( "
			SELECT *
			  FROM $wpdb->options
			WHERE 1 = 1
			  AND option_name like '$option_name'
			ORDER BY option_name
		" );

		return $options;
	}

	/**
	 * コンタクトフォームのオプションを取得する。
	 * 
	 * DBに存在しない場合デフォルト値で新規作成する。
	 * カウント値のリセットチェックアクションを実行する。
	 * 
	 * @param int $form_id コンタクトフォームID
	 * @return mixed[] コンタクトフォームのオプションを返す。
	 */
	public function get_options( $form_id ) {
		do_action( 'nt_wpcf7sn_check_reset_count' );

		$form_id = intval( $form_id );

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;
		
		$option_value = get_option( $option_name );

		// DBに存在しない場合はデフォルト値で新規作成
		if ( false === $option_value ) {
			return self::setup_options( $form_id );
		}

		// 変数型の変換
		$option_value = self::cast_type_options( $option_value );

		return $option_value;
	}

	/**
	 * コンタクトフォームのオプションを取得する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @param string $name オプション名
	 * @return mixed コンタクトフォームのオプションを返す。
	 *               オプション名が定義されていない場合はfalseを返す。
	 */
	public function get_option( $form_id, $name ) {
		// オプション名が未定義の場合はfalse
		if ( ! isset( NT_WPCF7SN_FORM_OPTION[$name] ) ) {
			return false;
		}

		$form_id = intval( $form_id );

		$option_value = self::get_options( $form_id );

		if ( isset( $option_value[$name] ) ) {
			return self::cast_type_option( $name, $option_value[$name] );
		} else {
			// オプションが未設定(NULL)の場合はデフォルト値で更新
			$default = NT_WPCF7SN_FORM_OPTION[$name]['default'];
			$default = self::cast_type_option( $name, $default );
			self::update_option( $form_id, $name, $default );
			return $default;
		}
	}

	/**
	 * コンタクトフォームのオプションを更新する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @param string $name オプション名
	 * @param mixed $value オプション値
	 * @return bool オプション名が定義されていない場合はfalseを返す。
	 */
	public function update_option( $form_id, $name, $value ) {
		// オプション名が未定義の場合はfalse
		if ( ! isset( NT_WPCF7SN_FORM_OPTION[$name] ) ) {
			return false;
		}

		$form_id = intval( $form_id );

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_value = self::get_options( $form_id );

		// 変数型の変換
		$value = self::cast_type_option( $name, $value );

		// 現在の設定にマージする
		$option_value = array_merge( $option_value, array( $name => $value ) );
		update_option( $option_name, $option_value );
	}

	/**
	 * コンタクトフォームのオプションを削除する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @param string $name オプション名
	 * @return void
	 */
	public function delete_option( $form_id, $name ) {
		$form_id = intval( $form_id );

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_value = self::get_options( $form_id );

		// 現在の設定から指定オプションを削除し更新
		unset( $option_value[$name] );
		update_option( $option_name, $option_value );
	}

	/**
	 * コンタクトフォームのオプションの整合性をチェックする。
	 * 
	 * DBに存在する全てのコンタクトフォームのオプションをチェックする。
	 *
	 * @return void
	 */
	public function check_all_options() {
		// DBからコンタクトフォームのオプションを取得
		$wpdb_options = self::get_wpdb_options();

		foreach ( $wpdb_options as $wpdb_option ) {
			$option_name = $wpdb_option->option_name;

			preg_match( '/(?P<id>[0-9]+)$/', $option_name, $match );
			$form_id = intval( $match['id'] );

			self::check_options( $form_id );
		}
	}

	/**
	 * コンタクトフォームのオプションの整合性をチェックする。
	 * 
	 * 不要(削除)オプションをチェックする。 (DB[有]/定義[無])
	 * 不足(追加)オプションをチェックする。 (DB[無]/定義[有])
	 *
	 * @param int $form_id コンタクトフォームID
	 * @return bool DBに存在しない場合はfalseを返す
	 */
	public function check_options( $form_id ) {
		$form_id = intval( $form_id );

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		$option_value = get_option( $option_name );

		// DBに存在しない場合は終了
		if ( false === $option_value ) {
			return;
		}

		foreach ( $option_value as $key => $value ) {
			// 整合性チェック：DB[有] / 定義[無] = 不要(削除オプション)
			if ( ! isset( NT_WPCF7SN_FORM_OPTION[$key] ) ) {
				self::delete_option( $form_id, $key );
			} else {
				// 整合性チェック：DB[有] / 定義[有] / 型不一致 = 変更(変更オプション)
				$type = NT_WPCF7SN_FORM_OPTION[$key]['type'];
				if ( $type != gettype( $value ) ){
					// データ構造が変更された可能性があるため初期値で更新
					$default = NT_WPCF7SN_FORM_OPTION[$key]['default'];
					$default = self::cast_type_option( $key, $default );
					self::update_option( $form_id, $key, $default );
				}
			}
		}

		foreach( NT_WPCF7SN_FORM_OPTION as $key => $value ) {
			// 整合性チェック：DB[無] / 定義[有] = 不足(追加オプション)
			if ( ! isset( $option_value[$key] ) ) {
				$default = $value['default'];
				$default = self::cast_type_option( $key, $default );
				self::update_option( $form_id, $key, $default );
			}
		}
	}

	/**
	 * コンタクトフォームのオプションの変数型を変換する。
	 *
	 * @param mixed $options コンタクトフォームのオプション
	 * @return mixed[] コンタクトフォームのオプションを返す。
	 */
	public function cast_type_options( $options ) {
		foreach( $options as $key => $value ) {
			$value = self::cast_type_option( $key, $value );
			$options[$key] = $value;
		}

		return $options;
	}

	/**
	 * コンタクトフォームのオプションの変数型を変換する。
	 *
	 * @param string $name オプション名
	 * @param mixed $value オプション値
	 * @return mixed[] コンタクトフォームのオプションを返す。
	 */
	public function cast_type_option( $name, $value ) {
		// オプション名が未定義の場合は終了
		if ( ! isset( NT_WPCF7SN_FORM_OPTION[$name] ) ) {
			return $value;
		}

		$type = NT_WPCF7SN_FORM_OPTION[$name]['type'];

		if ( $type == gettype( $value ) ){
			return $value;
		}
		
		switch ( $type ) {
			case 'integer' :
				$value = intval( $value );
				break;
			case 'string' :
				$value = strval( $value );
				break;
			default :
		}

		return $value;
	}

	/**
	 * カウント値を取得する。
	 * 
	 * カウンタータイプによりカウント値またはデイリーカウント値を取得する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @return int 現在のカウント値を返す。
	 */
	public function get_count( $form_id ) {
		$form_id = intval( $form_id );

		$options = self::get_options( $form_id );

		if ( 'yes' == $options['dayreset'] ) {
			$now_count = intval( $options['daycount'] );
		} else {
			$now_count = intval( $options['count'] );
		}

		return $now_count;
	}

	/**
	 * カウントを増加する。
	 * 
	 * カウンタータイプにかかわらず全てのカウントを増加する。
	 *
	 * @param int $form_id コンタクトフォームID
	 * @return void
	 */
	public function increment_count( $form_id ) {
		$form_id = intval( $form_id );

		$options = self::get_options( $form_id );

		// カウント増加
		$new_count = intval( $options['count'] ) + 1;
		update_option( $form_id, 'count', $new_count );

		// デイリーカウント増加
		$new_daycount = intval( $options['daycount'] ) + 1;
		update_option( $form_id, 'daycount', $new_daycount );
	}

	/**
	 * デイリーカウントをリセットする。
	 * 
	 * 全てのコンタクトフォームのオプションのカウント値をリセットする。
	 *
	 * @return void
	 */
	public function reset_daily_count() {
		// POSTデータからコンタクトフォーム情報を取得
		$wpcf7_posts = nt_wpcf7sn_get_posts_wpcf7();

		// 全てのコンタクトフォームのオプションをセットアップ
		foreach( $wpcf7_posts as $wpcf7_post ) {
			$form_id = intval( $wpcf7_post->ID );
			self::update_option( $form_id, 'daycount', 0 );
		}
	}

}
