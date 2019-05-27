<?php
/**
 * WP_Framework_Core Traits Translate
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits;

use WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Translate
 * @package WP_Framework_Core\Traits
 * @property WP_Framework $app
 * @mixin Package
 */
trait Translate {

	/**
	 * @var array $_loaded_languages
	 */
	private static $_loaded_languages = [];

	/**
	 * @var array $_textdomains
	 */
	private static $_textdomains;

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function translate( $value ) {
		foreach ( $this->get_textdomains() as $textdomain => $path ) {
			$translated = __( $value, $textdomain );
			if ( $value !== $translated ) {
				return $translated;
			}
		}

		return $value;
	}

	/**
	 * @return array
	 */
	protected function get_textdomains() {
		$package = $this->get_package();
		if ( ! isset( self::$_textdomains[ $package ][ $this->app->plugin_name ] ) ) {
			self::$_textdomains[ $package ][ $this->app->plugin_name ] = [];
			! empty( $this->app->define->plugin_textdomain ) and self::$_textdomains[ $package ][ $this->app->plugin_name ][ $this->app->define->plugin_textdomain ] = $this->app->define->plugin_languages_dir;
			$instance = $this->get_package_instance();
			foreach ( $instance->get_translate_settings() as $textdomain => $path ) {
				self::$_textdomains[ $package ][ $this->app->plugin_name ][ $textdomain ] = $path;
			}

			foreach ( self::$_textdomains[ $package ][ $this->app->plugin_name ] as $textdomain => $path ) {
				if ( ! $this->setup_textdomain( $textdomain, $path ) ) {
					unset( self::$_textdomains[ $package ][ $this->app->plugin_name ][ $textdomain ] );
				}
			}
		}

		return self::$_textdomains[ $package ][ $this->app->plugin_name ];
	}

	/**
	 * @param string $textdomain
	 * @param string $dir
	 *
	 * @return bool
	 */
	private function setup_textdomain( $textdomain, $dir ) {
		if ( ! isset( self::$_loaded_languages[ $textdomain ] ) ) {
			if ( function_exists( 'determine_locale' ) ) {
				$locale = apply_filters( 'plugin_locale', determine_locale(), $textdomain );
			} else {
				$locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );
			}
			$mofile = $textdomain . '-' . $locale . '.mo';
			$path   = $dir . DS . $mofile;

			self::$_loaded_languages[ $textdomain ] = load_textdomain( $textdomain, $path );
		}

		return self::$_loaded_languages[ $textdomain ];
	}
}
