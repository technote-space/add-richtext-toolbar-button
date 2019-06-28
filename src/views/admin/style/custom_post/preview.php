<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var string $post_type */
?>

<style>
	.widefat td.<?php $instance->h( $post_type ); ?>-preview {
		padding: 0;
	}

	.preview-iframe {
		max-width: 100%;
	}
</style>
