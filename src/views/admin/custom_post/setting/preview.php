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
/** @var string $class_name */
/** @var string $tag_name */
?>
<iframe class="preview-iframe" data-tag_name="<?php $instance->h( $tag_name ); ?>" data-class_name="<?php $instance->h( $class_name ); ?>"></iframe>
