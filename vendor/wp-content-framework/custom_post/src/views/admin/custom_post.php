<?php
/**
 * WP_Framework_Custom_Post Views Admin Custom Post
 *
 * @version 0.0.34
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
/** @var WP_Post $post */
/** @var array $data */
/** @var array $columns */
/** @var string $prefix */
?>
<div class="block form custom-post">
    <dl>
		<?php foreach ( $columns as $name => $column ): ?>
			<?php if ( empty( $column['is_user_defined'] ) || 'post_id' === $instance->app->array->get( $column, 'name' ) ): continue; endif; ?>
            <dt>
                <label for="<?php $instance->h( $prefix . $name ); ?>">
					<?php $instance->h( $instance->app->array->search( $column, 'comment', 'name', '' ) ); ?>
					<?php if ( ! empty( $column['required'] ) ): ?><span class="required">*</span><?php endif; ?>
                </label>
            </dt>
            <dd>
				<?php $instance->get_view( 'admin/include/custom_post/' . $column['form_type'], [
					'data'   => $data,
					'column' => $column,
					'name'   => $name,
					'prefix' => $prefix,
				], true ); ?>
            </dd>
		<?php endforeach; ?>
    </dl>
</div>
