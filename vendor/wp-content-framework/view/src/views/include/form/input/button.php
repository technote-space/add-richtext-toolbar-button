<?php
/**
 * WP_Framework_View Views Include Form Input Button
 *
 * @version 0.0.2
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $args */
! isset( $args['translate'] ) and $args['translate'] = [ 'value' ];
?>
<?php $instance->form( 'input', array_merge( $args, [
	'type' => 'button',
] ) ); ?>