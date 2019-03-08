<?php
/**
 * WP_Framework_Admin Views Admin Include Dashboard_buttons
 *
 * @version 0.0.15
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
?>
<div class="form-buttons">
	<?php if ( empty( $args['no_update_button'] ) ): ?>
		<?php $instance->form( 'input/submit', $args, [
			'name'  => 'update',
			'value' => 'Update',
			'class' => 'button-primary large',
		] ); ?>
	<?php endif; ?>
	<?php if ( empty( $args['no_reset_button'] ) ): ?>
		<?php $instance->form( 'input/button', $args, [
			'name'  => 'reset',
			'value' => 'Reset',
			'class' => 'button-primary',
		] ); ?>
	<?php endif; ?>
</div>
