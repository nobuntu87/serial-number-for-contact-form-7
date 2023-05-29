<?php
if( !defined( 'ABSPATH' ) ) exit;

// ==========================================================================
// WordPress Library「Admin Menu」使用方法
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// (1) ライブラリのインクルード
//    ・ wplib-admin-menu.php ファイルのパスを設定
//    ・ 名前空間のエイリアスを設定 (使用バージョンを指定)
//    ・ ↑ もしくは、クラスエイリアス関数を定義
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// (2) 管理メニュークラスを作成
//    (2-A) 必須：メニュー/タブの追加 [add_menu()]
//        ・詳細は関数サンプルコードを参照
//    (2-B) 推奨：バリテーション処理の実装 [validate_options()]
//        ・詳細は関数サンプルコードを参照
//    (3-B) 任意：表示許可HTMLの追加 [add_allowed_html()]
//        ・詳細は関数サンプルコードを参照
// ==========================================================================

// ------------------------------------
// ライブラリ設定
// ------------------------------------

require_once( __DIR__ . '/wplib-admin-menu.php' );

# [use as] もしくは [class_alias()] を使用する。
use _Nt\WpLib\AdminMenu\vX_Y_Z\Admin_Menu_Base as Nt_WpLib_Admin_Menu;
# class_alias( '_Nt\WpLib\AdminMenu\vX_Y_Z\Admin_Menu_Base', 'Nt_WpLib_Admin_Menu' );

// ------------------------------------
// 管理メニュークラス
// ------------------------------------

class App_Prefix_Admin_Menu extends Nt_WpLib_Admin_Menu {

