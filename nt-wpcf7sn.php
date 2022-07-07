<?php
/*
Plugin Name: Serial Number for Contact Form 7
Plugin URI: 
Description: An add-on for the Contact Form 7 plugin. Add a mail-tag to display the serial number.
Author: Nobuntu
Author URI: https://profiles.wordpress.org/nobuntu87/
Text Domain: serial-number-for-contact-form-7
Domain Path: /languages/
Version: 0.1.0
License: GPL2+ (GNU General Public License v2 or later)
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * プラグイン定義：設定関連
 */
define( 'NT_WPCF7SN_VERSION', '0.1.0' );

define( 'NT_WPCF7SN_REQUIRED_WP_VERSION', '5.9' );

define( 'NT_WPCF7SN_TEXT_DOMAIN', 'serial-number-for-contact-form-7' );

define( 'NT_WPCF7SN_PREFIX', array(
	'_' => 'nt_wpcf7sn',
	'-' => 'nt-wpcf7sn',
) );

/**
 * プラグイン定義：パス関連
 * 
 * _PLUGIN          : ~\wp-content\plugins\plugin-name\plugin-name.php
 * _PLUGIN_BASENAME : plugin-name\plugin-name.php
 * _PLUGIN_NAME     : plugin-name
 * _PLUGIN_DIR      : ~\wp-content\plugins\plugin-name
 * _PLUGIN_URL      : ~/wp-content/plugins/plugin-name
 */
define( 'NT_WPCF7SN_PLUGIN', __FILE__ );
define( 'NT_WPCF7SN_PLUGIN_BASENAME', plugin_basename( NT_WPCF7SN_PLUGIN ) );
define( 'NT_WPCF7SN_PLUGIN_NAME', trim( dirname( NT_WPCF7SN_PLUGIN_BASENAME ), '/' ) );
define( 'NT_WPCF7SN_PLUGIN_DIR', untrailingslashit( dirname( NT_WPCF7SN_PLUGIN ) ) );
define( 'NT_WPCF7SN_PLUGIN_URL', untrailingslashit( plugins_url( '', NT_WPCF7SN_PLUGIN ) ) );

/**
 * プラグイン定義：オプション関連
 */
define( 'NT_WPCF7SN_FORM_OPTION_NAME', NT_WPCF7SN_PREFIX['_'] . '_form_' );

define( 'NT_WPCF7SN_FORM_OPTION', array(
	'type'      => array( 'default' => 0,  'type' => 'integer' ),
	'count'     => array( 'default' => 0,  'type' => 'integer' ),
	'digits'    => array( 'default' => 1,  'type' => 'integer' ),
	'prefix'    => array( 'default' => '', 'type' => 'string'  ),
	'separator' => array( 'default' => '', 'type' => 'string'  ),
	'year2dig'  => array( 'default' => '', 'type' => 'string'  ),
	'nocount'   => array( 'default' => '', 'type' => 'string'  ),
) );

define( 'NT_WPCF7SN_MAIL_TAG', '_serial_number_' );

define( 'NT_WPCF7SN_POST_FIELD', 'serial-number' );


/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/load.php';
