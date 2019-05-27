<?php
/**
 * WP_Framework_View Views Include Form Select
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
/** @var string $name */
/** @var string $size */
/** @var string $multiple */
/** @var string $disabled */
/** @var array $attributes */
/** @var array $options */
/** @var array $selected */
empty( $attributes ) and $attributes = [];
isset( $id ) and $attributes['id'] = $id;
isset( $class ) and $attributes['class'] = $class;
$attributes['name'] = $name;
$attributes['size'] = isset( $size ) ? $size : '1';
! empty( $multiple ) and $attributes['multiple'] = 'multiple';
! empty( $disabled ) and $attributes['disabled'] = 'disabled';
isset( $selected ) and ! is_array( $selected ) and $selected = [ $selected ];
empty( $multiple ) and ! empty( $selected ) and count( $selected ) > 1 and $selected = array_splice( $selected, 0, 1 );
! empty( $selected ) and $selected = $instance->app->array->map( $selected, function ( $d ) use ( $instance ) {
	return $instance->convert_select_value( $d );
} );
?>
<select <?php $instance->get_view( 'include/attributes', array_merge( $args, [ 'attributes' => $attributes ] ), true ); ?> >
	<?php if ( ! empty( $options ) ): ?>
		<?php foreach ( $options as $value => $option ): ?>
            <option value="<?php $instance->h( $value ); ?>"<?php if ( ! empty( $selected ) && in_array( $instance->convert_select_value( $value ), $selected, true ) ): ?> selected="selected"<?php endif; ?>><?php $instance->h( $option, true ); ?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>