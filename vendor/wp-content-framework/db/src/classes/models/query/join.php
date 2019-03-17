<?php
/**
 * WP_Framework_Db Classes Models Query Join
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

use Closure;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Join
 * @package WP_Framework_Db\Classes\Models\Query
 */
class Join extends Builder {

	/**
	 * The type of join being performed.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * The table the join clause is joining to.
	 *
	 * @var string
	 */
	public $table;

	/**
	 * The parent query builder instance.
	 *
	 * @var Builder
	 */
	private $parent_query;

	/**
	 * Create a new join clause instance.
	 *
	 * @param  \WP_Framework $app
	 * @param  Builder $parent_query
	 * @param  string $type
	 * @param  string $table
	 *
	 * @return void
	 */
	public function __construct(
		\WP_Framework $app,
		Builder $parent_query,
		$type,
		$table
	) {
		$this->type         = $type;
		$this->table        = $table;
		$this->parent_query = $parent_query;
		parent::__construct(
			$app,
			$parent_query->get_connection(),
			$parent_query->get_grammar(),
			$parent_query->get_processor()
		);
	}

	/**
	 * Add an "on" clause to the join.
	 *
	 * On clauses can be chained, e.g.
	 *
	 *  $join->on('contacts.user_id', '=', 'users.id')
	 *       ->on('contacts.info_id', '=', 'info.id')
	 *
	 * will produce the following SQL:
	 *
	 * on `contacts`.`user_id` = `users`.`id` and `contacts`.`info_id` = `info`.`id`
	 *
	 * @param  \Closure|string $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 * @param  string $boolean
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function on( $first, $operator = null, $second = null, $boolean = 'and' ) {
		if ( $first instanceof Closure ) {
			return $this->where_nested( $first, $boolean );
		}

		return $this->where_column( $first, $operator, $second, $boolean );
	}

	/**
	 * Add an "or on" clause to the join.
	 *
	 * @param  \Closure|string $first
	 * @param  string|null $operator
	 * @param  string|null $second
	 *
	 * @return Join
	 */
	public function or_on( $first, $operator = null, $second = null ) {
		return $this->on( $first, $operator, $second, 'or' );
	}

	/**
	 * Get a new instance of the join clause builder.
	 *
	 * @return Join
	 */
	public function new_query() {
		return new static( $this->app, $this->parent_query, $this->type, $this->table );
	}

	/**
	 * Create a new query instance for sub-query.
	 *
	 * @return Builder
	 */
	protected function for_sub_query() {
		return $this->parent_query->new_query();
	}
}
