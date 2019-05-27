<?php
/**
 * WP_Framework_Admin Views Admin Include Layout
 *
 * @version 0.0.32
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

use WP_Framework_Presenter\Interfaces\Presenter;
use WP_Framework_Admin\Classes\Controllers\Admin\Base;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var Base $page */
/** @var string $slug */
$instance->add_style_view( 'admin/style/button' );
?>
<div class="wrap <?php $instance->id(); ?>-wrap">
    <div class="icon32 icon32-<?php $instance->h( $slug ); ?>"><br/></div>
    <div id="<?php $instance->id(); ?>-main-contents">
        <h2 id="<?php $instance->id(); ?>-page_title"><?php $instance->h( $page->get_page_title(), true ); ?></h2>
		<?php $page->presenter(); ?>
    </div>
</div>