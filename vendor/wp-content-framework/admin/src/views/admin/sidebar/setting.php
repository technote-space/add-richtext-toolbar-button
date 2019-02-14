<?php
/**
 * WP_Framework_Admin Views Admin Sidebar Setting
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
$contact = $instance->app->get_config( 'config', 'contact_url' );
$twitter = $instance->app->get_config( 'config', 'twitter' );
$github  = $instance->app->get_config( 'config', 'github' );
if ( empty( $contact ) && empty( $twitter ) && empty( $github ) ) {
	return;
}
?>

<ul>
	<?php if ( ! empty( $contact ) ): ?>
        <li>
			<?php $instance->url( $contact, 'Contact', true, true ); ?>
        </li>
	<?php endif; ?>
	<?php if ( ! empty( $twitter ) ): ?>
        <li>
			<?php $instance->url( 'https://twitter.com/' . $twitter, 'Twitter', true, true ); ?>
        </li>
	<?php endif; ?>
	<?php if ( ! empty( $github ) ): ?>
        <li>
			<?php $instance->url( 'https://github.com/' . $github, 'Github', true, true ); ?>
        </li>
	<?php endif; ?>
</ul>
