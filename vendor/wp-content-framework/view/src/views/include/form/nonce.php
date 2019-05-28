<?php
/**
 * WP_Framework_View Views Include Form Nonce
 *
 * @version 0.0.8
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var array $args */
/** @var string $nonce_key */
/** @var string $nonce_value */
if ( empty( $nonce_key ) || empty( $nonce_value ) ) {
	return;
}
?>
<?php $instance->form( 'input/hidden', array_merge( $args, [
	'name'  => $nonce_key,
	'value' => $nonce_value,
] ) ); ?>