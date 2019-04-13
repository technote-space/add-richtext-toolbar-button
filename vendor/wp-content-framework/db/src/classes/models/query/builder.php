<?php
/**
 * WP_Framework_Db Classes Models Query Builder
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

namespace WP_Framework_Db\Classes\Models\Query;

use Closure;
use DateTimeInterface;
use InvalidArgumentException;
use BadMethodCallException;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Builder
 * @package WP_Framework_Db\Classes\Models\Query
 */
class Builder {

	/**
	 * @var \WP_Framework
	 */
	protected $app;

	/**
	 * The database connection instance.
	 *
	 * @var Connection
	 */
	public $connection;

	/**
	 * The database query grammar instance.
	 *
	 * @var Grammar
	 */
	public $grammar;

	/**
	 * The database query post processor instance.
	 *
	 * @var Processor
	 */
	public $processor;

	/**
	 * The current query value bindings.
	 *
	 * @var array
	 */
	public $bindings = [
		'select' => [],
		'from'   => [],
		'join'   => [],
		'where'  => [],
		'having' => [],
		'order'  => [],
		'union'  => [],
	];

	/**
	 * An aggregate function and column to be run.
	 *
	 * @var array
	 */
	public $aggregate;

	/**
	 * The columns that should be returned.
	 *
	 * @var array
	 */
	public $columns;

	/**
	 * Indicates if the query returns distinct results.
	 *
	 * @var bool
	 */
	public $distinct = false;

	/**
	 * The table which the query is targeting.
	 *
	 * @var string
	 */
	public $from;

	/**
	 * The table joins for the query.
	 *
	 * @var array
	 */
	public $joins;

	/**
	 * The where constraints for the query.
	 *
	 * @var array
	 */
	public $wheres = [];

	/**
	 * The groupings for the query.
	 *
	 * @var array
	 */
	public $groups;

	/**
	 * The having constraints for the query.
	 *
	 * @var array
	 */
	public $havings;

	/**
	 * The orderings for the query.
	 *
	 * @var array
	 */
	public $orders;

	/**
	 * The maximum number of records to return.
	 *
	 * @var int
	 */
	public $limit;

	/**
	 * The number of records to skip.
	 *
	 * @var int
	 */
	public $offset;

	/**
	 * The query union statements.
	 *
	 * @var array
	 */
	public $unions;

	/**
	 * The maximum number of union records to return.
	 *
	 * @var int
	 */
	public $union_limit;

	/**
	 * The number of union records to skip.
	 *
	 * @var int
	 */
	public $union_offset;

	/**
	 * The orderings for the union query.
	 *
	 * @var array
	 */
	public $union_orders;

	/**
	 * Indicates whether row locking is being used.
	 *
	 * @var string|bool
	 */
	public $lock;

	/**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	public $operators = [
		'=',
		'<',
		'>',
		'<=',
		'>=',
		'<>',
		'!=',
		'<=>',
		'like',
		'like binary',
		'not like',
		'ilike',
		'&',
		'|',
		'^',
		'<<',
		'>>',
		'rlike',
		'regexp',
		'not regexp',
		'~',
		'~*',
		'!~',
		'!~*',
		'similar to',
		'not similar to',
		'not ilike',
		'~~*',
		'!~~*',
	];

	/**
	 * @var array $_managed_column_cache
	 */
	protected $_managed_column_cache = [];

	/**
	 * @var int $ttl
	 */
	public $ttl;

	/**
	 * @var bool $is_object
	 */
	public $is_object;

	/**
	 * Create a new query builder instance.
	 *
	 * @param \WP_Framework $app
	 * @param Connection $connection
	 * @param Grammar $grammar
	 * @param Processor $processor
	 *
	 * @return void
	 */
	public function __construct(
		\WP_Framework $app,
		Connection $connection,
		Grammar $grammar,
		Processor $processor
	) {
		$this->app        = $app;
		$this->connection = $connection;
		$this->grammar    = $grammar;
		$this->processor  = $processor;
	}

	/**
	 * @param null|string $table
	 *
	 * @return array
	 */
	protected function get_managed_table( $table = null ) {
		return $this->app->db->get_managed_table( $table ? $table : $this->from );
	}

	/**
	 * @param string $column
	 * @param bool $select
	 *
	 * @return string
	 */
	protected function get_managed_column( $column, $select = false ) {
		if ( $this->grammar->is_expression( $column ) ) {
			return $column;
		}
		$column = trim( $column );
		$key    = $column;
		if ( ! isset( $this->_managed_column_cache[ $key ] ) ) {
			$column_table = null;
			$exploded     = explode( '.', $column );
			if ( count( $exploded ) >= 2 ) {
				if ( $this instanceof Join ) {
					/** @noinspection PhpUnusedLocalVariableInspection */
					list( $managed, $table, $as ) = $this->get_managed_table( $this->table );
					if ( $as === $exploded[0] ) {
						$column_table = $this->table;
					} else {
						$column_table = $this->app->db->unwrap( $exploded[0] );
					}
				} else {
					$column_table = $this->app->db->unwrap( $exploded[0] );
				}
				$_column = $exploded[1];
			} else {
				$_column = $column;
			}
			list( $managed, $table, $as ) = $this->get_managed_table( $column_table );
			if ( $managed ) {
				$name = $this->app->array->get( $this->app->db->get_columns( $table ), $this->app->db->unwrap( $_column ) . '.name', $_column );
				$as   .= '.';
				if ( $select && '*' === $name && ! $column_table ) {
					$as = '';
				}
				if ( $select && $name !== $_column ) {
					$column = "{$as}{$name} as {$_column}";
				} else {
					$column = "{$as}{$name}";
				}
			}
			$this->_managed_column_cache[ $key ] = $column;
		}

		return $this->_managed_column_cache[ $key ];
	}

	/**
	 * @param array|mixed $columns
	 */
	protected function set_select_columns( $columns ) {
		$this->_set_select_columns( is_array( $columns ) ? $columns : func_get_args(), function () {
			$this->columns = [];
		} );
	}

	/**
	 * @param array|mixed $columns
	 */
	private function add_select_columns( $columns ) {
		$this->_set_select_columns( is_array( $columns ) ? $columns : func_get_args(), function () {
			! is_array( $this->columns ) and $this->columns = [];
		} );
	}

	/**
	 * @param array $columns
	 * @param callable $callback
	 */
	private function _set_select_columns( $columns, $callback ) {
		$callback();
		foreach ( $columns as $column ) {
			$column = $this->get_managed_column( $column, true );
			$hash   = $this->create_hash( $column );
			if ( array_key_exists( $hash, $this->columns ) ) {
				unset( $this->columns[ $hash ] );
			}
			$this->columns[ $hash ] = $column;
		}
	}

	/**
	 * @param mixed $value
	 *
	 * @return string
	 */
	private function create_hash( $value ) {
		if ( $this->grammar->is_expression( $value ) ) {
			$value = $value->get_value();
		}

		return sha1( json_encode( $value ) );
	}

	/**
	 * Set the columns to be selected.
	 *
	 * @param  array|mixed $columns
	 *
	 * @return $this
	 */
	public function select( $columns = [ '*' ] ) {
		$this->set_select_columns( $columns );

		return $this;
	}

	/**
	 * Add a subselect expression to the query.
	 *
	 * @param  \Closure|Builder|string $query
	 * @param  string $as
	 *
	 * @return Builder|static
	 *
	 * @throws \InvalidArgumentException
	 */
	public function select_sub( $query, $as ) {
		list( $query, $bindings ) = $this->create_sub( $query );

		return $this->select_raw(
			'(' . $query . ') as ' . $this->grammar->wrap( $as ), $bindings
		);
	}

	/**
	 * Add a new "raw" select expression to the query.
	 *
	 * @param  string $expression
	 * @param  array $bindings
	 *
	 * @return Builder|static
	 */
	public function select_raw( $expression, array $bindings = [] ) {
		$this->add_select( new Expression( $expression ) );
		if ( $bindings ) {
			$this->add_binding( $bindings, 'select' );
		}

		return $this;
	}

	/**
	 * Makes "from" fetch from a subquery.
	 *
	 * @param  \Closure|Builder|string $query
	 * @param  string $as
	 *
	 * @return Builder|static
	 *
	 * @throws \InvalidArgumentException
	 */
	public function from_sub( $query, $as ) {
		list( $query, $bindings ) = $this->create_sub( $query );

		return $this->from_raw( '(' . $query . ') as ' . $this->grammar->wrap( $as ), $bindings );
	}

