<?php
/**
 * WP_Framework_Common Classes Models Config
 *
 * @version 0.0.22
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Config
 * @package WP_Framework_Common\Classes\Models
 */
class Config implements \WP_Framework_Core\Interfaces\Singleton {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Common\Traits\Package;

	/**
	 * @var mixed[] $_configs
	 */
	private $_configs = [];

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	public function load( $name ) {
		if ( ! isset( $this->_configs[ $name ] ) ) {
			$plugin_config = $this->load_config_file( $this->app->define->plugin_configs_dir, $name );
			$configs       = [];
			foreach ( $this->app->get_packages() as $package ) {
				$configs = array_replace_recursive( $configs, $package->get_config( $name ) );
			}
			$this->_configs[ $name ] = array_replace_recursive( $configs, $plugin_config );
		}

		return $this->_configs[ $name ];
	}

	/**
	 * @param string $name
	 * @param string|null $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $name, $key = null, $default = null ) {
		return isset( $key ) ? $this->app->utility->array_get( $this->load( $name ), $key, $default ) : $this->load( $name );
	}

	/**
	 * @param string $name
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $name, $key, $value ) {
		$this->load( $name );
		$this->app->utility->array_set( $this->_configs[ $name ], $key, $value );
	}

	/**
	 * @param string $dir
	 * @param string $name
	 *
	 * @return array
	 */
	private function load_config_file( $dir, $name ) {
		$path = rtrim( $dir, DS ) . DS . $name . '.php';
		if ( ! file_exists( $path ) ) {
			return [];
		}
		/** @noinspection PhpIncludeInspection */
		$config = include $path;
		if ( ! is_array( $config ) ) {
			$config = [];
		}

		return $config;
	}
}
