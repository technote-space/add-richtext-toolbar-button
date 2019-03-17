<?php
/**
 * WP_Framework_Custom_Post Views Admin Include Custom Post Input Checkbox
 *
 * @version 0.0.26
 * @author Technote
 * @copyright Technote All Rights Reserved
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
$default = ! empty( $instance->app->array->get( $column, 'default' ) );
$val     = $instance->old( $prefix . $name, $data, $name, $default, true ) - 0;
?>
<?php $instance->form( 'input/checkbox', [
	'name'    => $prefix . $name,
	'id'      => $prefix . $name,
	'value'   => 1,
	'label'   => $instance->app->array->search( $column, 'label', 'comment', 'name', '' ),
	'checked' => ! empty( $val ),
], $instance->app->array->get( $column, 'args', [] ) ); ?>