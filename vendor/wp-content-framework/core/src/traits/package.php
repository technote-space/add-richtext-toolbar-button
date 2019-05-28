<?php
/**
 * WP_Framework_Core Traits Package
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits;

use WP_Framework;
use WP_Framework\Package_Base;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Package
 * @package WP_Framework_Core\Traits
 * @property WP_Framework $app
 */
trait Package {

	/**
	 * @return string
	 */
	public abstract function get_package();

	/**
	 * @param string|null $package
	 *
	 * @return string
	 */
	protected function get_package_directory( $package = null ) {
		return $this->app->get_package_directory( $package ? $package : $this->get_package() );
	}

	/**
	 * @param string|null $package
	 *
	 * @return Package_Base
	 */
	protected function get_package_instance( $package = null ) {
		return $this->app->get_package_instance( $package ? $package : $this->get_package() );
	}

	/**
	 * @param string|null $package
	 *
	 * @return string
	 */
	protected function get_package_namespace( $package = null ) {
		return $this->get_package_instance( $package )->get_namespace();
	}

	/**
	 * @param string $package
	 *
	 * @return bool
	 */
	protected function is_valid_package( $package ) {
		return $this->app->is_valid_package( $package );
	}
}
