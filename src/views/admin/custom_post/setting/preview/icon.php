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
