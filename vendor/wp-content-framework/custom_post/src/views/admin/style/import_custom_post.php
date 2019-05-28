<?php
/**
 * WP_Framework_Custom_Post Views Admin Style Import Custom Post
 *
 * @version 0.0.34
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
?>
<style>
    .import-button-wrapper {
        display: inline-flex;
        align-items: center;
        padding: 9px 0 4px;
        vertical-align: top;
    }

    .import-button-wrapper input[type="file"] {
        display: none;
    }

    .result-wrap .result-message {
        font-size: 1.2em;
        font-weight: bold;
        line-height: 2em;
        margin: 10px;
    }

    .validation-errors {
        margin-bottom: 10px;
    }
</style>