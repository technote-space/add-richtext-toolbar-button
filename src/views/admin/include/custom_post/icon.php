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
/** @var array $data */
/** @var array $column */
/** @var string $name */
/** @var string $prefix */
$val    = $instance->old( $prefix . $name, $data, $name );
$target = '#' . preg_replace( '#/#', '\\/', $prefix . $name );
?>
<div class="icon-wrapper">
	<div class="display-area"></div>
	<div class="input-wrapper">
		<?php $instance->form( 'input/text', [
			'name'  => $prefix . $name,
			'id'    => $prefix . $name,
			'value' => $val,
		], $instance->app->array->get( $column, 'args', [] ) ); ?>

		<?php $instance->form( 'dashicon', [
			'target' => $target,
			'value'  => 'Select icon',
			'class'  => 'button-primary',
		] ); ?>
		<?php $instance->form( 'uploader', [
			'target' => $target,
			'value'  => 'Media uploader',
			'class'  => 'button-primary',
		] ); ?>
		<?php $instance->form( 'input/button', [
			'value'      => 'reset',
			'name'       => 'reset',
			'class'      => 'button-primary reset-icon',
			'attributes' => [
				'data-target' => $target,
				'data-value'  => $instance->app->array->get( $data, $name ),
			],
		] ); ?>
	</div>
</div>
