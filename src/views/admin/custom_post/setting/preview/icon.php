<?php
/**
 * @version 1.0.0
 * @author technote-space
 * @since 1.0.0
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $args */
?>
<div class="icon-wrapper">
	<?php $instance->form( 'input/hidden', [
		'name'  => '',
		'value' => $value,
		'class' => 'display-icon',
	] ); ?>
    <div class="display-area"></div>
</div>
