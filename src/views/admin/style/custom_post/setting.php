<?php
/**
 * @version 0.0.3
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var string $name_prefix */
?>

<style>
    .setting-preview {
        font-size: 1.3em;
        line-height: 1;
    }

    .setting-preview.auxiliary-line {
        border: dashed #ddd 2px;
    }

    .setting-preview.auxiliary-line .preview-item {
        border: dotted #666 1px;
    }

    .display-area {
        max-width: 100px;
        max-height: 100px;
    }

    .search-result {
        display: inline-block;
    }

    .search-result .select-group {
        margin: 0 2px 2px;
    }

    .search-result .select-group.disabled {
        display: none;
    }

    #<?php $instance->h($name_prefix);?>style {
        line-height: 1.3em;
    }

    .custom-post fieldset {
        padding: 10px;
        border: solid 1px #ccc;
        border-radius: 5px;
    }

    .style-buttons {
        text-align: right;
    }

    .custom-post fieldset .preset-style {
        margin: 0 3px 10px;
    }

    .custom-post fieldset .preset-style.multiple {
        background: #3ba949;
        border-color: #33b933;
        box-shadow: 0 1px 0 #33b933;
        text-shadow: 0 -1px 1px #33b933, 1px 0 1px #33b933, 0 1px 1px #33b933, -1px 0 1px #33b933;
    }

    .custom-post fieldset .preset-style.multiple:hover {
        background: #4bb949;
    }

    .block.checkbox {
        display: inline-block;
    }

    .checkbox label {
        display: block;
        padding: 3px;
        margin: 3px;
    }

    .checkbox input[type=checkbox] {
        vertical-align: bottom;
    }

    .display-auxiliary-line-wrap {
        text-align: right;
    }
</style>