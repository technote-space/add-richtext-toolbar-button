<?php
/**
 * WP_Framework_Admin Views Admin Style Dashboard_info
 *
 * @version 0.0.15
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
    #<?php $instance->id(); ?>-info-wrap {
        display: table-cell;
        width: 10%;
        vertical-align: top;
    }

    #<?php $instance->id(); ?>-info-wrap .inner {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background: #f5f5f5;
    }

    #<?php $instance->id(); ?>-info-wrap .box {
        margin: 20px 0;
    }

    #<?php $instance->id(); ?>-info-wrap .box .title {
        margin: 5px 0;
    }

    #<?php $instance->id(); ?>-info-wrap .box a {
        white-space: nowrap;
    }
</style>