	/**
	 * Add a raw from clause to the query.
	 *
	 * @param  string $expression
	 * @param  mixed $bindings
	 *
	 * @return Builder|static
	 */
	public function from_raw( $expression, $bindings = [] ) {
		$this->from = new Expression( $expression );
		$this->add_binding( $bindings, 'from' );

		return $this;
	}

	/**
	 * Creates a subquery and parse it.
	 *
	 * @param  \Closure|Builder|string $query
	 *
	 * @return array
	 */
	protected function create_sub( $query ) {
		// If the given query is a Closure, we will execute it while passing in a new
		// query instance to the Closure. This will give the developer a chance to
		// format and work with the query before we cast it to a raw SQL string.
		if ( $query instanceof Closure ) {
			$callback = $query;
			$callback( $query = $this->for_sub_query() );
		}

		return $this->parse_sub( $query );
	}

	/**
	 * Parse the subquery into SQL and bindings.
	 *
	 * @param  mixed $query
	 *
	 * @return array
	 */
	protected function parse_sub( $query ) {
		if ( $query instanceof self ) {
			return [ $query->to_sql(), $query->get_bindings() ];
		} elseif ( is_string( $query ) ) {
			return [ $query, [] ];
		} else {
			throw new InvalidArgumentException;
		}
	}

	/**
	 * Add a new select column to the query.
	 *
	 * @param  array|mixed $column
	 *
	 * @return $this
	 */
	public function add_select( $column ) {
		$this->add_select_columns( $column );

		return $this;
	}

	/**
	 * Force the query to only return distinct results.
	 *
	 * @return $this
	 */
	public function distinct() {
		$this->distinct = true;

		return $this;
	}

	/**
	 * Set the table which the query is targeting.
	 *
	 * @param  string $table
	 *
	 * @return $this
	 */
	public function from( $table ) {
		$this->from = $table;

		return $this;
	}

	/**
	 * @param $table
	 *
	 * @return $this
	 */
	public function table( $table ) {
		return $this->from( $table );
	}

	/**
	 * Add a join clause to the query.
	 *
	 * @param  string $table
	 * @param  \Closure|string $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 * @param  string $type
	 * @param  bool $where
	 *
	 * @return $this
	 */
	public function join( $table, $first, $operator = null, $second = null, $type = 'inner', $where = false ) {
		$join = $this->new_join_clause( $this, $type, $table );
		// If the first "column" of the join is really a Closure instance the developer
		// is trying to build a join with a complex "on" clause containing more than
		// one condition, so we'll add the join and call a Closure with the query.
		if ( $first instanceof Closure ) {
			call_user_func( $first, $join );
			$this->joins[] = $join;
			$this->add_binding( $join->get_bindings(), 'join' );
		}
		// If the column is simply a string, we can assume the join simply has a basic
		// "on" clause with a single condition. So we will just build the join with
		// this simple join clauses attached to it. There is not a join callback.
		else {
			$method        = $where ? 'where' : 'on';
			$this->joins[] = $join->$method( $first, $operator, $second );
			$this->add_binding( $join->get_bindings(), 'join' );
		}

		return $this;
	}

	/**
	 * Add a "join where" clause to the query.
	 *
	 * @param  string $table
	 * @param  \Closure|string $first
	 * @param  string $operator
	 * @param  string $second
	 * @param  string $type
	 *
	 * @return Builder|static
	 */
	public function join_where( $table, $first, $operator, $second, $type = 'inner' ) {
		return $this->join( $table, $first, $operator, $second, $type, true );
	}

	/**
	 * Add a subquery join clause to the query.
	 *
	 * @param  \Closure|Builder|string $query
	 * @param  string $as
	 * @param  \Closure|string $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 * @param  string $type
	 * @param  bool $where
	 *
	 * @return Builder|static
	 *
	 * @throws \InvalidArgumentException
	 */
	public function join_sub( $query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false ) {
		list( $query, $bindings ) = $this->create_sub( $query );
		$expression = '(' . $query . ') as ' . $this->grammar->wrap( $as );
		$this->add_binding( $bindings, 'join' );

		return $this->join( new Expression( $expression ), $first, $operator, $second, $type, $where );
	}

	/**
	 * Add a left join to the query.
	 *
	 * @param  string $table
	 * @param  \Closure|string $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 *
	 * @return Builder|static
	 */
	public function left_join( $table, $first, $operator = null, $second = null ) {
		return $this->join( $table, $first, $operator, $second, 'left' );
	}

	/**
	 * Add a "join where" clause to the query.
	 *
	 * @param  string $table
	 * @param  \Closure|string $first
	 * @param  string $operator
	 * @param  string $second
	 *
	 * @return Builder|static
	 */
	public function left_join_where( $table, $first, $operator, $second ) {
		return $this->join_where( $table, $first, $operator, $second, 'left' );
	}

	/**
	 * Add a subquery left join to the query.
	 *
	 * @param  \Closure|Builder|string $query
	 * @param  string $as
	 * @param  \Closure|string $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 *
	 * @return Builder|static
	 */
	public function left_join_sub( $query, $as, $first, $operator = null, $second = null ) {
		return $this->join_sub( $query, $as, $first, $operator, $second, 'left' );
	}

	/**
	 * Add a right join to the query.
	 *
	 * @param  string $table
	 * @param  \Closure|string $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 *
	 * @return Builder|static
	 */
	public function right_join( $table, $first, $operator = null, $second = null ) {
		return $this->join( $table, $first, $operator, $second, 'right' );
	}

	/**
	 * Add a "right join where" clause to the query.
	 *
	 * @param  string $table
	 * @param  \Closure|string $first
	 * @param  string $operator
	 * @param  string $second
	 *
	 * @return Builder|static
	 */
	public function right_join_where( $table, $first, $operator, $second ) {
		return $this->join_where( $table, $first, $operator, $second, 'right' );
	}

	/**
	 * Add a subquery right join to the query.
	 *
	 * @param  \Closure|Builder|string $query
	 * @param  string $as
	 * @param  \Closure|string $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 *
	 * @return Builder|static
	 */
	public function right_join_sub( $query, $as, $first, $operator = null, $second = null ) {
		return $this->join_sub( $query, $as, $first, $operator, $second, 'right' );
	}

	/**
	 * Add a "cross join" clause to the query.
	 *
	 * @param  string $table
	 * @param  \Closure|string|null $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 *
	 * @return Builder|static
	 */
	public function cross_join( $table, $first = null, $operator = null, $second = null ) {
		if ( $first ) {
			return $this->join( $table, $first, $operator, $second, 'cross' );
		}
		$this->joins[] = $this->new_join_clause( $this, 'cross', $table );

		return $this;
	}

	/**
	 * @param string $table
	 * @param string $as
	 * @param \Closure|string $first
	 * @param string|null $operator
	 * @param string|null $second
	 * @param string $type
	 * @param bool $where
	 *
	 * @return Builder
	 */
	public function alias_join( $table, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false ) {
		return $this->join( $this->app->db->alias( $table, $as ), $first, $operator, $second, $type, $where );
	}

	/**
	 * @param string $table
	 * @param string $as
	 * @param \Closure|string $first
	 * @param string|null $operator
	 * @param string|null $second
	 *
	 * @return Builder
	 */
	public function alias_left_join( $table, $as, $first, $operator = null, $second = null ) {
		return $this->left_join( $this->app->db->alias( $table, $as ), $first, $operator, $second );
	}

	/**
	 * @param string $table
	 * @param string $as
	 * @param \Closure|string $first
	 * @param string|null $operator
	 * @param string|null $second
	 *
	 * @return Builder
	 */
	public function alias_right_join( $table, $as, $first, $operator = null, $second = null ) {
		return $this->right_join( $this->app->db->alias( $table, $as ), $first, $operator, $second );
	}

	/**
	 * @param string $table
	 * @param string $as
	 * @param \Closure|string $first
	 * @param string|null $operator
	 * @param string|null $second
	 * @param string $type
	 * @param bool $where
	 *
	 * @return Builder
	 */
	public function alias_join_wp( $table, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false ) {
		return $this->join( $this->app->db->get_wp_table( $table, $as ), $first, $operator, $second, $type, $where );
	}

