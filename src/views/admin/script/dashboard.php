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
$instance->add_script_view( 'admin/script/icon' );
$target = [];
foreach ( $settings as $k => $v ) {
	if ( $instance->app->string->ends_with( $k, '_icon' ) ) {
		$target[] = '#' . preg_replace( '#/#', '\\\\/', $v['name'] );
	}
}
?>
<script>
	( function( $ ) {
		$( function() {
			// icon
			( function() {
				<?php $instance->h( 'const target = ' );?><?php $instance->json( $target );?>;
				Object.keys( target ).forEach( function( key ) {
					const $target = $( target[ key ] );
					$target.on( 'change', function() {
						const icon = artbGetIcon( $( this ).val().trim() );
						const $area = $( this ).closest( '.icon-wrapper' ).find( '.display-area' );
						$area.html( '' );
						if ( ! icon ) {
							return;
						}
						$area.append( icon );
					} ).trigger( 'change' );
				} );

				$( '.reset-icon' ).on( 'click', function() {
					const $target = $( $( this ).data( 'target' ) );
					$target.val( $( this ).data( 'value' ) ).trigger( 'change' );
					return false;
				} );
			} )();
		} );
	} )( jQuery );
</script>
