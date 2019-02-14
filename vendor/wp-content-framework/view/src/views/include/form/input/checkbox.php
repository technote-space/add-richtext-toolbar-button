<?php
/**
 * WP_Framework_View Views Include Form Input Checkbox
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var string $id */
/** @var string $label */
/** @var array $args */
/** @var bool|null $checked */
! empty( $checked ) and $args['attributes']['checked'] = 'checked';
?>
<?php if ( isset( $label ) ): ?>
    <label>
		<?php $instance->form( 'input', array_merge( $args, [
			'type' => 'checkbox',
		] ) ); ?>
		<?php $instance->h( $label, true ); ?>
    </label>
<?php else: ?>
	<?php $instance->form( 'input', array_merge( $args, [
		'type' => 'checkbox',
	] ) ); ?>
<?php endif; ?>
