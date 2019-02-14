<?php
/**
 * WP_Framework_View Views Include Style Dashicon
 *
 * @version 0.0.3
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 *
 * Based on: https://github.com/bradvin/dashicons-picker/
 * @license https://github.com/bradvin/dashicons-picker/blob/master/LICENSE
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
?>

<style>
    .dashicon-picker-container {
        position: absolute;
        width: 220px;
        height: 252px;
        font-size: 14px;
        background-color: #fff;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.1);
        box-shadow: 0 1px 2px rgba(0,0,0,.1);
        overflow: hidden;
        padding: 5px;
        box-sizing: border-box
    }

    .dashicon-picker-container ul {
        margin: 0 0 10px;
        padding: 0
    }

    .dashicon-picker-container ul .dashicons {
        width: 20px;
        height: 20px;
        font-size: 20px
    }

    .dashicon-picker-container ul li {
        display: inline-block;
        margin: 5px;
        float: left
    }

    .dashicon-picker-container ul li a {
        display: block;
        text-decoration: none;
        color: #373737;
        padding: 5px;
        border: 1px solid #dfdfdf
    }

    .dashicon-picker-container ul li a:hover {
        border-color: #999;
        background: #efefef
    }

    .dashicon-picker-control {
        height: 32px
    }

    .dashicon-picker-control a {
        padding: 5px;
        text-decoration: none;
        line-height: 32px;
        width: 25px
    }

    .dashicon-picker-control a span {
        display: inline;
        vertical-align: middle
    }

    .dashicon-picker-control input {
        font-size: 12px;
        width: 140px
    }

</style>