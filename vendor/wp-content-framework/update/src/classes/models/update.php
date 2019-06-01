<?php
/**
 * WP_Framework_Update Classes Models Update
 *
 * @version 0.0.8
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Update\Classes\Models;

use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Presenter;
use WP_Framework_Update\Traits\Package;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Update
 * @package WP_Framework_Update\Classes\Models
 */
class Update implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * setup update
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_update() {
		add_action( 'in_plugin_update_message-' . $this->app->define->plugin_base_name, function ( $data, $r ) {
			$new_version = $r->new_version;
			$url         = $this->app->array->get( $data, 'PluginURI' );
			$notices     = $this->get_upgrade_notices( $new_version, $url );
			if ( ! empty( $notices ) ) {
				$this->get_view( 'admin/include/update', [
					'notices' => $notices,
				], true );
			}
		}, 10, 2 );
	}

	/**
	 * @return string|false
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_config_readme_url() {
		$url = $this->app->get_config( 'config', 'readme_file_check_url' );
		if ( ! empty( $url ) ) {
			return $url;
		}

		return false;
	}

	/**
	 * @return string|false
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_readme_url_from_update_info_url() {
		$url = $this->app->get_config( 'config', 'update_info_file_url' );
		if ( ! empty( $url ) ) {
			$info = pathinfo( $url );

			return $info['dirname'] . '/readme.txt';
		}

		return false;
	}

	/**
	 * @param string|false $slug
	 *
	 * @return string|false
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_trunk_readme_url( $slug ) {
		return $slug ? $this->apply_filters( 'trunk_readme_url', 'https://plugins.svn.wordpress.org/' . $slug . '/trunk/readme.txt', $slug ) : false;
	}

	/**
	 * @param string $slug
	 *
	 * @return array|false
	 */
	private function get_upgrade_notice( $slug ) {
		$notice = $this->apply_filters( 'pre_get_update_notice', false, $slug );
		if ( is_array( $notice ) ) {
			return $notice;
		}

		$plugin_version = $this->app->get_plugin_version();
		if ( $this->app->get_config( 'config', 'local_test_upgrade_notice' ) ) {
			$readme = $this->app->define->plugin_dir . DS . 'readme.txt';
			if ( @is_readable( $readme ) ) {
				$test_version   = $this->app->get_config( 'config', 'local_test_upgrade_version' );
				$plugin_version = $test_version ? $test_version : $plugin_version;

				return $this->parse_update_notice( @file_get_contents( $readme ), $plugin_version );
			}

			return false;
		}

		foreach (
			[
				'get_config_readme_url',
				'get_readme_url_from_update_info_url',
				'get_trunk_readme_url',
			] as $method
		) {
			$url = $this->$method( $slug );
			if ( $url ) {
				$response = wp_safe_remote_get( $url );
				if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
					return $this->parse_update_notice( $response['body'], $plugin_version );
				}
			}
		}

		return false;
	}

	/**
	 * @param string $version
	 * @param string $url
	 *
	 * @return bool|mixed
	 */
	private function get_upgrade_notices( $version, $url ) {
		$slug = $this->get_plugin_slug( $url );
		if ( $this->app->get_config( 'config', 'local_test_upgrade_notice' ) ) {
			return $this->get_upgrade_notice( $slug );
		}

		$hash           = $this->app->utility->create_hash( $this->app->plugin_name . '/' . $version, 'upgrade' );
		$transient_name = 'upgrade_notice-' . $hash;
		$upgrade_notice = get_transient( $transient_name );

		if ( false === $upgrade_notice ) {
			$upgrade_notice = $this->get_upgrade_notice( $slug );
			if ( $upgrade_notice ) {
				set_transient( $transient_name, $upgrade_notice, $this->app->get_config( 'config', 'upgrade_notice_cache_duration' ) );
			} else {
				set_transient( $transient_name, '', $this->app->get_config( 'config', 'upgrade_notice_empty_cache_duration' ) );
			}
		}

		return $upgrade_notice;
	}

	/**
	 * @param string $url
	 *
	 * @return false|string
	 */
	private function get_plugin_slug( $url ) {
		if ( preg_match( '#\Ahttps://(\w+\.)?wordpress.org/plugins/(.+?)/?\z#', $url, $matches ) ) {
			return $matches[2];
		}

		return false;
	}

	/**
	 * @param string $content
	 * @param string $plugin_version
	 *
	 * @return array
	 */
	private function parse_update_notice( $content, $plugin_version ) {
		$notices         = [];
		$version_notices = [];
		if ( preg_match( '#==\s*Upgrade Notice\s*==([\s\S]+?)==#', $content, $matches ) ) {
			$version = false;
			foreach ( (array) preg_split( '~[\r\n]+~', trim( $matches[1] ) ) as $line ) {
				$line = preg_replace( '~\[\[([^\]]*)\]\]\(([^\)]*)\)~', '<span style="${2}">${1}</span>', $line );
				/** @noinspection HtmlUnknownTarget */
				$line = preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}" target="_blank" rel="noopener noreferrer">${1}</a>', $line );
				$line = preg_replace( '#\A\s*\*+\s*#', '', $line );
				$line = preg_replace( '#\*\*\s*([^*]+)\s*\*\*#', '<b>${1}</b>', $line );
				$line = preg_replace( '#`\s*(.+)\s*`#', '<code>${1}</code>', $line );
				$line = preg_replace( '#~~\s*(.+)\s*~~#', '<s>${1}</s>', $line );
				if ( preg_match( '#\A\s*=\s*([^\s]+)\s*=\s*\z#', $line, $m1 ) && preg_match( '#\s*(v\.?)?(\d+[\d.]*)*\s*#', $m1[1], $m2 ) ) {
					$version = $m2[2];
					continue;
				}
				if ( $version && version_compare( $version, $plugin_version, '<=' ) ) {
					continue;
				}
				$line = preg_replace( '#\A\s*=\s*([^\s]+)\s*=\s*\z#', '[ $1 ]', $line );
				$line = trim( $line );
				if ( '' !== $line ) {
					$line = $this->app->string->strip_tags( $line, [
						'span' => [ 'style' => true ],
					] );
					if ( $version ) {
						$version_notices[ $version ][] = $line;
					} else {
						$notices[] = $line;
					}
				}
			}
			if ( ! empty( $version_notices ) ) {
				uksort( $version_notices, function ( $a, $b ) {
					return version_compare( $a, $b );
				} );
				foreach ( $version_notices as $version => $items ) {
					$notices[ $version ] = $items;
				}
			}
		}

		return $notices;
	}
}
