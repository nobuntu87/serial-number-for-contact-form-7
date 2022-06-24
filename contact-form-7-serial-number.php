<?php
/*
Plugin Name: Contact Form 7 Serial Number Addon
Plugin URI: 
Description: 
Author: Nobuntu
Author URI: https://profiles.wordpress.org/nobuntu87/
Text Domain: contact-form-7-serial-number
Domain Path: /languages/
Version: 0.0.0
License: GPL2+ (GNU General Public License v2 or later)
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit;


// [contact-form-7-serial-number]
define( 'NT_WPCF7SN_TEXT_DOMAIN', 'contact-form-7-serial-number' );

// [\~\wp-content\plugins\contact-form-7-serial-number\contact-form-7-serial-number.php]
define( 'NT_WPCF7SN_PLUGIN', __FILE__ );

// [contact-form-7-serial-number/contact-form-7-serial-number.php]
define( 'NT_WPCF7SN_PLUGIN_BASENAME', plugin_basename( NT_WPCF7SN_PLUGIN ) );

// [contact-form-7-serial-number]
define( 'NT_WPCF7SN_PLUGIN_NAME', trim( dirname( NT_WPCF7SN_PLUGIN_BASENAME ), '/' ) );

// [\~\wp-content\plugins\contact-form-7-serial-number]
define( 'NT_WPCF7SN_PLUGIN_DIR', untrailingslashit( dirname( NT_WPCF7SN_PLUGIN ) ) );


require_once NT_WPCF7SN_PLUGIN_DIR . '/load.php';
