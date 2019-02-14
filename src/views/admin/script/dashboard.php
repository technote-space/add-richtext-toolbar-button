<?php
/**
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
$instance->add_script_view( 'admin/script/icon' );
$target = '#' . preg_replace( '#/#', '\\\\\\\\/', $settings['default_icon']['name'] );
?>
<script>
    (function ($) {
        $(function () {
            // icon
            (function () {
                const $target = $('<?php $instance->h( $target );?>');
                $target.on('change', function () {
                    const icon = artbGetIcon($(this).val().trim());
                    const $area = $(this).closest('.icon-wrapper').find('.display-area');
                    $area.html('');
                    if (!icon) return;
                    $area.append(icon);
                }).trigger('change');
            })();

            $('input[name="reset"]').on('click', function () {
                return window.confirm('<?php $instance->h( 'Are you sure to reset settings?', true );?>');
            });
        });
    })(jQuery);
</script>
