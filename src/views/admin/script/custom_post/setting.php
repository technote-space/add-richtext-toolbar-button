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
/** @var string $name_prefix */
/** @var array $groups */
$instance->add_script_view( 'admin/script/icon' );
?>

<script>
    (function ($) {
        $(function () {
            // style
            (function () {
                const applyStyles = function (style) {
                    const selector = '.setting-preview span.preview-item';
                    const previewStyleId = 'setting-preview-style';
                    {
                        $('#' + previewStyleId).remove();
                    }

                    {
                        $('<style type="text/css" id="' + previewStyleId + '"/>').appendTo('head');
                        applyStyle(style, selector, $('#' + previewStyleId));
                    }
                };

                const applyStyle = function (style, selector, $elem) {
                    const styles = {};
                    style.split(/\r\n|\r|\n|;/).forEach(function (v) {
                        const match = v.match(/^(\[([-().#>+~|*a-z]+)]\s*)?(.+?)\s*:\s*(.+?)\s*$/);
                        if (match) {
                            const pseudo = undefined === match[2] ? '' : match[2];
                            const key = match[3];
                            const val = match[4];
                            if (!styles[pseudo]) styles[pseudo] = [];
                            styles[pseudo].push(key + ': ' + val);
                        }
                    });

                    let results = [];
                    Object.keys(styles).forEach(function (pseudo) {
                        let s = selector;
                        if (pseudo) s += ':' + pseudo;
                        results.push(s + '{' + styles[pseudo].join(';\r\n') + '}');
                    });
                    $elem.text(results.join('\r\n'));
                };

                const $target = $('#<?php $instance->h( $name_prefix );?>style');
                $target.on('blur', function () {
                    applyStyles($target.val());
                    $target.trigger('input');
                });

                $('.clear-style').on('click', function () {
                    $target.val('').trigger('blur');
                    return false;
                });

                $('.reset-style').on('click', function () {
                    $target.val(JSON.parse($target.data('reset'))).trigger('blur');
                    return false;
                });

                $target.on('input', function (e) {
                    applyStyles($target.val());
                    if (e.target.scrollHeight > e.target.offsetHeight) {
                        $(e.target).height(e.target.scrollHeight);
                    } else {
                        const lineHeight = Number($(e.target).css("lineHeight").split("px")[0]);
                        const minHeight = lineHeight * $target.attr('rows');
                        while (true) {
                            $(e.target).height($(e.target).height() - lineHeight);
                            if (e.target.scrollHeight > e.target.offsetHeight || minHeight > e.target.offsetHeight) {
                                $(e.target).height(Math.max(e.target.scrollHeight, minHeight));
                                break;
                            }
                        }
                    }
                }).trigger('input');
            })();

            // icon
            (function () {
                const $target = $('#<?php $instance->h( $name_prefix );?>icon');
                $target.on('change', function () {
                    const icon = artbGetIcon($(this).val().trim());
                    const $area = $(this).closest('.icon-wrapper').find('.display-area');
                    $area.html('');
                    if (!icon) return;
                    $area.append(icon);
                }).trigger('change');
            })();

            // reset
            (function () {
                $('.reset-icon').on('click', function () {
                    const $target = $($(this).data('target'));
                    $target.val($(this).data('value')).trigger('change');
                    return false;
                });
            })();

            // group
            (function () {
                const $target = $('#<?php $instance->h( $name_prefix );?>group_name');
                const $parent = $target.closest('dd');
                $parent.append('<span class="search-result"/>');
                const $result = $parent.find('.search-result');
                Object.keys(<?php $instance->json( $groups );?>).forEach(function (key) {
                    const group = <?php $instance->json( $groups );?>[key];
                    $result.append($('<input type="button" class="select-group button-primary disabled"/>').val(group));
                });
                $result.find('.select-group').on('click', function () {
                    $target.val($(this).val());
                    $result.find('.select-group').addClass('disabled');
                    return false;
                });
                $target.on('keyup', function () {
                    const search = $(this).val().toLowerCase();
                    $result.find('.select-group').addClass('disabled');
                    if ('' !== search) {
                        $result.find('.select-group').each(function () {
                            if ($(this).val().toLowerCase().indexOf(search) !== -1) {
                                $(this).removeClass('disabled');
                            }
                        });
                    }
                });
            })();

            // preset
            (function () {
                const $target = $('#<?php $instance->h( $name_prefix );?>style');
                $('.preset-style').on('click', function () {
                    let result = $target.val().trim();
                    const value = $(this).data('value');
                    if (value instanceof Array) {
                        value.forEach(function (item) {
                            if ('' !== result) result += '\r\n';
                            result += item;
                        });
                    } else {
                        if ('' !== result) result += '\r\n';
                        result += JSON.parse(value);
                    }
                    result += '\r\n';
                    $target.val(result).trigger('blur');
                    return false;
                });
            })();
        });
    })(jQuery);
</script>
