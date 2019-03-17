<?php
/**
 * WP_Framework_Custom_Post Views Admin Include Custom Post Textarea
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
$attr         = $instance->app->array->get( $column, 'attributes', [] );
$attr['rows'] = $instance->app->array->get( $column, 'rows', 5 );
?>
<?php $instance->form( 'textarea', [
	'name'       => $prefix . $name,
	'id'         => $prefix . $name,
	'value'      => $instance->old( $prefix . $name, $data, $name, $instance->app->array->get( $column, 'default', '' ) ),
	'attributes' => $attr,
], $instance->app->array->get( $column, 'args', [] ) ); ?>
