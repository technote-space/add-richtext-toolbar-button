<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var array $details */
?>
<table class="widefat striped">
	<?php foreach ( $details as $name => $value ) : ?>
		<tr>
			<th><?php $instance->h( $name ); ?></th>
			<td>
				<?php if ( is_array( $value ) ) : ?>
					<?php if ( ! empty( $value['form_type'] ) ) : ?>
						<?php $instance->get_view( 'admin/custom_post/setting/preview/' . $value['form_type'], $value, true, false ); ?>
					<?php else : ?>
						<?php $instance->h( $instance->app->array->get( $value, 'value' ) ); ?>
					<?php endif; ?>
				<?php else : ?>
					<?php $instance->h( $value ); ?>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
