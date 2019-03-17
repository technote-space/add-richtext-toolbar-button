<?php
/**
 * WP_Framework_Db Classes Models Query Expression
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

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Expression
 * @package WP_Framework_Db\Classes\Models\Query
 */
class Expression {

	/**
	 * The value of the expression.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Create a new raw query expression.
	 *
	 * @param  mixed $value
	 *
	 * @return void
	 */
	public function __construct( $value ) {
		$this->value = $value;
	}

	/**
	 * Get the value of the expression.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Get the value of the expression.
	 *
	 * @return string
	 */
	public function __toString() {
		return (string) $this->get_value();
	}
}
