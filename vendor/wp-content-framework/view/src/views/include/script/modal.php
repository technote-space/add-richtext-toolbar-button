<?php
/**
 * WP_Framework_View Views Include Script Modal
 *
 * @version 0.0.6
 * @author Technote
 * @copyright Technote All Rights Reserved
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
        if (window.<?php $instance->modal_class();?> !== undefined) {
            return;
        }

        function <?php $instance->modal_class();?>() {
            const target = '<?php if ( is_admin() ): ?>#wpwrap<?php else: ?>body<?php endif;?>';
            const html = '<div id="<?php $instance->id();?>-modal"><div class="<?php $instance->id();?>-loading"></div><div class="<?php $instance->id();?>-loading-message"></div></div><div id="<?php $instance->id();?>-modal-message-wrap"><div id="<?php $instance->id();?>-modal-message"></div></div>';
            $(html).prependTo(target).hide();
            $('#<?php $instance->id();?>-modal-message').click(function (e) {
                e.stopPropagation();
            });
            this.timer = null;

            this._modal = function () {
                return $('#<?php $instance->id();?>-modal');
            };

            this._loading = function () {
                return $('#<?php $instance->id();?>-modal .<?php $instance->id();?>-loading');
            };

            this._loading_message = function () {
                return $('#<?php $instance->id();?>-modal .<?php $instance->id();?>-loading-message');
            };

            this._message_wrap = function () {
                return $('#<?php $instance->id();?>-modal-message-wrap');
            };

            this._message = function () {
                return $('#<?php $instance->id();?>-modal-message');
            };

            this._modal_and_message_wrap = function () {
                return $('#<?php $instance->id();?>-modal, #<?php $instance->id();?>-modal-message-wrap');
            };

            this.show = function (loading, click, message) {
                this._modal().stop(true, true).fadeIn();
                if (loading) {
                    this._loading().stop(true, true).fadeIn();
                    this._loading_message().stop(true, true).fadeIn();
                    if (message) {
                        this._loading_message().html(message);
                    }
                }
                this._message_wrap().stop(true, true).fadeOut();
                if (click) {
                    this._modal_and_message_wrap().unbind('click').click(function () {
                        click();
                        return false;
                    });
                }
            };

            this.show_loading = function () {
                this._loading().stop(true, true).fadeIn();
            };

            this.show_message = function (message) {
                this._message_wrap().stop(true, true).show();
                if (message) {
                    this.set_message(message);
                }
                const $this = this;
                let check_resize = function () {
                    if ($this.timer) {
                        clearTimeout($this.timer);
                        $this.timer = null;
                    }
                    if ($this._message_wrap().is(':visible')) {
                        $this._set_message_size();
                        $this.timer = setTimeout(check_resize, 1000);
                    }
                };
                check_resize();
            };

            this.hide = function () {
                this._modal().stop(true, true).fadeOut();
                this.hide_loading();
                this.hide_message();
            };

            this.hide_loading = function () {
                this._loading().stop(true, true).fadeOut();
                this._loading_message().stop(true, true).fadeOut();
            };

            this.hide_message = function () {
                this._message_wrap().stop(true, true).fadeOut();
            };

            this.set_message = function (message) {
                this._message().html(message);
                this._set_message_size();
            };

            this._set_message_size = function () {
                const height = parseInt(this._message_wrap().get(0).offsetHeight / 2);
                this._message_wrap().css('margin-top', -height + 'px');
            };

        }

        window.<?php $instance->modal_class();?> = new <?php $instance->modal_class();?> ();
    })(jQuery);
</script>
