<?php
/**
 * WP_Framework_Presenter Views Include Script Api
 *
 * @version 0.0.10
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var string $endpoint */
/** @var string $namespace */
/** @var string $nonce */
/** @var array $functions */
/** @var string $api_class */
/** @var array $scripts */
/** @var bool $is_admin_ajax */
/** @var string $nonce_key */
/** @var string $nonce_value */
?>

<script>
    (function ($) {
        function <?php $instance->h( $api_class );?>() {
            this.is_admin_ajax = <?php $instance->h( $is_admin_ajax ? 'true' : 'false' );?>;
            this.endpoint = '<?php $instance->h( $endpoint );?>';
            this.namespace = '<?php $instance->h( $namespace );?>';
            this.functions = <?php echo json_encode( $functions );?>;
            this.xhr = {};
            this.nonce_key = '<?php $instance->h( isset( $nonce_key ) ? $nonce_key : '' );?>';
            this.nonce_value = '<?php $instance->h( isset( $nonce_value ) ? $nonce_value : '' );?>';
            this.nonce = '<?php $instance->h( isset( $nonce ) ? $nonce : '' );?>';

            this.ajax = function (func, args, single, nonce_check) {
                if (args === undefined) args = {};
                if (single === undefined) single = true;
                if (this.functions[func]) {
                    const setting = this.functions[func];
                    let url = this.endpoint;
                    if (!this.is_admin_ajax) {
                        url += this.namespace + '/' + setting.endpoint;
                    } else {
                        if (args instanceof FormData) {
                            args.append(this.nonce_key, this.nonce_value);
                            args.append('action', this.namespace + '_' + setting.endpoint);
                        } else {
                            args[this.nonce_key] = this.nonce_value;
                            args.action = this.namespace + '_' + setting.endpoint;
                        }
                    }
                    const method = setting.method.toUpperCase();
                    const config = {};
                    if (method === 'GET' || method === 'HEAD') {
                        config.method = method;
                        const query = [];
                        args._ = (new Date()).getTime();
                        for (let prop in args) {
                            if (args.hasOwnProperty(prop)) {
                                query.push(prop + '=' + encodeURIComponent(args[prop]));
                            }
                        }
                        if (url.indexOf('?') !== -1) {
                            url += '&' + query.join('&');
                        } else {
                            url += '?' + query.join('&');
                        }
                    } else {
                        config.method = 'POST';
                        config.data = args;
                        if (method !== 'POST') {
                            if (args instanceof FormData) {
                                config.data.append('_method', method);
                            } else {
                                config.data._method = method;
                            }
                        }
                    }
                    config.url = url;
                    return this._ajax(config, func, args, single, nonce_check);
                } else {
                    const $defer = $.Deferred();
                    setTimeout(function () {
                        $defer.reject([-1, null, null]);
                    }, 1);
                    return $defer.promise();
                }
            };

            this.abort = function (func) {
                if (this.xhr[func]) {
                    this.xhr[func].abort();
                    this.xhr[func] = null;
                }
            };


            this._param = function (a) {
                const s = [];
                const add = function (key, value) {
                    s[s.length] = encodeURIComponent(key) + "=" + encodeURIComponent(value == null ? "" : value);
                };

                if (Array.isArray(a)) {
                    this._each(a, function () {
                        add(this.name, this.value);
                    });
                } else {
                    for (let prefix in a) {
                        if (a.hasOwnProperty(prefix)) {
                            this._buildParams(prefix, a[prefix], add);
                        }
                    }
                }
                return s.join('&');
            };

            this._buildParams = function (prefix, obj, add) {
                const self = this;
                if (Array.isArray(obj)) {
                    this._each(obj, function (i, v) {
                        self._buildParams(prefix + "[" + (typeof v === "object" && v != null ? i : "") + "]", v, add);
                    });
                } else if ("object" === typeof obj) {
                    for (let name in obj) {
                        self._buildParams(prefix + "[" + name + "]", obj[name], add);
                    }
                } else {
                    add(prefix, obj);
                }
            };

            this._each = function (obj, fn) {
                if (obj.length === undefined) {
                    for (let i in obj) {
                        if (obj.hasOwnProperty(i)) {
                            fn.call(obj[i], i, obj[i]);
                        }
                    }
                }
                else {
                    for (let i = 0, ol = obj.length, val = obj[0];
                         i < ol && fn.call(val, i, val) !== false; val = obj[++i]) {
                    }
                }
                return obj;
            };

            this._ajax = function (config, func, args, single, nonce_check) {
                const $this = this;
                if (single) this.abort(func);
                const $defer = $.Deferred();
                const xhr = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();

                xhr.open(config.method, config.url, true);
                if (!(args instanceof FormData)) {
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                }
                if (!this.is_admin_ajax && !nonce_check) {
                    xhr.setRequestHeader('X-WP-Nonce', this.nonce);
                }
                xhr.onreadystatechange = function () {
                    if (4 === xhr.readyState) {
                        let json, err = null;
                        try {
                            json = JSON.parse(xhr.responseText);
                        } catch (e) {
                            json = undefined;
                            err = e;
                        }
                        if (200 === xhr.status) {
                            if (undefined === json) {
                                $defer.reject([xhr.status, err, xhr, json]);
                            } else {
                                if (json.nonce_data) {
                                    $this._update_nonce(json.nonce_data);
                                }
                                $defer.resolve(json);
                            }
                        } else if (403 === xhr.status && nonce_check === undefined) {
                            $this.ajax('get_nonce', {}, false, true).done(function (json) {
                                $this._update_nonce(json);
                                $this.ajax(func, args, single, false).done(function (json) {
                                    $defer.resolve(json);
                                }).fail(function (err) {
                                    $defer.reject(err);
                                });
                            }).fail(function () {
                                $defer.reject([xhr.status, null, xhr, json]);
                            });
                        } else {
                            $defer.reject([xhr.status, null, xhr, json]);
                        }
                        $this.xhr[func] = null;
                    }
                };
                if (config.data) {
                    if (args instanceof FormData) {
                        xhr.send(config.data);
                    } else {
                        xhr.send($this._param(config.data));
                    }
                } else {
                    xhr.send();
                }
                if (single) $this.xhr[func] = xhr;
                return $defer.promise();
            };

            this._update_nonce = function (json) {
                this.is_admin_ajax = json.is_admin_ajax;
                this.nonce_key = json.nonce_key;
                this.nonce_value = json.nonce_value;
                this.nonce = json.nonce;
            };
        }

        window.<?php $instance->h( $api_class );?> = new <?php $instance->h( $api_class );?> ();
    })(jQuery);
</script>
