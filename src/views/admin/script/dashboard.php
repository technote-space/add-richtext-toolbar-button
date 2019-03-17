<?php
/**
 * @version 1.0.0
 * @author Technote
 * @since 1.0.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
$instance->add_script_view( 'admin/script/icon' );
$target = [];
foreach ( $settings as $k => $v ) {
	if ( $instance->app->utility->ends_with( $k, '_icon' ) ) {
		$target[] = '#' . preg_replace( '#/#', '\\\\/', $v['name'] );
	}
}
?>
<script>
    (function ($) {
        $(function () {
            // icon
            (function () {
				<?php $instance->h( 'const target = ' );?><?php $instance->json( $target );?>;
                Object.keys(target).forEach(function (key) {
                    const $target = $(target[key]);
                    $target.on('change', function () {
                        const icon = artbGetIcon($(this).val().trim());
                        const $area = $(this).closest('.icon-wrapper').find('.display-area');
                        $area.html('');
                        if (!icon) return;
                        $area.append(icon);
                    }).trigger('change');
                });

                $('.reset-icon').on('click', function () {
                    const $target = $($(this).data('target'));
                    $target.val($(this).data('value')).trigger('change');
                    return false;
                });
            })();

            $('.form-buttons input[name="reset"]').on('click', function () {
                if (window.confirm('<?php $instance->h( 'Are you sure to reset settings?', true );?>')) {
                    $(this).closest('form').submit();
                }
                return false;
            });
        });
    })(jQuery);
</script>
