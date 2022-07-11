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
	'type'      => array( 'default' => 0,  'type' => 'integer', 'pattern' => '^[0-4]$'                      ),
	'count'     => array( 'default' => 0,  'type' => 'integer', 'pattern' => '^[0-9]{1,5}$'                 ),
	'digits'    => array( 'default' => 1,  'type' => 'integer', 'pattern' => '^[1-9]$'                      ),
	'prefix'    => array( 'default' => '', 'type' => 'string',  'pattern' => '^(?!.*[\\\"&\'<>])\S{0,10}$'  ),
	'separator' => array( 'default' => '', 'type' => 'string',  'pattern' => '^(|yes)$'                     ),
	'year2dig'  => array( 'default' => '', 'type' => 'string',  'pattern' => '^(|yes)$'                     ),
	'nocount'   => array( 'default' => '', 'type' => 'string',  'pattern' => '^(|yes)$'                     ),
) );

define( 'NT_WPCF7SN_MAIL_TAG', '_serial_number_' );

define( 'NT_WPCF7SN_POST_FIELD', 'serial-number' );

/**
 * プラグイン定義：サニタイズ関連
 */
define( 'NT_WPCF7SN_ALLOWED_HTML', wp_kses_allowed_html( 'post' ) + array(
	'input' => array(
		'class' => array(),
		'id' => array(),
		'type' => array(),
		'name' => array(),
		'value' => array(),
		'checked' => array(),
		'size' => array(),
		'maxlength' => array(),
		'pattern' => array(),
	),
	'form' => array(
		'class' => array(),
		'id' => array(),
		'method' => array(),
		'action' => array(),
	),
) );


/**
 * ファイル読み込み
 */
require_once NT_WPCF7SN_PLUGIN_DIR . '/load.php';
