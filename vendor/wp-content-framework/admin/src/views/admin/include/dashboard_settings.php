<?php
/**
 * WP_Framework_Admin Views Admin Include Dashboard_settings
 *
 * @version 0.0.26
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
/** @var array|null $settings */
?>
<?php if ( ! empty( $settings ) && is_array( $settings ) ): ?>
    <table>
		<?php foreach ( $settings as $k => $v ) : ?>
            <tr>
                <th>
                    <label for="<?php $instance->h( $v['id'] ); ?>"><?php $instance->h( $instance->app->array->search( $v, 'title', 'label', '' ) ); ?></label>
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
