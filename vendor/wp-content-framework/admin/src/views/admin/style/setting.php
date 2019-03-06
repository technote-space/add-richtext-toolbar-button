<?php
/**
 * WP_Framework_Admin Views Admin Style Setting
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
$instance->add_style_view( 'admin/style/table' );
?>
<style>
    #<?php $instance->id();?>-main-contents table .<?php $instance->id(); ?>-setting-detail {
        float: right;
    }
</style>