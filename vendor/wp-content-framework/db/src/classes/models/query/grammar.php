<?php
/**
 * WP_Framework_Db Classes Models Query Grammar
 *
 * @version 0.0.18
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 *
 * reference illuminate/database Copyright (c) Taylor Otwell
 * The MIT License (MIT) (https://github.com/illuminate/database/blob/master/LICENSE.md)
 */

namespace WP_Framework_Db\Classes\Models\Query;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Grammar
 * @package WP_Framework_Db\Classes\Models\Query
 */
class Grammar extends \WP_Framework_Db\Classes\Models\Grammar {

	/**
	 * The grammar specific operators.
	 *
	 * @var array
	 */
	protected $operators = [ 'sounds like' ];

	/**
	 * The components that make up a select clause.
	 *
	 * @var array
	 */
	protected $select_components = [
		'aggregate',
		'columns',
		'from',
		'joins',
		'wheres',
		'groups',
		'havings',
		'orders',
		'limit',
		'offset',
		'lock',
	];

	/**
	 * Compile a select query into SQL.
	 *
	 * @param Builder $query
	 *
	 * @return string
	 */
	public function compile_select( Builder $query ) {
		if ( $query->unions && $query->aggregate ) {
			return $this->compile_union_aggregate( $query );
		}
		// If the query does not have any columns set, we'll set the columns to the
		// * character to just get all of the columns from the database. Then we
		// can build the query and concatenate all the pieces together as one.
		$original = $query->columns;
		if ( is_null( $query->columns ) ) {
			$query->columns = [ '*' ];
		}
		// To compile the query, we'll spin through each component of the query and
		// see if that component exists. If it does we'll just call the compiler
		// function for the component which is responsible for making the SQL.
		$sql            = trim( $this->concatenate(
			$this->compile_components( $query ) )
		);
		$query->columns = $original;

		if ( $query->unions ) {
			$sql = '(' . $sql . ') ' . $this->compile_unions( $query );
		}

		return $sql;
	}

	/**
	 * Compile the components necessary for a select clause.
	 *
	 * @param Builder $query
	 *
	 * @return array
	 */
	protected function compile_components( Builder $query ) {
		$sql = [];
		foreach ( $this->select_components as $component ) {
			// To compile the query, we'll spin through each component of the query and
			// see if that component exists. If it does we'll just call the compiler
			// function for the component which is responsible for making the SQL.
			if ( isset( $query->$component ) && ! is_null( $query->$component ) ) {
				$method            = 'compile_' . $component;
				$sql[ $component ] = $this->$method( $query, $query->$component );
			}
		}

		return $sql;
	}

	/**
	 * Compile an aggregated select clause.
	 *
	 * @param Builder $query
	 * @param array $aggregate
	 *
	 * @return string
	 */
	protected function compile_aggregate( Builder $query, $aggregate ) {
		$column = $this->columnize( $aggregate['columns'] );
		// If the query has a "distinct" constraint and we're not asking for all columns
		// we need to prepend "distinct" onto the column name so that the query takes
		// it into account when it performs the aggregating operations on the data.
		if ( $query->distinct && $column !== '*' ) {
			$column = 'distinct ' . $column;
		}

		return 'select ' . $aggregate['function'] . '(' . $column . ') as aggregate';
	}

	/**
	 * Compile the "select *" portion of the query.
	 *
	 * @param Builder $query
	 * @param array $columns
	 *
	 * @return string|null
	 */
	protected function compile_columns( Builder $query, $columns ) {
		// If the query is actually performing an aggregating select, we will let that
		// compiler handle the building of the select clauses, as it will need some
		// more syntax that is best handled by that function to keep things neat.
		if ( ! is_null( $query->aggregate ) ) {
			return null;
		}
		$select = $query->distinct ? 'select distinct ' : 'select ';

		return $select . $this->columnize( $columns );
	}

	/**
	 * Compile the "from" portion of the query.
	 *
	 * @param Builder $query
	 * @param string $table
	 *
	 * @return string
	 */
	protected function compile_from(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $table
	) {
		return 'from ' . $this->wrap_table( $table );
	}

