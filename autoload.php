<?php
/**
 * Plugin Name: Add RichText Toolbar Button
 * Plugin URI: https://wordpress.org/plugins/add-richtext-toolbar-button
 * Description: This plugin makes it easy to add RichText toolbar button.
 * Author: Technote
 * Version: 1.2.0
 * Author URI: https://technote.space
 * Text Domain: add-richtext-toolbar-button
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}

define( 'ADD_RICHTEXT_TOOLBAR_BUTTON', 'Richtext_Toolbar_Button' );

@require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

WP_Framework::get_instance( ADD_RICHTEXT_TOOLBAR_BUTTON, __FILE__, 'artb' );
