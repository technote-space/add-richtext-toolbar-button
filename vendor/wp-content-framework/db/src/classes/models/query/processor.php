<?php
/**
 * WP_Framework_Db Classes Models Query Processor
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

namespace WP_Framework_Db\Classes\Models\Query;

use WP_Framework;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Processor
 * @package WP_Framework_Db\Classes\Models\Query
 */
class Processor {

	/**
	 * @var WP_Framework
	 */
	protected $app;

	/**
	 * Create a new database processor instance.
	 *
	 * @param WP_Framework $app
	 *
	 * @return void
	 */
	public function __construct( WP_Framework $app ) {
		$this->app = $app;
	}

	/**
	 * Process the results of a "select" query.
	 *
	 * @param Builder $query
	 * @param array $results
	 *
	 * @return array
	 */
	public function process_select(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $results
	) {
		if ( empty( $query->aggregate ) ) {
			list( $managed, $table ) = $this->app->db->get_managed_table( $query->from );
			if ( $managed ) {
				$columns  = $this->app->db->get_columns( $table );
				$id       = $this->app->array->get( $columns, 'id.name' );
				$is_admin = $this->app->utility->is_admin();
				$results  = array_map( function ( $result ) use ( $columns, $id, $is_admin ) {
					if ( $id && isset( $result[ $id ] ) ) {
						$result['id'] = (int) $result[ $id ];
					}
					if ( ! $is_admin ) {
						foreach ( $columns as $column ) {
							if ( ! empty( $column['only_admin'] ) ) {
								unset( $result[ $column['name'] ] );
							}
						}
					}

					return $result;
				}, $results );
			}
			if ( $query->is_object ) {
				$results = array_map( function ( $result ) {
					return (object) $result;
				}, $results );
			}
		}

		return $results;
	}

	/**
	 * Process the results of a column listing query.
	 *
	 * @param array $results
	 *
	 * @return array
	 */
	public function process_column_listing( $results ) {
		return array_map( function ( $result ) {
			$result = (object) $result;

			return $result->column_name;
		}, $results );
	}
}