	/**
	 * メニューの追加を行う。
	 *
	 * @return void
	 */
	protected function add_menu()
	{
		// ========================================================
		// トップメニューの追加方法
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// 関数 add_top_menu( $menu_option ) を使用する
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (1) トップメニューのオプション項目 (*必須項目)
		//    [menu_slug]  | * | メニューページのスラッグ名 (ユニークID)
		//    [page_title] |   | ブラウザに表示されるページ名
		//    [menu_title] |   | 管理メニューに表示されるメニュー名
		//    [capability] |   | 管理メニューの表示権限 (デフォルト：manage_options)
		//    [position]   |   | 管理メニューの表示位置
		//    [icon_url]   |   | 管理メニューのアイコン (WordPress Dashicons)
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (2) メニューページのオプション項目 (*必須項目)
		//    [header_title] |   | ヘッダータイトル (H1)
		//    [header_icon]  |   | ヘッダーアイコン (Font Awesome Icon v6)
		//    [description]  |   | メニューページの説明文
		//    [page_file]    |   | メニューページの表示用ファイル (絶対パス)
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (3) オプションフォームのオプション項目 (*必須項目)
		//    [form_title]   |   | フォームタイトル (H2)
		//    [form_icon]    |   | フォームアイコン (Font Awesome Icon v6)
		//    [form_file]    |   | フォームの表示用ファイル (絶対パス)
		// ========================================================

		// ========================================================
		// サブメニューの追加方法
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// 関数 add_sub_menu( $menu_option ) を使用する
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (1) サブメニューのオプション項目 (*必須項目)
		//    [parent_slug] | * | 親メニューページのスラッグ名
		//    ※トップメニューと同様
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (2) メニューページのオプション項目 (*必須項目)
		//    ※トップメニューと同様
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (3) オプションフォームのオプション項目 (*必須項目)
		//    ※トップメニューと同様
		// ========================================================

		// ========================================================
		// メニュータブの追加方法
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// 関数 add_menu_tab( $tab_option ) を使用する
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (1) メニュータブのオプション項目 (*必須項目)
		//    [parent_slug] |   | 親メニューページのスラッグ名
		//    [tab_slug]    |   | タブページのスラッグ名 (ユニークID)
		//    [tab_title]   |   | タブタイトル
		//    [tab_icon]    |   | タブアイコン (Font Awesome Icon v6)
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (2) メニューページのオプション項目 (*必須項目)
		//    ※トップメニューと同様
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (3) オプションフォームのオプション項目 (*必須項目)
		//    ※トップメニューと同様
		// ========================================================



		// ========================================================
		// 【使用例:A】新規メニュー (トップページ分離型)
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		//    [メインメニュー(A-M)]
		//       L[メインメニュー(A-M)]
		//       L[サブメニュー(A-M-S1)]
		// ========================================================

		// メインメニュー：[メインメニュー(A-M)]
		$this->add_top_menu( array(
			// メニュー設定
			'menu_slug'    => 'xxxxx-a-m',
			'page_title'   => 'ページタイトル (A-M) [page_title]',
			'menu_title'   => 'メニュー (A-M) [menu_title]',
			// ページ設定
			'header_title' => 'ヘッダータイトル (A-M) [header_title]',
			'header_icon'  => 'fa-solid fa-gear',
			'description'  => 'ページ説明文 (A-M) [description]',
			'page_file'    => __DIR__ . '/view/menu-page.php',
			// フォーム設定
			'form_title'   => 'フォームタイトル (A-M) [form_title]',
			'form_icon'    => 'fa-solid fa-sliders',
			'form_file'    => __DIR__ . '/view/menu-form.php',
		) );

		// サブメニュー：[サブメニュー(A-M-S1)]
		$this->add_sub_menu( array(
			// メニュー設定
			'parent_slug'  => 'xxxxx-a-m',
			'menu_slug'    => 'xxxxx-a-m-s1',
			'page_title'   => 'ページタイトル (A-M-S1) [page_title]',
			'menu_title'   => 'メニュー (A-M-S1) [menu_title]',
			// ページ設定
			'header_title' => 'ヘッダータイトル (A-M-S1) [header_title]',
			'header_icon'  => 'fa-solid fa-gear',
			'description'  => 'ページ説明文 (A-M-S1) [description]',
			'page_file'    => __DIR__ . '/view/menu-page.php',
			// フォーム設定
			'form_title'   => 'フォームタイトル (A-M-S1) [form_title]',
			'form_icon'    => 'fa-solid fa-sliders',
			'form_file'    => __DIR__ . '/view/menu-form.php',
		) );

		// ========================================================
		// 【使用例:B】新規メニュー (トップページ統合)
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		//    [メインメニュー(B-M)]
		//       L[サブメニュー(B-M-S1)]
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		//    ・トップとサブメニューのスラッグを一致させる
		// ========================================================

		// メインメニュー：[メインメニュー(B-M)]
		$this->add_top_menu( array(
			// メニュー設定
			'menu_slug'    => 'xxxxx-b-m',
			'page_title'   => 'ページタイトル (B-M) [page_title]',
			'menu_title'   => 'メニュー (B-M) [menu_title]',
		) );

		// サブメニュー：[サブメニュー(B-M-S1)]
		$this->add_sub_menu( array(
			// メニュー設定
			'parent_slug'  => 'xxxxx-b-m',
			'menu_slug'    => 'xxxxx-b-m',
			'page_title'   => 'ページタイトル (B-M-S1) [page_title]',
			'menu_title'   => 'メニュー (B-M-S1) [menu_title]',
			// ページ設定
			'header_title' => 'ヘッダータイトル (B-M-S1) [header_title]',
			'header_icon'  => 'fa-solid fa-gear',
			'description'  => 'ページ説明文 (B-M-S1) [description]',
			'page_file'    => __DIR__ . '/view/menu-page.php',
			// フォーム設定
			'form_title'   => 'フォームタイトル (B-M-S1) [form_title]',
			'form_icon'    => 'fa-solid fa-sliders',
			'form_file'    => __DIR__ . '/view/menu-form.php',
		) );

		// サブメニュー：[サブメニュー(B-M-S2)]
		$this->add_sub_menu( array(
			// メニュー設定
			'parent_slug'  => 'xxxxx-b-m',
			'menu_slug'    => 'xxxxx-b-m-s2',
			'page_title'   => 'ページタイトル (B-M-S2) [page_title]',
			'menu_title'   => 'メニュー (B-M-S2) [menu_title]',
			// ページ設定
			'header_title' => 'ヘッダータイトル (B-M-S2) [header_title]',
			'header_icon'  => 'fa-solid fa-gear',
			'description'  => 'ページ説明文 (B-M-S2) [description]',
			'page_file'    => __DIR__ . '/view/menu-page.php',
			// フォーム設定
			'form_title'   => 'フォームタイトル (B-M-S2) [form_title]',
			'form_icon'    => 'fa-solid fa-sliders',
			'form_file'    => __DIR__ . '/view/menu-form.php',
		) );

		// ========================================================
		// 【使用例:C】新規メニュー (タブ)
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		//    [メインメニュー(C-M)]
		//       L[メインメニュー(C-M)]
		//           L[タブ(B-M-T1)]
		//           L[タブ(B-M-T2)]
		// ========================================================

		// メインメニュー：[メインメニュー(C-M)]
		$this->add_top_menu( array(
			// メニュー設定
			'menu_slug'    => 'xxxxx-c-m',
			'page_title'   => 'ページタイトル (C-M) [page_title]',
			'menu_title'   => 'メニュー (C-M) [menu_title]',
			// ページ設定
			'header_title' => 'ヘッダータイトル (C-M) [header_title]',
			'header_icon'  => 'fa-solid fa-gear',
			'description'  => 'ページ説明文 (C-M) [description]',
			'page_file'    => __DIR__ . '/view/menu-page.php',
			// フォーム設定
			'form_title'   => 'フォームタイトル (C-M) [form_title]',
			'form_icon'    => 'fa-solid fa-sliders',
			'form_file'    => __DIR__ . '/view/menu-form.php',
		) );

		// メニュータブ：[タブ(C-M-T1)]
		$this->add_menu_tab( array(
			// タブ設定
			'parent_slug'  => 'xxxxx-c-m',
			'tab_slug'     => 'xxxxx-c-m-t1',
			'tab_title'    => 'タブ (C-M-T1) [tab_title]',
			'tab_icon'     => 'fa-solid fa-table-cells-large',
			// ページ設定
			'header_title' => 'ヘッダータイトル (C-M-T1) [header_title]',
			'header_icon'  => 'fa-solid fa-gear',
			'description'  => 'ページ説明文 (C-M-T1) [description]',
			'page_file'    => __DIR__ . '/view/menu-page.php',
			// フォーム設定
			'form_title'   => 'フォームタイトル (C-M-T1) [form_title]',
			'form_icon'    => 'fa-solid fa-sliders',
			'form_file'    => __DIR__ . '/view/menu-form.php',
		) );

		// メニュータブ：[タブ(C-M-T2)]
		$this->add_menu_tab( array(
			// タブ設定
			'parent_slug'  => 'xxxxx-c-m',
			'tab_slug'     => 'xxxxx-c-m-t2',
			'tab_title'    => 'タブ (C-M-T2) [tab_title]',
			'tab_icon'     => 'fa-solid fa-table-cells-large',
			// ページ設定
			'header_title' => 'ヘッダータイトル (C-M-T2) [header_title]',
			'header_icon'  => 'fa-solid fa-gear',
			'description'  => 'ページ説明文 (C-M-T2) [description]',
			'page_file'    => __DIR__ . '/view/menu-page.php',
			// フォーム設定
			'form_title'   => 'フォームタイトル (C-M-T2) [form_title]',
			'form_icon'    => 'fa-solid fa-sliders',
			'form_file'    => __DIR__ . '/view/menu-form.php',
		) );

		// ========================================================
		// 【使用例:D】新規メニュー (設定メニュー)
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		//    [設定]
		//       L[サブメニュー(C-M-S1)]
		// ========================================================

		// サブメニュー：[サブメニュー(D-M-S1)]
		$this->add_sub_menu( array(
			// メニュー設定
			'parent_slug'  => 'options-general.php',
			'menu_slug'    => 'xxxxx-c-m-s1',
			'page_title'   => 'ページタイトル (C-M-S1)',
			'menu_title'   => 'メニュー (C-M-S1)',
			// ページ設定
			'header_title' => 'ヘッダータイトル (C-M-S1)',
			'header_icon'  => 'fa-solid fa-gear',
			'description'  => 'ページ説明文 (C-M-S1)',
			'page_file'    => __DIR__ . '/view/menu-page.php',
			// フォーム設定
			'form_title'   => 'フォームタイトル (C-M-S1)',
			'form_icon'    => 'fa-solid fa-sliders',
			'form_file'    => __DIR__ . '/view/menu-form.php',
		) );
	}

