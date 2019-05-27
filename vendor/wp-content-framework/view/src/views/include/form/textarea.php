<?php
/**
 * WP_Framework_View Views Include Form Textarea
 *
 * @version 0.0.6
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
/** @var string $name */
/** @var string $value */
/** @var array $attributes */
empty( $attributes ) and $attributes = [];
isset( $id ) and $attributes['id'] = $id;
isset( $class ) and $attributes['class'] = $class;
$attributes['name'] = $name;
! isset( $value ) and $value = '';
global $allowedposttags;
$allowed = $allowedposttags;
unset( $allowed['textarea'] );
?>
<textarea <?php $instance->get_view( 'include/attributes', array_merge( $args, [ 'attributes' => $attributes ] ), true ); ?> ><?php $instance->h( wp_kses( $value, $allowed ), false, true, false ); ?></textarea>