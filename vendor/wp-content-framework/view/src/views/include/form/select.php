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

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
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
?>
<select <?php $instance->get_view( 'include/attributes', array_merge( $args, [ 'attributes' => $attributes ] ), true ); ?> >
	<?php if ( ! empty( $options ) ): ?>
		<?php foreach ( $options as $value => $option ): ?>
            <option value="<?php $instance->h( $value ); ?>"<?php if ( ! empty( $selected ) && in_array( $value, $selected ) ): ?> selected="selected"<?php endif; ?>><?php $instance->h( $option, true ); ?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>