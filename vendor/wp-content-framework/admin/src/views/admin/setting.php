<?php
/**
 * WP_Framework_Admin Views Admin Setting
 *
 * @version 0.0.32
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
/** @var array $settings */
/** @var array $args */
?>

<?php $instance->form( 'open', $args ); ?>

<table class="widefat striped">
    <tr>
        <th><?php $instance->h( 'Group', true ); ?></th>
        <th><?php $instance->h( 'Parameter', true ); ?></th>
        <th><?php $instance->h( 'Saved value', true ); ?></th>
        <th><?php $instance->h( 'Used value', true ); ?></th>
    </tr>
	<?php if ( empty( $settings ) ): ?>
        <tr>
            <td colspan="4"><?php $instance->h( 'Item not found.', true ); ?></td>
        </tr>
	<?php else: ?>
		<?php $n = 0; ?>
		<?php foreach ( $settings as $group => $group_settings ): ?>
            <tr>
            <td rowspan="<?php $instance->n( $group_settings ); ?>"
                class="<?php $instance->id(); ?>-td-<?php $instance->h( $n++ % 2 ); ?>">
				<?php $instance->h( $group, true ); ?>
            </td>
			<?php if ( empty( $group_settings ) ): ?>
                <td colspan="3"><?php $instance->h( 'Item not found.', true ); ?></td>
			<?php else: ?>
				<?php $m = 0; ?>
				<?php foreach ( $group_settings as $setting => $detail ): ?>
					<?php if ( $m++ > 0 ): ?>
                        <tr>
					<?php endif; ?>
                    <td>
                        <label for="<?php $instance->h( $detail['key'] ); ?>">
							<?php $instance->h( $detail['label'], true ); ?>
							<?php if ( ! empty( $detail['info'] ) ): ?>
                                <span class="<?php $instance->id(); ?>-setting-detail">[<?php $instance->h( implode( ', ', $detail['info'] ) ); ?>
                                    ]</span>
							<?php endif; ?>
                        </label>
                    </td>
                    <td>
						<?php $instance->form( 'input/text', $args, [
							'id'         => $detail['key'],
							'name'       => $detail['name'],
							'value'      => $detail['saved'],
							'attributes' => [ 'placeholder' => $detail['placeholder'] ],
						] ); ?>
                    </td>
                    <td>
						<?php $instance->h( $detail['used'] ); ?>
                    </td>
                    </tr>
				<?php endforeach; ?>
			<?php endif; ?>
            </tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>

<?php $instance->form( 'input/submit', $args, [
	'name'  => 'update',
	'value' => 'Update',
	'class' => 'button-primary right large',
] ); ?>
<?php $instance->form( 'close', $args ); ?>
