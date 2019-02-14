<?php
/**
 * WP_Framework_View Views Include Form Dashicon
 *
 * @version 0.0.3
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
/** @var string $target */
$args['class']                     .= ' ' . $instance->get_dashicon_picker_class();
$args['attributes']['data-target'] = $target;
! isset( $args['name'] ) and $args['name'] = '';
?>
<?php $instance->form( 'input/button', $args ); ?>