	/**
	 * オプション値のバリテーション処理を行う。
	 *
	 * @param mixed[] $options オプション値
	 * @param string $page_slug ページスラッグ : {menu-slug}_{tab-slug}
	 * @return mixed[] オプション値を返す。
	 */
	protected function validate_options( $options, $page_slug )
	{
		// ========================================================
		// バリテーション処理の使用方法
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (1) オプション項目毎にバリテーション処理を行う
		// - - - - - - - - - - - - - - - - - - - - - - - - - - - -
		// (2) オプションエラーの登録を行う [add_option_error()]
		// ========================================================

		foreach( $options as $key => $value ) {
			switch ( $key ) {
				case 'checkbox_1' :
					// オプションエラー登録
					$this->add_option_error( $key, 'ERROR MESSAGE !!' );
					// バリテーション処理
					break;
				case 'number_1' :
					// バリテーション処理
					break;
				default:
					// 未定義の設定項目を削除
					//unset( $options[$key] );
					break;
			}
		}

		return $options;
	}

	/**
	 * 表示許可HTMLの追加を行う。
	 *
	 * @return mixed[] 表示許可HTMLを返す。
	 */
	protected function add_allowed_html() {
		return array(
			'html-tag-1' => array(
				'html-attr-1' => array(),
				'html-attr-2' => array(),
			),
			'html-tag-2' => array(
				'html-attr-1' => array(),
				'html-attr-2' => array(),
			),
		);
	}

}

// ------------------------------------
// 管理メニュー生成
// ------------------------------------

$app_prefix_admin_menu = new App_Prefix_Admin_Menu(
	'xxxxx', '1.0.0'
);
