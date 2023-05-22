<?php
namespace _Nt\WpPlg\WPCF7SN;
if( !defined( 'ABSPATH' ) ) exit;

// ========================================================
// 管理メニュー
// ========================================================

class Admin_Menu extends Admin_Menu_Base {

  // ========================================================
  // 定数定義
  // ========================================================

	private const _ADMIN_MENU_SLUG = 'wpcf7';

	private const _SETTING_FORM_FILE = __DIR__ . '/form-setting.php';

  // ========================================================
  // メニュー設定
  // ========================================================

	/**
	 * メニューの追加を行う。
	 *
	 * @return void
	 */
	protected function add_menu()
	{
	  // --------------------------------------
	  // [TOP:WPCF7] > [SUB:設定]
	  // --------------------------------------

		$this->add_sub_menu( array(
			// メニュー設定
			'parent_slug'  => SELF::_ADMIN_MENU_SLUG,
			'menu_slug'    => _PREFIX['-'],
			'page_title'   => __( 'Serial Number for Contact Form 7', _TEXT_DOMAIN ),
			'menu_title'   => __( 'Serial Number Settings', _TEXT_DOMAIN ),
			// ページ設定
			'header_title' => __( 'Serial Number for Contact Form 7', _TEXT_DOMAIN ),
			'header_icon'  => 'fa-solid fa-barcode',
			'description'  => __( 'Copy and paste the mail-tag anywhere in the mail template.', _TEXT_DOMAIN ),
			'page_file'    => __DIR__ . '/menu-page.php',
			// フォーム設定
			'form_title'   => __( 'Serial Number Settings', _TEXT_DOMAIN ),
			'form_icon'    => 'fa-solid fa-barcode',
			'form_file'    => SELF::_SETTING_FORM_FILE,
		) );
	}

  // ========================================================
  // サニタイズ設定
  // ========================================================

	/**
	 * オプション値のサニタイズ処理を行う。
	 *
	 * @param mixed[] $options オプション値
	 * @param string $page_slug ページスラッグ : {menu-slug}_{tab-slug}
	 * @return mixed[] オプション値を返す。
	 */
	protected function sanitize_options( $options, $page_slug )
	{
		return $options;
	}

  // ========================================================

}

// 管理メニュー生成
$admin_menu = new Admin_Menu(
	_PREFIX['_'], _VERSION
);
