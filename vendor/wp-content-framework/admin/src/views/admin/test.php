<?php
/**
 * WP_Framework_Admin Views Admin Test
 *
 * @version 0.0.8
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
/** @var array $tests */
$instance->add_style_view( 'admin/style/table' );
?>

<?php if ( count( $tests ) > 0 ): ?>
    <h3><?php $instance->h( 'Test count: ', true );
		echo count( $tests ); ?></h3>
    <table class="widefat striped">
		<?php foreach ( $tests as $test ): ?>
            <tr>
                <td>
					<?php $instance->h( $test ); ?>
                </td>
            </tr>
		<?php endforeach; ?>
    </table>
	<?php $instance->form( 'open' ); ?>
	<?php $instance->form( 'input/hidden', [
		'name'  => 'action',
		'value' => 'do_test',
	] ); ?>
	<?php $instance->form( 'input/submit', $args, [
		'name'  => 'execute',
		'value' => 'Execute',
		'class' => 'button-primary',
	] ); ?>
	<?php $instance->form( 'close' ); ?>
<?php else: ?>
    <h3><?php $instance->h( 'There is no tests.', true ); ?></h3>
<?php endif; ?>


