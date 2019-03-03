<?php
/**
 * @version 1.0.3
 * @author Technote
 * @since 1.0.0
 * @since 1.0.3 #34
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $args */
/** @var array $settings */
?>

<?php $instance->form( 'open', $args ); ?>
<div id="<?php $instance->id(); ?>-dashboard" class="wrap narrow">
    <div id="<?php $instance->id(); ?>-content-wrap">
        <table class="widefat striped">
			<?php foreach ( $settings as $k => $v ) : ?>
                <tr>
                    <th>
                        <label for="<?php $instance->h( $v['id'] ); ?>"><?php $instance->h( $v['title'] ); ?></label>
                    </th>
                    <td>
						<?php if ( ! empty( $v['form_type'] ) ) : ?>
							<?php $instance->get_view( 'admin/include/custom_post/' . $v['form_type'], [
								'data'   => [
									$v['name'] => $v['value'],
								],
								'column' => [],
								'name'   => $v['name'],
								'prefix' => '',
							], true ); ?>
						<?php else: ?>
							<?php $instance->form( $v['form'], $args, $v ); ?>
						<?php endif; ?>
                    </td>
                </tr>
			<?php endforeach; ?>
        </table>
        <div>
			<?php $instance->form( 'input/submit', $args, [
				'name'  => 'update',
				'value' => 'Update',
				'class' => 'button-primary large',
			] ); ?>
			<?php $instance->form( 'input/submit', $args, [
				'name'  => 'reset',
				'value' => 'Reset',
				'class' => 'button-primary',
			] ); ?>
        </div>
    </div>
</div>
<?php $instance->form( 'close', $args ); ?>





