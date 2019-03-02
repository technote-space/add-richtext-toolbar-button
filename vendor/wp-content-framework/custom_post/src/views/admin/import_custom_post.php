<?php
/**
 * WP_Framework_Custom_Post Views Admin Import Custom Post
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
/** @var int $total */
/** @var int $success */
/** @var array $failed */
?>
<div class="block form custom-post">
    <table class="widefat striped">
        <tr>
            <th><?php $instance->h( 'Total', true ); ?></th>
            <th><?php $instance->h( 'Success', true ); ?></th>
            <th><?php $instance->h( 'Fail', true ); ?></th>
        </tr>
        <tr>
            <td><?php $instance->h( $total ); ?></td>
            <td><?php $instance->h( $success ); ?></td>
            <td>
				<?php $instance->h( count( $failed ) ); ?>
				<?php if ( ! empty( $failed ) ): ?>
                    <div class="error">
						<?php foreach ( $failed as $value ): ?>
                            <table class="widefat striped validation-errors">
								<?php foreach ( $value[1] as $key => $errors ): ?>
                                    <tr>
                                        <th><?php $instance->h( $key ); ?></th>
                                        <td>
                                            <ul>
                                                <li><?php $instance->h( $value[0][ $key ] ); ?></li>
												<?php foreach ( $errors as $error ): ?>
                                                    <li><?php $instance->h( $error ); ?></li>
												<?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
								<?php endforeach; ?>
                            </table>
						<?php endforeach; ?>
                    </div>
				<?php endif; ?>
            </td>
        </tr>
    </table>
</div>
