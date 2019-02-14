<?php
/**
 * WP_Framework_Admin Views Admin Style Pagination
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
?>
<style>
    #<?php $instance->id();?>-main-contents .pagination {
        margin: 8px 0;
    }

    #<?php $instance->id();?>-main-contents .pagination .pagination-item {
        display: inline-block;
        padding: 2px 10px;
        margin: 2px;
        border: 1px solid #999;
        cursor: default;
        background: #ccc;
    }

    #<?php $instance->id();?>-main-contents .pagination .pagination-now {
        background: #eee;
    }

    #<?php $instance->id();?>-main-contents .pagination a.pagination-item {
        cursor: pointer;
        background: #efefff;
        text-decoration: none;
    }

    #<?php $instance->id();?>-main-contents .pagination a.pagination-item:hover {
        background: #ceceef;
    }
</style>
