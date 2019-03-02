<?php
/**
 * WP_Framework_Custom_Post Views Admin Custom Post
 *
 * @version 0.0.21
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var \WP_Post $post */
/** @var array $data */
/** @var array $columns */
/** @var string $prefix */
?>
<div class="block form custom-post">
    <dl>
		<?php foreach ( $columns as $name => $column ): ?>
			<?php if ( empty( $column['is_user_defined'] ) || 'post_id' === $column['name'] ): continue; endif; ?>
            <dt>
                <label for="<?php $instance->h( $prefix . $name ); ?>">
					<?php $instance->h( $instance->app->utility->array_get( $column, 'comment', $column['name'] ) ); ?>
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
