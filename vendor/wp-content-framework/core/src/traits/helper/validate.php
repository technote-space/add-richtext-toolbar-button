<?php
/**
 * WP_Framework_Core Traits Helper Validate
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Core\Traits\Helper;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Trait Validate
 * @package WP_Framework_Core\Traits\Helper
 * @property \WP_Framework $app
 * @mixin \WP_Framework_Core\Traits\Translate
 */
trait Validate {

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_not_empty( $var ) {
		if ( is_string( $var ) ) {
			$var = trim( $var );
			if ( ! empty( $var ) || '0' === $var ) {
				return true;
			}
		} elseif ( ! empty( $var ) ) {
			return true;
		}

		return new \WP_Error( 400, $this->translate( 'Value is required.' ) );
	}

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_kana( $var ) {
		if ( is_string( $var ) && preg_match( '#\A[ァ-ヴ][ァ-ヴー・]*\z#u', $var ) > 0 ) {
			return true;
		}

		return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
	}

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_date( $var ) {
		if ( is_string( $var ) && preg_match( '#\A\d{4}[/-]\d{1,2}[/-]\d{1,2}\z#', $var ) > 0 ) {
			return true;
		}

		return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
	}

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_time( $var ) {
		if ( is_string( $var ) && preg_match( '#\A\d{1,2}:\d{1,2}(:\d{1,2})?\z#', $var ) > 0 ) {
			return true;
		}

		return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
	}

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_email( $var ) {
		if ( is_string( $var ) && is_email( $var ) ) {
			return true;
		}

		return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
	}

