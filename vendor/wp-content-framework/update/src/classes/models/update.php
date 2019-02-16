<?php
/**
 * WP_Framework_Update Classes Models Update
 *
 * @version 0.0.2
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Update\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Update
 * @package WP_Framework_Update\Classes\Models
 */
class Update implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Update\Traits\Package;

	/**
	 * setup update
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_update() {
		$this->show_plugin_update_notices();
	}

	/**
	 * show plugin upgrade notices
	 */
	private function show_plugin_update_notices() {
		add_action( 'in_plugin_update_message-' . $this->app->define->plugin_base_name, function ( $data, $r ) {
			$new_version = $r->new_version;
			$url         = $this->app->utility->array_get( $data, 'PluginURI' );
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
	 * @param string $slug
	 *
	 * @return string
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_trunk_readme_url( $slug ) {
		return $this->apply_filters( 'trunk_readme_url', 'https://plugins.svn.wordpress.org/' . $slug . '/trunk/readme.txt', $slug );
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

		foreach (
			[
				'get_config_readme_url',
				'get_readme_url_from_update_info_url',
				'get_trunk_readme_url',
			] as $method
		) {
			$response = wp_safe_remote_get( $this->$method( $slug ) );
			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				return $this->parse_update_notice( $response['body'] );
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
		if ( empty( $slug ) ) {
			return false;
		}

		$transient_name = 'upgrade_notice-' . $slug . '_' . $version;
		$upgrade_notice = get_transient( $transient_name );

		if ( false === $upgrade_notice ) {
			$upgrade_notice = $this->get_upgrade_notice( $slug );
			if ( $upgrade_notice ) {
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
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
		if ( $this->app->utility->starts_with( $url, 'https://wordpress.org/plugins/' ) ) {
			return trim( str_replace( 'https://wordpress.org/plugins/', '', $url ), '/' );
		}

		return false;
	}

	/**
	 * @param string $content
	 *
	 * @return array
	 */
	private function parse_update_notice( $content ) {
		$notices         = [];
		$version_notices = [];
		if ( preg_match( '#==\s*Upgrade Notice\s*==([\s\S]+?)==#', $content, $matches ) ) {
			$version = false;
			foreach ( (array) preg_split( '~[\r\n]+~', trim( $matches[1] ) ) as $line ) {
				/** @noinspection HtmlUnknownTarget */
				$line = preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line );
				$line = preg_replace( '#\A\s*\*+\s*#', '', $line );
				if ( preg_match( '#\A\s*=\s*([^\s]+)\s*=\s*\z#', $line, $m1 ) && preg_match( '#\s*(v\.?)?(\d+[\d.]*)*\s*#', $m1[1], $m2 ) ) {
					$version = $m2[2];
					continue;
				}
				if ( $version && version_compare( $version, $this->app->get_plugin_version(), '<=' ) ) {
					continue;
				}
				$line = preg_replace( '#\A\s*=\s*([^\s]+)\s*=\s*\z#', '[ $1 ]', $line );
				$line = trim( $line );
				if ( '' !== $line ) {
					if ( $version ) {
						$version_notices[ $version ][] = $line;
					} else {
						$notices[] = $line;
					}
				}
			}
			if ( ! empty( $version_notices ) ) {
				ksort( $version_notices );
				foreach ( $version_notices as $version => $items ) {
					$notices[ $version ] = $items;
				}
			}
		}

		return $notices;
	}
}
