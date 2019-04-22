<?php
/**
 * WP_Framework_Admin Views Admin Script Dashboard_form
 *
 * @version 0.0.26
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array|null $tabs */
?>
<script>
	( function ( $ ) {
		$( function () {
			let submit = false;
			$( 'form' ).submit( function () {
				if ( submit ) {
					return false;
				}
				submit = true;
				$( 'form input, form select' ).prop( 'readonly', true ).addClass( 'disabled' ).off( 'click' ).on( 'click', function () {
					return false;
				} );
				return true;
			} );

			const $reset = $( '.form-buttons input[name="reset"]' );
			if ( $reset.length > 0 && ! $._data( $reset.get( 0 ), 'events' ) ) {
				$reset.on( 'click', function () {
					if ( window.confirm( '<?php $instance->h( 'Are you sure to reset settings?', true );?>' ) ) {
						$( this ).closest( 'form' ).submit();
					}
					return false;
				} );
			}

			<?php if (! empty( $tabs ) && is_array( $tabs )):?>
			$( '#<?php $instance->id(); ?>-dashboard .nav-tab' ).on( 'click', function () {
				const page = $( this ).data( 'target_page' );
				if ( page ) {
					location.href = $( this ).closest( 'h2' ).data( 'admin_page_url' ) + page;
					return false;
				}
				$( '#<?php $instance->id(); ?>-dashboard .nav-tab' ).removeClass( 'nav-tab-active' );
				$( '#<?php $instance->id(); ?>-dashboard .<?php $instance->id(); ?>-tab-content' ).removeClass( 'active' );
				$( this ).addClass( 'nav-tab-active' );
				$( '.<?php $instance->id(); ?>-tab-content[data-tab="' + $( this ).data( 'target' ) + '"]' ).addClass( 'active' );
				location.hash = $( this ).data( 'target' );
				const action  = $( this ).closest( 'form' ).attr( 'action' ).replace( /#\w+$/, '' ) + '#' + $( this ).data( 'target' );
				$( this ).closest( 'form' ).attr( 'action', action );
				return false;
			} );

			const hash = location.hash;
			let tab;
			if ( hash ) {
				tab = $( '[data-target="' + location.hash.replace( /^#/, '' ) + '"]' ).eq( 0 );
				if ( tab.length <= 0 ) {
					tab = null;
				}
			}
			if ( ! tab ) {
				tab = $( '#<?php $instance->id(); ?>-dashboard .nav-tab' ).eq( 0 );
			}
			tab.trigger( 'click' );
			<?php endif;?>
		} );
	} )( jQuery );
</script>