	/**
	 * @param mixed $var
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_phone( $var ) {
		if ( is_string( $var ) && preg_match( '#\A\d{2,3}-?\d{3,4}-?\d{4,5}\z#', $var ) > 0 ) {
			return true;
		}

		return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
	}

	/**
	 * @param mixed $var
	 * @param bool $include_zero
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_positive( $var, $include_zero = false ) {
		if ( ! is_numeric( $var ) ) {
			return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
		}
		if ( ( ! $include_zero && $var <= 0 ) || ( $include_zero && $var < 0 ) ) {
			return new \WP_Error( 400, $this->translate( 'Outside the range of allowed values.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param bool $include_zero
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_negative( $var, $include_zero = false ) {
		if ( ! is_numeric( $var ) ) {
			return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
		}
		if ( ( ! $include_zero && $var >= 0 ) || ( $include_zero && $var > 0 ) ) {
			return new \WP_Error( 400, $this->translate( 'Outside the range of allowed values.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param int|null $min
	 * @param int|null $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_int( $var, $min = null, $max = null ) {
		if ( ! is_int( $var ) && ! is_string( $var ) && empty( $var ) ) {
			return new \WP_Error( 400, $this->translate( 'Value is required.' ) );
		}
		if ( ! is_int( $var ) && ( ! is_string( $var ) || ! preg_match( '#\A-?\d+\z#', $var ) ) ) {
			return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
		}
		if ( isset( $min ) && $var < $min ) {
			return new \WP_Error( 400, $this->translate( 'Outside the range of allowed values.' ) );
		}
		if ( isset( $max ) && $var > $max ) {
			return new \WP_Error( 400, $this->translate( 'Outside the range of allowed values.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param float|null $min
	 * @param float|null $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_float( $var, $min = null, $max = null ) {
		if ( ! is_int( $var ) && ! is_float( $var ) && ! is_string( $var ) && empty( $var ) ) {
			return new \WP_Error( 400, $this->translate( 'Value is required.' ) );
		}
		if ( ! is_int( $var ) && ! is_float( $var ) && ( ! is_string( $var ) || ! preg_match( '#\A-?\d*\.?\d+\z#', $var ) ) ) {
			return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
		}
		if ( isset( $min ) && $var < $min ) {
			return new \WP_Error( 400, $this->translate( 'Outside the range of allowed values.' ) );
		}
		if ( isset( $max ) && $var > $max ) {
			return new \WP_Error( 400, $this->translate( 'Outside the range of allowed values.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param bool $include_zero
	 * @param int $min
	 * @param int $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_positive_int( $var, $include_zero = false, $min = null, $max = null ) {
		$validate = $this->validate_int( $var, $min, $max );
		if ( true === $validate ) {
			return $this->validate_positive( $var, $include_zero );
		}

		return $validate;
	}

	/**
	 * @param mixed $var
	 * @param bool $include_zero
	 * @param int $min
	 * @param int $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_negative_int( $var, $include_zero = false, $min = null, $max = null ) {
		$validate = $this->validate_int( $var, $min, $max );
		if ( true === $validate ) {
			return $this->validate_negative( $var, $include_zero );
		}

		return $validate;
	}

	/**
	 * @param mixed $var
	 * @param bool $include_zero
	 * @param int $min
	 * @param int $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_positive_float( $var, $include_zero = false, $min = null, $max = null ) {
		$validate = $this->validate_float( $var, $min, $max );
		if ( true === $validate ) {
			return $this->validate_positive( $var, $include_zero );
		}

		return $validate;
	}

	/**
	 * @param mixed $var
	 * @param bool $include_zero
	 * @param int $min
	 * @param int $max
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_negative_float( $var, $include_zero = false, $min = null, $max = null ) {
		$validate = $this->validate_float( $var, $min, $max );
		if ( true === $validate ) {
			return $this->validate_negative( $var, $include_zero );
		}

		return $validate;
	}

	/**
	 * @param mixed $var
	 * @param string $table
	 * @param string $id
	 * @param string $field
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_exists( $var, $table, $id = 'id', $field = '*' ) {
		if ( ! $this->app->is_valid_package( 'db' ) ) {
			return new \WP_Error( 400, $this->translate( 'DB module is not available.' ) );
		}
		$validate = $this->validate_positive_int( $var );
		if ( true === $validate ) {
			if ( $this->app->db->select_count( $table, $field, [
					$id => $var,
				] ) <= 0 ) {
				return new \WP_Error( 400, $this->translate( 'Data does not exist.' ) );
			}
		}

		return $validate;
	}

	/**
	 * @param string $capability
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_authority( $capability ) {
		if ( ! $this->app->user_can( $capability ) ) {
			return new \WP_Error( 400, $this->translate( 'You have no authority.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param string $target
	 * @param \WP_REST_Request $request
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_confirmation( $var, $target, \WP_REST_Request $request ) {
		$validate = $this->validate_not_empty( $var );
		if ( true === $validate ) {
			$compare = $request->get_param( $target );
			if ( $compare !== $var ) {
				return new \WP_Error( 400, $this->translate( 'The confirmation value does not match.' ) );
			}
		}

		return $validate;
	}

	/**
	 * @param mixed $var
	 * @param string $pattern
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_regex( $var, $pattern ) {
		if ( is_string( $var ) && preg_match( $pattern, $var ) > 0 ) {
			return true;
		}

		return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
	}

	/**
	 * @param mixed $var
	 * @param int $len
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate_string_length( $var, $len ) {
		if ( ! is_string( $var ) ) {
			return new \WP_Error( 400, $this->translate( 'Invalid format.' ) );
		}

		if ( strlen( $var ) > $len ) {
			return new \WP_Error( 400, $this->translate( 'Input value is too long.' ) );
		}

		return true;
	}

	/**
	 * @param mixed $var
	 * @param array $setting
	 *
	 * @return bool|\WP_Error
	 */
	protected function validate( $var, array $setting ) {
		$validate = $this->validate_not_empty( $var );
		if ( $setting['required'] ) {
			if ( $validate instanceof \WP_Error ) {
				return $validate;
			}
		} else {
			if ( $validate instanceof \WP_Error ) {
				return true;
			}
		}
		switch ( $setting['type'] ) {
			case 'int':
				if ( ! empty( $setting['unsigned'] ) ) {
					return $this->validate_positive_int( $var, true );
				}

				return $this->validate_int( $var );
			case 'float':
			case 'number':
				if ( ! empty( $setting['unsigned'] ) ) {
					return $this->validate_positive_float( $var, true );
				}

				return $this->validate_float( $var );
			case 'bool':
				return true;
			case 'string':
				if ( ! empty( $setting['length'] ) ) {
					return $this->validate_string_length( $var, $setting['length'] );
				}
				break;
		}

		return true;
	}
}
