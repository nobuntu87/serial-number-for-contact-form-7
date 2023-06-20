<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

// ============================================================================
// REST API 制御クラス：REST_Controller
// ============================================================================

class REST_Controller {

  // ========================================================
  // Contact Form 7 プラグインフック設定
  // ========================================================

   // ------------------------------------
   // フィルターフック
   // ------------------------------------

	/**
	 * カスタムDOMイベントのAPIレスポンスを設定する。
	 * 
	 * [Filter Hook] wpcf7_refill_response / wpcf7_feedback_response
	 *
	 * @param mixed[] $items APIレスポンス情報
	 * @return mixed[] APIレスポンス情報を返す。
	 */
	public static function set_dom_api_response( $items )
	{
		if ( !is_array( $items ) ) { return $items; }

		// ------------------------------------
		// コンタクトフォーム取得
		// ------------------------------------

		// コンタクトフォーム設定取得
		$contact_form = Utility::get_wpcf7_submission_contact_form();
		if ( !$contact_form ) { return $items; }

		if ( strval( $contact_form->id ) !== strval( $items['contact_form_id'] ) ) { return $items; }

		// コンタクトフォーム設定取得 (シリアル番号)
		$serial_num = Utility::get_wpcf7_submission_posted_data( _POST_FIELD );
		if ( empty( $serial_num ) ) { return $items; }

		// ------------------------------------
		// APIレスポンス設定
		// ------------------------------------

		$items['serial_number'] = rawurlencode( $serial_num );

		// ------------------------------------

		return $items;
	}

  // ========================================================

}
