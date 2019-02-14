<?php
/**
 * WP_Framework_View Views Include Script Color
 *
 * @version 0.0.3
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
?>

<script>
    (function ($) {
        $(".<?php $instance->h( $instance->get_color_picker_class() );?>").each(function () {
            const $this = $(this);
            $this.wpColorPicker({
                defaultColor: false,
                change: function (event, ui) {
                    $this.val(ui.color.toString()).trigger('change');
                },
                clear: function () {
                    $this.trigger('<?php $instance->h( $instance->app->slug_name . '-' );?>cleared');
                },
                hide: true,
                palettes: true
            });
        });
    })(jQuery);
</script>
