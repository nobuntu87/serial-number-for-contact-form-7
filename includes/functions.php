<?php
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * POSTデータからコンタクトフォーム情報を取得する。
 *
 * @return mixed[] コンタクトフォームのPOSTデータを返す。
 */
function nt_wpcf7sn_get_posts_wpcf7() {
	$wpcf7_posts = get_posts( array(
		'post_type'      => 'wpcf7_contact_form',
		'post_status'    => 'publish',
		'orderby'        => 'ID',
		'order'          => 'ASC',
		'posts_per_page' => -1,
		'offset'         => 0,
	) );
	
	return $wpcf7_posts;
}
