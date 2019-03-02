<?php
/**
 * WP_Framework_Custom_Post Views Admin Style Custom Post
 *
 * @version 0.0.21
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
    .block {
        border: solid 1px #e3e8ed;
        margin-bottom: 20px;
        background-color: #FFF;
        padding: 20px;
        box-sizing: border-box;
        border-radius: 2px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
    }

    div.form dl {
        width: 100%;
    }

    div.form dl.list.login {
        margin-left: 50px;
    }

    div.form dl dt,
    div.form dl dd {
        display: inline-block;
        vertical-align: top;
        padding: 5px 0;
        box-sizing: border-box;
        line-height: 1.5;
    }

    div.form dl dd .error {
        margin: .5em 0 0 0;
    }

    div.form dl:last-child {
        border: none;
    }

    div.form dl dt.first,
    div.form dl dd.first {
        border: none;
    }

    div.form dl dt {
        width: 25%;
        font-weight: bold;
        line-height: 2;
    }

    div.form.wide dl dt,
    .box.login dl dt,
    .column.right dl dt,
    .column.half dl dt {
        width: 23%;
    }

    div.form dl dd {
        width: 70%;
        line-height: 2;
        margin: 5px 0;
    }

    div.form dl dd.select span {
        vertical-align: middle;
        margin-right: 1em;
    }

    .icheckbox_flat-aero, .iradio_flat-aero {
        /*margin-top: 0px !important;*/
        line-height: 1;
        height: 23px !important;
    }

    div.form.wide dl dd,
    .box.login dl dd,
    .column.right dl dd,
    .column.half dl dd {
        width: 76%;
    }

    div.form dl dd ul {
        margin-top: 10px;
    }

    div.form dl.list dd ul {
        margin-top: 0;
    }

    input[type="text"],
    input[type="password"],
    input[type="reset"],
    textarea {
        border: 1px solid #e3e8ed;
        padding: 3px;
        box-sizing: border-box;
        width: 40%;
        line-height: 1;
    }

    input[type="text"].size_s,
    span.ctitel {
        width: 15%;
    }

    input[type="text"].size_m {
        width: 30%;
    }

    input[type="text"].size_auto {
        width: auto;
    }

    input[type="text"].size_l,
    input[type="password"].size_l {
        width: 60%;
    }

    input[type="text"].half {
        width: 48%;
    }

    input[type="text"].full {
        width: 100%;
    }

    input[type="text"].cropped,
    textarea.cropped,
    input[type="password"].cropped {
        width: 80%;
    }

    input[type="text"] {
        margin-right: 10px;
    }

    textarea {
        color: #666;
        width: 100%;
        height: 10em;
    }

    textarea.confirm {
        line-height: 1.5;
    }

    .column.left {
        float: left;
        margin-right: 2%;
    }

    .column.right {
        float: right;
    }

    .column.half {
        width: 49%;
    }

    #message.notice-success,
    #custom-permalinks-edit-box {
        display: none;
    }
</style>