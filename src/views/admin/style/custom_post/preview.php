<?php
/**
 * @version 1.0.14
 * @author Technote
 * @since 1.0.14
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var string $post_type */
?>

<style>
    .widefat td.<?php $instance->h($post_type);?>-preview {
        padding: 0;
    }

    .preview-iframe {
        max-width: 100%;
    }
</style>