<?php
namespace _Nt\WpLib\AdminMenu\v2_8_1;
if( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// 定数定義
// ============================================================================

if ( !defined( __NAMESPACE__ . '\_LIBRARY_DOMAIN' ) ):

define( __NAMESPACE__ . '\_LIBRARY_DOMAIN', 'nt-wplib-admin-menu' );

define( __NAMESPACE__ . '\_OPTION_GROUP_SUFFIX', 'group' );
define( __NAMESPACE__ . '\_OPTION_NAME_SUFFIX', 'conf' );

endif;

// ============================================================================
// 管理メニュー基底クラス：Admin_Menu_Base
// ============================================================================

if ( !class_exists( __NAMESPACE__ . '\Admin_Menu_Base' ) ):
abstract class Admin_Menu_Base {

  // ========================================================
  // 定数定義
  // ========================================================

	// 表示用ファイル定義
	protected const _VIEW_PAGE_FILE = __DIR__ . '\view\menu-page.php';
	protected const _VIEW_FORM_FILE = __DIR__ . '\view\menu-form.php';

	// メニューフォーマット定義 (トップメニュー)
	protected const _MENU_TOP_FORMAT = array(
		// メニュー設定
		'menu_slug'   => '',						// 必須 : スラッグ名 : {menu-slug}
		'page_title'  => '',						//      : タイトル (ページ)
		'menu_title'  => '',						//      : タイトル (メニュー)
		'capability'  => 'manage_options',			//      : 権限
		'position'    => null,						//      : 表示位置
		'icon_url'    => '',						//      : アイコン (WordPress Dashicons)
		// タブ設定
		'tabs'        => array(),
	);

	// メニューフォーマット定義 (サブメニュー)
	protected const _MENU_SUB_FORMAT = array(
		// メニュー設定
		'parent_slug' => '',						// 必須 : スラッグ名 (親ページ) : {menu-slug}
	) + SELF::_MENU_TOP_FORMAT;

	// ページフォーマット定義
	protected const _PAGE_FORMAT = array(
		// ページ設定
		'header_title' => '',						//      : タイトル (ヘッダー)
		'header_icon'  => '',						//      : アイコン (Font Awesome Icon v6)
		'description'  => '',						//      : 説明文
		'page_file'    => SELF::_VIEW_PAGE_FILE,	//      : ページ表示ファイル (絶対パス)
		// フォーム設定
		'form_title'   => '',						//      : タイトル (フォーム)
		'form_icon'    => '',						//      : アイコン (Font Awesome Icon v6)
		'form_file'    => SELF::_VIEW_FORM_FILE,	//      : フォーム表示ファイル
	);

	// タブフォーマット定義
	protected const _TAB_FORMAT = array(
		// タブ設定
		'parent_slug' => '',						// 必須 : スラッグ名 (親ページ) : {menu-slug}
		'tab_slug'    => '',						// 必須 : スラッグ名 : {tab-slug}
		'tab_title'   => '',						//      : タイトル (タブ)
		'tab_icon'    => '',						//      : アイコン (Font Awesome Icon v6)
	);

	// オプションフォーマット定義 (KEY:オプションキー : {opt-key})
	protected const _OPTION_FORMAT = array(
		'value' => '',							// オプション値
		'error' => '',							// エラーメッセージ
	);

	// スクリプト定義 (FontAwesome)
	protected const _SCRIPT_FONTAWESOME = array(
		'url'     => '//use.fontawesome.com/releases/v6.4.0/css/all.css',
		'version' => '6.4.0',
		'slug'    => 'fontawesome-all',
	);

	// 表示許可HTMLタグ定義
	protected const _ALLOWED_HTML_TAGS = array(
		'form', 'input', 'select', 'option',
	);

	// 表示許可HTML属性定義
	protected const _ALLOWED_HTML_ATTR = array(
		'method', 'action',
		'checked', 'selected',
		'readonly', 'disabled',
		'minlength', 'maxlength',
		'placeholder', 'pattern',
		'size', 'min', 'max',
	);

  // ========================================================
  // 変数定義
  // ========================================================

	// クラス設定
	protected $m_class = array(
		'lib' => array(
			'slug'    => '',			// ライブラリスラッグ : {lib-slug}
			'version' => '',			// ライブラリバージョン : {lib-ver}
		),
		'app' => array(
			'slug'    => '',			// アプリケーションスラッグ : {app-slug}
			'version' => '',			// アプリケーションバージョン : {app-ver}
		),
	);

	// メニュー設定
	protected $m_menu = array(
		'top' => array(),				// トップメニュー
		'sub' => array(),				// サブメニュー
	);

	// ページ設定
	protected $m_page = array(
		'slug'   => '',					// ページスラッグ : {menu-slug}_{tab-slug}
		// メニュー設定
		'menu'   => array(
			'slug'   => null,			// メニュースラッグ : {menu-slug}
			'option' => null,			// メニュー設定
		),
		// タブ設定
		'tab'    => array(
			'slug'    => null,			// タブスラッグ : {tab-slug}
			'options' => null,			// タブ設定 (全項目)
		),
		// 表示設定
		'view'   => array(
			SELF::_PAGE_FORMAT,			// 表示設定
		),
		// オプション設定
		'option' => array(
			'group' => null,			// オプショングループ名 : {app-slug}_{page-slug}_group
			'name'  => null,			// オプション名 : {app-slug}_{page-slug}_{opt-suffix}
		),
	);

	// オプション設定
	protected $m_option = array();

	// 表示許可HTML設定
	protected $m_kses_allowed_html = array();

  // ========================================================

	/**
	 * コンストラクタ処理を行う。
	 *
	 * @param string $slug スラッグ
	 * @param string $version バージョン
	 */
	public final function __construct( $slug = '', $version = '' )
	{
		// クラス初期化
		$this->init_class( $slug, $version );

		// メニュー初期化
		$this->init_menu();

		// ページ初期化
		$this->init_page();

		// オプション初期化
		$this->init_option();
	}

  // ========================================================
  // 継承関数
  // ========================================================

	/**
	 * メニューの追加を行う。
	 *
	 * @return void
	 */
	abstract protected function add_menu();

	/**
	 * オプション値のサニタイズ処理を行う。
	 *
	 * @param mixed[] $options オプション値
	 * @param string $page_slug ページスラッグ : {menu-slug}_{tab-slug}
	 * @return mixed[] オプション値を返す。
	 */
	protected function sanitize_options( $options, $page_slug )
	{
		if ( !is_array( $options ) ) { return []; }

		// 空白文字を除去
		$options = array_map(
			array( __NAMESPACE__ . '\Library_Utility', 'strip_whitespace' ),
			$options
		);

		return $options;
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
		return $options;
	}

	/**
	 * 表示許可HTMLの追加を行う。
	 *
	 * @return mixed[] 表示許可HTMLを返す。
	 */
	protected function add_allowed_html()
	{
		return [];
	}

  // ========================================================
  // クラス設定
  // ========================================================

	/**
	 * クラスの初期化を行う。
	 *
	 * @param string $slug スラッグ : {app-slug}
	 * @param string $version バージョン : {app-ver}
	 * @return void
	 */
	private function init_class( $slug, $version )
	{
		// ライブラリ初期化
		$this->init_class_library();

		// アプリケーション初期化
		$this->init_class_application( $slug, $version );

		// スクリプト登録
		$this->register_script();

		// 許可HTML初期化
		$this->init_allowed_html();
	}

	/**
	 * ライブラリ設定の初期化を行う。
	 *
	 * @return void
	 */
	private function init_class_library()
	{
		$lib_slug = '';
		$lib_version = '';

		// ------------------------------------
		// ライブラリ設定取得
		// ------------------------------------

		// 正規表現マッチング
		$pattern = '/^_(?P<namespace>.+)\\\v(?P<version>\d_\d_\d)$/';
		if ( 1 !== preg_match( $pattern, __NAMESPACE__, $matches ) ) {
			return false;
		}

		$lib_slug = _LIBRARY_DOMAIN;

		$lib_version = str_replace( '_', '.', $matches['version'] );

		// ------------------------------------
		// 変数設定
		// ------------------------------------

		$this->m_class['lib']['slug'] = $lib_slug;
		$this->m_class['lib']['version'] = $lib_version;
	}

	/**
	 * アプリケーション設定の初期化を行う。
	 *
	 * @param string $slug スラッグ : {app-slug}
	 * @param string $version バージョン : {app-ver}
	 * @return void
	 */
	private function init_class_application( $slug, $version )
	{
		$app_slug = '';
		$app_version = '';

		// ------------------------------------
		// 自動取得
		// ------------------------------------

		// 引数チェック (NG：未定義/空/NULL)
		if ( empty( $slug ) ) {

			// ------------------------------------
			// 動作タイプ判定 (テーマ/プラグイン)
			// ------------------------------------

			// 正規表現マッチング
			$pattern = '/wp-content[\/\\\](?P<target>[^\/\\\]+)[\/\\\](?P<root>[^\/\\\]+).+$/';
			if ( 1 !== preg_match( $pattern, __DIR__, $matches ) ) {
				return false;
			}

			$app_slug = $matches['root'];

			// ------------------------------------
			// アプリケーション設定取得：プラグイン
			// ------------------------------------

			if ( 'plugins' == $matches['target'] ) {
				if ( !function_exists( 'get_plugins' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}
				foreach ( get_plugins() as $basename => $object ) {
					if ( dirname( $basename ) == $matches['root'] ) {
						$app_slug = $object->get( 'TextDomain' );
						$app_version = $object->get( 'Version' );
					}
				}
			}

			// ------------------------------------
			// アプリケーション設定取得：テーマ
			// ------------------------------------

			else {
				$app_slug = get_stylesheet();
				$app_version = wp_get_theme( $slug )->get( 'Version' );
			}

		}

		// ------------------------------------
		// 引数指定
		// ------------------------------------

		else {
			$app_slug = $slug;
			$app_version = $version;
		}

		// ------------------------------------
		// 変数設定
		// ------------------------------------

		$this->m_class['app']['slug'] = $app_slug;
		$this->m_class['app']['version'] = $app_version;
	}

	/**
	 * 表示許可HTML設定の初期化を行う。
	 *
	 * @return void
	 */
	private function init_allowed_html()
	{
		$allowed_html = array();

		$wp_allowed_html = wp_kses_allowed_html( 'post' );

		// ------------------------------------
		// HTMLタグ設定
		// ------------------------------------

		$allowed_tags = array();

		// WP標準設定を取得
		foreach ( array_keys( $wp_allowed_html ) as $tag ) {
			$allowed_tags += array( $tag => array() );
		}

		// ライブラリ定義を追加
		foreach ( SELF::_ALLOWED_HTML_TAGS as $tag ) {
			$allowed_tags += array( $tag => array() );
		}

		// ------------------------------------
		// HTML属性設定
		// ------------------------------------

		$allowed_attrs = array();

		// WP標準設定を取得
		foreach ( array_values( $wp_allowed_html ) as $attrs ) {
			foreach ( array_keys( $attrs ) as $attr ) {
				$allowed_attrs += array( $attr => array() );
			}
		}

		// ライブラリ定義を追加
		foreach ( SELF::_ALLOWED_HTML_ATTR as $attr ) {
			$allowed_attrs += array( $attr => array() );
		}

		// ------------------------------------
		// 表示許可HTML生成
		// ------------------------------------

		foreach ( array_keys( $allowed_tags ) as $tag ) {
			$allowed_html += array( $tag => $allowed_attrs );
		}

		// ------------------------------------
		// 追加設定
		// ------------------------------------

		foreach ( $this->add_allowed_html() as $tag => $attrs ) {
			$allowed_html[$tag] = array_merge( $allowed_html[$tag], $attrs );
		}

		// ------------------------------------
		// 変数設定
		// ------------------------------------

		$this->m_kses_allowed_html = $allowed_html;
	}

  // ========================================================
  // メニュー設定
  // ========================================================

	/**
	 * メニューの初期化を行う。
	 *
	 * @return void
	 */
	private function init_menu()
	{
		// メニュー追加
		$this->add_menu();

		// メニュー登録
		$this->register_menu();
	}

   // ------------------------------------
   // メニュー追加
   // ------------------------------------

	/**
	 * トップメニューを追加する。
	 *
	 * @param mixed[] $menu_option メニュー設定
	 * @return void
	 */
	protected final function add_top_menu( $menu_option )
	{
		// 必須項目チェック (NG：未定義/空/NULL)
		if ( empty( $menu_option['menu_slug'] ) ) { return; }

		$slug = $menu_option['menu_slug'];
		$menu = SELF::_MENU_TOP_FORMAT + SELF::_PAGE_FORMAT;

		$this->m_menu['top'] = array_merge(
			$this->m_menu['top'],
			array( $slug => $this->array_update( $menu, $menu_option ) )
		);
	}

	/**
	 * サブメニューを追加する。
	 *
	 * @param mixed[] $menu_option メニュー設定
	 * @return void
	 */
	protected final function add_sub_menu( $menu_option )
	{
		// 必須項目チェック (NG：未定義/空/NULL)
		if ( empty( $menu_option['menu_slug'] ) ) { return; }
		if ( empty( $menu_option['parent_slug'] ) ) { return; }

		$slug = $menu_option['menu_slug'];
		$menu = SELF::_MENU_SUB_FORMAT + SELF::_PAGE_FORMAT;

		$this->m_menu['sub'] = array_merge(
			$this->m_menu['sub'],
			array( $slug => $this->array_update( $menu, $menu_option ) )
		);
	}

	/**
	 * メニュータブを設定する。
	 *
	 * @param mixed[] $tab_option タブ設定
	 * @return void
	 */
	protected final function add_menu_tab( $tab_option )
	{
		// 必須項目チェック (NG：未定義/空/NULL)
		if ( empty( $tab_option['tab_slug'] ) ) { return; }
		if ( empty( $tab_option['parent_slug'] ) ) { return; }

		$slug = $tab_option['tab_slug'];
		$tab = SELF::_TAB_FORMAT + SELF::_PAGE_FORMAT;

		foreach( $this->m_menu as $type => $menu ) {
			foreach( $menu as $menu_slug => $menu_option ) {

				if ( $menu_slug == $tab_option['parent_slug'] ) {
					$this->m_menu[$type][$menu_slug]['tabs'] = array_merge( 
						$this->m_menu[$type][$menu_slug]['tabs'],
						array( $slug => $this->array_update( $tab, $tab_option ) )
					);
				}

			}
		}
	}

   // ------------------------------------
   // メニュー登録
   // ------------------------------------

	/**
	 * メニューを登録する。
	 *
	 * @return void
	 */
	private function register_menu()
	{
		add_action( 'admin_menu', array( $this, 'register_wp_menu_callback' ), 11, 0 );
	}

	/**
	 * WordPressにメニューを登録する。
	 *
	 * @return void
	 */
	public final function register_wp_menu_callback()
	{
		// トップメニュー登録
		if ( !empty( $this->m_menu['top'] ) ) {
			foreach( $this->m_menu['top'] as $slug => $menu_option ) {
				$this->register_wp_menu_top( $menu_option );
			}
		}

		// サブメニュー登録
		if ( !empty( $this->m_menu['sub'] ) ) {
			foreach( $this->m_menu['sub'] as $slug => $menu_option ) {
				$this->register_wp_menu_sub( $menu_option );
			}
		}
	}

	/**
	 * WordPressにトップメニューを登録する。
	 *
	 * @param mixed[] $menu_option メニュー設定
	 * @return void
	 */
	private function register_wp_menu_top( $menu_option )
	{
		$hook = add_menu_page(
			$menu_option['page_title'],
			$menu_option['menu_title'],
			$menu_option['capability'],
			$menu_option['menu_slug'],
			array( $this, 'view_menu_page_callback' ),
			$menu_option['icon_url'],
			$menu_option['position']
		);
	}

	/**
	 * WordPressにサブメニューを登録する。
	 *
	 * @param mixed[] $menu_option メニュー設定
	 * @return void
	 */
	private function register_wp_menu_sub( $menu_option )
	{
		$hook = add_submenu_page(
			$menu_option['parent_slug'],
			$menu_option['page_title'],
			$menu_option['menu_title'],
			$menu_option['capability'],
			$menu_option['menu_slug'],
			array( $this, 'view_menu_page_callback' ),
			$menu_option['position']
		);
	}

  // ========================================================
  // ページ設定
  // ========================================================

	/**
	 * ページの初期化を行う。
	 *
	 * @return void
	 */
	private function init_page()
	{
		// ページメニュー初期化
		if ( !$this->init_page_menu() ) { return; }

		// ページタブ初期化
		if ( !$this->init_page_tab() ) { return; }

		// ページオプション初期化
		$this->init_page_option();

		// ページ表示初期化
		$this->init_page_view();
	}

	/**
	 * ページメニューの初期化を行う。
	 *
	 * @return bool 初期化結果を返す。(true:成功/false:失敗)
	 */
	private function init_page_menu()
	{
		$menu_slug = '';
		$menu_option = array();

		// ------------------------------------
		// メニュースラッグ取得
		// - - - - - - - - - - - - - - - - - -
		//   {menu-slug}
		// ------------------------------------

		$menu_slug = $this->get_page_slug();

		// チェック処理
		if ( false === $menu_slug ) { return false; }
		if ( !$this->exists_menu_slug( $menu_slug ) ) { return false; }

		// ------------------------------------
		// メニュー設定取得
		// ------------------------------------

		$menu_option = $this->get_menu_option( $menu_slug );

		// チェック処理
		if ( false === $menu_option ) { return false; }

		// ------------------------------------
		// 変数設定
		// ------------------------------------

		$this->m_page['menu']['slug'] = $menu_slug;
		$this->m_page['menu']['option'] = $menu_option;

		return true;
	}

	/**
	 * ページタブの初期化を行う。
	 *
	 * @return bool 初期化結果を返す。(true:成功/false:失敗)
	 */
	private function init_page_tab()
	{
		$tab_slug = '';
		$tab_options = array();

		// ------------------------------------
		// 事前確認
		// ------------------------------------

		// 定義チェック (OK：未定義/空/NULL)
		if ( empty( $this->m_page['menu']['option']['tabs'] ) ) { return true; }
		
		// ------------------------------------
		// タブスラッグ取得
		// - - - - - - - - - - - - - - - - - -
		//   {tab-slug}
		// ------------------------------------

		$tab_slug = $this->get_tab_slug();
		
		// チェック処理
		if ( false === $tab_slug ) {
			// 先頭タブをデフォルト値として設定
			$tab_slug = array_values( $this->m_page['menu']['option']['tabs'] )[0]['tab_slug'];
		}

		$menu_slug = $this->m_page['menu']['slug'];
		if ( !$this->exists_tab_slug( $menu_slug, $tab_slug ) ) { return false; }

		// ------------------------------------
		// タブ設定取得
		// ------------------------------------

		$tab_options = $this->m_page['menu']['option']['tabs'];

		// ------------------------------------
		// 変数設定
		// ------------------------------------

		$this->m_page['tab']['slug'] = $tab_slug;
		$this->m_page['tab']['options'] = $tab_options;

		return true;
	}

	/**
	 * ページオプションの初期化を行う。
	 *
	 * @return void
	 */
	private function init_page_option()
	{
		$option_group = '';
		$option_name = '';

		// ------------------------------------
		// オプショングループ名取得
		// - - - - - - - - - - - - - - - - - -
		//   {app-slug}_{page-slug}_group
		// ------------------------------------

		$option_group = Library_Utility::get_option_group(
			$this->m_class['app']['slug'],
			$this->m_page['menu']['slug'],
			$this->m_page['tab']['slug']
		);

		// ------------------------------------
		// オプション名取得
		// - - - - - - - - - - - - - - - - - -
		//   {app-slug}_{page-slug}_{opt-suffix}
		// ------------------------------------

		$option_name = Library_Utility::get_option_name(
			$this->m_class['app']['slug'],
			$this->m_page['menu']['slug'],
			$this->m_page['tab']['slug']
		);

		// ------------------------------------
		// 変数設定
		// ------------------------------------

		$this->m_page['option']['group'] = $option_group;
		$this->m_page['option']['name'] = $option_name;
	}

	/**
	 * ページ表示の初期化を行う。
	 *
	 * @return void
	 */
	private function init_page_view()
	{
		$page_slug = '';
		$view_option = array();

		// ------------------------------------
		// ページスラッグ取得
		// - - - - - - - - - - - - - - - - - -
		//   {menu-slug} or {menu-slug}_{tab-slug}
		// ------------------------------------

		$page_slug = Library_Utility::get_page_slug(
			$this->m_page['menu']['slug'],
			$this->m_page['tab']['slug']
		);

		// ------------------------------------
		// 表示設定取得
		// - - - - - - - - - - - - - - - - - -
		//   優先度：タブ ＞ メニュー
		// ------------------------------------

		// 表示項目：タブ
		if ( !empty( $this->m_page['tab']['options'] ) ) {
			$tab_slug = $this->m_page['tab']['slug'];
			$view_option = $this->m_page['tab']['options'][$tab_slug];
		}
		// 表示項目：メニュー
		else {
			$view_option = $this->m_page['menu']['option'];
		}

		// ------------------------------------
		// 表示補正
		// - - - - - - - - - - - - - - - - - -
		//   [tab]header_title > [menu]header_title > [menu]menu_title
		//   [tab]form_title > [tab]tab_title > header_title
		// ------------------------------------

		// 表示項目：タブ
		if ( !empty( $this->m_page['tab']['options'] ) ) {

			// ヘッダータイトル補正 (NG：未定義/空/NULL)
			//   優先度：[tab]header_title > [menu]header_title
			if ( empty( $view_option['header_title'] ) ) {
				$view_option['header_title'] = $this->m_page['menu']['option']['header_title'];
				$view_option['header_icon']  = $this->m_page['menu']['option']['header_icon'];
			}

			// フォームタイトル補正 (NG：未定義/空/NULL)
			//   優先度：[tab]form_title > [tab]tab_title
			if ( empty( $view_option['form_title'] ) ) {
				$view_option['form_title'] = $view_option['tab_title'];
				$view_option['form_icon']  = $view_option['tab_icon'];
			}

		}

		// ヘッダータイトル補正 (NG：未定義/空/NULL)
		//   優先度：header_title > [menu]menu_title
		if ( empty( $view_option['header_title'] ) ) {
			$view_option['header_title'] = $this->m_page['menu']['option']['menu_title'];
			$view_option['header_icon']  = '';
		}

		// フォームタイトル補正 (NG：未定義/空/NULL)
		//   優先度：[tab]tab_title > header_title
		if ( empty( $view_option['form_title'] ) ) {
			$view_option['form_title'] = $view_option['header_title'];
			$view_option['form_icon']  = $view_option['header_icon'];
		}

		// ------------------------------------
		// 変数設定
		// ------------------------------------

		$this->m_page['slug'] = $page_slug;
		$this->m_page['view'] = $view_option;
	}

  // ========================================================
  // オプション設定
  // ========================================================

	/**
	 * オプションの初期化を行う。
	 *
	 * @return void
	 */
	private function init_option()
	{
		add_action( 'admin_init', array( $this, 'init_option_callback' ), 11, 0 );
	}

	/**
	 * オプション設定の初期化を行う。
	 *
	 * @return void
	 */
	public final function init_option_callback()
	{
		// オプション取得
		$this->get_page_option();
	}

  // ========================================================
  // スクリプト設定
  // ========================================================

	/**
	 * スクリプトを登録する。
	 *
	 * @return void
	 */
	private function register_script()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'register_wp_script_callback' ), 11, 1 );
	}

	/**
	 * WordPressにスクリプトを登録する。
	 *
	 * @param string $hook_suffix 管理画面の接尾辞
	 * @return void
	 */
	public final function register_wp_script_callback( $hook_suffix )
	{
		// メニューページ用JavaScript
		wp_enqueue_script(
			$this->m_class['lib']['slug'],
			$this->get_uri( __DIR__ ) . '/js/admin.js',
			array(),
			$this->m_class['lib']['version'], 'all'
		);

		// メニューページ用CSS
		wp_enqueue_style(
			$this->m_class['lib']['slug'],
			$this->get_uri( __DIR__ ) . '/css/style.css',
			array(),
			$this->m_class['lib']['version'], 'all'
		);

		// FontAwesomeアイコン
		wp_enqueue_style(
			$this->m_class['lib']['slug'] . '-' . SELF::_SCRIPT_FONTAWESOME['slug'],
			SELF::_SCRIPT_FONTAWESOME['url'],
			array(),
			SELF::_SCRIPT_FONTAWESOME['version'], 'all'
		);
	}

  // ========================================================
  // ページ表示
  // ========================================================

	/**
	 * メニューページを表示する。
	 *
	 * @return void
	 */
	public final function view_menu_page_callback()
	{
		$this->view_html( sprintf( ''
			. '<div class="%s-wrap %s %s %s %s %s">'
			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $this->m_class['app']['slug'] )
			, esc_attr( $this->m_page['menu']['slug'] )
			, esc_attr( $this->m_page['tab']['slug'] )
			, esc_attr( $this->m_page['slug'] )
			, !empty( $this->m_page['tab']['slug'] ) ? 'tab' : ''
		) );

		// 定義チェック (NG：未定義/空/NULL)
		if ( !empty( $this->m_page['view']['page_file'] ) ) {
			$this->require_file( $this->m_page['view']['page_file'] );
		}

		$this->view_html( '</div>' );
	}

	/**
	 * メニューフォームを表示する。
	 *
	 * @return void
	 */
	private function view_menu_form()
	{
		// POSTデータ設定
		$this->post_settings();

		$this->view_html( sprintf( ''
			. '<form class="%s-form" method="post" action="">'
			, esc_attr( $this->m_class['lib']['slug'] )
		) );

		// 認証フィールド設定
		$this->set_nonce_field();

		// 定義チェック (NG：未定義/空/NULL)
		if ( !empty( $this->m_page['view']['form_file'] ) ) {
			$this->require_file( $this->m_page['view']['form_file'] );
		}

		$this->view_html( '</form>' );
	}

  // ========================================================
  // フォーム設定
  // ========================================================

	/**
	 * 認証フィールドを設定する。
	 *
	 * @return void
	 */
	private function set_nonce_field()
	{
		wp_nonce_field(
			$this->m_page['slug'],
			'_wpnonce_' . $this->m_page['slug']
		);
	}

	/**
	 * 認証フィールドを検証する。
	 *
	 * @return bool 検証結果を返す。(true:検証OK/false:検証NG)
	 */
	private function check_referer()
	{
		$nonce_field = '_wpnonce_' . $this->m_page['slug'];

		// POSTデータ確認 (NG：未定義/空/NULL)
		if ( empty( $_POST ) ) { return false; }

		// 認証フィールド確認 (NG：未定義/NULL)
		if ( !isset( $_POST[$nonce_field] ) ) { return false; }

		// 認証フィールド検証
		return check_admin_referer(
			$this->m_page['slug'],
			$nonce_field
		);
	}

   // ------------------------------------
   // POSTデータ処理
   // ------------------------------------

	/**
	 * POSTデータの有効性を検証する。
	 *
	 * @return bool 検証結果を返す。(true:検証OK/false:検証NG)
	 */
	private function verify_post_validity()
	{
		// POSTデータなし：通常表示 (NG：未定義/空/NULL)
		if ( empty( $_POST ) ) { return false; }

		// 認証フィールドNG：遷移異常
		if ( !$this->check_referer() ) {
			return false;
		}

		// 送信ボタンなし：規定外POST送信 (NG：未定義)
		if ( !array_key_exists( 'submit', $_POST ) ) {
			return false;
		}

		// オプション値なし：POSTデータ異常 (NG：未定義/空/NULL)
		$option_name = $this->m_page['option']['name'];
		if ( empty( $_POST[$option_name] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * POSTデータの設定処理を行う。
	 *
	 * @return void
	 */
	private function post_settings()
	{
		// ------------------------------------
		// POSTデータ検証
		// ------------------------------------

		// オプション更新 実行判定
		if ( !$this->verify_post_validity() ) { return; }

		// ------------------------------------

		// POSTデータからオプション値を取得
		$option_name = $this->m_page['option']['name'];
		$post_option = $_POST[$option_name];

		// 一時的にエスケープ処理を解除
		if ( is_array( $post_option ) ) {
			$post_option = array_map(
				array( __NAMESPACE__ . '\Library_Utility', 'unesc_decode' ),
				$post_option
			);
		}

		// ------------------------------------
		// サニタイズ処理
		// ------------------------------------

		// サニタイズ処理 (ユーザー定義)
		$sanitized_option = $this->sanitize_options(
			$post_option, $this->m_page['slug']
		);

		// ------------------------------------

		// POSTデータからページオプションを設定
		$this->set_page_option( $sanitized_option );

		// ------------------------------------
		// バリテーション処理
		// ------------------------------------

		// バリテーション処理 (ユーザー定義)
		$validated_option = $this->validate_options(
			$sanitized_option, $this->m_page['slug']
		);

		// ------------------------------------
		// オプション更新：更新実行
		// ------------------------------------

		if ( !$this->option_error_exists() ) {

			// オプション更新
			$this->update_page_option( $validated_option );
			
			// 管理画面に通知
			$notice_slug = $this->m_class['app']['slug'] . '-update-success';
			Library_Utility::notice_admin_message(
				$notice_slug, '', __( 'Settings saved.' ), 'success'
			);

		}

		// ------------------------------------
		// オプション更新：更新キャンセル
		// ------------------------------------

		else {

			// 管理画面に通知
			$notice_slug = $this->m_class['app']['slug'] . '-update-failed';
			Library_Utility::notice_admin_message(
				$notice_slug, '', __( 'Settings save failed.' ), 'error'
			);

		}
	}

  // ========================================================
  // オプション操作
  // ========================================================

	/**
	 * オプションキーを取得する。
	 *
	 * @param string $key キー
	 * @return string オプションキーを返す。
	 */
	protected final function get_option_key( $key )
	{
		return sprintf( '%s[%s]'
			, $this->m_page['option']['name']
			, $key
		);
	}

	/**
	 * ページオプションを設定する。
	 *
	 * @param mixed[] $option_value オプション値
	 * @return void
	 */
	private function set_page_option( $option_value )
	{
		// オプションを設定
		$this->m_option = [];
		if ( is_array( $option_value ) ) {
			foreach( $option_value as $key => $value ) {
				$this->m_option[$key] = SELF::_OPTION_FORMAT;
				$this->m_option[$key]['value'] = $value;
			}
		}
	}

	/**
	 * ページオプションを取得する。
	 *
	 * @return void
	 */
	private function get_page_option()
	{
		// オプション取得
		$option_name = $this->m_page['option']['name'];
		$option_value = Library_Utility::get_option( $option_name );

		// オプションを設定
		$this->set_page_option( $option_value );
	}

	/**
	 * ページオプションを更新する。
	 *
	 * @param mixed[] $option_value オプション値
	 * @return void
	 */
	private function update_page_option( $option_value )
	{
		// オプション更新
		$option_name = $this->m_page['option']['name'];
		Library_Utility::update_option( $option_name, $option_value );

		// オプションを再取得
		$this->get_page_option();
	}

   // ------------------------------------
   // オプションエラー
   // ------------------------------------

	/**
	 * オプションエラーが登録されているかチェックする。
	 *
	 * @return bool チェック結果を返す。(true:エラー有り/false:エラー無し)
	 */
	protected final function option_error_exists()
	{
		foreach( $this->m_option as $key => $option ) {
			// 設定チェック (NG：未定義/空/NULL)
			if ( !empty( $option['error'] ) ) { return true; }
		}

		return false;
	}

	/**
	 * オプションエラーを登録する。
	 *
	 * @param string $key キー
	 * @param string $message メッセージ
	 * @return void
	 */
	protected final function add_option_error( $key, $message )
	{
		$this->m_option[$key]['error'] = $message;
	}

  // ========================================================
  // ページ表示部品：メニュー
  // ========================================================

	/**
	 * ヘッダータイトルを表示する。
	 *
	 * @return void
	 */
	protected function view_header_title()
	{
		if ( empty( $this->m_page['view']['header_title'] ) ) { return; }

		$header_title = $this->m_page['view']['header_title'];
		$header_icon = $this->m_page['view']['header_icon'];

		$icon = '';
		if ( !empty( $header_icon ) ) {
			$icon = sprintf( ''
				. '<i class="%s fa-fw"></i>'
				, esc_attr( $header_icon )
			);
		}

		$this->view_html( sprintf( ''
			. '<h1 class="wp-heading-inline">%s%s</h1>'
			. '<hr class="wp-header-end">'
			, $icon
			, esc_html( $header_title )
		) );
	}

	/**
	 * ディスクリプションを表示する。
	 *
	 * @return void
	 */
	protected function view_description()
	{
		if ( empty( $this->m_page['view']['description'] ) ) { return; }

		$description = $this->m_page['view']['description'];

		$this->view_html( sprintf( ''
			. '<div class="description"><p>%s</p></div>'
			, esc_html( $description )
		) );
	}

	/**
	 * メニュータブを表示する。
	 *
	 * @return void
	 */
	protected function view_menu_tab()
	{
		if ( empty( $this->m_page['tab']['slug'] ) ) { return; }
		
		$tab_list = '';
		foreach( $this->m_page['tab']['options'] as $slug => $option ) {

			$icon = '';
			if ( !empty( $option['tab_icon'] ) ) {
				$icon = sprintf( ''
					. '<i class="%s fa-fw"></i>'
					, esc_attr( $option['tab_icon'] )
				);
			}

			$class = 'menu-tab-item';
			if ( $slug == $this->m_page['tab']['slug'] ) {
				$class .= ' now';
				$title = sprintf( ''
					. '<a>%s%s</a>'
					, $icon
					, esc_html( $option['tab_title'] )
				);
			}
			else {
				$title = sprintf( ''
					. '<a href="%s">%s%s</a>'
					, esc_url( add_query_arg( 'tab', $option['tab_slug'] ) )
					, $icon
					, esc_html( $option['tab_title'] )
				);
			}

			$tab_list .= sprintf( ''
				. '<li class="%s">%s</li>'
				, esc_attr( $class )
				, $title
			);

		}

		$this->view_html( sprintf( ''
			. '<span class="%s-tab-wrap">'
			.   '<ul class="menu-tab-list">%s</ul>'
			. '</span>'
			, esc_attr( $this->m_class['lib']['slug'] )
			, $tab_list
		) );
	}

  // ========================================================
  // ページ表示部品：フォーム
  // ========================================================

	/**
	 * フォームタイトルを表示する。
	 *
	 * @return void
	 */
	protected function view_form_title()
	{
		if ( empty( $this->m_page['view']['form_title'] ) ) { return; }

		$form_title = $this->m_page['view']['form_title'];
		$form_icon = $this->m_page['view']['form_icon'];

		$icon = '';
		if ( !empty( $form_icon ) ) {
			$icon = sprintf( ''
				. '<i class="%s fa-fw"></i>'
				, esc_attr( $form_icon )
			);
		}

		$this->view_html( sprintf( ''
			. '<h2 class="form-title">%s%s</h2>'
			, $icon
			, esc_html( $form_title )
		) );
	}

	/**
	 * 送信ボタンを表示する。
	 *
	 * @param string $label 表示ラベル
	 * @return void
	 */
	protected function submit( $label )
	{
		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap option submit">'
			. '  <span class="hr"></span>'
			. '  <input type="submit" name="submit" id="submit" class="button button-primary" value="%s">'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )
			
			, esc_html( $label )
		) );
	}

	/**
	 * チェックボックスを表示する。
	 *
	 * @param string $key キー
	 * @param string|null $label 表示ラベル
	 * @param mixed[] $attributes 属性設定
	 * @param string|null $default デフォルト値
	 * @return void
	 */
	protected function checkbox( $key, $label = '', $attributes = [], $default = '' )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;
		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// 属性設定
		// ------------------------------------

		$attr = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $attributes ) && is_array( $attributes ) ) {
			foreach( $attributes as $attr_name => $attr_val ) {
				$attr .= sprintf( '%s="%s" '
					, esc_attr( $attr_name )
					, esc_attr( $attr_val )
				);
			}
		}

		// ------------------------------------
		// チェック判定
		// ------------------------------------

		$check = ( 'yes' === $option_value ) ? ' checked' : '';

		// 無効設定時は強制的にチェック解除
		if ( array_key_exists( 'disabled', $attributes ) ) { $check = ''; }

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap checkbox">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <input type="hidden" name="%s" value="no">'
			. '      <input type="checkbox" id="%s" name="%s" value="yes" %s %s>'
			. '      <label for="%s" class="title">%s</label>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_key )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, $attr, esc_attr( $check )

			, esc_attr( $option_id ), esc_html( $label )

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * スイッチを表示する。
	 *
	 * @param string $key キー
	 * @param string|null $label 表示ラベル
	 * @param mixed[] $attributes 属性設定
	 * @param string|null $default デフォルト値
	 * @return void
	 */
	protected function switch( $key, $label = '', $attributes = [], $default = '' )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;
		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// 属性設定
		// ------------------------------------

		$attr = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $attributes ) && is_array( $attributes ) ) {
			foreach( $attributes as $attr_name => $attr_val ) {
				$attr .= sprintf( '%s="%s" '
					, esc_attr( $attr_name )
					, esc_attr( $attr_val )
				);
			}
		}

		// ------------------------------------
		// チェック判定
		// ------------------------------------

		$check = ( 'yes' === $option_value ) ? ' checked' : '';

		// 無効設定時は強制的にチェック解除
		if ( array_key_exists( 'disabled', $attributes ) ) { $check = ''; }

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap switch">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <input type="hidden" name="%s" value="no">'
			. '      <input type="checkbox" id="%s" name="%s" value="yes" %s %s>'
			. '      <label for="%s" class="switchbox">'
			. '        <span class="switchbox-label"></span>'
			. '        <span class="switchbox-circle"></span>'
			. '      </label>'
			. '      <label for="%s" class="title">%s</label>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_key )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, $attr, esc_attr( $check )

			, esc_attr( $option_id )

			, esc_attr( $option_id ), esc_html( $label )

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * 数値フィールドを表示する。
	 *
	 * @param string $key キー
	 * @param mixed[] $attributes 属性設定
	 * @param integer $size フィールドサイズ [0~100(%)]
	 * @param integer $default デフォルト値 [0]
	 * @return void
	 */
	protected function number( $key, $attributes = [], $size = '', $default = 0 )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;
		$option_value = intval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) && is_numeric( $option['value'] ) ) {
			$option_value = intval( $option['value'] );
		}

		// ------------------------------------
		// 属性設定
		// ------------------------------------

		$attr = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $attributes ) && is_array( $attributes ) ) {
			foreach( $attributes as $attr_name => $attr_val ) {
				$attr .= sprintf( '%s="%s" '
					, esc_attr( $attr_name )
					, esc_attr( $attr_val )
				);
			}
		}

		// ------------------------------------
		// スタイル設定
		// ------------------------------------

		$style = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $size ) && is_numeric( $size ) ) {
			// 範囲補正 [0~100(%)]
			if ( 0 > $size ) { $size = 0; }
			if ( 100 < $size ) { $size = 100; }

			$style .= sprintf( 'width:%s%%;'
				, esc_attr( intval( $size ) )
			);
		}

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap number" style="%s">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <input type="number" id="%s" name="%s" value="%d" %s>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $style )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, esc_attr( intval( $option_value ) )
			, $attr

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * テキストフィールドを表示する。
	 *
	 * @param string $key キー
	 * @param mixed[] $attributes 属性設定
	 * @param integer $size フィールドサイズ [0~100(%)]
	 * @param string $default デフォルト値
	 * @return void
	 */
	protected function text( $key, $attributes = [], $size = '', $default = '' )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;
		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// 属性設定
		// ------------------------------------

		$attr = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $attributes ) && is_array( $attributes ) ) {
			foreach( $attributes as $attr_name => $attr_val ) {
				$attr .= sprintf( '%s="%s" '
					, esc_attr( $attr_name )
					, esc_attr( $attr_val )
				);
			}
		}

		// ------------------------------------
		// スタイル設定
		// ------------------------------------

		$style = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $size ) && is_numeric( $size ) ) {
			// 範囲補正 [0~100(%)]
			if ( 0 > $size ) { $size = 0; }
			if ( 100 < $size ) { $size = 100; }

			$style .= sprintf( 'width:%s%%;'
				, esc_attr( intval( $size ) )
			);
		}

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap text" style="%s">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <input type="text" id="%s" name="%s" value="%s" %s>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $style )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, esc_html( $option_value )
			, $attr

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * テキストエリアを表示する。
	 *
	 * @param string $key キー
	 * @param mixed[] $attributes 属性設定
	 * @param integer $size フィールドサイズ [0~100(%)]
	 * @param string $default デフォルト値
	 * @return void
	 */
	protected function textarea( $key, $attributes = [], $size = '', $default = '' )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;
		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// 属性設定
		// ------------------------------------

		$attr = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $attributes ) && is_array( $attributes ) ) {
			foreach( $attributes as $attr_name => $attr_val ) {
				$attr .= sprintf( '%s="%s" '
					, esc_attr( $attr_name )
					, esc_attr( $attr_val )
				);
			}
		}

		// ------------------------------------
		// スタイル設定
		// ------------------------------------

		$style = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $size ) && is_numeric( $size ) ) {
			// 範囲補正 [0~100(%)]
			if ( 0 > $size ) { $size = 0; }
			if ( 100 < $size ) { $size = 100; }

			$style .= sprintf( 'width:%s%%;'
				, esc_attr( intval( $size ) )
			);
		}

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap textarea" style="%s">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <textarea id="%s" name="%s" %s>%s</textarea>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $style )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, $attr
			, esc_textarea( $option_value )

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * ラジオボタンを表示する。
	 *
	 * @param string $key キー
	 * @param mixed[] $labels 表示ラベル
	 * @param boolean $list リスト表示 (true:縦/false:横) [true]
	 * @param mixed|null $default デフォルト値 [null]
	 * @return void
	 */
	protected function radio( $key, $labels = [], $list = true, $default = null )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;

		// デフォルト値補正 (NG：未定義/NULL)
		if ( !isset( $default ) || !array_key_exists( $default, $labels ) ) {
			// 先頭要素をデフォルト値に設定
			$default = array_key_first( $labels );
		}

		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) && array_key_exists( $option['value'], $labels ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// ラジオボタン生成
		// ------------------------------------

		$radio_list = '';

		foreach( $labels as $label_val => $label ) {

			$label_id = sprintf( "%s_%s", $option_id, $label_val );

			// チェック判定
			$check = ( $label_val == $option_value ) ? ' checked' : '';

			$list_item = sprintf( ''
				. '<input type="radio" id="%s" name="%s" value="%s" %s>'
				. '<label for="%s" class="title">%s</label>'
				, esc_attr( $label_id ), esc_attr( $option_key )
				, esc_attr( $label_val ), esc_attr( $check )
				, esc_attr( $label_id ), esc_html( $label )
			);

			if ( $list ) {
				$radio_list .= sprintf( ''
					. '<span class="radio-item list">%s</span>'
					, $list_item
				);
			}
			else {
				$radio_list .= sprintf( ''
					. '<span class="radio-item">%s</span>'
					, $list_item
				);
			}

		}

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap radio">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">%s</span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, $radio_list

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * セレクトボックスを表示する。
	 *
	 * @param string $key キー
	 * @param mixed[] $labels 表示ラベル
	 * @param mixed|null $default デフォルト値 [null]
	 * @return void
	 */
	protected function select( $key, $labels = [], $default = null )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;

		// デフォルト値補正 (NG：未定義/NULL)
		if ( !isset( $default ) || !array_key_exists( $default, $labels ) ) {
			// 先頭要素をデフォルト値に設定
			$default = array_key_first( $labels );
		}

		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) && array_key_exists( $option['value'], $labels ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// セレクトボックス生成
		// ------------------------------------

		$select_list = '';

		foreach( $labels as $label_val => $label ) {

			// 選択判定
			$select = ( $label_val == $option_value ) ? ' selected' : '';

			$select_list .= sprintf( ''
				. '<option value="%s" %s>%s</option>'
				, esc_attr( $label_val ), esc_attr( $select ), esc_html( $label )
			);

		}

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap select">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <select id="%s" name="%s">%s</select>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, $select_list

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * 非表示フィールドを表示する。
	 *
	 * @param string $key キー
	 * @param string $default デフォルト値
	 * @return void
	 */
	protected function hidden( $key, $default = '' )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;
		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap hidden">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <input type="hidden" id="%s" name="%s" value="%s" readonly>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, esc_html( $option_value )

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * コピーテキストフィールドを表示する。
	 *
	 * @param string $key キー
	 * @param mixed[] $attributes 属性設定
	 * @param integer $size フィールドサイズ [0~100(%)]
	 * @param string $default デフォルト値
	 * @return void
	 */
	protected function copy_text( $key, $attributes = [], $size = '', $default = '' )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;
		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// 属性設定
		// ------------------------------------

		$attr = '';

		// 読み取り専用
		$attributes = array_merge(
			$attributes, array( 'readonly' => 'readonly' )
		);

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $attributes ) && is_array( $attributes ) ) {
			foreach( $attributes as $attr_name => $attr_val ) {
				$attr .= sprintf( '%s="%s" '
					, esc_attr( $attr_name )
					, esc_attr( $attr_val )
				);
			}
		}

		// ------------------------------------
		// スタイル設定
		// ------------------------------------

		$style = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $size ) && is_numeric( $size ) ) {
			// 範囲補正 [0~100(%)]
			if ( 0 > $size ) { $size = 0; }
			if ( 100 < $size ) { $size = 100; }

			$style .= sprintf( 'width:%s%%;'
				, esc_attr( intval( $size ) )
			);
		}

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap text copy" style="%s">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <input type="text" class="nt-copy-target" id="%s" name="%s" value="%s" %s>'
			. '      <button type="button" class="nt-copy-button button-primary">'
			. '        <i class="fa-regular fa-clipboard"></i>'
			. '        <i class="fa-solid fa-clipboard-check hidden"></i>'
			. '      </button>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $style )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, esc_html( $option_value )
			, $attr

			, esc_html( $option['error'] )
		) );
	}

	/**
	 * コピーテキストエリアを表示する。
	 *
	 * @param string $key キー
	 * @param mixed[] $attributes 属性設定
	 * @param integer $size フィールドサイズ [0~100(%)]
	 * @param string $default デフォルト値
	 * @return void
	 */
	protected function copy_textarea( $key, $attributes = [], $size = '', $default = '' )
	{
		// ------------------------------------
		// オプション設定 取得
		// ------------------------------------

		$option = SELF::_OPTION_FORMAT;

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option = $this->m_option[$key];
		}

		// ------------------------------------
		// オプション値 取得
		// ------------------------------------

		$option_key = $this->get_option_key( $key );
		$option_id = $key;
		$option_value = strval( $default );

		// 保存値チェック
		if ( array_key_exists( $key, $this->m_option ) ) {
			$option_value = strval( $option['value'] );
		}

		// ------------------------------------
		// 属性設定
		// ------------------------------------

		$attr = '';

		// 読み取り専用
		$attributes = array_merge(
			$attributes, array( 'readonly' => 'readonly' )
		);

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $attributes ) && is_array( $attributes ) ) {
			foreach( $attributes as $attr_name => $attr_val ) {
				$attr .= sprintf( '%s="%s" '
					, esc_attr( $attr_name )
					, esc_attr( $attr_val )
				);
			}
		}

		// ------------------------------------
		// スタイル設定
		// ------------------------------------

		$style = '';

		// 引数チェック (NG：未定義/空/NULL)
		if ( !empty( $size ) && is_numeric( $size ) ) {
			// 範囲補正 [0~100(%)]
			if ( 0 > $size ) { $size = 0; }
			if ( 100 < $size ) { $size = 100; }

			$style .= sprintf( 'width:%s%%;'
				, esc_attr( intval( $size ) )
			);
		}

		// ------------------------------------
		// エラー判定
		// ------------------------------------

		$error = ( !empty( $option['error'] ) ) ? 'invalid' : '';

		// ------------------------------------
		// HTML表示
		// ------------------------------------

		$this->view_html( sprintf( ''
			. '<span class="%s-form-option-wrap textarea copy" style="%s">'
			. '  <span class="%s-form-option-input %s">'
			. '    <span class="input-group">'
			. '      <textarea class="nt-copy-target" id="%s" name="%s" %s>%s</textarea>'
			. '      <button type="button" class="nt-copy-button button-primary">'
			. '        <i class="fa-regular fa-clipboard"></i>'
			. '        <i class="fa-solid fa-clipboard-check hidden"></i>'
			. '      </button>'
			. '    </span>'
			. '    <span class="input-error">%s</span>'
			. '  </span>'
			. '</span>'

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $style )

			, esc_attr( $this->m_class['lib']['slug'] )
			, esc_attr( $error )

			, esc_attr( $option_id ), esc_attr( $option_key )
			, $attr
			, esc_textarea( $option_value )

			, esc_html( $option['error'] )
		) );
	}

  // ========================================================
  // ユーティリティ：メニュー
  // ========================================================

	/**
	 * メニュースラッグが存在するかチェックする。
	 * 
	 * @param string $menu_slug メニュースラッグ : {menu-slug}
	 * @return bool チェック結果を返す。(true:有り/false:無し)
	 */
	protected final function exists_menu_slug( $menu_slug )
	{
		foreach( $this->m_menu as $type => $menu ) {
			if ( array_key_exists( $menu_slug, $menu ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * タブスラッグが存在するかチェックする。
	 * 
	 * @param string $menu_slug メニュースラッグ : {menu-slug}
	 * @param string $tab_slug タブスラッグ : {tab-slug}
	 * @return bool チェック結果を返す。(true:有り/false:無し)
	 */
	protected final function exists_tab_slug( $menu_slug, $tab_slug )
	{
		$menu_option = $this->get_menu_option( $menu_slug );
		if ( false === $menu_option ) { return false; }

		if ( array_key_exists( $tab_slug, $menu_option['tabs'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * メニュー設定を取得する。
	 *
	 * @param mixed[] $menu_option メニュー設定 : {menu-slug}
	 * @return mixed[]|bool メニュー設定を返す。(false:失敗)
	 */
	protected final function get_menu_option( $menu_slug )
	{
		// サブメニュー
		if ( array_key_exists( $menu_slug, $this->m_menu['sub'] ) ) {
			return $this->m_menu['sub'][$menu_slug];
		}

		// トップメニュー
		if ( array_key_exists( $menu_slug, $this->m_menu['top'] ) ) {
			return $this->m_menu['top'][$menu_slug];
		}

		return false;
	}

  // ========================================================
  // ユーティリティ
  // ========================================================

	/**
	 * 配列要素を更新する。
	 * 
	 * 元配列に存在するデータのみ更新(上書き)を行う。
	 *
	 * @param mixed[] $dst コピー先の配列
	 * @param mixed[] $src コピー元の配列
	 * @return mixed[] 更新した配列を返す。
	 */
	protected final function array_update( $dst, $src )
	{
		foreach( $dst as $key => $value ) {
			if ( array_key_exists( $key, $src ) ) {
				$dst[$key] = $src[$key];
			}
		}
		return $dst;
	}

	/**
	 * ページスラッグを取得する。
	 *
	 * @return string|bool ページスラッグを返す。(false:失敗)
	 */
	protected final function get_page_slug()
	{
		// 定義チェック (NG：未定義/空/NULL)
		return empty( $_GET['page'] ) ? false :$_GET['page'];
	}

	/**
	 * タブスラッグを取得する。
	 *
	 * @return string|bool タブスラッグを返す。(false:失敗)
	 */
	protected final function get_tab_slug()
	{
		// 定義チェック (NG：未定義/空/NULL)
		return empty( $_GET['tab'] ) ? false : $_GET['tab'];
	}

	/**
	 * ファイルを読み込む。
	 *
	 * @param string $file 読み込みファイル (絶対パス)
	 * @return void
	 */
	protected final function require_file( $file_path )
	{
		// ファイルチェック (NG：未定義/空/NULL)
		if ( empty( $file_path ) ) { return; }
		if ( file_exists( $file_path ) ) { require_once( $file_path ); }
	}

	/**
	 * ディレクトリのURIを取得する。
	 *
	 * @param [type] $path ディレクトリパス (絶対パス)
	 * @return string ディレクトリのURIを返す。
	 */
	protected final function get_uri( $path )
	{
		return preg_replace(
			array( '/^.+[\/\\\]wp-content[\/\\\]/', '/[\/\\\]/' ),
			array( content_url() . '/', '/' ),
			$path
		);
	}

	/**
	 * HTMLコードを表示する。
	 * 
	 * @param string $html HTMLコード
	 * @return void
	 */
	protected final function view_html( $html )
	{
		echo wp_kses( 
			trim( $html ),
			$this->m_kses_allowed_html
		);
	}

  // ========================================================

} endif;

// ============================================================================
// ライブラリ用ユーティリティクラス：Library_Utility
// ============================================================================

if ( !class_exists( __NAMESPACE__ . '\Library_Utility' ) ):
class Library_Utility {

	/**
	 * オプショングループ名を取得する。
	 *
	 * @param string $prefix プレフィックス : {app-slug}
	 * @param string $menu_slug メニュースラッグ : {menu-slug}
	 * @param string $tab_slug タブスラッグ : {tab-slug}
	 * @return string オプショングループ名を返す。
	 */
	public static function get_option_group( $prefix, $menu_slug, $tab_slug = '' )
	{
		return sprintf( '%s_%s_%s'
			, $prefix
			, Library_Utility::get_page_slug( $menu_slug, $tab_slug )
			, _OPTION_GROUP_SUFFIX
		);
	}

	/**
	 * オプション名を取得する。
	 *
	 * @param string $prefix プレフィックス : {app-slug}
	 * @param string $menu_slug メニュースラッグ : {menu-slug}
	 * @param string $tab_slug タブスラッグ : {tab-slug}
	 * @return string オプション名を返す。
	 */
	public static function get_option_name( $prefix, $menu_slug, $tab_slug = '' )
	{
		return sprintf( '%s_%s_%s'
			, $prefix
			, Library_Utility::get_page_slug( $menu_slug, $tab_slug )
			, _OPTION_NAME_SUFFIX
		);
	}

	/**
	 * ページスラッグを取得する。
	 *
	 * @param string $menu_slug メニュースラッグ : {menu-slug}
	 * @param string $tab_slug タブスラッグ : {tab-slug}
	 * @return string ページスラッグを返す。
	 */
	public static function get_page_slug( $menu_slug, $tab_slug = '' )
	{
		return empty( $tab_slug )
			? $menu_slug
			: $menu_slug . '_' . $tab_slug;
	}

	/**
	 * オプション値を取得する。
	 * 
	 * @param string $option_name オプション名
	 * @return mixed[] オプション値を返す。
	 */
	public static function get_option( $option_name )
	{
		// WordPressデータベース取得
		$option_value = get_option( $option_name );
		if ( false === $option_value ) { return []; }

		// ------------------------------------
		// デコード処理
		// ------------------------------------

		// JSON形式の文字列をデコード
		$option_value = @json_decode( $option_value, true );

		// アンエスケープ・デコード
		if ( is_array( $option_value ) ) {
			$option_value = array_map(
				array( __NAMESPACE__ . '\Library_Utility', 'unesc_decode' ),
				$option_value
			);
		}

		// ------------------------------------

		return $option_value;
	}

	/**
	 * オプション値を更新する。
	 *
	 * @param string $option_name オプション名
	 * @param mixed[] $option_value オプション値
	 * @return void
	 */
	public static function update_option( $option_name, $option_value )
	{
		// ------------------------------------
		// エンコード処理
		// ------------------------------------

		// エスケープ・エンコード
		if ( is_array( $option_value ) ) {
			$option_value = array_map(
				array( __NAMESPACE__ . '\Library_Utility', 'esc_encode' ),
				$option_value
			);
		}

		// JSON形式の文字列にエンコード
		$option_value = @json_encode( $option_value );

		// ------------------------------------

		// WordPressデータベース更新
		update_option( $option_name, $option_value );
	}

	/**
	 * 文字列のエスケープ/エンコード処理を行う。
	 *
	 * @param string $string 文字列
	 * @return string エスケープ処理した文字列を返す。
	 */
	public static function esc_encode( $string )
	{
		if ( !is_string( $string ) ) { return $string; }

		// エンコード
		$string = htmlspecialchars( $string, ENT_QUOTES, 'UTF-8' );

		// エスケープ
		$string = addslashes( $string );

		return $string;
	}

	/**
	 * 文字列のアンエスケープ/デコード処理を行う。
	 *
	 * @param string $string 文字列
	 * @return string アンエスケープ処理した文字列を返す。
	 */
	public static function unesc_decode( $string )
	{
		if ( !is_string( $string ) ) { return $string; }

		// アンエスケープ
		$string = stripslashes( $string );

		// デコード
		$string = htmlspecialchars_decode( $string, ENT_QUOTES );

		return $string;
	}

	/**
	 * 文字列の空白文字を除去する。
	 *
	 * @param string $string 文字列
	 * @param boolean $remove_all 完全削除フラグ
	 * @return string 除去処理した文字列を返す。
	 */
	public static function strip_whitespace( $string, $remove_all = false )
	{
		if ( !is_string( $string ) ) { return $string; }

		// 先頭&末尾の空白文字を除去
		$string = preg_replace( '/\A\s+|\s+\z/u', '', $string );

		// 空白文字を完全削除
		if ( $remove_all ) {
			$string = preg_replace( '/\s/u', '', $string );
		}

		// 連続する空白文字を削除 (全角文字を事前に半角変換)
		else {
			$string = preg_replace( '/\s/u', ' ', $string );
			$string = preg_replace( '/\s+/u', ' ', $string );
		}

		return $string;
	}

	/**
	 * 管理画面にメッセージを通知する。
	 *
	 * @param string $slug スラッグ名
	 * @param string $code 識別コード名 (HTMLのid属性)
	 * @param string $message メッセージ
	 * @param string $type メッセージ種別 (error/success/warning/info) [error]
	 * @return void
	 */
	public static function notice_admin_message( $slug, $code, $message, $type = 'error' )
	{
		if ( !in_array( $type, [ 'error', 'success', 'warning', 'info' ] ) ) { return; }

		// メッセージ設定
		add_settings_error( $slug, $code, $message, $type );
	
		// メッセージ表示
		settings_errors( $slug );
	}

} endif;

// ============================================================================
// PHP バージョン互換性対応
// ============================================================================

// ------------------------------------
// array_key_first : (PHP 7 >= 7.3.0, PHP 8)
// ------------------------------------

if ( !function_exists( 'array_key_first' ) ) {
	function array_key_first( array $arr ) {
		foreach ( $arr as $key => $unused ) {
			return $key;
		}
		return NULL;
	}
}
