<?php
namespace _Nt\WpPlg\WPCF7SN;
if ( !defined( 'ABSPATH' ) ) exit;

class Utility {

	/**
	 * Contact Form 7 の投稿情報を取得する。
	 *
	 * @return WP_Post[] Contact Form 7 の投稿オブジェクトを返す。
	 */
	public static function get_wpcf7_posts()
	{
		return get_posts( array(
			'post_type'      => 'wpcf7_contact_form',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'posts_per_page' => -1,
			'offset'         => 0,
		) );
	}

}
