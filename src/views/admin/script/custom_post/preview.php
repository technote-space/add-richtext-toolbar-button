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
/** @var string $css_handle */
/** @var string $fontawesome_handle */
$instance->add_script_view( 'admin/script/icon' );
?>
<script>
	( function( $ ) {
		$( function() {
			$( '.display-icon' ).each( function() {
				const icon = artbGetIcon( $( this ).val() );
				if ( ! icon ) {
					return;
				}
				$( this ).closest( '.icon-wrapper' ).append( icon );
			} );

			$( '.preview-iframe' ).each( function() {
				$( this ).contents().find( 'body' ).append( '<div id="preview-wrap">' );

				$( this ).contents().find( '#preview-wrap' ).append( $( '<' + $( this ).data( 'tag_name' ) + '>', {
					class: $( this ).data( 'class_name' ),
					text: '<?php $instance->h( $instance->app->filter->apply_filters( 'test_phrase' ) );?>',
				} ) );

				$( this ).contents().find( 'head' ).append( $( '<style>', {
					type: 'text/css',
					text: 'body{font-size: 13px; margin: 0; background: transparent!important} body::before, body::after {background: transparent!important} #preview-wrap{margin: 1em}',
				} ) );

				const main_css = $( '#<?php $instance->h( $css_handle );?>-css' );
				$( this ).contents().find( 'head' ).append( main_css.clone() );
				<?php if ( ! empty( $fontawesome_handle ) ) : ?>
				const fontawesome_css = $( '#<?php $instance->h( $fontawesome_handle );?>-css' );
				$( this ).contents().find( 'head' ).append( fontawesome_css.clone() );
				<?php endif;?>
			} );
		} );
	} )( jQuery );
</script>
