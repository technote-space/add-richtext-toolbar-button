<?php
/**
 * WP_Framework_View Views Include Img
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var array $args */
/** @var string $id */
/** @var string $class */
/** @var string $src */
/** @var string $alt */
/** @var string $width */
/** @var string $height */
/** @var array $attributes */
empty( $attributes ) and $attributes = [];
isset( $id ) and $attributes['id'] = $id;
isset( $class ) and $attributes['class'] = $class;
$attributes['src'] = $src;
isset( $alt ) and $attributes['alt'] = $alt;
isset( $width ) and $attributes['width'] = $width;
isset( $height ) and $attributes['height'] = $height;
?>
<img <?php $instance->get_view( 'include/attributes', array_merge( $args, [ 'attributes' => $attributes ] ), true ); ?> />
