<?php
/**
 * WP_Framework_Presenter Classes Models Minify
 *
 * @version 0.0.15
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Presenter\Classes\Models;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Package;
use WP_Framework_Presenter\Traits\Presenter;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Minify
 * @package WP_Framework_Presenter\Classes\Models
 */
class Minify implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * @var array $_script
	 */
	private $_script = [];

	/**
	 * @var bool $_has_output_script
	 */
	private $_has_output_script = false;

	/**
	 * @var array $_css
	 */
	private $_css = [];

	/**
	 * @var bool $_end_footer
	 */
	private $_end_footer = false;

	/**
	 * @param string $src
	 * @param string $name
	 *
	 * @return bool
	 */
	private function check_cache( $src, $name ) {
		$name  = $name . '_minify_cache';
		$hash  = sha1( $src );
		$cache = $this->app->get_shared_object( $name, 'all' );
		if ( $cache ) {
			if ( isset( $cache[ $hash ] ) ) {
				return true;
			}
		} else {
			$cache = [];
		}
		$cache[ $hash ] = true;
		$this->app->set_shared_object( $name, $cache, 'all' );

		return false;
	}

	/**
	 * @param string $name
	 */
	private function clear_cache( $name ) {
		$name = $name . '_minify_cache';
		$this->app->delete_shared_object( $name, 'all' );
	}

	/**
	 * @param string $script
	 * @param int $priority
	 */
	public function register_script( $script, $priority = 10 ) {
		$this->set_script( preg_replace( '/<\s*\/?script\s*>/', '', $script ), $priority );
	}

	/**
	 * @param string $file
	 * @param int $priority
	 */
	public function register_js_file( $file, $priority = 10 ) {
		$this->set_script( @file_get_contents( $file ), $priority );
	}

	/**
	 * @param string $script
	 * @param int $priority
	 */
	private function set_script( $script, $priority ) {
		$script = trim( $script );
		if ( '' === $script ) {
			return;
		}

		if ( $this->check_cache( $script, 'script' ) ) {
			return;
		}

		$this->_script[ $priority ][] = $script;
		if ( $this->_has_output_script ) {
			$this->output_js();
		}
	}

	/**
	 * @param string $script
	 * @param bool $wrap
	 *
	 * @return string
	 */
	public function minify_js( $script, $wrap = true ) {
		$script = preg_replace( '/<\s*\/?script\s*>/', '', $script );
		if ( $this->apply_filters( 'minify_js' ) ) {
			$minify = new JS();
			$minify->add( $script );
			$script = $minify->minify();
		}

		if ( $wrap ) {
			return "<script>{$script}</script>";
		}

		return $script;
	}

	/**
	 * @param bool $clear_cache
	 */
	public function output_js( $clear_cache = false ) {
		if ( $clear_cache ) {
			$this->clear_cache( 'script' );
		}
		if ( empty( $this->_script ) ) {
			return;
		}
		ksort( $this->_script );
		$script = implode( "\n", array_map( function ( $s ) {
			return implode( "\n", $s );
		}, $this->_script ) );

		$this->h( $this->minify_js( $script ), false, true, false );
		$this->_script            = [];
		$this->_has_output_script = true;
	}

	/**
	 * @param string $css
	 * @param int $priority
	 */
	public function register_style( $css, $priority = 10 ) {
		$this->set_style( preg_replace( '/<\s*\/?style\s*>/', '', $css ), $priority );
	}

	/**
	 * @param string $file
	 * @param int $priority
	 */
	public function register_css_file( $file, $priority = 10 ) {
		$this->set_style( @file_get_contents( $file ), $priority );
	}

	/**
	 * @param string $css
	 * @param int $priority
	 */
	private function set_style( $css, $priority ) {
		$css = trim( $css );
		if ( '' === $css ) {
			return;
		}

		if ( $this->check_cache( $css, 'style' ) ) {
			return;
		}

		$this->_css[ $priority ][] = $css;
		if ( $this->_end_footer ) {
			$this->output_css();
		}
	}

	/**
	 * @param string $css
	 * @param bool $wrap
	 *
	 * @return string
	 */
	public function minify_css( $css, $wrap = true ) {
		$css = preg_replace( '/<\s*\/?style\s*>/', '', $css );
		if ( $this->apply_filters( 'minify_css' ) ) {
			$minify = new CSS();
			$minify->add( $css );
			$css = $minify->minify();
		}

		if ( $wrap ) {
			return "<style>{$css}</style>";
		}

		return $css;
	}

	/**
	 * @param bool $clear_cache
	 */
	public function output_css( $clear_cache = false ) {
		if ( $clear_cache ) {
			$this->clear_cache( 'style' );
		}
		if ( empty( $this->_css ) ) {
			return;
		}
		ksort( $this->_css );
		$css = implode( "\n", array_map( function ( $s ) {
			return implode( "\n", $s );
		}, $this->_css ) );

		$this->h( $this->minify_css( $css ), false, true, false );
		$this->_css = [];
	}

	/**
	 * end footer
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function end_footer() {
		$this->_end_footer = true;
	}
}
