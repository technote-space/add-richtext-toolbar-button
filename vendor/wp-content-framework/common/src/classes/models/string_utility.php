<?php
/**
 * WP_Framework_Common Classes Models String Utility
 *
 * @version 0.0.49
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Singleton;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class String_Utility
 * @package WP_Framework_Common\Classes\Models
 */
class String_Utility implements \WP_Framework_Core\Interfaces\Singleton {

	use Singleton, Package;

	/**
	 * @var string[] $_replace_time
	 */
	private $_replace_time;

	/**
	 * @var string[][] $_snake_cache
	 */
	private $_snake_cache = [];

	/**
	 * @var string[] $_camel_cache
	 */
	private $_camel_cache = [];

	/**
	 * @var string[] $_studly_cache
	 */
	private $_studly_cache = [];

	/**
	 * @return bool
	 */
	protected static function is_shared_class() {
		return true;
	}

	/**
	 * @param string $string
	 * @param array $data
	 *
	 * @return string
	 */
	public function replace( $string, array $data ) {
		foreach ( $data as $k => $v ) {
			$string = str_replace( '${' . $k . '}', $v, $string );
		}

		return $string;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function replace_time( $string ) {
		if ( ! isset( $this->_replace_time ) ) {
			$this->_replace_time = [];
			foreach (
				[
					'Y',
					'y',
					'M',
					'm',
					'n',
					'D',
					'd',
					'H',
					'h',
					'i',
					'j',
					's',
				] as $t
			) {
				$this->_replace_time[ $t ] = date_i18n( $t );
			}
		}

		return $this->replace( $string, $this->_replace_time );
	}

	/**
	 * @param string $string
	 * @param string|array $delimiter
	 *
	 * @return array
	 */
	public function explode( $string, $delimiter = ',' ) {
		if ( is_array( $delimiter ) ) {
			$results = [ $string ];
			foreach ( $delimiter as $d ) {
				$tmp = [];
				foreach ( $results as $result ) {
					$tmp = array_merge( $tmp, $this->explode( $result, $d ) );
				}
				$results = $tmp;
			}

			return array_unique( $results );
		}

		return array_filter( array_unique( array_map( 'trim', explode( $delimiter, $string ) ) ) );
	}

	/**
	 * @param mixed $array
	 * @param string $glue
	 *
	 * @return string
	 */
	public function implode( $array, $glue = ', ' ) {
		return implode( $glue, array_filter( $this->app->array->to_array( $array ) ) );
	}

	/**
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return bool
	 */
	public function starts_with( $haystack, $needle ) {
		if ( '' === $haystack || '' === $needle ) {
			return false;
		}
		if ( $haystack === $needle ) {
			return true;
		}

		return strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
	}

	/**
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return bool
	 */
	public function ends_with( $haystack, $needle ) {
		if ( '' === $haystack || '' === $needle ) {
			return false;
		}
		if ( $haystack === $needle ) {
			return true;
		}

		return substr_compare( $haystack, $needle, -strlen( $needle ) ) === 0;
	}

	/**
	 * @param string $haystack
	 * @param string|array $needles
	 *
	 * @return bool
	 */
	public function contains( $haystack, $needles ) {
		foreach ( (array) $needles as $needle ) {
			if ( $needle !== '' && mb_strpos( $haystack, $needle ) !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function lower( $value ) {
		return mb_strtolower( $value, 'UTF-8' );
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function camel( $value ) {
		if ( ! isset( $this->_camel_cache[ $value ] ) ) {
			$this->_camel_cache[ $value ] = lcfirst( $this->studly( $value ) );
		}

		return $this->_camel_cache[ $value ];
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function studly( $value ) {
		if ( ! isset( $this->_studly_cache[ $value ] ) ) {
			$_value                        = ucwords( str_replace( [ '-', '_' ], ' ', $value ) );
			$this->_studly_cache[ $value ] = str_replace( ' ', '', $_value );
		}

		return $this->_studly_cache[ $value ];
	}

	/**
	 * @param string $value
	 * @param string $delimiter
	 *
	 * @return string
	 */
	public function snake( $value, $delimiter = '_' ) {
		if ( ! isset( $this->_snake_cache[ $value ][ $delimiter ] ) ) {
			$_value = $value;
			if ( ! ctype_lower( $_value ) ) {
				$_value = preg_replace( '/\s+/u', '', ucwords( $_value ) );
				$_value = $this->lower( preg_replace( '/(.)(?=[A-Z])/u', '$1' . $delimiter, $_value ) );
			}
			$this->_snake_cache[ $value ][ $delimiter ] = $_value;
		}

		return $this->_snake_cache[ $value ][ $delimiter ];
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function kebab( $value ) {
		return $this->snake( $value, '-' );
	}

	/**
	 * @param string $message
	 * @param null|array $override_allowed_html
	 *
	 * @return string
	 */
	public function strip_tags( $message, $override_allowed_html = null ) {
		$allowed_html = [
			'a'      => [ 'href' => true, 'target' => true, 'rel' => true ],
			'b'      => [],
			'br'     => [],
			'sub'    => [],
			'sup'    => [],
			'strong' => [],
			's'      => [],
			'u'      => [],
			'em'     => [],
			'h1'     => [],
			'h2'     => [],
			'h3'     => [],
			'h4'     => [],
			'h5'     => [],
			'h6'     => [],
		];
		if ( ! empty( $override_allowed_html ) && is_array( $override_allowed_html ) ) {
			$allowed_html = array_replace_recursive( $allowed_html, $override_allowed_html );
		}

		return wp_kses( $message, $allowed_html );
	}
}
