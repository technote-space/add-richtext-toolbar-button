<?php
/**
 * WP_Framework_Core Traits Readonly
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits;

use OutOfRangeException;
use WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Readonly
 * @package WP_Framework_Core\Traits
 * @property WP_Framework $app
 * @internal|@property-read array|string[] $readonly_properties
 */
trait Readonly {

	/**
	 * @var bool $_is_allowed_access
	 */
	private $_is_allowed_access = false;

	/**
	 * @param bool $flag
	 */
	private function set_allowed_access( $flag ) {
		$this->_is_allowed_access = $flag;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws OutOfRangeException
	 */
	public function __set( $name, $value ) {
		if ( $this->_is_allowed_access && $this->is_readonly_property( $name ) ) {
			$this->$name = $value;
		} else {
			throw new OutOfRangeException( sprintf( $this->translate( 'you cannot access %s->%s.' ), static::class, $name ) );
		}
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws OutOfRangeException
	 */
	public function __get( $name ) {
		if ( $this->is_readonly_property( $name ) ) {
			if ( property_exists( $this, $name ) ) {
				return $this->$name;
			}

			return null;
		}
		throw new OutOfRangeException( sprintf( $this->translate( '%s is undefined.' ), $name ) );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return $this->is_readonly_property( $name ) && property_exists( $this, $name ) && ! is_null( $this->$name );
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	private function is_readonly_property( $name ) {
		return is_string( $name ) && property_exists( $this, 'readonly_properties' ) && in_array( $name, $this->readonly_properties );
	}
}
