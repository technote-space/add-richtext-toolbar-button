<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var Presenter $instance */
?>
<script>
	function artbGetIcon( icon, is_retry ) {
		if ( /^https?:\/\//.test( icon ) || /^data:image/.test( icon ) ) {
			return jQuery( '<div/>' ).css( {
				'background': 'url(' + icon + ')',
				'background-size': 'contain',
				'background-position': 'left top',
				'background-repeat': 'no-repeat',
				'width': '24px',
				'height': '24px',
				'margin-bottom': '10px',
			} );
		} else if ( /^dashicons-/.test( icon ) ) {
			return jQuery( '<span class="dashicons" />' ).addClass( icon );
		} else if ( is_retry ) {
			return artbGetIcon( '<?php $instance->h( $instance->get_img_url( 'icon-24x24.png', null, false ) );?>' );
		}
		return artbGetIcon( '<?php $instance->h( $instance->app->filter->apply_filters( 'default_icon' ) );?>', true );
	}
</script>
