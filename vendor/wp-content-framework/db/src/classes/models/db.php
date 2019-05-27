<?php
/**
 * WP_Framework_Db Classes Models Db
 *
 * @version 0.0.18
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Db\Classes\Models;

use Exception;
use WP_Framework_Common\Traits\Uninstall;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Db\Traits\Package;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Db
 * @package WP_Framework_Db\Classes\Models
 */
class Db implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Common\Interfaces\Uninstall {

	use Singleton, Hook, Uninstall, Package;

	/**
	 * @var array $table_defines
	 */
	protected $table_defines = null;

	/**
	 * @var array $_type2format
	 */
	private static $_type2format = [];

	/**
	 * @var Exception $_error
	 */
	private $_error = null;

	/**
	 * @var int $_transaction_level
	 */
	private $_transaction_level = 0;

	/**
	 * @var array $_managed_table_cache
	 */
	private $_table_name_cache = [];

	/**
	 * @var int $_blog_id
	 */
	private $_blog_id;

	/**
	 * initialize
	 */
	protected function initialize() {
		$this->_blog_id = $this->app->define->blog_id;
		$this->load_table_defines();
		$this->db_update();
		$this->setup_wp_table_defines();
		add_action( 'switch_blog', function ( $new_blog ) {
			$this->switch_blog( $new_blog );
		} );
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	private function type2format( $type ) {
		if ( ! isset( self::$_type2format[ $type ] ) ) {
			$format = '%s';
			switch ( true ) {
				case stristr( $type, 'INT' ) !== false:
					$format = '%d';
					break;
				case stristr( $type, 'BIT' ) !== false:
					$format = '%d';
					break;
				case stristr( $type, 'BOOL' ) !== false:
					$format = '%d';
					break;
				case stristr( $type, 'DECIMAL' ) !== false:
					$format = '%f';
					break;
				case stristr( $type, 'FLOAT' ) !== false:
					$format = '%f';
					break;
				case stristr( $type, 'DOUBLE' ) !== false:
					$format = '%f';
					break;
				case stristr( $type, 'REAL' ) !== false:
					$format = '%f';
					break;
			}
			self::$_type2format[ $type ] = $this->apply_filters( 'type2format', $format, $type );
		}

		return self::$_type2format[ $type ];
	}

	/**
	 * load table defines
	 */
	private function load_table_defines() {
		$cache = $this->cache_get( 'table_defines' );
		if ( is_array( $cache ) ) {
			$this->table_defines = $cache;

			return;
		}

		$this->table_defines = $this->app->config->load( 'db' );
		empty( $this->table_defines ) and $this->table_defines = [];
		$added_tables = $this->app->option->get_grouped( 'added_tables', 'db', [] );

		foreach ( $this->table_defines as $table => $define ) {
			list( $id, $columns ) = $this->setup_table_columns( $table, $define );
			if ( empty( $id ) ) {
				continue;
			}
			$this->table_defines[ $table ]['id']      = $id;
			$this->table_defines[ $table ]['columns'] = $columns;
			if ( ! empty( $this->table_defines[ $table ]['comment'] ) ) {
				$this->table_defines[ $table ]['comment'] = $this->translate( $this->table_defines[ $table ]['comment'] );
			}
			$added_tables[ $table ] = $table;
		}
		$this->cache_set( 'table_defines', $this->table_defines );
		$this->app->option->set_grouped( 'added_tables', 'db', $added_tables );
	}

	/**
	 * @param int $new_blog
	 */
	private function switch_blog( $new_blog ) {
		if ( $new_blog === $this->_blog_id ) {
			return;
		}

		foreach ( $this->table_defines as $table => $table_define ) {
			if ( ! empty( $table_define['wordpress'] ) ) {
				unset( $this->table_defines[ $table ] );
			}
		}
		$this->setup_wp_table_defines();
		$this->_blog_id = $new_blog;
	}

	/**
	 * for wp table
	 */
	private function setup_wp_table_defines() {
		$current_blog_id = get_current_blog_id();
		$tables          = $this->apply_filters( 'allowed_wp_tables', [
			$this->get_wp_table( 'posts' )    => $this->get_wp_table( 'posts' ),
			$this->get_wp_table( 'postmeta' ) => $this->get_wp_table( 'postmeta' ),
			$this->get_wp_table( 'users' )    => $this->get_wp_table( 'users' ),
			$this->get_wp_table( 'usermeta' ) => $this->get_wp_table( 'usermeta' ),
			$this->get_wp_table( 'options' )  => $this->get_wp_table( 'options' ),
		], $current_blog_id );

		$changed = false;
		$cache   = $this->cache_get( 'wp_table_defines', [] );
		foreach ( $tables as $table ) {
			if ( isset( $cache[ $table ] ) ) {
				$table_define = $cache[ $table ];
			} else {
				$changed      = true;
				$sql          = "DESCRIBE $table";
				$columns      = $this->wpdb()->get_results( $sql, ARRAY_A );
				$table_define = [];
				foreach ( $columns as $column ) {
					$name = $column['Field'];
					$key  = $name;
					if ( isset( $column['Key'] ) && $column['Key'] === 'PRI' ) {
						$key                = 'id';
						$table_define['id'] = $name;
					}
					$type     = explode( ' ', $column['Type'] );
					$unsigned = in_array( 'unsigned', $type );
					$type     = reset( $type );
					$null     = $column['Null'] != 'NO';

					$table_define['columns'][ $key ] = [
						'name'     => $name,
						'type'     => $type,
						'format'   => $this->type2format( $type ),
						'unsigned' => $unsigned,
						'null'     => $null,
					];
				}
				$table_define['delete']    = 'physical';
				$table_define['wordpress'] = true;
				$cache[ $table ]           = $table_define;
			}
			$this->table_defines[ $table ] = $table_define;
		}
		if ( $changed ) {
			$this->cache_set( 'wp_table_defines', $cache );
		}
	}

	/**
	 * @param string $table
	 * @param array $define
	 *
	 * @return array
	 */
	protected function setup_table_columns( $table, array $define ) {
		if ( empty( $define['columns'] ) ) {
			return [ false, false ];
		}

		$id = $table . '_id';
		if ( ! empty( $define['id'] ) ) {
			$id = $define['id'];
		}

		$columns       = [];
		$columns['id'] = [
			'name'     => $id,
			'type'     => 'bigint(20)',
			'unsigned' => true,
			'null'     => false,
			'format'   => '%d',
		];

		$check = true;
		foreach ( $define['columns'] as $key => $column ) {
			if ( ! is_array( $column ) ) {
				$check = false;
				break;
			}
			$type = trim( $this->app->array->get( $column, 'type' ) );
			if ( empty( $type ) ) {
				$check = false;
				break;
			}

			$column['name']   = $this->app->array->get( $column, 'name', $key );
			$column['format'] = $this->app->array->get( $column, 'format', function () use ( $type ) {
				return $this->type2format( $type );
			} );
			$column['length'] = null;
			if ( preg_match( '/\(\s*(\d+)\s*\)/', $type, $matches ) ) {
				$column['length'] = $matches[1] - 0;
			}
			$column['is_user_defined'] = true;
			if ( ! empty( $column['comment'] ) ) {
				$column['comment'] = $this->translate( $column['comment'] );
			}
			$columns[ $key ] = $column;
		}
		if ( ! $check ) {
			return [ false, false ];
		}

		$columns['created_at'] = [
			'name'       => 'created_at',
			'type'       => 'datetime',
			'null'       => false,
			'format'     => '%s',
			'only_admin' => true,
		];
		$columns['created_by'] = [
			'name'       => 'created_by',
			'type'       => 'varchar(32)',
			'null'       => false,
			'format'     => '%s',
			'only_admin' => true,
		];
		$columns['updated_at'] = [
			'name'       => 'updated_at',
			'type'       => 'datetime',
			'null'       => false,
			'format'     => '%s',
			'only_admin' => true,
		];
		$columns['updated_by'] = [
			'name'       => 'updated_by',
			'type'       => 'varchar(32)',
			'null'       => false,
			'format'     => '%s',
			'only_admin' => true,
		];

		if ( $this->is_logical( $define ) ) {
			$columns['deleted_at'] = [
				'name'       => 'deleted_at',
				'type'       => 'datetime',
				'format'     => '%s',
				'only_admin' => true,
			];
			$columns['deleted_by'] = [
				'name'       => 'deleted_by',
				'type'       => 'varchar(32)',
				'format'     => '%s',
				'only_admin' => true,
			];
		}

		return $this->apply_filters( 'setup_table_columns', [ $id, $columns ], $table, $define, $id, $columns );
	}

	/**
	 * @return array
	 */
	public function get_table_defines() {
		return $this->table_defines;
	}

	/**
	 * @return string
	 */
	public function get_table_prefix() {
		global $table_prefix;

		return $table_prefix . $this->get_slug( 'table_prefix', '_' );
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	public function unwrap( $value ) {
		$value = trim( $value );
		$value = trim( $value, '`"\'' );

		return trim( $value );
	}

	/**
	 * @param string $value
	 *
	 * @return array
	 */
	public function get_table_name( $value ) {
		$value = trim( $value );
		if ( ! isset( $this->_table_name_cache[ $value ] ) ) {
			$_value = $value;
			$as     = null;
			if ( stripos( $_value, ' as ' ) !== false ) {
				$segments = preg_split( '/\s+as\s+/i', $_value );
				$_value   = $segments[0];
				$as       = $this->unwrap( $segments[1] );
			}
			$_value = $this->unwrap( explode( '.', $_value )[0] );
			empty( $as ) and $as = $_value;
			$this->_table_name_cache[ $value ] = [ $_value, $as ];
		}

		return $this->_table_name_cache[ $value ];
	}

	/**
	 * @param string $table
	 *
	 * @return bool
	 */
	public function is_managed_table( $table ) {
		return isset( $this->table_defines[ $table ] );
	}

	/**
	 * @param string $table
	 *
	 * @return array
	 */
	public function get_managed_table( $table ) {
		list( $table, $as ) = $this->get_table_name( $table );

		return $this->is_managed_table( $table ) ? [ true, $table, $as ] : [ false, $table, $as ];
	}

	/**
	 * @param string $table
	 * @param bool $not_check
	 *
	 * @return string
	 */
	public function get_table( $table, $not_check = false ) {
		if (
			! $not_check && (
				! isset( $this->table_defines[ $table ] ) ||
				! empty( $this->table_defines[ $table ]['wordpress'] ) ||
				! empty( $this->table_defines[ $table ]['global'] )
			)
		) {
			return $table;
		}

		return $this->get_table_prefix() . $table;
	}

	/**
	 * @param string $table
	 *
	 * @return array
	 */
	public function get_columns( $table ) {
		if ( ! isset( $this->table_defines[ $table ]['columns'] ) ) {
			return [];
		}

		return $this->table_defines[ $table ]['columns'];
	}

	/**
	 * db update
	 */
	private function db_update() {
		$db_update = $this->cache_get( 'db_update' );
		if ( $db_update ) {
			return;
		}

		$this->do_framework_action( 'start_db_update' );
		$this->transaction( function () {
			$this->cache_set( 'db_update', true );
			set_time_limit( 60 * 5 );
			foreach ( $this->table_defines as $table => $define ) {
				$results = $this->table_update( $table, $define );
				if ( $results ) {
					$message = implode( '<br>', array_filter( $results, function ( $d ) {
						return ! empty( $d );
					} ) );
					if ( $message ) {
						$this->app->add_message( $message, 'db', false, false );
					}
				}
			}
		} );
		$this->do_framework_action( 'finished_db_update' );
	}

	/**
	 * @param string $table
	 * @param array $define
	 *
	 * @return array
	 */
	protected function table_update( $table, array $define ) {
		/** @noinspection PhpIncludeInspection */
		require_once ABSPATH . "wp-admin" . DS . "includes" . DS . "upgrade.php";
		$char = $this->app->utility->definedv( 'DB_CHARSET', 'utf8' );
		if ( empty( $define['id'] ) ) {
			$define['id'] = $table . '_id';
		}

		$table = $this->get_table( $table );
		$sql   = "CREATE TABLE {$table} (\n";
		foreach ( $define['columns'] as $key => $column ) {
			$name     = $this->app->array->get( $column, 'name' );
			$type     = strtolower( $this->app->array->get( $column, 'type' ) );
			$unsigned = $this->app->array->get( $column, 'unsigned', false );
			$null     = $this->app->array->get( $column, 'null', true );
			$comment  = $this->app->array->get( $column, 'comment', '' );

			$sql .= $name . ' ' . $type;
			if ( $unsigned && '%s' !== $this->type2format( $type ) && strstr( $type, 'bit' ) === false && strstr( $type, 'bool' ) === false ) {
				$sql .= ' unsigned';
			}
			if ( $null ) {
				$sql .= ' NULL';
			} else {
				$sql .= ' NOT NULL';
			}
			if ( $key === 'id' ) {
				$sql .= ' AUTO_INCREMENT';
			} elseif ( $this->app->array->exists( $column, 'default' ) ) {
				$default = $this->app->array->get( $column, 'default' );
				if ( ! is_string( $default ) ) {
					if ( is_bool( $default ) ) {
						$default = (int) $default;
					} else {
						$default = var_export( $default, true );
					}
					$sql .= " DEFAULT {$default}";
				} else {
					$default = str_replace( '\'', '\\\'', $default );
					$sql     .= " DEFAULT '{$default}'";
				}
			}
			if ( ! empty( $comment ) ) {
				$comment = str_replace( '\'', '\\\'', $comment );
				$sql     .= " COMMENT '{$comment}'";
			}
			$sql .= ",\n";
		}

		$index   = [];
		$index[] = "PRIMARY KEY  ({$define['columns']['id']['name']})";
		if ( ! empty( $define['index']['key'] ) ) {
			foreach ( $define['index']['key'] as $name => $columns ) {
				if ( ! is_array( $columns ) ) {
					$columns = [ $columns ];
				}
				$columns = implode( ', ', $columns );
				$index[] = "KEY {$name} ({$columns})";
			}
		}
		if ( ! empty( $define['index']['unique'] ) ) {
			foreach ( $define['index']['unique'] as $name => $columns ) {
				if ( ! is_array( $columns ) ) {
					$columns = [ $columns ];
				}
				$columns = implode( ', ', $columns );
				$index[] = "UNIQUE KEY {$name} ({$columns})";
			}
		}
		$sql .= implode( ",\n", $index );
		$sql .= "\n) ENGINE = InnoDB DEFAULT CHARSET = {$char}";
		if ( ! empty( $define['comment'] ) ) {
			$define['comment'] = str_replace( '\'', '\\\'', $define['comment'] );
			$sql               .= " COMMENT '{$define['comment']}'";
		}
		$sql .= ';';

		return dbDelta( $sql );
	}

	/**
	 * @param array $define
	 *
	 * @return bool
	 */
	protected function is_logical( array $define ) {
		return $this->apply_filters( 'is_logical', 'physical' !== $this->app->array->get( $define, 'delete', function () {
				return $this->app->get_config( 'config', 'default_delete_rule' );
			} ), $define );
	}

	/**
	 * @param string $table
	 *
	 * @return bool
	 */
	public function is_logical_table( $table ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		return $this->is_logical( $this->table_defines[ $table ] );
	}

	/**
	 * @param array $data
	 * @param bool $create
	 * @param bool $update
	 * @param bool $delete
	 * @param string|null $table
	 *
	 * @return array
	 */
	public function set_update_params( array $data, $create, $update, $delete, $table = null ) {
		$now  = $this->apply_filters( 'set_update_params_date', $this->date(), $data, $create, $update, $delete );
		$user = $this->apply_filters( 'set_update_params_user', substr( $this->app->user->user_name, 0, 32 ), $data, $create, $update, $delete );

		$table = $table ? ( $table . '.' ) : '';
		if ( $create ) {
			$data[ $table . 'created_at' ] = $now;
			$data[ $table . 'created_by' ] = $user;
		}
		if ( $update ) {
			$data[ $table . 'updated_at' ] = $now;
			$data[ $table . 'updated_by' ] = $user;
		}
		if ( $delete ) {
			$data[ $table . 'deleted_at' ] = $now;
			$data[ $table . 'deleted_by' ] = $user;
		}

		return $data;
	}

	/**
	 * @return Query\Builder
	 */
	public function builder() {
		$grammar = new Query\Grammar( $this->app );

		return new Query\Builder( $this->app, new Query\Connection( $this->app, $grammar ), $grammar, new Query\Processor( $this->app ) );
	}

	/**
	 * @param string $table
	 *
	 * @return false|int
	 */
	public function truncate( $table ) {
		return $this->table( $table )->truncate();
	}

	/**
	 * @param mixed $value
	 *
	 * @return Query\Expression
	 */
	public function get_raw( $value ) {
		return new Query\Expression( $value );
	}

	/**
	 * @return int
	 */
	public function get_insert_id() {
		return $this->wpdb()->insert_id;
	}

	/**
	 * @return string
	 */
	public function get_last_error() {
		return $this->wpdb()->last_error;
	}

	/**
	 * @return Exception|null
	 */
	public function get_last_transaction_error() {
		return $this->_error;
	}

	/**
	 * @param string $sql
	 * @param array $values
	 *
	 * @return string
	 */
	public function prepare( $sql, array $values ) {
		return empty( $values ) ? $sql : $this->wpdb()->prepare( $sql, $values );
	}

	/**
	 * @param string $sql
	 *
	 * @return false|int
	 */
	protected function query( $sql ) {
		return $this->wpdb()->query( $sql );
	}

	/**
	 * @return false|int
	 */
	protected function begin() {
		return $this->query( 'START TRANSACTION' );
	}

	/**
	 * @return false|int
	 */
	protected function commit() {
		return $this->query( 'COMMIT' );
	}

	/**
	 * @return false|int
	 */
	protected function rollback() {
		return $this->query( 'ROLLBACK' );
	}

	/**
	 * @param callable $func
	 *
	 * @return bool
	 */
	public function transaction( callable $func ) {
		$level = $this->_transaction_level;
		$this->_transaction_level++;
		if ( $level === 0 ) {
			$this->_error = null;
			try {
				$this->begin();
				$func();
				$this->commit();

				return true;
			} catch ( Exception $e ) {
				$this->rollback();
				$this->app->log( $e );
				$this->_error = $e;
			} finally {
				$this->_transaction_level = $level;
			}
		} else {
			try {
				$func();

				return true;
			} finally {
				$this->_transaction_level = $level;
			}
		}

		return false;
	}

	/**
	 * performance report
	 */
	public function performance_report() {
		$queries = Connection::queries( $this->app->plugin_name );
		if ( false !== $queries ) {
			error_log( '' );

			$count   = $this->app->array->sum( $queries, function ( $item ) {
				return count( $item['execute'] );
			} );
			$elapsed = $this->app->array->sum( $queries, function ( $item ) {
				return array_sum( $item['execute'] );
			} );
			$message = sprintf( 'total = %2d, elapsed = %12.8fms', $count, $elapsed );

			error_log( "{$this->app->plugin_name} :  {$message}" );
			foreach ( $queries as $query ) {
				$elapsed = array_sum( $query['execute'] );
				$count   = count( $query['execute'] );
				$message = sprintf( 'total = %2d, elapsed = %12.8fms', $count, $elapsed );
				error_log( "  {$message} : {$query['query']}" );
				foreach ( $query['execute'] as $item ) {
					error_log( sprintf( '      %12.8fms', $item ) );
				}
			}
			error_log( '' );
		}
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$added_tables = $this->app->option->get_grouped( 'added_tables', 'db', [] );
		foreach ( $added_tables as $table ) {
			$sql = 'DROP TABLE IF EXISTS `' . $this->get_table( $table, true ) . '`';
			$this->query( $sql );
		}
	}

	/**
	 * @return int
	 */
	public function get_uninstall_priority() {
		return 500;
	}
}
