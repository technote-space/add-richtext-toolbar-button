<?php
/**
 * WP_Framework_Custom_Post Views Admin Include Custom Post Input Number
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
/** @var array $data */
/** @var array $column */
/** @var string $name */
/** @var string $prefix */
$attr = $instance->app->utility->array_get( $column, 'attributes', [] );
if ( isset( $column['maxlength'] ) ) {
	$attr['maxlength'] = $column['maxlength'];
}
if ( ! empty( $column['unsigned'] ) ) {
	$attr['min'] = 0;
}
$attr['placeholder'] = $instance->app->utility->array_get( $column, 'default', '' );
?>
<?php $instance->form( 'input/number', [
	'name'       => $prefix . $name,
	'id'         => $prefix . $name,
	'value'      => $instance->old( $prefix . $name, $data, $name ),
	'attributes' => $attr,
], $instance->app->utility->array_get( $column, 'args', [] ) ); ?>