<?php
/**
 * WP_Framework_Custom_Post Views Admin Script Import Custom Post
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
/** @var string $api_class */
/** @var string $post_type */
?>

<script>
    ( function ( $ ) {
        const $button = $( '.page-title-action' );
        if ( $button.length ) {
            $button.before( '<div class="import-button-wrapper"><input type="file"/><input type="button" value="<?php $instance->h( 'Import from JSON', true );?>" class="import-button button-primary large"/></div>' );

            const $wrapper = $('.import-button-wrapper');
            let importing = false;
            const importing_message = '<?php $instance->h( 'importing', true );?>...';
            const result_message = '<div class="result-wrap"><div class="result-message"></div><div><input type="button" class="button-primary large close" value="<?php $instance->h('Close', true);?>"/></div></div>';
            $wrapper.find( 'input[type="file"]' ).on( 'change', function () {
                if ( this.files.length > 0 ) {
                    if ( importing ) {
                        return false;
                    }
                    importing = true;

                    const formData = new FormData();
                    formData.append( 'import', this.files[0] );
                    formData.append( 'post_type', '<?php $instance->h( $post_type );?>' );
                    window.<?php $instance->h( $api_class );?>.ajax('import_custom_post', formData).done(function (json) {
                        const close_modal = function() {
                            if (json.success) {
                                location.reload();
                            } else {
                                window.<?php $instance->modal_class();?>.hide();
                            }
                        };
                        window.<?php $instance->modal_class();?>.show(false, close_modal);

                        window.<?php $instance->modal_class();?>.show_message(result_message);
                        window.<?php $instance->modal_class();?>._message().find('.result-message').html(json.message);
                        window.<?php $instance->modal_class();?>._message().find('.close').on('click', close_modal);
                        if (json.result) {
                            window.<?php $instance->modal_class();?>._message().find('.result-message').addClass('updated');
                        } else {
                            window.<?php $instance->modal_class();?>._message().find('.result-message').addClass('error');
                        }
                        window.<?php $instance->modal_class();?>._set_message_size();
                    }).fail(function (err) {
                        window.<?php $instance->modal_class();?>.hide();
                        console.log(err);
                    }).always(function () {
                        importing = false;
                    });

                    window.<?php $instance->modal_class();?>.show(true, null, importing_message);
                }
            } );
            $wrapper.find( '.import-button' ).on( 'click', function () {
                $wrapper.find( 'input[type="file"]' ).click();
            } );
        }
    } )( jQuery );
</script>
