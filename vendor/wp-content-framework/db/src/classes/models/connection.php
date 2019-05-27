<?php
/**
 * WP_Framework_Db Classes Models Connection
 *
 * @version 0.0.19
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 *
 * reference illuminate/database Copyright (c) Taylor Otwell
 * The MIT License (MIT) (https://github.com/illuminate/database/blob/master/LICENSE.md)
 */

namespace WP_Framework_Db\Classes\Models;

use DateTimeInterface;
use Exception;
use WP_Framework;
use wpdb;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Connection
 * @package WP_Framework_Db\Classes\Models
 */
abstract class Connection {

	/**
	 * @var WP_Framework
	 */
	protected $app;

	/**
	 * @var Grammar
	 */
	protected $grammar;

	/**
	 * @var array $_queries
	 */
	protected static $_queries = [];

	/**
	 * Create a new database connection instance.
	 *
	 * @param WP_Framework $app
	 * @param Grammar $grammar
	 *
	 * @return void
	 */
	public function __construct( WP_Framework $app, Grammar $grammar ) {
		$this->app     = $app;
		$this->grammar = $grammar;
	}

	/**
	 * @param string $query
	 * @param array $bindings
	 *
	 * @return string
	 */
	protected function prepare( $query, $bindings ) {
		return $this->app->db->prepare( $query, $bindings );
	}

	/**
	 * @return wpdb
	 */
	protected function wpdb() {
		return $this->app->db->wpdb();
	}

	/**
	 * Run a select statement against the database.
	 *
	 * @param string $query
	 * @param array $bindings
	 * @param int $ttl
	 *
	 * @return array
	 */
	public function select( $query, $bindings = [], $ttl = 0 ) {
		return $this->run( $query, $bindings, function ( $query ) {
			return $this->wpdb()->get_results( $query, ARRAY_A );
		}, $ttl );
	}

	/**
	 * Run an insert statement against the database.
	 *
	 * @param string $query
	 * @param array $bindings
	 *
	 * @return int|false
	 */
	public function insert( $query, $bindings = [] ) {
		$result = $this->statement( $query, $bindings );
		if ( false === $result ) {
			return false;
		}

		return $this->app->db->get_insert_id();
	}

	/**
	 * @param string $query
	 * @param array $bindings
	 *
	 * @return int
	 */
	public function bulk_insert( $query, $bindings = [] ) {
		return (int) $this->statement( $query, $bindings );
	}

	/**
	 * Run an update statement against the database.
	 *
	 * @param string $query
	 * @param array $bindings
	 *
	 * @return int
	 */
	public function update( $query, $bindings = [] ) {
		return (int) $this->statement( $query, $bindings );
	}

	/**
	 * Run a delete statement against the database.
	 *
	 * @param string $query
	 * @param array $bindings
	 *
	 * @return int|false
	 */
	public function delete( $query, $bindings = [] ) {
		return $this->statement( $query, $bindings );
	}

	/**
	 * Execute an SQL statement and return the boolean result.
	 *
	 * @param string $query
	 * @param array $bindings
	 *
	 * @return int|false
	 */
	public function statement( $query, $bindings = [] ) {
		return $this->run( $query, $bindings, function ( $query ) {
			return $this->wpdb()->query( $query );
		} );
	}

	/**
	 * @param string $query
	 * @param array $bindings
	 * @param callable $callback
	 * @param int $ttl
	 *
	 * @return mixed
	 */
	protected function run( $query, $bindings, $callback, $ttl = 0 ) {
		if ( $this->app->utility->defined( 'WP_FRAMEWORK_PERFORMANCE_REPORT' ) ) {
			$start = microtime( true ) * 1000;

			$real_query = $this->prepare( $query, $this->prepare_bindings( $bindings ) );
			$hash       = sha1( $real_query );
			if ( $ttl > 0 ) {
				// TODO: cache control
				error_log( $hash );
			}

			$result = $callback( $real_query );

			$elapsed = microtime( true ) * 1000 - $start;

			! isset( static::$_queries[ $this->app->plugin_name ] ) and static::$_queries[ $this->app->plugin_name ] = [];
			! isset( static::$_queries[ $this->app->plugin_name ][ $hash ] ) and static::$_queries[ $this->app->plugin_name ][ $hash ] = [
				'query'   => $real_query,
				'execute' => [],
			];
			static::$_queries[ $this->app->plugin_name ][ $hash ]['execute'][] = $elapsed;

			if ( $elapsed > 10 * 1000 ) {
				if ( $this->app->utility->defined( 'WP_FRAMEWORK_REPORT_SLOW_QUERY' ) ) {
					$this->app->log( new Exception( 'slow query detected.' ), [ 'query' => $real_query, 'elapsed ms' => $elapsed ] );
				}
			}
		} else {
			$real_query = $this->prepare( $query, $this->prepare_bindings( $bindings ) );
			$result     = $callback( $real_query );
		}

		return $result;
	}

	/**
	 * Prepare the query bindings for execution.
	 *
	 * @param array $bindings
	 *
	 * @return array
	 */
	public function prepare_bindings( array $bindings ) {
		foreach ( $bindings as $key => $value ) {
			// We need to transform all instances of DateTimeInterface into the actual
			// date string. Each query grammar maintains its own date string format
			// so we'll just ask the grammar for the format to get from the date.
			if ( $value instanceof DateTimeInterface ) {
				$bindings[ $key ] = $value->format( $this->grammar->get_date_format() );
			} elseif ( is_bool( $value ) ) {
				$bindings[ $key ] = (int) $value;
			} elseif ( is_null( $value ) ) {
				unset( $bindings[ $key ] );
			}
		}

		return $bindings;
	}

	/**
	 * Get a new raw query expression.
	 *
	 * @param mixed $value
	 *
	 * @return Query\Expression
	 */
	public function raw( $value ) {
		return new Query\Expression( $value );
	}

	/**
	 * @param string
	 *
	 * @return array|false
	 */
	public static function queries( $name ) {
		return isset( static::$_queries[ $name ] ) ? static::$_queries[ $name ] : false;
	}
}
