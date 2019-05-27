<?php
/**
 * WP_Framework_Core Traits Helper Data Helper
 *
 * @version 0.0.55
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits\Helper;

use WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Data_Helper
 * @package WP_Framework_Core\Traits\Helper
 * @property WP_Framework $app
 */
trait Data_Helper {

	/**
	 * @param array $data
	 * @param string $key
	 *
	 * @return bool
	 */
	protected function convert_to_bool( array $data, $key ) {
		return ! empty( $data[ $key ] ) && $data[ $key ] !== '0' && $data[ $key ] !== 'false';
	}

	/**
	 * @param string $str
	 *
	 * @return string
	 */
	protected function the_content( $str ) {
		return apply_filters( 'the_content', $str );
	}

	/**
	 * @param mixed $param
	 * @param string $type
	 * @param bool $check_null
	 * @param bool $nullable
	 * @param bool $update
	 *
	 * @return mixed
	 */
	protected function sanitize_input( $param, $type, $check_null = false, $nullable = false, $update = false ) {
		if ( $check_null && is_null( $param ) ) {
			return null;
		}

		switch ( $type ) {
			case 'int':
				if ( ! is_int( $param ) && ! ctype_digit( ltrim( $param, '-' ) ) ) {
					return null;
				}
				$param -= 0;
				$param = (int) $param;
				break;
			case 'float':
			case 'number':
				if ( ! is_numeric( $param ) && ! ctype_alpha( $param ) ) {
					return null;
				}
				$param -= 0;
				break;
			case 'bool':
				if ( $nullable && ( is_null( $param ) || $param === '' ) ) {
					return null;
				}
				if ( $update && $param === '' ) {
					return null;
				}
				if ( is_string( $param ) ) {
					$param = strtolower( trim( $param ) );
					if ( $param === 'true' ) {
						$param = 1;
					} elseif ( $param === 'false' ) {
						$param = 0;
					} elseif ( $param === '0' ) {
						$param = 0;
					} else {
						$param = ! empty( $param ) ? 1 : 0;
					}
				} else {
					$param = ! empty( $param ) ? 1 : 0;
				}
				break;
			default:
				if ( is_null( $param ) || ( (string) $param === '' ) ) {
					return null;
				}
				break;
		}

		return $param;
	}

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	protected function is_default( $value ) {
		return ! is_array( $value ) && ! is_bool( $value ) && '' === (string) ( $value );
	}
}
