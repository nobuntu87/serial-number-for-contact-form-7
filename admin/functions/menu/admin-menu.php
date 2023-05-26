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
			'parent_slug'  => _EXTERNAL_PLUGIN['wpcf7']['menu_slug'],
			'menu_slug'    => _ADMIN_MENU_SLUG,
			'page_title'   => __( 'Serial Number for Contact Form 7', _TEXT_DOMAIN ),
			'menu_title'   => __( 'Serial Number Settings', _TEXT_DOMAIN ),
			// ページ設定
			'header_title' => __( 'Serial Number for Contact Form 7', _TEXT_DOMAIN ),
			'header_icon'  => 'fa-solid fa-barcode',
		) );

	  // --------------------------------------
	  // [TOP:WPCF7] > [SUB:設定] > [TAB:フォーム設定]
	  // --------------------------------------

		foreach ( Utility::get_wpcf7_posts() as $wpcf7_post ) {

			$form_id = strval( $wpcf7_post->ID );

			$tab_slug = sprintf( '%s%s', _ADMIN_MENU_TAB_PREFIX , $form_id );

			$tab_title = strval( $wpcf7_post->post_title );

			$form_title = sprintf( '%s ( %s ) [ CF7-ID : %s ]'
				, __( 'Serial Number Settings', _TEXT_DOMAIN )
				, $tab_title , $form_id
			);

			$this->add_menu_tab( array(
				// タブ設定
				'parent_slug'  => _ADMIN_MENU_SLUG,
				'tab_slug'     => esc_attr( $tab_slug ),
				'tab_title'    => esc_attr( $tab_title ),
				'tab_icon'     => 'fa-brands fa-wpforms',
				// ページ設定
				'description'  => __( 'Copy and paste the mail-tag anywhere in the mail template.', _TEXT_DOMAIN ),
				'page_file'    => __DIR__ . '/menu-page.php',
				// フォーム設定
				'form_title'   => esc_attr( $form_title ),
				'form_icon'    => 'fa-solid fa-barcode',
				'form_file'    => SELF::_SETTING_FORM_FILE,
			) );

		}
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
  // ユーティリティ
  // ========================================================

	/**
	 * コンタクトフォームIDを取得する。
	 *
	 * @return string コンタクトフォームIDを返す。
	 */
	protected final function get_form_id()
	{
		preg_match( _ADMIN_MENU_REGEX['tab_slug'], $this->m_page['tab']['slug'], $matches );

		return strval( $matches['form_id'] );
	}

  // ========================================================

}

// 管理メニュー生成
$admin_menu = new Admin_Menu(
	_PREFIX['_'], _VERSION
);
