<?php
/**
 * WP_Framework_Db Deprecated Classes Models Db
 *
 * @version 0.0.14
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Db\Deprecated\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Db
 * @package WP_Framework_Db\Deprecated\Classes\Models
 */
class Db extends \WP_Framework_Db\Classes\Models\Db {

	/**
	 * initialize
	 */
	protected function initialize() {

	}

	/**
	 * @param array $data
	 * @param array $columns
	 *
	 * @return array
	 */
	private function filter( array $data, array $columns ) {
		$_format  = [];
		$_data    = [];
		$_columns = $columns;
		foreach ( $data as $k => $v ) {
			$columns = $_columns;
			list( $name, $columns ) = $this->get_field_data( $k, $columns );
			if ( isset( $columns[ $k ] ) ) {
				$_format[] = $columns[ $k ]['format'];
			} else {
				$_format[] = '%s';
			}
			$_data[ $name ] = $v;
		}

		return [ $_data, $_format ];
	}

	/**
	 * @param string $k
	 * @param array|null $columns
	 *
	 * @return array
	 */
	private function get_field_data( $k, $columns ) {
		$table = null;
		if ( strpos( $k, '.' ) !== false && strpos( $k, '(' ) === false ) {
			$exploded = explode( '.', $k );
			$table    = trim( $exploded[0], '`' );
			$k        = trim( $exploded[1], '`' );
			if ( isset( $this->table_defines[ $table ]['columns'][ $k ] ) ) {
				$name    = $this->table_defines[ $table ]['columns'][ $k ]['name'];
				$columns = $this->table_defines[ $table ];
				$table   = $this->get_table( $table );
			} else {
				$name = $k;
			}
		} else {
			if ( empty( $columns ) ) {
				return [ $k, $columns ];
			}
			$k = trim( $k, '`' );
			if ( isset( $columns[ $k ] ) ) {
				$name = $columns[ $k ]['name'];
			} else {
				$name = $k;
			}
		}
		if ( ! empty( $table ) ) {
			$name = $table . '.' . $name;
		}

		return [ $name, $columns ];
	}

	/**
	 * @param string $k
	 * @param array|null $columns
	 *
	 * @return string
	 */
	private function get_field_name( $k, $columns ) {
		return $this->get_field_data( $k, $columns )[0];
	}

