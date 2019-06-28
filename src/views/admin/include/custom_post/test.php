<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var Presenter $instance */
?>
<fieldset>
	<legend><?php $instance->h( 'preview', true ); ?></legend>
	<div class="setting-preview">
		<iframe class="preview-item-wrap"></iframe>
	</div>
</fieldset>
<div class="display-preview-settings-wrap">
	<?php $instance->form( 'input/checkbox', [
		'value'   => 1,
		'name'    => '',
		'label'   => 'Display auxiliary line',
		'checked' => false,
		'class'   => 'display-auxiliary-line',
	] ); ?>
	<?php $instance->form( 'input/checkbox', [
		'value'   => 1,
		'name'    => '',
		'label'   => 'Multiple lines',
		'checked' => false,
		'class'   => 'multiple-lines',
	] ); ?>
</div>
