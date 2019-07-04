<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;

// @codeCoverageIgnoreStart
if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}
// @codeCoverageIgnoreEnd

/**
 * Class Style
 * @package Richtext_Toolbar_Button\Classes\Models
 */
class Style implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook {

	use Singleton, Hook, Package;

	/**
	 * @param mixed $style
	 *
	 * @return string
	 */
	public function encode_style( $style ) {
		if ( ! is_string( $style ) ) {
			$style = '';
		}
		$style  = trim( stripslashes( $style ) );
		$styles = [];
		$index  = [];

		foreach ( preg_split( '/\R|;/', $style ) as $k => $v ) {
			if ( ! preg_match( '/^(\[([-().#>+~|*a-z]+)]\s*)?(.+?)\s*:\s*(.+?)\s*$/', $v, $matches ) ) {
				continue;
			}

			$pseudo = trim( $matches[2] );
			$key    = trim( $matches[3] );
			$val    = trim( $matches[4] );
			if ( ! preg_match( '/\A[a-z-]+\z/i', $key ) ) {
				continue;
			}

			$value                      = "{$key}: {$val};";
			$index[ $pseudo ][ $value ] = $k;
			$styles[ $pseudo ][ $k ]    = $value;
		}

		ksort( $styles );
		foreach ( $styles as $pseudo => $values ) {
			foreach ( $values as $k => $value ) {
				if ( $index[ $pseudo ][ $value ] !== $k ) {
					unset( $styles[ $pseudo ][ $k ] );
				}
			}
			$styles[ $pseudo ] = array_values( $styles[ $pseudo ] );
		}

		return wp_json_encode( $styles );
	}

	/**
	 * @param string $style
	 * @param bool $is_editor
	 *
	 * @return array|string
	 */
	public function decode_style( $style, $is_editor = false ) {
		$styles = @json_decode( $style, true ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( ! is_array( $styles ) ) {
			$styles = [];
		}

		if ( ! $is_editor ) {
			return $styles;
		}

		$items = [];
		foreach ( $styles as $pseudo => $values ) {
			foreach ( $values as $value ) {
				$items[] = '' === $pseudo ? $value : "[{$pseudo}] {$value}";
			}
			$items[] = '';
		}

		return implode( "\r\n", $items );
	}
}
