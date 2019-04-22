<?php
/**
 * WP_Framework_Admin Views Admin Include Dashboard_content
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
/** @var array|null $tabs */
/** @var array|null $tab_settings */
/** @var string|null $admin_page_url */
$instance->add_style_view( 'admin/style/dashboard_content' );
?>
<div id="<?php $instance->id(); ?>-content-wrap">
	<?php $instance->get_view( 'admin/include/dashboard_before_content', $args, true, false ); ?>
	<?php if ( ! empty( $tabs ) && is_array( $tabs ) ): ?>
        <h2 class="nav-tab-wrapper wp-clearfix" data-admin_page_url="<?php $instance->h( isset( $admin_page_url ) ? $admin_page_url : '' ); ?>">
			<?php foreach ( $tabs as $tab => $name ): ?>
                <a href="#" data-target="<?php $instance->h( $tab ); ?>" class="nav-tab"><?php $instance->h( $name, true ); ?></a>
			<?php endforeach; ?>
			<?php if ( isset( $admin_page_url ) ): ?>
                <a href="#" data-target_page="related_post-setting" class="nav-tab"><?php $instance->h( 'Go to Detail Settings', true ); ?></a>
			<?php endif; ?>
        </h2>
        <div id="<?php $instance->id(); ?>-tab-content-wrap">
			<?php foreach ( $tabs as $tab => $name ): ?>
                <div class="<?php $instance->id(); ?>-tab-content" data-tab="<?php $instance->h( $tab ); ?>">
					<?php
					$new_args = array_merge( $args, [
						'settings' => $instance->app->array->get( $tab_settings, $tab, [] ),
					] );
					if ( $instance->view_exists( 'admin/include/dashboard/' . $tab ) ):
						$instance->get_view( 'admin/include/dashboard/' . $tab, $new_args, true, false );
					else:
						$instance->get_view( 'admin/include/dashboard_settings', $new_args, true );
					endif; ?>
                </div>
			<?php endforeach; ?>
        </div>
	<?php else: ?>
		<?php $instance->get_view( 'admin/include/dashboard_settings', $args, true ); ?>
	<?php endif; ?>
	<?php $instance->get_view( 'admin/include/dashboard_buttons', $args, true ); ?>
	<?php $instance->get_view( 'admin/include/dashboard_after_content', $args, true, false ); ?>
</div>
