<?php
/**
 * WP_Framework_Db Classes Models Grammar
 *
 * @version 0.0.14
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 *
 * reference illuminate/database Copyright (c) Taylor Otwell
 * The MIT License (MIT) (https://github.com/illuminate/database/blob/master/LICENSE.md)
 */

namespace WP_Framework_Db\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Grammar
 * @package WP_Framework_Db\Classes\Models\Query
 */
abstract class Grammar {

	/**
	 * @var \WP_Framework
	 */
	protected $app;

	/**
	 * Create a new query builder instance.
	 *
	 * @param  \WP_Framework $app
	 *
	 * @return void
	 */
	public function __construct(
		\WP_Framework $app
	) {
		$this->app = $app;
	}

	/**
	 * Wrap an array of values.
	 *
	 * @param  array $values
	 *
	 * @return array
	 */
	public function wrap_array( array $values ) {
		return array_map( [ $this, 'wrap' ], $values );
	}

	/**
	 * Wrap a table in keyword identifiers.
	 *
	 * @param  Query\Expression|string $table
	 *
	 * @return string
	 */
	public function wrap_table( $table ) {
		if ( ! $this->is_expression( $table ) ) {
			return $this->wrap( $table, true );
		}

		return $this->get_value( $table );
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param Query\Expression|string $value
	 * @param bool $is_table
	 *
	 * @return string
	 */
	public function wrap( $value, $is_table = false ) {
		if ( $this->is_expression( $value ) ) {
			return $this->get_value( $value );
		}
		// If the value being wrapped has a column alias we will need to separate out
		// the pieces so we can wrap each of the segments of the expression on its
		// own, and then join these both back together using the "as" connector.
		if ( stripos( $value, ' as ' ) !== false ) {
			return $this->wrap_aliased_value( $value, $is_table );
		}

		return $this->wrap_segments( explode( '.', $value ), $is_table );
	}

	/**
	 * Wrap a value that has an alias.
	 *
	 * @param string $value
	 * @param bool $is_table
	 *
	 * @return string
	 */
	protected function wrap_aliased_value( $value, $is_table ) {
		$segments = preg_split( '/\s+as\s+/i', $value );

		return $this->wrap( $segments[0], $is_table ) . ' as ' . $this->wrap_value( $segments[1] );
	}

	/**
	 * Wrap the given value segments.
	 *
	 * @param array $segments
	 * @param bool $is_table
	 *
	 * @return string
	 */
	protected function wrap_segments( $segments, $is_table = false ) {
		return implode( '.', $this->app->array->map( $segments, function ( $segment, $key ) use ( $segments, $is_table ) {
			return $key == 0 && count( $segments ) > 1
				? $this->wrap_table( $segment )
				: $this->wrap_value( $segment, $is_table );
		} ) );
	}

	/**
	 * Wrap a single string in keyword identifiers.
	 *
	 * @param string $value
	 * @param bool $is_table
	 *
	 * @return string
	 */
	protected function wrap_value(
		/** @noinspection PhpUnusedParameterInspection */
		$value, $is_table = false
	) {
		if ( $value !== '*' ) {
			return '"' . str_replace( '"', '""', $value ) . '"';
		}

		return $value;
	}

	/**
	 * Convert an array of column names into a delimited string.
	 *
	 * @param  array $columns
	 *
	 * @return string
	 */
	public function columnize( array $columns ) {
		return implode( ', ', array_map( [ $this, 'wrap' ], $columns ) );
	}

	/**
	 * Create query parameter place-holders for an array.
	 *
	 * @param  array $values
	 *
	 * @return string
	 */
	public function parameterize( array $values ) {
		return implode( ', ', array_map( [ $this, 'parameter' ], $values ) );
	}

	/**
	 * Get the appropriate query parameter place-holder for a value.
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function parameter( $value ) {
		if ( $this->is_expression( $value ) ) {
			return $this->get_value( $value );
		}
		switch ( gettype( $value ) ) {
			case 'boolean':
			case 'integer':
				return '%d';
			case 'double':
				return '%f';
			default:
				return '%s';
		}
	}

	/**
	 * Quote the given string literal.
	 *
	 * @param  string|array $value
	 *
	 * @return string
	 */
	public function quote_string( $value ) {
		if ( is_array( $value ) ) {
			return implode( ', ', array_map( [ $this, __FUNCTION__ ], $value ) );
		}

		return "'$value'";
	}

	/**
	 * Determine if the given value is a raw expression.
	 *
	 * @param  mixed $value
	 *
	 * @return bool
	 */
	public function is_expression( $value ) {
		return $value instanceof Query\Expression;
	}

	/**
	 * Get the value of a raw expression.
	 *
	 * @param  Query\Expression $expression
	 *
	 * @return string
	 */
	public function get_value( $expression ) {
		return $expression->get_value();
	}

	/**
	 * Get the format for database stored dates.
	 *
	 * @return string
	 */
	public function get_date_format() {
		return 'Y-m-d H:i:s';
	}
}