	/**
	 * Compile the "join" portions of the query.
	 *
	 * @param Builder $query
	 * @param array $joins
	 *
	 * @return string
	 */
	protected function compile_joins( Builder $query, $joins ) {
		return implode( ' ', $this->app->array->map( $joins, function ( $join ) use ( $query ) {
			$table                  = $this->wrap_table( $join->table );
			$nested_joins           = is_null( $join->joins ) ? '' : ' ' . $this->compile_joins( $query, $join->joins );
			$table_and_nested_joins = is_null( $join->joins ) ? $table : '(' . $table . $nested_joins . ')';

			return trim( "{$join->type} join {$table_and_nested_joins} {$this->compile_wheres($join)}" );
		} ) );
	}

	/**
	 * Compile the "where" portions of the query.
	 *
	 * @param Builder $query
	 *
	 * @return string
	 */
	protected function compile_wheres( Builder $query ) {
		// Each type of where clauses has its own compiler function which is responsible
		// for actually creating the where clauses SQL. This helps keep the code nice
		// and maintainable since each clause has a very small method that it uses.
		if ( is_null( $query->wheres ) ) {
			return '';
		}
		// If we actually have some where clauses, we will strip off the first boolean
		// operator, which is added by the query builders for convenience so we can
		// avoid checking for the first clauses in each of the compilers methods.
		if ( count( $sql = $this->compile_wheres_to_array( $query ) ) > 0 ) {
			return $this->concatenate_where_clauses( $query, $sql );
		}

		return '';
	}

	/**
	 * Get an array of all the where clauses for the query.
	 *
	 * @param Builder $query
	 *
	 * @return array
	 */
	protected function compile_wheres_to_array( $query ) {
		return $this->app->array->map( $query->wheres, function ( $where ) use ( $query ) {
			return $where['boolean'] . ' ' . $this->{"where_{$where['type']}"}( $query, $where );
		} );
	}

	/**
	 * Format the where clause statements into one string.
	 *
	 * @param Builder $query
	 * @param array $sql
	 *
	 * @return string
	 */
	protected function concatenate_where_clauses( $query, $sql ) {
		$conjunction = $query instanceof Join ? 'on' : 'where';

		return $conjunction . ' ' . $this->remove_leading_boolean( implode( ' ', $sql ) );
	}

