<?php
/**
 * WP_Framework_Presenter Classes Models Drawer
 *
 * @version 0.0.16
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Presenter\Classes\Models;

use WP_Framework_Common\Traits\Uninstall;
use WP_Framework_Core\Interfaces\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Presenter;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Drawer
 * @package WP_Framework_Presenter\Classes\Models
 */
class Drawer implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter, \WP_Framework_Common\Interfaces\Uninstall {

	use Singleton, Hook, Presenter, Uninstall;

	/**
	 * @var string|false $_package
	 */
	private $_package = false;

	/**
	 * @return string
	 */
	public function get_package() {
		$package        = $this->_package ? $this->_package : 'presenter';
		$this->_package = false;

		return $package;
	}

	/**
	 * @param Package $package
	 */
	public function set_package( Package $package ) {
		$this->_package = $package->get_package();
	}

	/**
	 * @param string $html
	 * @param string $handle
	 * @param string $href
	 * @param string $media
	 *
	 * @return string
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function style_loader_tag(
		/** @noinspection PhpUnusedParameterInspection */
		$html, $handle, $href, $media
	) {
		if ( $handle === $this->app->get_config( 'config', 'fontawesome_handle' ) ) {
			$integrity   = $this->app->get_config( 'config', 'fontawesome_integrity' );
			$crossorigin = $this->app->get_config( 'config', 'fontawesome_crossorigin' );
			if ( empty( $integrity ) && empty( $crossorigin ) ) {
				return $html;
			}

			$replace = "media='{$media}'";
			! empty( $integrity ) and $replace .= " integrity='{$integrity}'";
			! empty( $crossorigin ) and $replace .= " crossorigin='{$crossorigin}'";

			return str_replace( "media='{$media}' />", "{$replace}  />", $html );
		}

		return $html;
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$this->app->file->delete_upload_dir( $this->app );
	}
}
