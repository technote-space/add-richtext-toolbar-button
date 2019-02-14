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
?>

<script>
    (function ($) {
        $(function () {
            $('.display-icon').each(function(){
                const icon = artbGetIcon($(this).val());
                if (!icon) return;
                $(this).closest('.icon-wrapper').append(icon);
            });
        });
    })(jQuery);
</script>
