<?php
/**
 * WP_Framework_Admin Views Admin Style Dashboard_form
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
/** @var array $tabs */
?>
<style>
    #<?php $instance->id(); ?>-dashboard {
        display: table;
        margin: 15px 10px;
        width: 100%;
    }

    <?php if (! empty( $tabs ) && is_array( $tabs )):?>
    #<?php $instance->id(); ?>-content-wrap .nav-tab-wrapper {
        margin-right: 5%;
    }

    #<?php $instance->id(); ?>-tab-content-wrap {
        margin: 10px;
    }

    .<?php $instance->id(); ?>-tab-content {
        display: none;
        font-size: 1em;
        margin: 25px 25px 25px 10px;
    }

    .<?php $instance->id(); ?>-tab-content.active {
        display: block;
    }

    <?php endif;?>
</style>
