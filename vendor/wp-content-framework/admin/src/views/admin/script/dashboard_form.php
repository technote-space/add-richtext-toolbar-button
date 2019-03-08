<?php
/**
 * WP_Framework_Admin Views Admin Script Dashboard_form
 *
 * @version 0.0.15
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
?>
<script>
    (function ($) {
        $(function () {
            let submit = false;
            $('form').submit(function () {
                if (submit) return false;
                submit = true;
                $('form input, form select').prop('readonly', true).addClass('disabled').off('click').on('click', function () {
                    return false;
                });
                return true;
            });
        });
    })(jQuery);
</script>
