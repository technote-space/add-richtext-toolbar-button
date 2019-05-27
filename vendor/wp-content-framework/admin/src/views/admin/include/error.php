<?php
/**
 * WP_Framework_Admin Views Admin Include Error
 *
 * @version 0.0.32
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
/** @var string $message */
?>
<div class="wrap cf-wrap">
    <div class="icon32 icon32-error"><br/></div>
    <h2>Error</h2>
    <div class="error">
        <p>
			<?php $instance->h( $message, true ); ?>
        </p>
    </div>
</div>