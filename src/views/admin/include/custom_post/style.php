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
$attr                 = $instance->app->array->get( $column, 'attributes', [] );
$attr['rows']         = $instance->app->array->get( $column, 'rows', 5 );
$attr['data-reset']   = $instance->json( $instance->app->array->get( $data, $name, '' ), false );
$preset               = $instance->app->array->get( $column['args'], 'preset' );
$is_valid_fontawesome = $instance->app->array->get( $column['args'], 'is_valid_fontawesome' );
?>
<?php $instance->form( 'textarea', [
	'name'       => $prefix . $name,
	'id'         => $prefix . $name,
	'value'      => $instance->old( $prefix . $name, $data, $name, $instance->app->array->get( $column, 'default', '' ) ),
	'attributes' => $attr,
], $instance->app->array->get( $column, 'args', [] ) ); ?>
<div class="style-buttons">
	<?php $instance->form( 'input/button', [
		'value' => 'clear',
		'name'  => 'clear',
		'class' => 'button-primary clear-style',
	] ); ?>
	<?php $instance->form( 'input/button', [
		'value' => 'reset',
		'name'  => 'reset',
		'class' => 'button-primary reset-style',
	] ); ?>
</div>
<?php if ( ! empty( $preset ) ) : ?>
	<fieldset>
		<legend><?php $instance->h( 'preset', true ); ?></legend>
		<?php foreach ( $preset as $key => $value ) : ?>
			<?php $instance->form( 'input/button', [
				'value'      => $key,
				'name'       => $key,
				'class'      => 'button-primary preset-style' . ( is_array( $value ) && count( $value ) > 1 ? ' multiple' : '' ),
				'attributes' => [
					'data-value' => $instance->json( $value, false ),
				],
			] ); ?>
		<?php endforeach; ?>
	</fieldset>
<?php endif; ?>
<?php if ( ! empty( $is_valid_fontawesome ) ) :
	$instance->url( $instance->app->get_config( 'config', 'fontawesome_icon_url' ), 'Font Awesome Icons', true, true );
endif; ?>
