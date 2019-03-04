<?php
/**
 * @version 1.0.0
 * @author Technote
 * @since 1.0.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}

add_action( 'artb/app_initialize', function ( $app ) {
	/** @var \WP_Framework $app */
	$app->setting->remove_setting( 'assets_version' );
} );
