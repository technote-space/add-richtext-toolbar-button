<?php
/**
 * @version 1.0.0
 * @author technote-space
 * @since 1.0.0
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $data */
/** @var array $column */
/** @var string $name */
/** @var string $prefix */
$post_types = $instance->app->utility->array_get( $column, 'post_types' );
$val        = $instance->old( $prefix . $name, $data, $name );
?>
<?php if ( ! empty( $post_types ) ): ?>
    <div class="block form checkbox">
		<?php foreach ( $post_types as $post_type ): ?>
			<?php $instance->form( 'input/checkbox', [
				'value'   => $post_type,
				'name'    => $prefix . $name . '[]',
				'label'   => $post_type,
				'checked' => in_array( $post_type, $val ),
			] ); ?>
		<?php endforeach; ?>
    </div>
<?php endif; ?>
