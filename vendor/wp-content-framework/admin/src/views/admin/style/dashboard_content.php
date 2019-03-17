<?php
/**
 * WP_Framework_Admin Views Admin Style Dashboard_content
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
    #<?php $instance->id(); ?>-content-wrap {
        display: table-cell;
        width: 90%;
        vertical-align: top;
    }

    #<?php $instance->id(); ?>-content-wrap table {
        width: 95%;
        border-top: 1px dotted #ccc;
        margin: 0 0 15px 0;
        border-collapse: collapse;
        border-spacing: 0;
        empty-cells: show;
    }

    #<?php $instance->id(); ?>-content-wrap th {
        font-weight: bold;
        border-bottom: 1px dotted #ccc;
        padding: 10px 10px;
        vertical-align: middle;
        text-align: left;
    }

    #<?php $instance->id(); ?>-content-wrap td {
        border-bottom: 1px dotted #ccc;
        padding: 10px 10px;
        vertical-align: middle;
        text-align: left;
    }
</style>
