<?php
/**
 * WP_Framework_Admin Views Admin Dashboard
 *
 * @version 0.0.19
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
$instance->add_script_view( 'admin/script/dashboard_form' );
?>
<?php $instance->form( 'open', $args ); ?>
<div id="<?php $instance->id(); ?>-dashboard">
	<?php $instance->get_view( 'admin/include/dashboard_content', $args, true ); ?>
	<?php $instance->get_view( 'admin/include/dashboard_info', $args, true ); ?>
</div>
<?php $instance->form( 'close', $args ); ?>
