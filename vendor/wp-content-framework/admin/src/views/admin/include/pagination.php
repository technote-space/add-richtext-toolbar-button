<?php
/**
 * WP_Framework_Admin Views Admin Include Pagination
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
/** @var int $page */
/** @var int $total_page */
/** @var string $p */
$instance->add_style_view( 'admin/style/pagination' );
?>
<div class="pagination">
	<?php if ( $page > 1 ) : ?>
		<?php $instance->url( $instance->app->input->get_current_url( [ $p => $page - 1 ] ), '<', true, false, [
			'class' => 'pagination-item',
		] ); ?>
	<?php else: ?>
        <div class="pagination-item">
			<?php $instance->h( '<', true ); ?>
        </div>
	<?php endif; ?>
    <div class="pagination-item pagination-now">
		<?php $instance->h( $page ); ?>
    </div>
	<?php if ( $page < $total_page ): ?>
		<?php $instance->url( $instance->app->input->get_current_url( [ $p => $page + 1 ] ), '>', true, false, [
			'class' => 'pagination-item',
		] ); ?>
	<?php else: ?>
        <div class="pagination-item">
			<?php $instance->h( '>', true ); ?>
        </div>
	<?php endif; ?>
</div>