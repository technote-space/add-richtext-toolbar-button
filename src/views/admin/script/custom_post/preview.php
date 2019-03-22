<?php
/**
 * @version 1.1.0
 * @author Technote
 * @since 1.0.0
 * @since 1.0.14 #82
 * @since 1.1.0 trivial change
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var string $css_handle */
/** @var string $fontawesome_handle */
/** @var string $editor_styles */
$instance->add_script_view( 'admin/script/icon' );
?>

<script>
    (function ($) {
        $(function () {
            $('.display-icon').each(function () {
                const icon = artbGetIcon($(this).val());
                if (!icon) return;
                $(this).closest('.icon-wrapper').append(icon);
            });

            const main_css = $('#<?php $instance->h( $css_handle );?>-css');
            const fontawesome_css = $('#<?php $instance->h( $fontawesome_handle );?>-css');
            $('.preview-iframe').each(function () {
                $(this).contents().find('body').append('<div id="preview-wrap">');

                $(this).contents().find('#preview-wrap').append($('<' + $(this).data('tag_name') + '>', {
                    class: $(this).data('class_name'),
                    text: '<?php $instance->h( $instance->app->filter->apply_filters( 'test_phrase' ) );?>'
                }));

				<?php if ($editor_styles) :?>
                $(this).contents().find('head').append($('<style>', {
                    type: 'text/css',
                    text: "<?php $instance->h( $editor_styles, false, true, false );?>"
                }));
				<?php endif;?>
                $(this).contents().find('head').append($('<style>', {
                    type: 'text/css',
                    text: 'body{font-size: 13px; margin: 0; background: transparent!important} #preview-wrap{margin: 1em}'
                }));
                $(this).contents().find('head').append(main_css.clone());
                $(this).contents().find('head').append(fontawesome_css.clone());
            });
        });
    })(jQuery);
</script>
