<?php
/**
 * WP_Framework_Core Traits Loader
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits;

use Exception;
use Generator;
use WP_Framework;
use WP_Framework_Admin\Classes\Controllers\Admin\Base;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Loader
 * @package WP_Framework_Core\Traits\Controller
 * @property WP_Framework $app
 */
trait Loader {

	use Singleton, Hook;

	/**
	 * @var array $_list
	 */
	private $_list = null;
	/**
	 * @var int $_count
	 */
	private $_count = null;

	/**
	 * @var array
	 */
	private $_namespaces = null;

	/**
	 * @return string
	 */
	public function get_loader_name() {
		return $this->get_file_slug();
	}

	/**
	 * @param string $namespace
	 *
	 * @return string|false
	 */
	private function namespace_to_dir( $namespace ) {
		$namespace = ltrim( $namespace, '\\' );
		$dir       = null;
		if ( preg_match( "#\A{$this->app->define->plugin_namespace}(.+)\z#", $namespace, $matches ) ) {
			$namespace = $matches[1];
			$dir       = $this->app->define->plugin_src_dir;
		} else {
			foreach ( $this->app->get_packages() as $package ) {
				list( $dir, $relative ) = $package->namespace_to_dir( $namespace );
				if ( isset( $dir ) ) {
					$namespace = $relative;
					break;
				}
			}
		}

		if ( isset( $dir ) ) {
			$namespace = ltrim( $namespace, '\\' );
			$namespace = strtolower( $namespace );
			$path      = $dir . DS . str_replace( '\\', DS, $namespace );
			$path      = rtrim( $path, DS );
			if ( is_dir( $path ) ) {
				return $path;
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function get_class_list() {
		if ( ! isset( $this->_list ) ) {
			$this->_list = [];
			$cache       = $this->cache_get_common( 'class_settings', null, false, $this->is_common_cache_class_settings() );
			if ( is_array( $cache ) ) {
				/** @var Singleton $class */
				foreach ( $this->get_class_instances( $cache, $this->get_instanceof() ) as list( $class ) ) {
					$slug = $class->get_class_name();
					if ( ! isset( $this->_list[ $slug ] ) ) {
						$this->_list[ $slug ] = $class;
					}
				}
			} else {
				$sort    = [];
				$classes = [];
				foreach ( $this->_get_namespaces() as $namespace ) {
					/** @var Singleton $class */
					foreach ( $this->get_classes( $this->namespace_to_dir( $namespace ), $this->get_instanceof() ) as list( $class, $setting ) ) {
						$slug = $class->get_class_name();
						if ( ! isset( $classes[ $slug ] ) ) {
							$classes[ $slug ] = [ $class, $setting ];
							if ( method_exists( $class, 'get_load_priority' ) ) {
								$sort[ $slug ] = $class->get_load_priority();
								if ( $sort[ $slug ] < 0 ) {
									unset( $classes[ $slug ] );
									unset( $sort[ $slug ] );
								}
							}
						}
					}
				}
				if ( ! empty( $sort ) ) {
					uasort( $classes, function ( $a, $b ) use ( $sort ) {
						/** @var Singleton[] $a */
						/** @var Singleton[] $b */
						$pa = isset( $sort[ $a[0]->get_class_name() ] ) ? $sort[ $a[0]->get_class_name() ] : 10;
						$pb = isset( $sort[ $b[0]->get_class_name() ] ) ? $sort[ $b[0]->get_class_name() ] : 10;

						return $pa == $pb ? 0 : ( $pa < $pb ? - 1 : 1 );
					} );
				}
				$this->_list = $this->app->array->map( $classes, function ( $item ) {
					return $item[0];
				} );
				$settings    = $this->app->array->map( $classes, function ( $item ) {
					return $item[1];
				} );
				$this->cache_set_common( 'class_settings', $settings, false, null, $this->is_common_cache_class_settings() );
			}
		}

		return $this->_list;
	}

	/**
	 * @return int
	 */
	public function get_loaded_count() {
		return count( $this->get_class_list() );
	}

	/**
	 * @param string $dir
	 *
	 * @return Generator
	 */
	protected function get_class_settings( $dir ) {
		foreach ( $this->app->file->scan_dir_namespace_class( $dir, true ) as list( $namespace, $class, $path ) ) {
			$setting = $this->get_class_setting( $class, $namespace );
			if ( is_array( $setting ) ) {
				$setting[] = $path;
			}
			yield $setting;
		}
	}

	/**
	 * @param string $dir
	 * @param string $instanceof
	 *
	 * @return Generator
	 */
	protected function get_classes( $dir, $instanceof ) {
		foreach ( $this->get_class_instances( $this->get_class_settings( $dir ), $instanceof ) as list( $instance, $setting ) ) {
			yield [ $instance, $setting ];
		}
	}

	/**
	 * @param iterable $settings
	 * @param string $instanceof
	 *
	 * @return Generator
	 */
	protected function get_class_instances( $settings, $instanceof ) {
		foreach ( $settings as $setting ) {
			$instance = $this->get_class_instance( $setting, $instanceof );
			if ( false !== $instance ) {
				yield [ $instance, $setting ];
			}
		}
	}

	/**
	 * @param string $class_name
	 * @param string $add_namespace
	 *
	 * @return false|array
	 */
	protected function get_class_setting( $class_name, $add_namespace = '' ) {
		$namespaces = $this->_get_namespaces();
		if ( ! empty( $namespaces ) ) {
			foreach ( $namespaces as $namespace ) {
				$class = rtrim( $namespace, '\\' ) . '\\' . $add_namespace . $class_name;
				if ( class_exists( $class ) ) {
					return [ $class, $add_namespace ];
				}
			}
		}

		return false;
	}

	/**
	 * @param array|false $setting
	 * @param string $instanceof
	 *
	 * @return bool|Singleton
	 */
	protected function get_class_instance( $setting, $instanceof ) {
		if ( false === $setting ) {
			return false;
		}

		if ( count( $setting ) >= 3 && ! class_exists( $setting[0] ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once $setting[2];
		}
		if ( class_exists( $setting[0] ) && is_subclass_of( $setting[0], '\WP_Framework_Core\Interfaces\Singleton' ) ) {
			try {
				/** @var \WP_Framework_Core\Interfaces\Singleton[] $setting */
				$instance = $setting[0]::get_instance( $this->app );
				if ( $instance instanceof $instanceof ) {
					if ( class_exists( '\WP_Framework_Admin\Classes\Controllers\Admin\Base' ) && $instance instanceof Base ) {
						$instance->set_relative_namespace( $setting[1] );
					}

					return $instance;
				}
			} catch ( Exception $e ) {
			}
		}

		return false;
	}

	/**
	 * @return array
	 */
	protected abstract function get_namespaces();

	/**
	 * @return bool
	 */
	protected function is_common_cache_class_settings() {
		return false;
	}

	/**
	 * @return array
	 */
	private function _get_namespaces() {
		! isset( $this->_namespaces ) and $this->_namespaces = $this->get_namespaces();

		return $this->_namespaces;
	}

	/**
	 * @return string
	 */
	protected abstract function get_instanceof();

}
