<?php
/**
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
?>
<fieldset>
    <legend><?php $instance->h( 'preview', true ); ?></legend>
    <div class="setting-preview">
        <span class="preview-item"><?php $instance->h( $instance->app->filter->apply_filters( 'test_phrase' ) ); ?></span>
    </div>
</fieldset>