	/**
	 * @param string $table
	 * @param string $as
	 * @param \Closure|string $first
	 * @param string|null $operator
	 * @param string|null $second
	 *
	 * @return Builder
	 */
	public function alias_left_join_wp( $table, $as, $first, $operator = null, $second = null ) {
		return $this->left_join( $this->app->db->get_wp_table( $table, $as ), $first, $operator, $second );
	}

	/**
	 * @param string $table
	 * @param string $as
	 * @param \Closure|string $first
	 * @param string|null $operator
	 * @param string|null $second
	 *
	 * @return Builder
	 */
	public function alias_right_join_wp( $table, $as, $first, $operator = null, $second = null ) {
		return $this->right_join( $this->app->db->get_wp_table( $table, $as ), $first, $operator, $second );
	}

	/**
	 * Get a new join clause.
	 *
	 * @param  Builder $parent_query
	 * @param  string $type
	 * @param  string $table
	 *
	 * @return Join
	 */
	protected function new_join_clause( Builder $parent_query, $type, $table ) {
		return new Join( $this->app, $parent_query, $type, $table );
	}

	/**
	 * Merge an array of where clauses and bindings.
	 *
	 * @param  array $wheres
	 * @param  array $bindings
	 *
	 * @return void
	 */
	public function merge_wheres( $wheres, $bindings ) {
		$this->wheres            = array_merge( $this->wheres, (array) $wheres );
		$this->bindings['where'] = array_values( array_merge( $this->bindings['where'], (array) $bindings ) );
	}

	/**
	 * @param array $value
	 */
	protected function add_where( $value ) {
		foreach ( [ 'column', 'first', 'second' ] as $c ) {
			if ( array_key_exists( $c, $value ) ) {
				$value[ $c ] = $this->get_managed_column( $value[ $c ] );
			}
		}
		$this->wheres[ $this->create_hash( $value ) ] = $value;
	}

	/**
	 * Add a basic where clause to the query.
	 *
	 * @param  string|array|\Closure $column
	 * @param  mixed $operator
	 * @param  mixed $value
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	public function where( $column, $operator = null, $value = null, $boolean = 'and' ) {
		// If the column is an array, we will assume it is an array of key-value pairs
		// and can add them each as a where clause. We will maintain the boolean we
		// received when the method was called and pass it into the nested where.
		if ( is_array( $column ) ) {
			return $this->add_array_of_wheres( $column, $boolean );
		}
		// Here we will make some assumptions about the operator. If only 2 values are
		// passed to the method, we will assume that the operator is an equals sign
		// and keep going. Otherwise, we'll require the operator to be passed in.
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);
		// If the columns is actually a Closure instance, we will assume the developer
		// wants to begin a nested where statement which is wrapped in parenthesis.
		// We'll add that Closure to the query then return back out immediately.
		if ( $column instanceof Closure ) {
			return $this->where_nested( $column, $boolean );
		}
		// If the given operator is not found in the list of valid operators we will
		// assume that the developer is just short-cutting the '=' operators and
		// we will set the operators to '=' and set the values appropriately.
		if ( $this->invalid_operator( $operator ) ) {
			list( $value, $operator ) = [ $operator, '=' ];
		}
		// If the value is a Closure, it means the developer is performing an entire
		// sub-select within the query and we will need to compile the sub-select
		// within the where clause to get the appropriate query record results.
		if ( $value instanceof Closure ) {
			return $this->where_sub( $column, $operator, $value, $boolean );
		}
		// If the value is "null", we will just assume the developer wants to add a
		// where null clause to the query. So, we will allow a short-cut here to
		// that method for convenience so the developer doesn't have to check.
		if ( is_null( $value ) ) {
			return $this->where_null( $column, $boolean, $operator !== '=' );
		}
		// Now that we are working with just a simple query we can put the elements
		// in our array and add the query binding to our array of bindings that
		// will be bound to each SQL statements when it is finally executed.
		$type = 'basic';
		$this->add_where( compact( 'type', 'column', 'operator', 'value', 'boolean' ) );
		if ( ! $value instanceof Expression ) {
			$this->add_binding( $value, 'where' );
		}

		return $this;
	}

	/**
	 * Add an array of where clauses to the query.
	 *
	 * @param  array $column
	 * @param  string $boolean
	 * @param  string $method
	 *
	 * @return $this
	 */
	protected function add_array_of_wheres( $column, $boolean, $method = 'where' ) {
		return $this->where_nested( function ( $query ) use ( $column, $method, $boolean ) {
			foreach ( $column as $key => $value ) {
				if ( is_numeric( $key ) && is_array( $value ) ) {
					$query->{$method}( ...array_values( $value ) );
				} else {
					$query->$method( $key, '=', $value, $boolean );
				}
			}
		}, $boolean );
	}

	/**
	 * Prepare the value and operator for a where clause.
	 *
	 * @param  string $value
	 * @param  string $operator
	 * @param  bool $use_default
	 *
	 * @return array
	 *
	 * @throws \InvalidArgumentException
	 */
	public function prepare_value_and_operator( $value, $operator, $use_default = false ) {
		if ( $use_default ) {
			return [ $operator, '=' ];
		} elseif ( $this->invalid_operator_and_value( $operator, $value ) ) {
			throw new InvalidArgumentException( 'Illegal operator and value combination.' );
		}

		return [ $value, $operator ];
	}

	/**
	 * Determine if the given operator and value combination is legal.
	 *
	 * Prevents using Null values with invalid operators.
	 *
	 * @param  string $operator
	 * @param  mixed $value
	 *
	 * @return bool
	 */
	protected function invalid_operator_and_value( $operator, $value ) {
		return is_null( $value ) && in_array( $operator, $this->operators ) && ! in_array( $operator, [ '=', '<>', '!=' ] );
	}

	/**
	 * Determine if the given operator is supported.
	 *
	 * @param  string $operator
	 *
	 * @return bool
	 */
	protected function invalid_operator( $operator ) {
		return ! in_array( strtolower( $operator ), $this->operators, true ) &&
		       ! in_array( strtolower( $operator ), $this->grammar->get_operators(), true );
	}

	/**
	 * Add an "or where" clause to the query.
	 *
	 * @param  string|array|\Closure $column
	 * @param  mixed $operator
	 * @param  mixed $value
	 *
	 * @return Builder|static
	 */
	public function or_where( $column, $operator = null, $value = null ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);

