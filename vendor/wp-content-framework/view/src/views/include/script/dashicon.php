<?php
/**
 * WP_Framework_View Views Include Script Dashicon
 *
 * @version 0.0.3
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 *
 * Based on: https://github.com/bradvin/dashicons-picker/
 * @license https://github.com/bradvin/dashicons-picker/blob/master/LICENSE
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
$icons = $instance->app->get_config( 'dashicon', 'available', [] );
! is_array( $icons ) and $icons = [];
?>

<script>
    (function ($) {
        $.fn.dashiconsPicker = function (...args) {
            return this.each(function () {
                const target = $(this);
                const namespace = 'dashiconsPicker';
                const dashiconsPickerObj = {
                    op: {
                        icons: <?php $instance->json( $icons );?>
                    },
                    setOption: function (op) {
                        $.extend(this.op, op);
                    },
                    destroy: function () {
                        $('.dashicon-picker-container').remove();
                        target.attr('data-dashicons_picker', false);
                    },
                    create: function (op) {
                        const $this = this;
                        this.setOption(op);

                        const $target = $(target.data('target')),
                            popup = $('<div class="dashicon-picker-container"><div class="dashicon-picker-control" /><ul class="dashicon-picker-list" /></div>')
                                .css({
                                    'top': target.offset().top,
                                    'left': target.offset().left
                                }),
                            list = popup.find('.dashicon-picker-list');

                        $($this.op.icons).each(function (index, value) {
                            list.append('<li data-icon="' + value + '"><a href="#" title="' + value + '"><span class="dashicons dashicons-' + value + '"/></a></li>');
                        });

                        $('a', list).on('click', function (e) {
                            e.preventDefault();
                            $target.val('dashicons-' + $(this).attr('title')).trigger('change');
                            $this.destroy();
                            $this.stop();
                            return false;
                        });

                        const control = popup.find('.dashicon-picker-control');
                        control.html('<a data-direction="back" href="#"><span class="dashicons dashicons-arrow-left-alt2"/></a><input type="text" class="" placeholder="Search" /><a data-direction="forward" href="#"><span class="dashicons dashicons-arrow-right-alt2"/></a>');
                        $('a', control).on('click', function (e) {
                            e.preventDefault();
                            if ($(this).data('direction') === 'back') {
                                $('li:gt(' + ($this.op.icons.length - 26) + ')', list).prependTo(list);
                            } else {
                                $('li:lt(25)', list).appendTo(list);
                            }
                        });

                        popup.appendTo('body').show();

                        $('input', control).on('keyup', function () {
                            const search = $(this).val();
                            if (search === '') {
                                $('li:lt(25)', list).show();
                            } else {
                                $('li', list).each(function () {
                                    if ($(this).data('icon').toLowerCase().indexOf(search.toLowerCase()) !== -1) {
                                        $(this).show();
                                    } else {
                                        $(this).hide();
                                    }
                                });
                            }
                        });

                        $(document).on('mouseup.' + namespace, function (e) {
                            if (!popup.is(e.target) && popup.has(e.target).length === 0) {
                                $this.destroy();
                                $this.stop();
                            }
                        });
                    },
                    refresh: function () {
                        this.destroy();
                        this.stop();
                        this.create();
                    },
                    removeEvent: function () {
                        target.off('.' + namespace);
                        this.stop();
                    },
                    stop: function () {
                        $(document).off('mouseup.' + namespace);
                    }
                };
                if (typeof args[0] === 'string' && args[0] === 'destroy') {
                    target.trigger('destroy.' + namespace);
                } else if (typeof args[0] === 'string' && args[0] === 'refresh') {
                    target.trigger('refresh.' + namespace);
                } else {
                    if (target.attr('data-dashicons_picker')) {
                        dashiconsPickerObj.destroy();
                        dashiconsPickerObj.removeEvent();
                    }

                    const options = $.extend({}, args[0]);
                    target.on('click.' + namespace, function () {
                        dashiconsPickerObj.create(options);
                        return false;
                    }).on('destroy.' + namespace, function () {
                        dashiconsPickerObj.destroy();
                        dashiconsPickerObj.removeEvent();
                    }).on('refresh.' + namespace, function () {
                        dashiconsPickerObj.refresh();
                    });
                }
            });
        };

        $(function () {
            $(".<?php $instance->h( $instance->get_dashicon_picker_class() );?>").dashiconsPicker();
        });
    })(jQuery);
</script>
