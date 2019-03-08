<?php
/**
 * WP_Framework_Admin Views Admin Include Dashboard_info
 *
 * @version 0.0.15
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $args */
$detail_url = $instance->app->get_config( 'config', 'detail_url' );
$twitter    = $instance->app->get_config( 'config', 'twitter' );
$github     = $instance->app->get_config( 'config', 'github_repo' );
$instance->add_style_view( 'admin/style/dashboard_info' );
?>
<div id="<?php $instance->id(); ?>-info-wrap">
    <div class="inner">
	    <?php $instance->get_view( 'admin/include/dashboard_before_info', $args, true, false ); ?>
		<?php if ( ! empty( $detail_url ) ): ?>
            <div class="box">
                <div class="title">
					<?php $instance->h( 'Plugin details in Japanese:', true ); ?>
                </div>
                <div class="content">
					<?php $instance->url( $detail_url, $detail_url, true, true ); ?>
                </div>
            </div>
		<?php endif; ?>
		<?php if ( ! empty( $twitter ) || ! empty( $github ) ): ?>
            <div class="box">
                <div class="title">
					<?php $instance->h( 'Reporting a new bug or feature request:', true ); ?>
                </div>
                <div class="content">
					<?php if ( ! empty( $twitter ) ): ?>
                        <a
                                href="https://twitter.com/intent/tweet?screen_name=<?php $instance->h( $twitter ); ?>"
                                class="twitter-mention-button" data-lang="ja"
                                data-related="<?php $instance->h( $twitter ); ?>">Tweet to @<?php $instance->h( $twitter ); ?></a>
                        <script>!function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (!d.getElementById(id)) {
                                    js = d.createElement(s);
                                    js.id = id;
                                    js.src = "//platform.twitter.com/widgets.js";
                                    fjs.parentNode.insertBefore(js, fjs);
                                }
                            }(document, "script", "twitter-wjs");</script>
					<?php endif; ?>
					<?php if ( ! empty( $github ) ): ?>
                        <a
                                class="github-button"
                                href="https://github.com/<?php $instance->h( $github ); ?>/issues"
                                data-size="large"
                                aria-label="Issue <?php $instance->h( $github ); ?> on GitHub">Issue</a>
                        <script async defer src="https://buttons.github.io/buttons.js"></script>
					<?php endif; ?>
                </div>
            </div>
		<?php endif; ?>
	    <?php $instance->get_view( 'admin/include/dashboard_after_info', $args, true, false ); ?>
    </div>
</div>