	/**
	 * Compile a raw where clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_raw(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		return $where['sql'];
	}

	/**
	 * Compile a basic where clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_basic(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		$value = $this->parameter( $where['value'] );

		return $this->wrap( $where['column'] ) . ' ' . $where['operator'] . ' ' . $value;
	}

	/**
	 * Compile a "where in" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_in(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		if ( ! empty( $where['values'] ) ) {
			return $this->wrap( $where['column'] ) . ' in (' . $this->parameterize( $where['values'] ) . ')';
		}

		return '0 = 1';
	}

	/**
	 * Compile a "where not in" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_not_in(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		if ( ! empty( $where['values'] ) ) {
			return $this->wrap( $where['column'] ) . ' not in (' . $this->parameterize( $where['values'] ) . ')';
		}

		return '1 = 1';
	}

	/**
	 * Compile a "where not in raw" clause.
	 *
	 * For safety, where_integer_in_raw ensures this method is only used with integer values.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_not_in_raw(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		if ( ! empty( $where['values'] ) ) {
			return $this->wrap( $where['column'] ) . ' not in (' . implode( ', ', $where['values'] ) . ')';
		}

		return '1 = 1';
	}

	/**
	 * Compile a "where in raw" clause.
	 *
	 * For safety, where_integer_in_raw ensures this method is only used with integer values.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_in_raw(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		if ( ! empty( $where['values'] ) ) {
			return $this->wrap( $where['column'] ) . ' in (' . implode( ', ', $where['values'] ) . ')';
		}

		return '0 = 1';
	}

	/**
	 * Compile a "where null" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_null(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		return $this->wrap( $where['column'] ) . ' is null';
	}

	/**
	 * Compile a "where not null" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_not_null(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		return $this->wrap( $where['column'] ) . ' is not null';
	}

	/**
	 * Compile a "between" where clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_between(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		$between = $where['not'] ? 'not between' : 'between';
		$min     = $this->parameter( reset( $where['values'] ) );
		$max     = $this->parameter( end( $where['values'] ) );

		return $this->wrap( $where['column'] ) . ' ' . $between . ' ' . $min . ' and ' . $max;
	}

	/**
	 * Compile a "where date" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_date( Builder $query, $where ) {
		return $this->date_based_where( 'date', $query, $where );
	}

	/**
	 * Compile a "where time" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_time( Builder $query, $where ) {
		return $this->date_based_where( 'time', $query, $where );
	}

	/**
	 * Compile a "where day" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_day( Builder $query, $where ) {
		return $this->date_based_where( 'day', $query, $where );
	}

	/**
	 * Compile a "where month" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_month( Builder $query, $where ) {
		return $this->date_based_where( 'month', $query, $where );
	}

	/**
	 * Compile a "where year" clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_year( Builder $query, $where ) {
		return $this->date_based_where( 'year', $query, $where );
	}

	/**
	 * Compile a date based where clause.
	 *
	 * @param string $type
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function date_based_where(
		/** @noinspection PhpUnusedParameterInspection */
		$type, Builder $query, $where
	) {
		$value = $this->parameter( $where['value'] );

		return $type . '(' . $this->wrap( $where['column'] ) . ') ' . $where['operator'] . ' ' . $value;
	}

	/**
	 * Compile a where clause comparing two columns..
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_column(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		return $this->wrap( $where['first'] ) . ' ' . $where['operator'] . ' ' . $this->wrap( $where['second'] );
	}

	/**
	 * Compile a nested where clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_nested( Builder $query, $where ) {
		// Here we will calculate what portion of the string we need to remove. If this
		// is a join clause query, we need to remove the "on" portion of the SQL and
		// if it is a normal query we need to take the leading "where" of queries.
		$offset = $query instanceof Join ? 3 : 6;

		return '(' . substr( $this->compile_wheres( $where['query'] ), $offset ) . ')';
	}

	/**
	 * Compile a where condition with a sub-select.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_sub(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		$select = $this->compile_select( $where['query'] );

		return $this->wrap( $where['column'] ) . ' ' . $where['operator'] . " ($select)";
	}

	/**
	 * Compile a where exists clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_exists(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		return 'exists (' . $this->compile_select( $where['query'] ) . ')';
	}

	/**
	 * Compile a where exists clause.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_not_exists(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		return 'not exists (' . $this->compile_select( $where['query'] ) . ')';
	}

	/**
	 * Compile a where row values condition.
	 *
	 * @param Builder $query
	 * @param array $where
	 *
	 * @return string
	 */
	protected function where_row_values(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $where
	) {
		$columns = $this->columnize( $where['columns'] );
		$values  = $this->parameterize( $where['values'] );

		return '(' . $columns . ') ' . $where['operator'] . ' (' . $values . ')';
	}

	/**
	 * Compile the "group by" portions of the query.
	 *
	 * @param Builder $query
	 * @param array $groups
	 *
	 * @return string
	 */
	protected function compile_groups(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $groups
	) {
		return 'group by ' . $this->columnize( $groups );
	}

	/**
	 * Compile the "having" portions of the query.
	 *
	 * @param Builder $query
	 * @param array $havings
	 *
	 * @return string
	 */
	protected function compile_havings(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $havings
	) {
		$sql = implode( ' ', array_map( [ $this, 'compile_having' ], $havings ) );

		return 'having ' . $this->remove_leading_boolean( $sql );
	}

	/**
	 * Compile a single having clause.
	 *
	 * @param array $having
	 *
	 * @return string
	 */
	protected function compile_having( array $having ) {
		// If the having clause is "raw", we can just return the clause straight away
		// without doing any more processing on it. Otherwise, we will compile the
		// clause into SQL based on the components that make it up from builder.
		if ( $having['type'] === 'raw' ) {
			return $having['boolean'] . ' ' . $having['sql'];
		} elseif ( $having['type'] === 'between' ) {
			return $this->compile_having_between( $having );
		}

		return $this->compile_basic_having( $having );
	}

	/**
	 * Compile a basic having clause.
	 *
	 * @param array $having
	 *
	 * @return string
	 */
	protected function compile_basic_having( $having ) {
		$column    = $this->wrap( $having['column'] );
		$parameter = $this->parameter( $having['value'] );

		return $having['boolean'] . ' ' . $column . ' ' . $having['operator'] . ' ' . $parameter;
	}

	/**
	 * Compile a "between" having clause.
	 *
	 * @param array $having
	 *
	 * @return string
	 */
	protected function compile_having_between( $having ) {
		$between = $having['not'] ? 'not between' : 'between';
		$column  = $this->wrap( $having['column'] );
		$min     = $this->parameter( reset( $having['values'] ) );
		$max     = $this->parameter( end( $having['values'] ) );

		return $having['boolean'] . ' ' . $column . ' ' . $between . ' ' . $min . ' and ' . $max;
	}

	/**
	 * Compile the "order by" portions of the query.
	 *
	 * @param Builder $query
	 * @param array $orders
	 *
	 * @return string
	 */
	protected function compile_orders( Builder $query, $orders ) {
		if ( ! empty( $orders ) ) {
			return 'order by ' . implode( ', ', $this->compile_orders_to_array( $query, $orders ) );
		}

		return '';
	}

	/**
	 * Compile the query orders to an array.
	 *
	 * @param Builder $query
	 * @param array $orders
	 *
	 * @return array
	 */
	protected function compile_orders_to_array(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $orders
	) {
		return array_map( function ( $order ) {
			return ! isset( $order['sql'] )
				? $this->wrap( $order['column'] ) . ' ' . $order['direction']
				: $order['sql'];
		}, $orders );
	}

	/**
	 * Compile the random statement into SQL.
	 *
	 * @param string $seed
	 *
	 * @return string
	 */
	public function compile_random( $seed ) {
		return 'RAND(' . $seed . ')';
	}

	/**
	 * Compile the "limit" portions of the query.
	 *
	 * @param Builder $query
	 * @param int $limit
	 *
	 * @return string
	 */
	protected function compile_limit(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $limit
	) {
		return 'limit ' . (int) $limit;
	}

	/**
	 * Compile the "offset" portions of the query.
	 *
	 * @param Builder $query
	 * @param int $offset
	 *
	 * @return string
	 */
	protected function compile_offset(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $offset
	) {
		if ( empty( $query->offset ) ) {
			return '';
		}

		return 'offset ' . (int) $offset;
	}

	/**
	 * Compile the "union" queries attached to the main query.
	 *
	 * @param Builder $query
	 *
	 * @return string
	 */
	protected function compile_unions( Builder $query ) {
		$sql = '';
		foreach ( $query->unions as $union ) {
			$sql .= $this->compile_union( $union );
		}
		if ( ! empty( $query->union_orders ) ) {
			$sql .= ' ' . $this->compile_orders( $query, $query->union_orders );
		}
		if ( isset( $query->union_limit ) ) {
			$sql .= ' ' . $this->compile_limit( $query, $query->union_limit );
		}
		if ( isset( $query->union_offset ) ) {
			$sql .= ' ' . $this->compile_offset( $query, $query->union_offset );
		}

		return ltrim( $sql );
	}

	/**
	 * Compile a single union statement.
	 *
	 * @param array $union
	 *
	 * @return string
	 */
	protected function compile_union( array $union ) {
		$conjunction = $union['all'] ? ' union all ' : ' union ';

		return $conjunction . '(' . $union['query']->to_sql() . ')';
	}

	/**
	 * Compile a union aggregate query into SQL.
	 *
	 * @param Builder $query
	 *
	 * @return string
	 */
	protected function compile_union_aggregate( Builder $query ) {
		$sql              = $this->compile_aggregate( $query, $query->aggregate );
		$query->aggregate = null;

		return $sql . ' from (' . $this->compile_select( $query ) . ') as ' . $this->wrap_table( 'temp_table' );
	}

	/**
	 * Compile an exists statement into SQL.
	 *
	 * @param Builder $query
	 *
	 * @return string
	 */
	public function compile_exists( Builder $query ) {
		$select = $this->compile_select( $query );

		return "select exists({$select}) as {$this->wrap('exists')}";
	}

	/**
	 * Compile an insert statement into SQL.
	 *
	 * @param Builder $query
	 * @param array $values
	 *
	 * @return string
	 */
	public function compile_insert( Builder $query, array $values ) {
		// Essentially we will force every insert to be treated as a batch insert which
		// simply makes creating the SQL easier for us since we can utilize the same
		// basic routine regardless of an amount of records given to us to insert.
		$table = $this->wrap_table( $query->from );
		if ( ! is_array( reset( $values ) ) ) {
			$values = [ $values ];
		}
		$columns = $this->columnize( array_keys( reset( $values ) ) );
		// We need to build a list of parameter place-holders of values that are bound
		// to the query. Each insert should have the exact same amount of parameter
		// bindings so we will loop through the record and parameterize them all.
		$parameters = implode( ', ', $this->app->array->map( $values, function ( $record ) {
			return '(' . $this->parameterize( $record ) . ')';
		} ) );

		return "insert into $table ($columns) values $parameters";
	}

	/**
	 * Compile an insert statement using a subquery into SQL.
	 *
	 * @param Builder $query
	 * @param array $columns
	 * @param string $sql
	 *
	 * @return string
	 */
	public function compile_insert_using( Builder $query, array $columns, $sql ) {
		return "insert into {$this->wrap_table($query->from)} ({$this->columnize($columns)}) $sql";
	}

	/**
	 * Compile an update statement into SQL.
	 *
	 * @param Builder $query
	 * @param array $values
	 *
	 * @return string
	 */
	public function compile_update( Builder $query, $values ) {
		$table = $this->wrap_table( $query->from );
		// Each one of the columns in the update statements needs to be wrapped in the
		// keyword identifiers, also a place-holder needs to be created for each of
		// the values in the list of bindings so we can make the sets statements.
		$columns = $this->compile_update_columns( $values );
		// If the query has any "join" clauses, we will setup the joins on the builder
		// and compile them so we can attach them to this update, as update queries
		// can get join statements to attach to other tables when they're needed.
		$joins = '';
		if ( isset( $query->joins ) ) {
			$joins = ' ' . $this->compile_joins( $query, $query->joins );
		}
		// Of course, update queries may also be constrained by where clauses so we'll
		// need to compile the where clauses and attach it to the query so only the
		// intended records are updated by the SQL statements we generate to run.
		$where = $this->compile_wheres( $query );
		$sql   = rtrim( "update {$table}{$joins} set $columns $where" );
		// If the query has an order by clause we will compile it since MySQL supports
		// order bys on update statements. We'll compile them using the typical way
		// of compiling order bys. Then they will be appended to the SQL queries.
		if ( ! empty( $query->orders ) ) {
			$sql .= ' ' . $this->compile_orders( $query, $query->orders );
		}
		// Updates on MySQL also supports "limits", which allow you to easily update a
		// single record very easily. This is not supported by all database engines
		// so we have customized this update compiler here in order to add it in.
		if ( isset( $query->limit ) ) {
			$sql .= ' ' . $this->compile_limit( $query, $query->limit );
		}

		return rtrim( $sql );
	}

	/**
	 * Compile all of the columns for an update statement.
	 *
	 * @param array $values
	 *
	 * @return string
	 */
	protected function compile_update_columns( $values ) {
		return implode( ', ', $this->app->array->map( $values, function ( $value, $key ) {
			return $this->wrap( $key ) . ' = ' . $this->parameter( $value );
		} ) );
	}

	/**
	 * Prepare the bindings for an update statement.
	 *
	 * @param array $bindings
	 * @param array $values
	 *
	 * @return array
	 */
	public function prepare_bindings_for_update( array $bindings, array $values ) {
		$clean_bindings = $bindings;
		unset( $clean_bindings['join'] );
		unset( $clean_bindings['select'] );

		return array_values(
			array_merge( $bindings['join'], $values, $this->app->array->flatten( $clean_bindings ) )
		);
	}

	/**
	 * Compile a delete statement into SQL.
	 *
	 * @param Builder $query
	 *
	 * @return string
	 */
	public function compile_delete( Builder $query ) {
		$table = $this->wrap_table( $query->from );
		$where = is_array( $query->wheres ) ? $this->compile_wheres( $query ) : '';

		return isset( $query->joins )
			? $this->compile_delete_with_joins( $query, $table, $where )
			: $this->compile_delete_without_joins( $query, $table, $where );
	}

	/**
	 * Prepare the bindings for a delete statement.
	 *
	 * @param array $bindings
	 *
	 * @return array
	 */
	public function prepare_bindings_for_delete( array $bindings ) {
		$clean_bindings = $bindings;
		unset( $clean_bindings['join'] );
		unset( $clean_bindings['select'] );

		return array_values(
			array_merge( $bindings['join'], $this->app->array->flatten( $clean_bindings ) )
		);
	}

	/**
	 * Compile a delete query that does not use joins.
	 *
	 * @param Builder $query
	 * @param string $table
	 * @param string $where
	 *
	 * @return string
	 */
	protected function compile_delete_without_joins( $query, $table, $where ) {
		$sql = trim( /** @lang text */ "delete from {$table} {$where}" );
		// When using MySQL, delete statements may contain order by statements and limits
		// so we will compile both of those here. Once we have finished compiling this
		// we will return the completed SQL statement so it will be executed for us.
		if ( ! empty( $query->orders ) ) {
			$sql .= ' ' . $this->compile_orders( $query, $query->orders );
		}
		if ( isset( $query->limit ) ) {
			$sql .= ' ' . $this->compile_limit( $query, $query->limit );
		}

		return $sql;
	}

	/**
	 * Compile a delete query that uses joins.
	 *
	 * @param Builder $query
	 * @param string $table
	 * @param string $where
	 *
	 * @return string
	 */
	protected function compile_delete_with_joins( $query, $table, $where ) {
		$joins = ' ' . $this->compile_joins( $query, $query->joins );
		$alias = stripos( $table, ' as ' ) !== false
			? explode( ' as ', $table )[1] : $table;

		return trim( /** @lang text */ "delete {$alias} from {$table}{$joins} {$where}" );
	}

	/**
	 * Compile a truncate table statement into SQL.
	 *
	 * @param Builder $query
	 *
	 * @return string
	 */
	public function compile_truncate( Builder $query ) {
		return 'truncate ' . $this->wrap_table( $query->from );
	}

	/**
	 * Compile a describe table statement into SQL.
	 *
	 * @param Builder $query
	 *
	 * @return string
	 */
	public function compile_describe( Builder $query ) {
		return 'describe ' . $this->wrap_table( $query->from );
	}

	/**
	 * Compile the lock into SQL.
	 *
	 * @param Builder $query
	 * @param bool|string $value
	 *
	 * @return string
	 */
	protected function compile_lock(
		/** @noinspection PhpUnusedParameterInspection */
		Builder $query, $value
	) {
		if ( ! is_string( $value ) ) {
			return $value ? 'for update' : 'lock in share mode';
		}

		return $value;
	}

	/**
	 * Determine if the grammar supports savepoints.
	 *
	 * @return bool
	 */
	public function supports_savepoints() {
		return true;
	}

	/**
	 * Compile the SQL statement to define a savepoint.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function compile_savepoint( $name ) {
		return 'SAVEPOINT ' . $name;
	}

	/**
	 * Compile the SQL statement to execute a savepoint rollback.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function compile_savepoint_roll_back( $name ) {
		return 'ROLLBACK TO SAVEPOINT ' . $name;
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param Expression|string $value
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
	 * Wrap a single string in keyword identifiers.
	 *
	 * @param string $value
	 * @param bool $is_table
	 *
	 * @return string
	 */
	protected function wrap_value( $value, $is_table = false ) {
		if ( $is_table ) {
			$value = trim( $value, '`' );
			$value = $this->app->db->get_table( $value );
		}

		return $value === '*' ? $value : '`' . str_replace( '`', '``', $value ) . '`';
	}

	/**
	 * Concatenate an array of segments, removing empties.
	 *
	 * @param array $segments
	 *
	 * @return string
	 */
	protected function concatenate( $segments ) {
		return implode( ' ', array_filter( $segments, function ( $value ) {
			return (string) $value !== '';
		} ) );
	}

	/**
	 * Remove the leading boolean from a statement.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function remove_leading_boolean( $value ) {
		return preg_replace( '/and |or /i', '', $value, 1 );
	}

	/**
	 * Get the grammar specific operators.
	 *
	 * @return array
	 */
	public function get_operators() {
		return $this->operators;
	}
}
