<?php
/**
 * WP_Framework_Admin Views Admin Dashboard
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
/** @var array $args */
$instance->add_script_view( 'admin/script/dashboard_form', $args );
$instance->add_style_view( 'admin/style/dashboard_form', $args );
?>
<?php $instance->form( 'open', $args ); ?>
<div id="<?php $instance->id(); ?>-dashboard">
	<?php $instance->get_view( 'admin/include/dashboard_content', $args, true ); ?>
	<?php $instance->get_view( 'admin/include/dashboard_info', $args, true ); ?>
</div>
<?php $instance->form( 'close', $args ); ?>
