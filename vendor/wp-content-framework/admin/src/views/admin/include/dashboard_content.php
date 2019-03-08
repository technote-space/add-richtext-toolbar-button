<?php
/**
 * WP_Framework_Admin Views Admin Include Dashboard_content
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
/** @var array $settings */
$instance->add_style_view( 'admin/style/dashboard_content' );
?>
<div id="<?php $instance->id(); ?>-content-wrap">
	<?php $instance->get_view( 'admin/include/dashboard_before_content', $args, true, false ); ?>
	<?php if ( ! empty( $settings ) && is_array( $settings ) ): ?>
        <table>
			<?php foreach ( $settings as $k => $v ) : ?>
                <tr>
                    <th>
                        <label for="<?php $instance->h( $v['id'] ); ?>"><?php $instance->h( $instance->app->utility->array_get( $v, 'title', $v['label'] ) ); ?></label>
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
	<?php endif; ?>
	<?php $instance->get_view( 'admin/include/dashboard_buttons', $args, true ); ?>
	<?php $instance->get_view( 'admin/include/dashboard_after_content', $args, true, false ); ?>
</div>
