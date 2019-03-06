<?php
/**
 * WP_Framework_Admin Views Admin Style Table
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
?>
<style>
    #<?php $instance->id();?>-main-contents table .<?php $instance->id(); ?>-td-0 {
        background: #e0e0e0 !important;
    }

    #<?php $instance->id();?>-main-contents table .<?php $instance->id(); ?>-td-1 {
        background: #eaeaea !important;
    }
</style>