		return $this->where( $column, $operator, $value, 'or' );
	}

	/**
	 * Add a "where" clause comparing two columns to the query.
	 *
	 * @param  string|array $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 * @param  string|null $boolean
	 *
	 * @return Builder|static
	 */
	public function where_column( $first, $operator = null, $second = null, $boolean = 'and' ) {
		// If the column is an array, we will assume it is an array of key-value pairs
		// and can add them each as a where clause. We will maintain the boolean we
		// received when the method was called and pass it into the nested where.
		if ( is_array( $first ) ) {
			return $this->add_array_of_wheres( $first, $boolean, 'where_column' );
		}
		// If the given operator is not found in the list of valid operators we will
		// assume that the developer is just short-cutting the '=' operators and
		// we will set the operators to '=' and set the values appropriately.
		if ( $this->invalid_operator( $operator ) ) {
			list( $second, $operator ) = [ $operator, '=' ];
		}
		// Finally, we will add this where clause into this array of clauses that we
		// are building for the query. All of them will be compiled via a grammar
		// once the query is about to be executed and run against the database.
		$type = 'column';
		$this->add_where( compact( 'type', 'first', 'operator', 'second', 'boolean' ) );

		return $this;
	}

	/**
	 * Add an "or where" clause comparing two columns to the query.
	 *
	 * @param  string|array $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 *
	 * @return Builder|static
	 */
	public function or_where_column( $first, $operator = null, $second = null ) {
		return $this->where_column( $first, $operator, $second, 'or' );
	}

	/**
	 * Add a raw where clause to the query.
	 *
	 * @param  string $sql
	 * @param  mixed $bindings
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	public function where_raw( $sql, $bindings = [], $boolean = 'and' ) {
		$this->add_where( [ 'type' => 'raw', 'sql' => $sql, 'boolean' => $boolean ] );
		$this->add_binding( (array) $bindings, 'where' );

		return $this;
	}

	/**
	 * Add a raw or where clause to the query.
	 *
	 * @param  string $sql
	 * @param  mixed $bindings
	 *
	 * @return Builder|static
	 */
	public function or_where_raw( $sql, $bindings = [] ) {
		return $this->where_raw( $sql, $bindings, 'or' );
	}

	/**
	 * Add a "where in" clause to the query.
	 *
	 * @param  string $column
	 * @param  mixed $values
	 * @param  string $boolean
	 * @param  bool $not
	 *
	 * @return $this
	 */
	public function where_in( $column, $values, $boolean = 'and', $not = false ) {
		$type = $not ? 'not_in' : 'in';
		// If the value is a query builder instance we will assume the developer wants to
		// look for any values that exists within this given query. So we will add the
		// query accordingly so that this query is properly executed when it is run.
		if ( $values instanceof self ||
		     $values instanceof Closure ) {
			list( $query, $bindings ) = $this->create_sub( $values );
			$values = [ new Expression( $query ) ];
			$this->add_binding( $bindings, 'where' );
		}
		$this->add_where( compact( 'type', 'column', 'values', 'boolean' ) );
		// Finally we'll add a binding for each values unless that value is an expression
		// in which case we will just skip over it since it will be the query as a raw
		// string and not as a parameterized place-holder to be replaced by the PDO.
		$this->add_binding( $this->clean_bindings( $values ), 'where' );

		return $this;
	}

	/**
	 * Add an "or where in" clause to the query.
	 *
	 * @param  string $column
	 * @param  mixed $values
	 *
	 * @return Builder|static
	 */
	public function or_where_in( $column, $values ) {
		return $this->where_in( $column, $values, 'or' );
	}

	/**
	 * Add a "where not in" clause to the query.
	 *
	 * @param  string $column
	 * @param  mixed $values
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_not_in( $column, $values, $boolean = 'and' ) {
		return $this->where_in( $column, $values, $boolean, true );
	}

	/**
	 * Add an "or where not in" clause to the query.
	 *
	 * @param  string $column
	 * @param  mixed $values
	 *
	 * @return Builder|static
	 */
	public function or_where_not_in( $column, $values ) {
		return $this->where_not_in( $column, $values, 'or' );
	}

	/**
	 * Add a "where in raw" clause for integer values to the query.
	 *
	 * @param  string $column
	 * @param  array $values
	 * @param  string $boolean
	 * @param  bool $not
	 *
	 * @return $this
	 */
	public function where_integer_in_raw( $column, $values, $boolean = 'and', $not = false ) {
		$type = $not ? 'not_in_raw' : 'in_raw';
		foreach ( $values as &$value ) {
			$value = (int) $value;
		}
		$this->add_where( compact( 'type', 'column', 'values', 'boolean' ) );

		return $this;
	}

	/**
	 * Add a "where not in raw" clause for integer values to the query.
	 *
	 * @param  string $column
	 * @param  array $values
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	public function where_integer_not_in_raw( $column, $values, $boolean = 'and' ) {
		return $this->where_integer_in_raw( $column, $values, $boolean, true );
	}

	/**
	 * Add a "where null" clause to the query.
	 *
	 * @param  string $column
	 * @param  string $boolean
	 * @param  bool $not
	 *
	 * @return $this
	 */
	public function where_null( $column, $boolean = 'and', $not = false ) {
		$type = $not ? 'not_null' : 'null';
		$this->add_where( compact( 'type', 'column', 'boolean' ) );

		return $this;
	}

	/**
	 * Add an "or where null" clause to the query.
	 *
	 * @param  string $column
	 *
	 * @return Builder|static
	 */
	public function or_where_null( $column ) {
		return $this->where_null( $column, 'or' );
	}

	/**
	 * Add a "where not null" clause to the query.
	 *
	 * @param  string $column
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_not_null( $column, $boolean = 'and' ) {
		return $this->where_null( $column, $boolean, true );
	}

	/**
	 * Add a where between statement to the query.
	 *
	 * @param  string $column
	 * @param  array $values
	 * @param  string $boolean
	 * @param  bool $not
	 *
	 * @return $this
	 */
	public function where_between( $column, array $values, $boolean = 'and', $not = false ) {
		$type = 'between';
		$this->add_where( compact( 'type', 'column', 'values', 'boolean', 'not' ) );
		$this->add_binding( $this->clean_bindings( $values ), 'where' );

		return $this;
	}

	/**
	 * Add an or where between statement to the query.
	 *
	 * @param  string $column
	 * @param  array $values
	 *
	 * @return Builder|static
	 */
	public function or_where_between( $column, array $values ) {
		return $this->where_between( $column, $values, 'or' );
	}

	/**
	 * Add a where not between statement to the query.
	 *
	 * @param  string $column
	 * @param  array $values
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_not_between( $column, array $values, $boolean = 'and' ) {
		return $this->where_between( $column, $values, $boolean, true );
	}

	/**
	 * Add an or where not between statement to the query.
	 *
	 * @param  string $column
	 * @param  array $values
	 *
	 * @return Builder|static
	 */
	public function or_where_not_between( $column, array $values ) {
		return $this->where_not_between( $column, $values, 'or' );
	}

	/**
	 * Add an "or where not null" clause to the query.
	 *
	 * @param  string $column
	 *
	 * @return Builder|static
	 */
	public function or_where_not_null( $column ) {
		return $this->where_not_null( $column, 'or' );
	}

	/**
	 * Add a "where date" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string $value
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_date( $column, $operator, $value = null, $boolean = 'and' ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'Y-m-d' );
		}

		return $this->add_date_based_where( 'date', $column, $operator, $value, $boolean );
	}

	/**
	 * Add an "or where date" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string $value
	 *
	 * @return Builder|static
	 */
	public function or_where_date( $column, $operator, $value = null ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);

		return $this->where_date( $column, $operator, $value, 'or' );
	}

	/**
	 * Add a "where time" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string $value
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_time( $column, $operator, $value = null, $boolean = 'and' ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'H:i:s' );
		}

		return $this->add_date_based_where( 'time', $column, $operator, $value, $boolean );
	}

	/**
	 * Add an "or where time" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string $value
	 *
	 * @return Builder|static
	 */
	public function or_where_time( $column, $operator, $value = null ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);

		return $this->where_time( $column, $operator, $value, 'or' );
	}

	/**
	 * Add a "where day" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string $value
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_day( $column, $operator, $value = null, $boolean = 'and' ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'd' );
		}

		return $this->add_date_based_where( 'day', $column, $operator, $value, $boolean );
	}

	/**
	 * Add an "or where day" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string $value
	 *
	 * @return Builder|static
	 */
	public function or_where_day( $column, $operator, $value = null ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);

		return $this->add_date_based_where( 'day', $column, $operator, $value, 'or' );
	}

	/**
	 * Add a "where month" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string $value
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_month( $column, $operator, $value = null, $boolean = 'and' ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'm' );
		}

		return $this->add_date_based_where( 'month', $column, $operator, $value, $boolean );
	}

	/**
	 * Add an "or where month" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string $value
	 *
	 * @return Builder|static
	 */
	public function or_where_month( $column, $operator, $value = null ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);

		return $this->add_date_based_where( 'month', $column, $operator, $value, 'or' );
	}

	/**
	 * Add a "where year" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string|int $value
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_year( $column, $operator, $value = null, $boolean = 'and' ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);
		if ( $value instanceof DateTimeInterface ) {
			$value = $value->format( 'Y' );
		}

		return $this->add_date_based_where( 'year', $column, $operator, $value, $boolean );
	}

	/**
	 * Add an "or where year" statement to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \DateTimeInterface|string|int $value
	 *
	 * @return Builder|static
	 */
	public function or_where_year( $column, $operator, $value = null ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);

		return $this->add_date_based_where( 'year', $column, $operator, $value, 'or' );
	}

	/**
	 * Add a date based (year, month, day, time) statement to the query.
	 *
	 * @param  string $type
	 * @param  string $column
	 * @param  string $operator
	 * @param  mixed $value
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	protected function add_date_based_where( $type, $column, $operator, $value, $boolean = 'and' ) {
		$this->add_where( compact( 'column', 'type', 'boolean', 'operator', 'value' ) );
		if ( ! $value instanceof Expression ) {
			$this->add_binding( $value, 'where' );
		}

		return $this;
	}

	/**
	 * Add a nested where statement to the query.
	 *
	 * @param  \Closure $callback
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_nested( Closure $callback, $boolean = 'and' ) {
		call_user_func( $callback, $query = $this->for_nested_where() );

		return $this->add_nested_where_query( $query, $boolean );
	}

	/**
	 * Create a new query instance for nested where condition.
	 *
	 * @return Builder
	 */
	public function for_nested_where() {
		return $this->new_query()->from( $this->from );
	}

	/**
	 * Add another query builder as a nested where to the query builder.
	 *
	 * @param  Builder|static $query
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	public function add_nested_where_query( $query, $boolean = 'and' ) {
		if ( count( $query->wheres ) ) {
			$type = 'nested';
			$this->add_where( compact( 'type', 'query', 'boolean' ) );
			$this->add_binding( $query->get_raw_bindings()['where'], 'where' );
		}

		return $this;
	}

	/**
	 * Add a full sub-select to the query.
	 *
	 * @param  string $column
	 * @param  string $operator
	 * @param  \Closure $callback
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	protected function where_sub( $column, $operator, Closure $callback, $boolean ) {
		$type = 'sub';
		// Once we have the query instance we can simply execute it so it can add all
		// of the sub-select's conditions to itself, and then we can cache it off
		// in the array of where clauses for the "main" parent query instance.
		call_user_func( $callback, $query = $this->for_sub_query() );
		$this->add_where( compact( 'type', 'column', 'operator', 'query', 'boolean' ) );
		$this->add_binding( $query->get_bindings(), 'where' );

		return $this;
	}

	/**
	 * Add an exists clause to the query.
	 *
	 * @param  \Closure $callback
	 * @param  string $boolean
	 * @param  bool $not
	 *
	 * @return $this
	 */
	public function where_exists( Closure $callback, $boolean = 'and', $not = false ) {
		$query = $this->for_sub_query();
		// Similar to the sub-select clause, we will create a new query instance so
		// the developer may cleanly specify the entire exists query and we will
		// compile the whole thing in the grammar and insert it into the SQL.
		call_user_func( $callback, $query );

		return $this->add_where_exists_query( $query, $boolean, $not );
	}

	/**
	 * Add an or exists clause to the query.
	 *
	 * @param  \Closure $callback
	 * @param  bool $not
	 *
	 * @return Builder|static
	 */
	public function or_where_exists( Closure $callback, $not = false ) {
		return $this->where_exists( $callback, 'or', $not );
	}

	/**
	 * Add a where not exists clause to the query.
	 *
	 * @param  \Closure $callback
	 * @param  string $boolean
	 *
	 * @return Builder|static
	 */
	public function where_not_exists( Closure $callback, $boolean = 'and' ) {
		return $this->where_exists( $callback, $boolean, true );
	}

	/**
	 * Add a where not exists clause to the query.
	 *
	 * @param  \Closure $callback
	 *
	 * @return Builder|static
	 */
	public function or_where_not_exists( Closure $callback ) {
		return $this->or_where_exists( $callback, true );
	}

	/**
	 * Add an exists clause to the query.
	 *
	 * @param  Builder $query
	 * @param  string $boolean
	 * @param  bool $not
	 *
	 * @return $this
	 */
	public function add_where_exists_query( Builder $query, $boolean = 'and', $not = false ) {
		$type = $not ? 'not_exists' : 'exists';
		$this->add_where( compact( 'type', 'query', 'boolean' ) );
		$this->add_binding( $query->get_bindings(), 'where' );

		return $this;
	}

	/**
	 * Adds a where condition using row values.
	 *
	 * @param  array $columns
	 * @param  string $operator
	 * @param  array $values
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	public function where_row_values( $columns, $operator, $values, $boolean = 'and' ) {
		if ( count( $columns ) !== count( $values ) ) {
			throw new InvalidArgumentException( 'The number of columns must match the number of values' );
		}
		$type = 'row_values';
		$this->add_where( compact( 'type', 'columns', 'operator', 'values', 'boolean' ) );
		$this->add_binding( $this->clean_bindings( $values ) );

		return $this;
	}

	/**
	 * Adds a or where condition using row values.
	 *
	 * @param  array $columns
	 * @param  string $operator
	 * @param  array $values
	 *
	 * @return $this
	 */
	public function or_where_row_values( $columns, $operator, $values ) {
		return $this->where_row_values( $columns, $operator, $values, 'or' );
	}

	/**
	 * Handles dynamic "where" clauses to the query.
	 * ex. where_name_and_age( 'name_foo', 30 )
	 *
	 * @param  string $method
	 * @param  array $parameters
	 *
	 * @return $this
	 */
	public function dynamic_where( $method, $parameters ) {
		$finder   = substr( $method, 6 );
		$segments = explode( '_', $finder );
		// The connector variable will determine which connector will be used for the
		// query condition. We will change it as we come across new boolean values
		// in the dynamic method strings, which could contain a number of these.
		$connector = 'and';
		$index     = 0;
		foreach ( $segments as $segment ) {
			// If the segment is not a boolean connector, we can assume it is a column's name
			// and we will add it to the query as a new constraint as a where clause, then
			// we can keep iterating through the dynamic method string's segments again.
			if ( $segment !== 'and' && $segment !== 'or' ) {
				$this->add_dynamic( $segment, $connector, $parameters, $index );
				$index ++;
			}
			// Otherwise, we will store the connector so we know how the next where clause we
			// find in the query should be connected to the previous ones, meaning we will
			// have the proper boolean connector to connect the next where clause found.
			else {
				$connector = $segment;
			}
		}

		return $this;
	}

	/**
	 * Add a single dynamic where clause statement to the query.
	 *
	 * @param  string $segment
	 * @param  string $connector
	 * @param  array $parameters
	 * @param  int $index
	 *
	 * @return void
	 */
	protected function add_dynamic( $segment, $connector, $parameters, $index ) {
		// Once we have parsed out the columns and formatted the boolean operators we
		// are ready to add it to this query as a where clause just like any other
		// clause on the query. Then we'll increment the parameter index values.
		$bool = strtolower( $connector );
		$this->where( $this->app->string->snake( $segment ), '=', $parameters[ $index ], $bool );
	}

	/**
	 * Add a "group by" clause to the query.
	 *
	 * @param  array ...$groups
	 *
	 * @return $this
	 */
	public function group_by( ...$groups ) {
		foreach ( $groups as $group ) {
			if ( empty( $group ) ) {
				continue;
			}
			$this->groups = array_merge(
				(array) $this->groups,
				[ $this->get_managed_column( $group ) ]
			);
		}

		return $this;
	}

	/**
	 * Add a "having" clause to the query.
	 *
	 * @param  string $column
	 * @param  string|null $operator
	 * @param  string|null $value
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	public function having( $column, $operator = null, $value = null, $boolean = 'and' ) {
		$type = 'basic';
		// Here we will make some assumptions about the operator. If only 2 values are
		// passed to the method, we will assume that the operator is an equals sign
		// and keep going. Otherwise, we'll require the operator to be passed in.
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);
		// If the given operator is not found in the list of valid operators we will
		// assume that the developer is just short-cutting the '=' operators and
		// we will set the operators to '=' and set the values appropriately.
		if ( $this->invalid_operator( $operator ) ) {
			list( $value, $operator ) = [ $operator, '=' ];
		}
		$this->havings[] = compact( 'type', 'column', 'operator', 'value', 'boolean' );
		if ( ! $value instanceof Expression ) {
			$this->add_binding( $value, 'having' );
		}

		return $this;
	}

	/**
	 * Add a "or having" clause to the query.
	 *
	 * @param  string $column
	 * @param  string|null $operator
	 * @param  string|null $value
	 *
	 * @return Builder|static
	 */
	public function or_having( $column, $operator = null, $value = null ) {
		list( $value, $operator ) = $this->prepare_value_and_operator(
			$value, $operator, func_num_args() === 2
		);

		return $this->having( $column, $operator, $value, 'or' );
	}

	/**
	 * Add a "having between " clause to the query.
	 *
	 * @param  string $column
	 * @param  array $values
	 * @param  string $boolean
	 * @param  bool $not
	 *
	 * @return Builder|static
	 */
	public function having_between( $column, array $values, $boolean = 'and', $not = false ) {
		$type            = 'between';
		$this->havings[] = compact( 'type', 'column', 'values', 'boolean', 'not' );
		$this->add_binding( $this->clean_bindings( $values ), 'having' );

		return $this;
	}

	/**
	 * Add a raw having clause to the query.
	 *
	 * @param  string $sql
	 * @param  array $bindings
	 * @param  string $boolean
	 *
	 * @return $this
	 */
	public function having_raw( $sql, array $bindings = [], $boolean = 'and' ) {
		$type            = 'raw';
		$this->havings[] = compact( 'type', 'sql', 'boolean' );
		$this->add_binding( $bindings, 'having' );

		return $this;
	}

	/**
	 * Add a raw or having clause to the query.
	 *
	 * @param  string $sql
	 * @param  array $bindings
	 *
	 * @return Builder|static
	 */
	public function or_having_raw( $sql, array $bindings = [] ) {
		return $this->having_raw( $sql, $bindings, 'or' );
	}

	/**
	 * Add an "order by" clause to the query.
	 *
	 * @param  string $column
	 * @param  string $direction
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function order_by( $column, $direction = 'asc' ) {
		$direction = strtolower( $direction );
		if ( ! in_array( $direction, [ 'asc', 'desc' ], true ) ) {
			throw new InvalidArgumentException( 'Order direction must be "asc" or "desc".' );
		}
		$this->{$this->unions ? 'union_orders' : 'orders'}[] = [
			'column'    => $this->get_managed_column( $column ),
			'direction' => $direction,
		];

		return $this;
	}

	/**
	 * Add a descending "order by" clause to the query.
	 *
	 * @param  string $column
	 *
	 * @return $this
	 */
	public function order_by_desc( $column ) {
		return $this->order_by( $column, 'desc' );
	}

	/**
	 * Add an "order by" clause for a timestamp to the query.
	 *
	 * @param  string $column
	 *
	 * @return Builder|static
	 */
	public function latest( $column = 'created_at' ) {
		return $this->order_by( $column, 'desc' );
	}

	/**
	 * Add an "order by" clause for a timestamp to the query.
	 *
	 * @param  string $column
	 *
	 * @return Builder|static
	 */
	public function oldest( $column = 'created_at' ) {
		return $this->order_by( $column, 'asc' );
	}

	/**
	 * Put the query's results in random order.
	 *
	 * @param  string $seed
	 *
	 * @return $this
	 */
	public function in_random_order( $seed = '' ) {
		return $this->order_by_raw( $this->grammar->compile_random( $seed ) );
	}

	/**
	 * Add a raw "order by" clause to the query.
	 *
	 * @param  string $sql
	 * @param  array $bindings
	 *
	 * @return $this
	 */
	public function order_by_raw( $sql, $bindings = [] ) {
		$type                                                = 'raw';
		$this->{$this->unions ? 'union_orders' : 'orders'}[] = compact( 'type', 'sql' );
		$this->add_binding( $bindings, 'order' );

		return $this;
	}

	/**
	 * Alias to set the "offset" value of the query.
	 *
	 * @param  int $value
	 *
	 * @return Builder|static
	 */
	public function skip( $value ) {
		return $this->offset( $value );
	}

	/**
	 * Set the "offset" value of the query.
	 *
	 * @param  int $value
	 *
	 * @return $this
	 */
	public function offset( $value ) {
		$property        = $this->unions ? 'union_offset' : 'offset';
		$this->$property = max( 0, $value );

		return $this;
	}

	/**
	 * Alias to set the "limit" value of the query.
	 *
	 * @param  int $value
	 *
	 * @return Builder|static
	 */
	public function take( $value ) {
		return $this->limit( $value );
	}

	/**
	 * Set the "limit" value of the query.
	 *
	 * @param  int $value
	 *
	 * @return $this
	 */
	public function limit( $value ) {
		$property = $this->unions ? 'union_limit' : 'limit';
		if ( $value >= 0 ) {
			$this->$property = $value;
		}

		return $this;
	}

	/**
	 * Get an array with all orders with a given column removed.
	 *
	 * @param  string $column
	 *
	 * @return array
	 */
	protected function remove_existing_orders_for( $column ) {
		return array_values( array_filter( $this->orders, function ( $order ) use ( $column ) {
			return isset( $order['column'] ) ? $order['column'] !== $column : true;
		} ) );
	}

	/**
	 * Add a union statement to the query.
	 *
	 * @param  Builder|\Closure $query
	 * @param  bool $all
	 *
	 * @return Builder|static
	 */
	public function union( $query, $all = false ) {
		if ( $query instanceof Closure ) {
			call_user_func( $query, $query = $this->new_query() );
		}
		$this->unions[] = compact( 'query', 'all' );
		$this->add_binding( $query->get_bindings(), 'union' );

		return $this;
	}

	/**
	 * Add a union all statement to the query.
	 *
	 * @param  Builder|\Closure $query
	 *
	 * @return Builder|static
	 */
	public function union_all( $query ) {
		return $this->union( $query, true );
	}

	/**
	 * Lock the selected rows in the table.
	 *
	 * @param  string|bool $value
	 *
	 * @return $this
	 */
	public function lock( $value = true ) {
		$this->lock = $value;

		return $this;
	}

	/**
	 * Lock the selected rows in the table for updating.
	 *
	 * @return Builder
	 */
	public function lock_for_update() {
		return $this->lock( true );
	}

	/**
	 * Share lock the selected rows in the table.
	 *
	 * @return Builder
	 */
	public function shared_lock() {
		return $this->lock( false );
	}

	/**
	 * Get the SQL representation of the query.
	 *
	 * @return string
	 */
	public function to_sql() {
		return $this->grammar->compile_select( $this );
	}

	/**
	 * Get an array with the values of a given column.
	 *
	 * @param  string $column
	 * @param  string|null $key
	 *
	 * @return array
	 */
	public function pluck( $column, $key = null ) {
		// First, we will need to select the results of the query accounting for the
		// given columns / key. Once we have the results, we will be able to take
		// the results and get the exact data that was requested for the query.
		$query_result = $this->once_with_columns(
			is_null( $key ) ? [ $column ] : [ $column, $key ],
			function () {
				return $this->processor->process_select(
					$this, $this->run_select()
				);
			}
		);
		if ( empty( $query_result ) ) {
			return [];
		}
		// If the columns are qualified with a table or have an alias, we cannot use
		// those directly in the "pluck" operations since the results from the DB
		// are only keyed by the column itself. We'll strip the table out here.
		$column = $this->strip_table_for_pluck( $column );
		$key    = $this->strip_table_for_pluck( $key );

		return is_array( $query_result[0] )
			? $this->pluck_from_array_column( $query_result, $column, $key )
			: $this->pluck_from_object_column( $query_result, $column, $key );
	}

	/**
	 * Strip off the table name or alias from a column identifier.
	 *
	 * @param  string $column
	 *
	 * @return string|null
	 */
	protected function strip_table_for_pluck( $column ) {
		return is_null( $column ) ? $column : end( preg_split( '~\.| ~', $column ) );
	}

	/**
	 * Retrieve column values from rows represented as objects.
	 *
	 * @param  array $query_result
	 * @param  string $column
	 * @param  string $key
	 *
	 * @return array
	 */
	protected function pluck_from_object_column( $query_result, $column, $key ) {
		$results = [];
		if ( is_null( $key ) ) {
			foreach ( $query_result as $row ) {
				$results[] = $row->$column;
			}
		} else {
			foreach ( $query_result as $row ) {
				$results[ $row->$key ] = $row->$column;
			}
		}

		return $results;
	}

	/**
	 * Retrieve column values from rows represented as arrays.
	 *
	 * @param  array $query_result
	 * @param  string $column
	 * @param  string $key
	 *
	 * @return array
	 */
	protected function pluck_from_array_column( $query_result, $column, $key ) {
		$results = [];
		if ( is_null( $key ) ) {
			foreach ( $query_result as $row ) {
				$results[] = $row[ $column ];
			}
		} else {
			foreach ( $query_result as $row ) {
				$results[ $row[ $key ] ] = $row[ $column ];
			}
		}

		return $results;
	}

	/**
	 * Concatenate values of a given column as a string.
	 *
	 * @param  string $column
	 * @param  string $glue
	 *
	 * @return string
	 */
	public function implode( $column, $glue = '' ) {
		return implode( $glue, $this->pluck( $column ) );
	}

	/**
	 * Determine if any rows exist for the current query.
	 *
	 * @return bool
	 */
	public function exists() {
		$results = $this->connection->select(
			$this->grammar->compile_exists( $this ), $this->get_bindings(), $this->ttl
		);
		// If the results has rows, we will get the row and see if the exists column is a
		// boolean true. If there is no results for this query we will return false as
		// there are no rows for this query at all and we can return that info here.
		if ( isset( $results[0] ) ) {
			$results = (array) $results[0];

			return (bool) $results['exists'];
		}

		return false;
	}

	/**
	 * Determine if no rows exist for the current query.
	 *
	 * @return bool
	 */
	public function doesnt_exist() {
		return ! $this->exists();
	}

	/**
	 * Retrieve the "count" result of the query.
	 *
	 * @param  string $columns
	 *
	 * @return int
	 */
	public function count( $columns = '*' ) {
		return (int) $this->aggregate( __FUNCTION__, $this->app->array->wrap( $columns ) );
	}

	/**
	 * Retrieve the minimum value of a given column.
	 *
	 * @param  string $column
	 *
	 * @return mixed
	 */
	public function min( $column ) {
		return $this->aggregate( __FUNCTION__, [ $column ] );
	}

	/**
	 * Retrieve the maximum value of a given column.
	 *
	 * @param  string $column
	 *
	 * @return mixed
	 */
	public function max( $column ) {
		return $this->aggregate( __FUNCTION__, [ $column ] );
	}

	/**
	 * Retrieve the sum of the values of a given column.
	 *
	 * @param  string $column
	 *
	 * @return mixed
	 */
	public function sum( $column ) {
		$result = $this->aggregate( __FUNCTION__, [ $column ] );

		return $result ?: 0;
	}

	/**
	 * Retrieve the average of the values of a given column.
	 *
	 * @param  string $column
	 *
	 * @return mixed
	 */
	public function avg( $column ) {
		return $this->aggregate( __FUNCTION__, [ $column ] );
	}

	/**
	 * Alias for the "avg" method.
	 *
	 * @param  string $column
	 *
	 * @return mixed
	 */
	public function average( $column ) {
		return $this->avg( $column );
	}

	/**
	 * Execute an aggregate function on the database.
	 *
	 * @param  string $function
	 * @param  array $columns
	 *
	 * @return mixed
	 */
	public function aggregate( $function, $columns = [ '*' ] ) {
		$results = $this->clone_without( $this->unions ? [] : [ 'columns' ] )
		                ->clone_without_bindings( $this->unions ? [] : [ 'select' ] )
		                ->set_aggregate( $function, $columns )
		                ->get( $columns );
		if ( ! empty( $results ) ) {
			return array_change_key_case( $results[0] )['aggregate'];
		}

		return null;
	}

	/**
	 * Execute a numeric aggregate function on the database.
	 *
	 * @param  string $function
	 * @param  array $columns
	 *
	 * @return float|int
	 */
	public function numeric_aggregate( $function, $columns = [ '*' ] ) {
		$result = $this->aggregate( $function, $columns );
		// If there is no result, we can obviously just return 0 here. Next, we will check
		// if the result is an integer or float. If it is already one of these two data
		// types we can just return the result as-is, otherwise we will convert this.
		if ( ! $result ) {
			return 0;
		}
		if ( is_int( $result ) || is_float( $result ) ) {
			return $result;
		}
		// If the result doesn't contain a decimal place, we will assume it is an int then
		// cast it to one. When it does we will cast it to a float since it needs to be
		// cast to the expected data type for the developers out of pure convenience.
		return strpos( (string) $result, '.' ) === false
			? (int) $result : (float) $result;
	}

	/**
	 * Set the aggregate property without running the query.
	 *
	 * @param  string $function
	 * @param  array $columns
	 *
	 * @return $this
	 */
	protected function set_aggregate( $function, $columns ) {
		$this->aggregate = compact( 'function', 'columns' );
		if ( empty( $this->groups ) ) {
			$this->orders            = null;
			$this->bindings['order'] = [];
		}

		return $this;
	}

	/**
	 * Execute the given callback while selecting the given columns.
	 *
	 * After running the callback, the columns are reset to the original value.
	 *
	 * @param  array $columns
	 * @param  callable $callback
	 *
	 * @return mixed
	 */
	protected function once_with_columns( $columns, $callback ) {
		$original = $this->columns;
		if ( is_null( $original ) ) {
			$this->set_select_columns( $columns );
		}
		$result        = $callback();
		$this->columns = $original;

		return $result;
	}

	/**
	 * Get a new instance of the query builder.
	 *
	 * @return Builder
	 */
	public function new_query() {
		return new static( $this->app, $this->connection, $this->grammar, $this->processor );
	}

	/**
	 * Create a new query instance for a sub-query.
	 *
	 * @return Builder
	 */
	protected function for_sub_query() {
		return $this->new_query();
	}

	/**
	 * @param int $ttl
	 *
	 * @return $this
	 */
	public function cache( $ttl ) {
		$this->ttl = $ttl;

		return $this;
	}

	/**
	 * @param bool $flag
	 *
	 * @return $this
	 */
	public function set_object_mode( $flag = true ) {
		$this->is_object = $flag;

		return $this;
	}

	/**
	 * Create a raw database expression.
	 *
	 * @param  mixed $value
	 *
	 * @return Expression
	 */
	public function raw( $value ) {
		return $this->connection->raw( $value );
	}

	/**
	 * Get the current query value bindings in a flattened array.
	 *
	 * @return array
	 */
	public function get_bindings() {
		return $this->app->array->flatten( $this->bindings );
	}

	/**
	 * Get the raw array of bindings.
	 *
	 * @return array
	 */
	public function get_raw_bindings() {
		return $this->bindings;
	}

	/**
	 * Set the bindings on the query builder.
	 *
	 * @param  array $bindings
	 * @param  string $type
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function set_bindings( array $bindings, $type = 'where' ) {
		if ( ! array_key_exists( $type, $this->bindings ) ) {
			throw new InvalidArgumentException( "Invalid binding type: {$type}." );
		}
		$this->bindings[ $type ] = $bindings;

		return $this;
	}

	/**
	 * Add a binding to the query.
	 *
	 * @param  mixed $value
	 * @param  string $type
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function add_binding( $value, $type = 'where' ) {
		if ( ! array_key_exists( $type, $this->bindings ) ) {
			throw new InvalidArgumentException( "Invalid binding type: {$type}." );
		}
		if ( is_array( $value ) ) {
			$this->bindings[ $type ] = array_values( array_merge( $this->bindings[ $type ], $value ) );
		} else {
			$this->bindings[ $type ][] = $value;
		}

		return $this;
	}

	/**
	 * Merge an array of bindings into our bindings.
	 *
	 * @param  Builder $query
	 *
	 * @return $this
	 */
	public function merge_bindings( Builder $query ) {
		$this->bindings = array_merge_recursive( $this->bindings, $query->bindings );

		return $this;
	}

	/**
	 * Remove all of the expressions from a list of bindings.
	 *
	 * @param  array $bindings
	 *
	 * @return array
	 */
	protected function clean_bindings( array $bindings ) {
		return array_values( array_filter( $bindings, function ( $binding ) {
			return ! $binding instanceof Expression;
		} ) );
	}

	/**
	 * Get the database connection instance.
	 *
	 * @return Connection
	 */
	public function get_connection() {
		return $this->connection;
	}

	/**
	 * Get the database query processor instance.
	 *
	 * @return Processor
	 */
	public function get_processor() {
		return $this->processor;
	}

	/**
	 * Get the query grammar instance.
	 *
	 * @return Grammar
	 */
	public function get_grammar() {
		return $this->grammar;
	}

	/**
	 * Clone the query without the given properties.
	 *
	 * @param  array $properties
	 *
	 * @return static
	 */
	public function clone_without( array $properties ) {
		$clone = clone $this;
		foreach ( $properties as $property ) {
			$clone->{$property} = null;
		}

		return $clone;
	}

	/**
	 * Clone the query without the given bindings.
	 *
	 * @param  array $except
	 *
	 * @return static
	 */
	public function clone_without_bindings( array $except ) {
		$clone = clone $this;
		foreach ( $except as $type ) {
			$clone->bindings[ $type ] = [];
		}

		return $clone;
	}

	/**
	 * Execute the query as a "select" statement.
	 *
	 * @param  array|string $columns
	 *
	 * @return array
	 */
	public function get( $columns = [ '*' ] ) {
		list( $managed, $table ) = $this->get_managed_table();
		if ( $managed && $this->app->db->is_logical_table( $table ) ) {
			$this->where_null( 'deleted_at' );
		}

		return $this->once_with_columns( $this->app->array->wrap( $columns ), function () {
			return $this->processor->process_select( $this, $this->run_select() );
		} );
	}

	/**
	 * @param array $columns
	 *
	 * @return array|null
	 */
	public function row( $columns = [ '*' ] ) {
		$results = $this->take( 1 )->get( $columns );
		if ( empty( $results ) ) {
			return null;
		}

		return reset( $results );
	}

	/**
	 * @param int $id
	 * @param array $columns
	 *
	 * @return array|null
	 */
	public function find( $id, $columns = [ '*' ] ) {
		return $this->where( 'id', $id )->row( $columns );
	}

	/**
	 * @param int $number
	 * @param callable $callback
	 * @param string $id
	 * @param array $columns
	 *
	 * @return bool
	 */
	public function chunk( $number, $callback, $id = 'id', $columns = [ '*' ] ) {
		$this->order_by( $id )->limit( $number )->shared_lock();
		$result = true;
		$this->app->db->transaction( function () use ( $number, $callback, $columns, &$result ) {
			$offset = 0;
			while ( $results = $this->offset( $offset )->get( $columns ) ) {
				if ( false === $callback( $results ) ) {
					$result = false;
					break;
				}
				$offset += $number;
			}
		} );

		return $result;
	}

	/**
	 * @param int $number
	 * @param callable $callback
	 * @param string $id
	 * @param array $columns
	 *
	 * @return bool
	 */
	public function each( $number, $callback, $id = 'id', $columns = [ '*' ] ) {
		return $this->chunk( $number, function ( $results ) use ( $callback ) {
			foreach ( $results as $key => $value ) {
				if ( false === $callback( $value, $key ) ) {
					return false;
				}
			}

			return true;
		}, $id, $columns );
	}

	/**
	 * @param int $number
	 * @param callable $callback
	 * @param string $id
	 * @param array $columns
	 */
	public function chunk_for_delete( $number, $callback, $id = 'id', $columns = [ '*' ] ) {
		$this->order_by( $id )->limit( $number );
		while ( $results = $this->get( $columns ) ) {
			$callback( $results );
		}
	}

	/**
	 * Run the query as a "select" statement against the connection.
	 *
	 * @return array
	 */
	protected function run_select() {
		return $this->connection->select( $this->to_sql(), $this->get_bindings(), $this->ttl );
	}

	/**
	 * Insert a new record into the database.
	 *
	 * @param  array $values
	 *
	 * @return int|false
	 */
	public function insert( array $values ) {
		// Since every insert gets treated like a batch insert, we will make sure the
		// bindings are structured in a way that is convenient when building these
		// inserts statements by verifying these elements are actually an array.
		if ( empty( $values ) ) {
			return true;
		}

		$bulk = false;
		/** @noinspection PhpUnusedLocalVariableInspection */
		list( $managed, $table, $as ) = $this->get_managed_table();
		if ( ! is_array( reset( $values ) ) ) {
			$values = [ $managed ? $this->app->db->set_update_params( $values, true, true, false, $as ) : $values ];
		}
		// Here, we will sort the insert keys for every record so that each insert is
		// in the same order for the record. We need to make sure this is the case
		// so there are not any errors or problems when inserting these records.
		else {
			$bulk = true;
			$time = $this->app->db->set_update_params( [], true, true, false, $as );
			foreach ( $values as $key => $value ) {
				$managed and $value = $value + $time;
				ksort( $value );
				$values[ $key ] = $value;
			}
		}
		// Finally, we will run this query against the database connection and return
		// the results. We will need to also flatten these bindings before running
		// the query so they are all in one huge, flattened array for execution.
		$flatten = [];
		foreach ( $values as $item ) {
			$flatten = array_merge( $flatten, array_values( $item ) );
		}

		$method = $bulk ? 'bulk_insert' : 'insert';

		return $this->connection->$method(
			$this->grammar->compile_insert( $this, $values ),
			$this->clean_bindings( $flatten )
		);
	}

	/**
	 * Insert new records into the table using a subquery.
	 *
	 * @param  array $columns
	 * @param  \Closure|Builder|string $query
	 *
	 * @return int|false
	 */
	public function insert_using( array $columns, $query ) {
		list( $sql, $bindings ) = $this->create_sub( $query );

		return $this->connection->insert(
			$this->grammar->compile_insert_using( $this, $columns, $sql ),
			$this->clean_bindings( $bindings )
		);
	}

	/**
	 * Update a record in the database.
	 *
	 * @param  array $values
	 *
	 * @return int
	 */
	public function update( array $values ) {
		list( $managed, $table, $as ) = $this->get_managed_table();
		$managed and $values = $this->app->db->set_update_params( $values, false, true, false, $as );
		if ( $managed && $this->app->db->is_logical_table( $table ) ) {
			$this->where_null( 'deleted_at' );
		}
		$sql = $this->grammar->compile_update( $this, $values );

		return $this->connection->update( $sql, $this->clean_bindings(
			$this->grammar->prepare_bindings_for_update( $this->bindings, $values )
		) );
	}

	/**
	 * Insert or update a record matching the attributes, and fill it with values.
	 *
	 * @param  array $attributes
	 * @param  array $values
	 *
	 * @return int|false
	 */
	public function update_or_insert( array $attributes, array $values = [] ) {
		list( $managed, $table ) = $this->get_managed_table();
		if ( ! $managed ) {
			return false;
		}
		if ( $managed && $this->app->db->is_logical_table( $table ) ) {
			$this->where_null( 'deleted_at' );
		}

		$row = $this->where( $attributes )->row();
		if ( empty( $row ) ) {
			return $this->insert( array_merge( $attributes, $values ) );
		}

		if ( ! empty( $values ) ) {
			$this->update( $values );
		}

		return $row['id'];
	}

	/**
	 * Increment a column's value by a given amount.
	 *
	 * @param  string $column
	 * @param  float|int $amount
	 * @param  array $extra
	 *
	 * @return int
	 */
	public function increment( $column, $amount = 1, array $extra = [] ) {
		if ( ! is_numeric( $amount ) ) {
			throw new InvalidArgumentException( 'Non-numeric value passed to increment method.' );
		}
		$wrapped = $this->grammar->wrap( $this->get_managed_column( $column ) );
		$columns = array_merge( [ $column => $this->raw( "$wrapped + $amount" ) ], $extra );

		return $this->update( $columns );
	}

	/**
	 * Decrement a column's value by a given amount.
	 *
	 * @param  string $column
	 * @param  float|int $amount
	 * @param  array $extra
	 *
	 * @return int
	 */
	public function decrement( $column, $amount = 1, array $extra = [] ) {
		if ( ! is_numeric( $amount ) ) {
			throw new InvalidArgumentException( 'Non-numeric value passed to decrement method.' );
		}
		$wrapped = $this->grammar->wrap( $this->get_managed_column( $column ) );
		$columns = array_merge( [ $column => $this->raw( "$wrapped - $amount" ) ], $extra );

		return $this->update( $columns );
	}

	/**
	 * Delete a record from the database.
	 *
	 * @param  null|int $id
	 *
	 * @return int|false
	 */
	public function delete( $id = null ) {
		list( $managed, $table, $as ) = $this->get_managed_table();

		// If an ID is passed to the method, we will set the where clause to check the
		// ID to let developers to simply and quickly remove a single row from this
		// database without manually specifying the "where" clauses on the query.
		if ( ! is_null( $id ) ) {
			$this->where( 'id', $id );
		}

		if ( $managed && $this->app->db->is_logical_table( $table ) ) {
			return $this->update( $this->app->db->set_update_params( [], false, false, true, $as ) );
		}

		return $this->connection->delete(
			$this->grammar->compile_delete( $this ), $this->clean_bindings(
			$this->grammar->prepare_bindings_for_delete( $this->bindings )
		) );
	}

	/**
	 * Run a truncate statement on the table.
	 *
	 * @return false|int
	 */
	public function truncate() {
		list( $managed, $table ) = $this->get_managed_table();
		if ( $managed && $this->app->db->is_logical_table( $table ) ) {
			return $this->delete();
		}

		return $this->connection->statement( $this->grammar->compile_truncate( $this ), [] );
	}

	/**
	 * @param string $column (*, Field, Type, Null, Key, Default, Extra)
	 * @param string $key
	 *
	 * @return array
	 */
	public function describe( $column = '*', $key = 'Field' ) {
		$results = $this->connection->select( $this->grammar->compile_describe( $this ), [] );

		return $this->app->array->combine( $results, $key, '*' === $column ? null : $column );
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string $method
	 * @param  array $parameters
	 *
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $method, $parameters ) {
		if ( $this->app->string->starts_with( $method, 'where' ) ) {
			return $this->dynamic_where( $method, $parameters );
		}
		throw new BadMethodCallException( sprintf(
			'Call to undefined method %s::%s()', static::class, $method
		) );
	}
}
