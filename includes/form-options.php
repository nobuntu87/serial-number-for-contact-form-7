<?php
if (!defined('ABSPATH')) exit;

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
			$type = $value['type'];
			switch ( $type ) {
				case 'integer' :
					$default = intval( $default );
					break;
				case 'string' :
					$default = strval( $default );
					break;
				default :
			}
			
			$option_value[$key] = $default;
		}

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;

		update_option( $option_name, $option_value );

		return $option_value;
	}

	/**
	 * コンタクトフォームのオプションを取得する。
	 * 
	 * DBに存在しない場合デフォルト値で新規作成する。
	 * 
	 * @param int $form_id コンタクトフォームID
	 * @return mixed[] コンタクトフォームのオプションを返す。
	 */
	public function get_options( $form_id ) {
		$form_id = intval( $form_id );

		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . $form_id;
		
		$option_value = get_option( $option_name );

		// DBに存在しない場合はデフォルト値で新規作成
		if ( false === $option_value ) {
			return self::setup_options( $form_id );
		}

		// 変数型の変換
		foreach( $option_value as $key => $value ) {
			$type = NT_WPCF7SN_FORM_OPTION[$key]['type'];
			switch ( $type ) {
				case 'integer' :
					$option_value[$key] = intval( $value );
					break;
				case 'string' :
					$option_value[$key] = strval( $value );
					break;
				default :
			}
		}

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
			return $option_value[$name];
		} else {
			// オプションが未設定(NULL)の場合はデフォルト値で更新
			$default = NT_WPCF7SN_FORM_OPTION[$name]['default'];
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
		$type = NT_WPCF7SN_FORM_OPTION[$name]['type'];
		switch ( $type ) {
			case 'integer' :
				$value = intval( $value );
				break;
			case 'string' :
				$value = strval( $value );
				break;
			default :
		}

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
		global $wpdb;
	
		$option_name = NT_WPCF7SN_FORM_OPTION_NAME . '%';
		$options = $wpdb->get_results( "
			SELECT *
			  FROM $wpdb->options
			WHERE 1 = 1
			  AND option_name like '$option_name'
			ORDER BY option_name
		" );

		if ( empty( $options ) ) {
			return;
		}

		foreach ( $options as $option ) {
			$option_name = $option->option_name;

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

		// 整合性チェック：DB[有] / 定義[無] = 不要(削除オプション)
		foreach ( $option_value as $key => $value ) {
			if ( ! isset( NT_WPCF7SN_FORM_OPTION[$key] ) ) {
				self::delete_option( $form_id, $key );
			} else {
				// 変数型チェック
				$type = NT_WPCF7SN_FORM_OPTION[$key]['type'];
				if ( $type != gettype( $value ) ){
					self::update_option( $form_id, $key, $value );
				}
			}
		}

		// 整合性チェック：DB[無] / 定義[有] = 不足(追加オプション)
		foreach( NT_WPCF7SN_FORM_OPTION as $key => $value ) {
			if ( ! isset( $option_value[$key] ) ) {
				$default = $value['default'];
				self::update_option( $form_id, $key, $default );
			}
		}
	}

}
