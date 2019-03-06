<?php
/**
 * WP_Framework_View Views Include Form Color
 *
 * @version 0.0.3
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $args */
$args['class'] .= ' ' . $instance->get_color_picker_class();
?>
<?php if ( isset( $label ) ): ?>
    <label>
		<?php $instance->h( $label, true ); ?>
		<?php $instance->form( 'input/text', $args ); ?>
    </label>
<?php else: ?>
	<?php $instance->form( 'input/text', $args ); ?>
<?php endif; ?>