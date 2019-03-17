<?php
/**
 * @version 1.1.0
 * @author Technote
 * @since 1.0.0
 * @since 1.1.0 wp-content-framework/admin#20
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
?>

<style>
    #<?php $instance->id(); ?>-dashboard {
        display: table;
        margin: 15px 10px;
        width: 100%;
    }

    .display-area {
        max-width: 100px;
        max-height: 100px;
    }

    .block.checkbox {
        display: inline-block;
    }
</style>