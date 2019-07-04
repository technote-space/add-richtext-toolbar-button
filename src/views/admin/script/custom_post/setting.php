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
/** @var string $name_prefix */
/** @var array $groups */
/** @var string $fontawesome_handle */
$instance->add_script_view( 'admin/script/icon' );
$phrase = $instance->app->filter->apply_filters( 'test_phrase' );
?>

<script>
	( function( $ ) {
		$( function() {
			const $preview = $( '.preview-item-wrap' ).contents();
			$preview.find( 'html' ).css( 'height', 'max-content' );
			$preview.find( 'body' ).css( 'height', 'max-content' );

			const controlHeight = function() {
				$( '.preview-item-wrap' ).height( $preview.find( 'html' ).outerHeight() );
			};

			// tagName, className
			( function() {
				const $tagName = $( '#<?php $instance->h( $name_prefix );?>tag_name' );
				const $className = $( '#<?php $instance->h( $name_prefix );?>class_name' );
				const setPreviewItem = function() {
					const tagName = $tagName.val() || 'span', className = $className.val() + ' preview-item';
					const phrase = $( '.multiple-lines' ).prop( 'checked' ) ? '<?php $instance->h( $phrase ); ?><br><?php $instance->h( $phrase ); ?><br><?php $instance->h( $phrase ); ?>' : '<?php $instance->h( $phrase ); ?>';
					const html = '<' + tagName + ' class="' + className + '">' + phrase + '</' + tagName + '>';
					$preview.find( 'body' ).html( '<div id="preview-wrap">' + html + '</div>' );
					controlHeight();
				};

				$tagName.on( 'input', function() {
					const original = $( this ).val();
					const replaced = $( this ).val()
						.replace( /あ/g, 'a' )
						.replace( /い/g, 'i' )
						.replace( /う/g, 'u' )
						.replace( /え/g, 'e' )
						.replace( /お/g, 'o' )
						.replace( /　/g, ' ' )
						.replace( /[Ａ-Ｚａ-ｚ０-９]/g, function( s ) {
							return String.fromCharCode( s.charCodeAt( 0 ) - 0xFEE0 );
						} ).replace( /[^a-zA-Z]/g, '' );
					if ( original !== replaced ) {
						$tagName.val( replaced );
					}
					setPreviewItem();
				} ).trigger( 'input' );

				$className.on( 'input', function() {
					const original = $( this ).val();
					const replaced = $( this ).val()
						.replace( /あ/g, 'a' )
						.replace( /い/g, 'i' )
						.replace( /う/g, 'u' )
						.replace( /え/g, 'e' )
						.replace( /お/g, 'o' )
						.replace( /　/g, ' ' )
						.replace( /ー/g, '-' )
						.replace( /＿/g, '_' )
						.replace( /[Ａ-Ｚａ-ｚ０-９]/g, function( s ) {
							return String.fromCharCode( s.charCodeAt( 0 ) - 0xFEE0 );
						} ).replace( /[^_a-zA-Z0-9-\s]/g, '' );
					if ( original !== replaced ) {
						$className.val( replaced );
					}
					setPreviewItem();
				} ).trigger( 'input' );
			} )();

			// style
			( function() {
				$preview.find( 'head' ).append( $( '<style>', {
					type: 'text/css',
					text: 'body{font-size: 15px; line-height: 1; margin: 0; background: transparent!important} body::before, body::after {background: transparent!important} #preview-wrap{margin: 1em} .auxiliary-line #preview-wrap{border: dashed #ddd 2px} .auxiliary-line #preview-wrap .preview-item{border: dotted #666 1px}',
				} ) );
				<?php if ( ! empty( $fontawesome_handle ) ) : ?>
				const fontawesome_css = $( '#<?php $instance->h( $fontawesome_handle );?>-css' );
				$preview.find( 'head' ).append( fontawesome_css.clone() );
				<?php endif;?>

				const applyStyles = function( style ) {
					const selector = '.preview-item';
					const previewStyleId = 'setting-preview-style';
					$preview.find( '#' + previewStyleId ).remove();
					$preview.find( 'head' ).append( $( '<style>', {
						type: 'text/css',
						id: previewStyleId,
					} ) );
					applyStyle( style, selector, $preview.find( '#' + previewStyleId ) );
					controlHeight();
				};

				const applyStyle = function( style, selector, $elem ) {
					const styles = {};
					style.split( /\r\n|\r|\n|;/ ).forEach( function( v ) {
						const match = v.match( /^(\[([-().#>+~|*a-z]+)]\s*)?(.+?)\s*:\s*(.+?)\s*$/ );
						if ( match ) {
							const pseudo = undefined === match[ 2 ] ? '' : match[ 2 ];
							const key = match[ 3 ];
							const val = match[ 4 ];
							if ( ! styles[ pseudo ] ) {
								styles[ pseudo ] = [];
							}
							styles[ pseudo ].push( key + ': ' + val + ' !important;' );
						}
					} );

					let results = [];
					Object.keys( styles ).forEach( function( pseudo ) {
						let s = selector;
						if ( pseudo ) {
							s += ':' + pseudo;
						}
						results.push( s + '{' + styles[ pseudo ].join( '\r\n' ) + '}' );
					} );
					$elem.text( results.join( '\r\n' ) );
				};

				const $target = $( '#<?php $instance->h( $name_prefix );?>style' );
				$target.on( 'blur', function() {
					applyStyles( $target.val() );
					$target.trigger( 'input' );
				} );

				$( '.clear-style' ).on( 'click', function() {
					$target.val( '' ).trigger( 'blur' );
					return false;
				} );

				$( '.reset-style' ).on( 'click', function() {
					$target.val( JSON.parse( $target.data( 'reset' ) ) ).trigger( 'blur' );
					return false;
				} );

				$target.on( 'input', function( e ) {
					applyStyles( $target.val() );
					if ( e.target.scrollHeight > e.target.offsetHeight ) {
						$( e.target ).height( e.target.scrollHeight );
					} else {
						const lineHeight = Number( $( e.target ).css( 'lineHeight' ).split( 'px' )[ 0 ] );
						const minHeight = lineHeight * $target.attr( 'rows' );
						while ( true ) {
							$( e.target ).height( $( e.target ).height() - lineHeight );
							if ( e.target.scrollHeight > e.target.offsetHeight || minHeight > e.target.offsetHeight ) {
								$( e.target ).height( Math.max( e.target.scrollHeight, minHeight ) );
								break;
							}
						}
					}
				} ).trigger( 'input' );
			} )();

			// icon
			( function() {
				const $target = $( '#<?php $instance->h( $name_prefix );?>icon' );
				$target.on( 'change', function() {
					const icon = artbGetIcon( $( this ).val().trim() );
					const $area = $( this ).closest( '.icon-wrapper' ).find( '.display-area' );
					$area.html( '' );
					if ( ! icon ) {
						return;
					}
					$area.append( icon );
				} ).trigger( 'change' );
			} )();

			// reset
			( function() {
				$( '.reset-icon' ).on( 'click', function() {
					const $target = $( $( this ).data( 'target' ) );
					$target.val( $( this ).data( 'value' ) ).trigger( 'change' );
					return false;
				} );
			} )();

			// group
			( function() {
				const $target = $( '#<?php $instance->h( $name_prefix );?>group_name' );
				const $parent = $target.closest( 'dd' );
				$parent.append( '<span class="search-result"/>' );
				const $result = $parent.find( '.search-result' );
				Object.keys(<?php $instance->json( $groups );?>).forEach( function( key ) {
					const group = <?php $instance->json( $groups );?>[ key ];
					$result.append( $( '<input type="button" class="select-group button-primary disabled"/>' ).val( group ) );
				} );
				$result.find( '.select-group' ).on( 'click', function() {
					$target.val( $( this ).val() );
					$result.find( '.select-group' ).addClass( 'disabled' );
					return false;
				} );
				$target.on( 'keyup', function() {
					const search = $( this ).val().toLowerCase();
					$result.find( '.select-group' ).addClass( 'disabled' );
					$result.find( '.select-group' ).each( function() {
						if ( '' !== search ) {
							const val = $( this ).val().toLowerCase();
							if ( val.indexOf( search ) !== -1 && val !== search ) {
								$( this ).removeClass( 'disabled' );
							}
						} else {
							$( this ).removeClass( 'disabled' );
						}
					} );
				} ).trigger( 'keyup' );
			} )();

			// preset
			( function() {
				const $target = $( '#<?php $instance->h( $name_prefix );?>style' );
				$( '.preset-style' ).on( 'click', function() {
					let result = $target.val().trim();
					const value = $( this ).data( 'value' );
					if ( value instanceof Array ) {
						value.forEach( function( item ) {
							if ( '' !== result ) {
								result += '\r\n';
							}
							result += item;
						} );
					} else {
						if ( '' !== result ) {
							result += '\r\n';
						}
						result += JSON.parse( value );
					}
					result += '\r\n';
					$target.val( result ).trigger( 'blur' );
					return false;
				} );
			} )();

			// preview
			( function() {
				$( '.display-auxiliary-line' ).on( 'change', function() {
					if ( $( this ).prop( 'checked' ) ) {
						$preview.find( 'body' ).addClass( 'auxiliary-line' );
					} else {
						$preview.find( 'body' ).removeClass( 'auxiliary-line' );
					}
				} ).trigger( 'change' );
				$( '.multiple-lines' ).on( 'change', function() {
					$( '#<?php $instance->h( $name_prefix );?>tag_name' ).trigger( 'input' );
				} );
			} )();
		} );
	} )( jQuery );
</script>