	/**
	 * @param array|null|string $fields
	 * @param array $columns
	 *
	 * @return array
	 */
	private function build_fields( $fields, array $columns ) {
		if ( ! isset( $fields ) ) {
			$fields = [ '*' ];
		}
		if ( is_string( $fields ) ) {
			$fields = [ $fields ];
		}
		$is_admin = $this->app->utility->is_admin();
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $k => $option ) {
				$key = $k;
				if ( is_int( $key ) ) {
					$key    = $option;
					$option = null;
				}
				if ( $key === '*' ) {
					if ( ! is_array( $option ) ) {
						unset ( $fields[ $k ] );
						foreach ( $columns as $key => $column ) {
							if ( ! $is_admin && ! empty( $column['only_admin'] ) ) {
								continue;
							}
							$name     = $this->app->array->get( $column, 'name' );
							$fields[] = $name === $key ? $name : $name . ' AS ' . $key;
						}
						continue;
					}
					$name = $key;
				} elseif ( isset( $columns[ $key ] ) ) {
					$name = $columns[ $key ]['name'];
				} else {
					$name = $key;
				}
				if ( is_array( $option ) ) {
					$group_func = $option[0];
					if ( strtoupper( $group_func ) == 'AS' ) {
						$fields[ $k ] = $name;
						if ( count( $option ) >= 2 ) {
							$fields[ $k ] .= ' AS ' . $option[1];
						}
					} else {
						$fields[ $k ] = "$group_func( $name )";
						if ( count( $option ) >= 2 ) {
							$fields[ $k ] .= ' AS ' . $option[1];
						}
					}
				} elseif ( ! isset( $option ) ) {
					$fields[ $k ] = $name === $key ? $name : $name . ' AS ' . $key;
				} else {
					$fields[ $k ] = $name . ' AS ' . $option;
				}
			}
		}
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			$fields = [];
			foreach ( $columns as $key => $column ) {
				if ( ! $is_admin && ! empty( $column['only_admin'] ) ) {
					continue;
				}
				$name     = $this->app->array->get( $column, 'name' );
				$fields[] = $name === $key ? $name : $name . ' AS ' . $key;
			}
		}
		empty( $fields ) and $fields = [ '*' ];
		$fields = implode( ', ', $fields );

		return $fields;
	}

	/**
	 * @param array $where
	 * @param array $columns
	 * @param string $glue
	 *
	 * @return array
	 */
	private function build_conditions( array $where, array $columns, $glue = 'AND' ) {
		list ( $_where, $_where_format ) = $this->filter( $where, $columns );
		$conditions = $values = [];
		$index      = 0;
		foreach ( $_where as $field => $value ) {
			$field  = trim( $field );
			$format = $_where_format[ $index ++ ];
			if ( is_null( $value ) ) {
				$conditions[] = "$field IS NULL";
				continue;
			}

			if ( in_array( strtoupper( $field ), [
				'EXISTS',
				'NOT EXISTS',
			] ) ) {
				! is_array( $value ) and $value = [ $value ];
				foreach ( $value as $sub_query ) {
					$conditions[] = "$field ($sub_query)";
				}
				continue;
			}

			$op = '=';
			if ( is_array( $value ) ) {
				if ( count( $value ) > 1 ) {
					$op  = trim( $value[0] );
					$val = $value[1];
					if ( in_array( strtoupper( $op ), [
						'OR',
						'AND',
					] ) ) {
						array_shift( $value );
						$_conditions = [];
						foreach ( $value as $v ) {
							if ( ! is_array( $v ) ) {
								$_conditions[] = "1=0";
								continue;
							}
							list( $c, $v ) = $this->build_conditions( $v, $columns );
							$values        = array_merge( $values, $v );
							$_conditions[] = "({$c})";
						}
						$conditions[] = implode( " {$op} ", $_conditions );

						continue;
					}
					if ( is_array( $val ) ) {
						if ( empty( $val ) ) {
							$conditions[] = "1=0";
						} else {
							foreach ( $val as $v ) {
								$values[] = $v;
							}
							$conditions[] = "$field $op (" . str_repeat( $format . ',', count( $val ) - 1 ) . $format . ')';
						}
						continue;
					}
					if ( count( $value ) > 2 ) {
						$val          = $this->get_field_name( $val, $columns );
						$conditions[] = "$field $op $val";
						continue;
					}
				} else {
					$value        = reset( $value );
					$conditions[] = "$field ($value)";
					continue;
				}
			} else {
				$val = $value;
			}

			$conditions[] = "$field $op $format";
			$values[]     = $val;
		}
		$conditions = implode( " {$glue} ", $conditions );

		return [ $conditions, $values ];
	}

	/**
	 * @param array|null $group_by
	 * @param array $columns
	 *
	 * @return string
	 */
	private function build_group_by( $group_by, array $columns ) {
		$sql = '';
		if ( ! empty( $group_by ) ) {
			$items = [];
			foreach ( $group_by as $k ) {
				$items[] = $this->get_field_name( $k, $columns );
			}
			if ( ! empty( $items ) ) {
				$sql .= ' GROUP BY ' . implode( ', ', $items );
			}
		}

		return $sql;
	}

	/**
	 * @param array|null $order_by
	 * @param array $columns
	 *
	 * @return string
	 */
	private function build_order_by( $order_by, array $columns ) {
		$sql = '';
		if ( ! empty( $order_by ) ) {
			$items = [];
			foreach ( $order_by as $k => $order ) {
				if ( is_int( $k ) ) {
					$k     = $order;
					$order = 'ASC';
				} else {
					$order = trim( strtoupper( $order ) );
				}
				if ( $order !== 'DESC' && $order !== 'ASC' ) {
					continue;
				}
				$k       = $this->get_field_name( $k, $columns );
				$items[] = "$k $order";
			}
			if ( ! empty( $items ) ) {
				$sql .= ' ORDER BY ' . implode( ', ', $items );
			}
		}

		return $sql;
	}

	/**
	 * @param array|null $join
	 *
	 * @return string
	 */
	private function build_join( $join ) {
		$sql = '';
		if ( ! empty( $join ) ) {
			$items = [];
			foreach ( $join as $data ) {
				if ( ! is_array( $data ) || count( $data ) < 3 ) {
					continue;
				}
				$table = $data[0];
				$rule  = $data[1];
				$rule  = strtoupper( $rule );
				if ( ! in_array( $rule, [
					'JOIN',
					'INNER JOIN',
					'LEFT JOIN',
					'RIGHT JOIN',
				] ) ) {
					continue;
				}

				$conditions = $data[2];
				if ( empty( $conditions ) ) {
					continue;
				}
				$check = reset( $conditions );
				if ( ! is_array( $check ) ) {
					$conditions = [ $conditions ];
				}
				$values = [];
				foreach ( $conditions as $condition ) {
					if ( ! is_array( $condition ) || count( $condition ) < 3 ) {
						continue;
					}
					$left     = $condition[0];
					$op       = $condition[1];
					$right    = $condition[2];
					$values[] = $this->get_field_name( $left, null ) . " $op " . $this->get_field_name( $right, null );
				}
				if ( ! empty( $values ) ) {
					$as = null;
					if ( is_array( $table ) && count( $table ) > 1 ) {
						$as    = $table[1];
						$table = $table[0];
					}
					$items[] = $rule . ' ' . $this->get_table( $table ) . ( isset( $as ) ? " AS $as" : '' ) . ' ON ' . implode( ' AND ', $values );
				}
			}
			if ( ! empty( $items ) ) {
				$sql .= ' ' . implode( ' ', $items );
			}
		}

		return $sql;
	}

	/**
	 * @param null|int $limit
	 * @param null|int $offset
	 *
	 * @return string
	 */
	private function build_limit( $limit, $offset ) {
		$sql = '';
		if ( isset( $limit ) && $limit > 0 ) {
			if ( isset( $offset ) && $offset > 0 ) {
				$sql .= " LIMIT {$offset}, {$limit}";
			} else {
				$sql .= " LIMIT {$limit}";
			}
		}

		return $sql;
	}

	/**
	 * @param array|string $tables
	 * @param array|null $where
	 * @param array|null|string $fields
	 * @param null|int $limit
	 * @param null|int $offset
	 * @param array|null $order_by
	 * @param array|null $group_by
	 * @param bool $for_update
	 *
	 * @return string|false
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_select_sql( $tables, $where = null, $fields = null, $limit = null, $offset = null, $order_by = null, $group_by = null, $for_update = false ) {
		$as = null;
		if ( is_array( $tables ) ) {
			if ( empty( $tables ) ) {
				return false;
			}
			$table = array_shift( $tables );
			$join  = $tables;
			if ( count( $table ) > 1 ) {
				$as = $table[1];
			}
			$table = $table[0];
		} else {
			$table = $tables;
			$join  = null;
		}
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		$columns = $this->table_defines[ $table ]['columns'];

		! is_array( $where ) and $where = [];
		if ( $this->is_logical( $this->table_defines[ $table ] ) ) {
			$where['deleted_at'] = null;
		}

		list( $conditions, $values ) = $this->build_conditions( $where, $columns );
		$table  = $this->get_table( $table );
		$fields = $this->build_fields( $fields, $columns );
		$sql    = "SELECT {$fields} FROM `{$table}`";
		if ( isset( $as ) ) {
			$sql .= " AS $as";
		}
		$sql .= $this->build_join( $join );
		if ( ! empty( $conditions ) ) {
			$sql .= " WHERE $conditions";
		}
		$sql .= $this->build_group_by( $group_by, $columns );
		$sql .= $this->build_order_by( $order_by, $columns );
		$sql .= $this->build_limit( $limit, $offset );
		if ( $for_update ) {
			$sql .= ' FOR UPDATE';
		}

		return $this->prepare( $sql, $values );
	}

	/**
	 * @param array|string $tables
	 * @param array|null $where
	 * @param array|null|string $fields
	 * @param null|int $limit
	 * @param null|int $offset
	 * @param array|null $order_by
	 * @param array|null $group_by
	 * @param null|string $output
	 * @param bool $for_update
	 *
	 * @return array|bool|null
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function select( $tables, $where = null, $fields = null, $limit = null, $offset = null, $order_by = null, $group_by = null, $output = null, $for_update = false ) {
		$sql = $this->get_select_sql( $tables, $where, $fields, $limit, $offset, $order_by, $group_by, $for_update );
		if ( false === $sql ) {
			return false;
		}

		! isset( $output ) and $output = ARRAY_A;

		return $this->wpdb()->get_results( $sql, $output );
	}

	/**
	 * @param array|string $tables
	 * @param array|null $where
	 * @param array|null|string $fields
	 * @param null|int $offset
	 * @param array|null $order_by
	 * @param array|null $group_by
	 * @param null|string $output
	 * @param bool $for_update
	 *
	 * @return array|bool|null
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function select_row( $tables, $where = null, $fields = null, $offset = null, $order_by = null, $group_by = null, $output = null, $for_update = false ) {
		$sql = $this->get_select_sql( $tables, $where, $fields, 1, $offset, $order_by, $group_by, $for_update );
		if ( false === $sql ) {
			return false;
		}

		! isset( $output ) and $output = ARRAY_A;

		return $this->wpdb()->get_row( $sql, $output );
	}

	/**
	 * @param $table
	 * @param string $field
	 * @param array|null $where
	 * @param null|int $limit
	 * @param null|int $offset
	 * @param array|null $order_by
	 * @param array|null $group_by
	 * @param bool $for_update
	 *
	 * @return int
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function select_count( $table, $field = '*', $where = null, $limit = null, $offset = null, $order_by = null, $group_by = null, $for_update = false ) {
		empty( $field ) and $field = '*';
		$result = $this->select( $table, $where, [
			$field => [
				'COUNT',
				'num',
			],
		], $limit, $offset, $order_by, $group_by, ARRAY_A, $for_update );
		if ( empty( $result ) ) {
			return 0;
		}

		return isset( $result[0]['num'] ) ? $result[0]['num'] - 0 : 0;
	}

	/**
	 * @param string $table
	 * @param array $data
	 * @param string $method
	 *
	 * @return false|int
	 */
	private function _insert_replace( $table, array $data, $method ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}
		if ( $method !== 'insert' && $method !== 'replace' ) {
			return false;
		}
		if ( $method === 'replace' && ! isset( $data['id'] ) ) {
			return false;
		}

		$columns = $this->table_defines[ $table ]['columns'];

		$data = $this->set_update_params( $data, $method === 'insert', true, false );
		list ( $_data, $_format ) = $this->filter( $data, $columns );

		return $this->wpdb()->$method( $this->get_table( $table ), $_data, $_format );
	}

	/**
	 * @param string $table
	 * @param array $data
	 *
	 * @return bool|false|int
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function insert( $table, array $data ) {
		return $this->_insert_replace( $table, $data, 'insert' );
	}

	/**
	 * @param string $table
	 * @param array $fields
	 * @param array $data_list
	 *
	 * @return false|int
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function bulk_insert( $table, array $fields, array $data_list ) {
		if ( ! isset( $this->table_defines[ $table ] ) || empty( $fields ) || empty( $data_list ) ) {
			return false;
		}
		$columns     = $this->table_defines[ $table ]['columns'];
		$table       = $this->get_table( $table );
		$sql         = "INSERT INTO `{$table}` ";
		$names       = [];
		$placeholder = [];
		$time        = $this->set_update_params( [], true, true, false );
		foreach ( $fields as $field ) {
			if ( ! isset( $columns[ $field ] ) ) {
				return false;
			}
			$names[]       = $columns[ $field ]['name'];
			$placeholder[] = $columns[ $field ]['format'];
		}
		foreach ( $time as $k => $v ) {
			$names[]       = $columns[ $k ]['name'];
			$placeholder[] = $columns[ $k ]['format'];
		}
		$placeholder = '(' . implode( ', ', $placeholder ) . ')';
		$sql         .= '(' . implode( ', ', $names ) . ') VALUES ';

		$values = [];
		foreach ( $data_list as $data ) {
			$data += $time;
			if ( count( $names ) != count( $data ) ) {
				return false;
			}

			$values[] = $this->prepare( $placeholder, $data );
		}
		$sql .= implode( ', ', $values );

		return $this->query( $sql );
	}

	/**
	 * @param string $table
	 * @param array $data
	 *
	 * @return false|int
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function replace( $table, $data ) {
		return $this->_insert_replace( $table, $data, 'replace' );
	}

	/**
	 * @param string $table
	 * @param array $data
	 * @param array $where
	 * @param bool $delete
	 *
	 * @return false|int
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function update( $table, array $data, array $where, $delete = false ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		$columns = $this->table_defines[ $table ]['columns'];

		if ( ! $delete && $this->is_logical( $this->table_defines[ $table ] ) ) {
			$where['deleted_at'] = null;
		}

		$data = $this->set_update_params( $data, false, true, false );
		list ( $_data, $_format ) = $this->filter( $data, $columns );
		list ( $_where, $_where_format ) = $this->filter( $where, $columns );

		return $this->wpdb()->update( $this->get_table( $table ), $_data, $_where, $_format, $_where_format );
	}

	/**
	 * @param string $table
	 * @param array $data
	 * @param array $where
	 *
	 * @return int|false
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function insert_or_update( $table, array $data, array $where ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		if ( $this->is_logical( $this->table_defines[ $table ] ) ) {
			$where['deleted_at'] = null;
		}

		$row = $this->select_row( $table, $where, 'id' );
		if ( empty( $row ) ) {
			$this->insert( $table, $data );
			if ( $this->get_last_error() ) {
				return false;
			}

			return $this->get_insert_id();
		}
		$where = [ 'id' => $row['id'] ];
		$this->update( $table, $data, $where );
		if ( $this->get_last_error() ) {
			return false;
		}

		return $row['id'];
	}

	/**
	 * @param string $table
	 * @param array $where
	 *
	 * @return bool|false|int
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function delete( $table, array $where ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		if ( $this->is_logical( $this->table_defines[ $table ] ) ) {
			$data = $this->set_update_params( [], false, false, true );

			return $this->update( $table, $data, $where, true );
		}

		$columns = $this->table_defines[ $table ]['columns'];

		list ( $_where, $_where_format ) = $this->filter( $where, $columns );

		return $this->wpdb()->delete( $this->get_table( $table ), $_where, $_where_format );
	}

	/**
	 * @param string $name
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function __call( $name, array $args ) {
		/** @var \WP_Framework_Db\Classes\Models\Db $db */
		$db                  = array_shift( $args );
		$this->table_defines = $db->get_table_defines();

		return call_user_func_array( [ $this, $name ], $args );
	}
}
