<?php
/**
 * WP_Framework_View Views Include Script Uploader
 *
 * @version 0.0.7
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
?>

<script>
	( function( $ ) {
		$( ".<?php $instance->h( $instance->get_media_uploader_class() );?>" ).each( function() {
			const $target = $( $( this ).data( 'target' ) );
			if ( $target.length === 0 ) {
				return;
			}

			$( this ).on( 'click', function() {
				const _send_to_editor = window.send_to_editor;
				window.send_to_editor = function( html ) {
					let img_url = $( 'img', html ).attr( 'src' );
					if ( undefined === img_url ) {
						img_url = $( html ).attr( 'src' );
					}
					$target.val( img_url ).trigger( 'change' );

					tb_remove();
					if ( _send_to_editor ) {
						window.send_to_editor = _send_to_editor;
					}
				};
				tb_show( null, 'media-upload.php?type=image&amp;TB_iframe=true' );
				return false;
			} );
		} );
	} )( jQuery );
</script>
