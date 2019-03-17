<?php
/**
 * WP_Framework_Custom_Post Deprecated Traits Custom Post
 *
 * @version 0.0.28
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Custom_Post\Deprecated\Traits;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Custom_Post
 * @package WP_Framework_Custom_Post\Deprecated\Traits
 */
class Custom_Post implements \WP_Framework_Core\Interfaces\Singleton {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Custom_Post\Traits\Package;

	/**
	 * @param \WP_Framework_Custom_Post\Traits\Custom_Post $instance
	 * @param bool $is_valid
	 * @param int|null $per_page
	 * @param int $page
	 * @param array|null $where
	 * @param array|null $orderby
	 *
	 * @return array
	 */
	public function list_data( $instance, $is_valid = true, $per_page = null, $page = 1, $where = null, $orderby = null ) {
		$table = $instance->get_related_table_name();
		$limit = $per_page;
		$page  = max( 1, $page );
		$table = [
			[ $table, 't' ],
			[
				[ $this->get_wp_table( 'posts' ), 'p' ],
				'INNER JOIN',
				[
					't.post_id',
					'=',
					'p.ID',
				],
			],
		];
		empty( $where ) and $where = [];
		$total      = $this->app->db->select_count( $table, null, $where );
		$total_page = isset( $per_page ) ? ceil( $total / $per_page ) : 1;
		$page       = min( $total_page, $page );
		$offset     = isset( $per_page ) && isset( $page ) ? $per_page * ( $page - 1 ) : null;
		if ( $is_valid ) {
			$where['p.post_status'] = 'publish';
		}

		$list = $this->app->db->select( $table, $where, null, $limit, $offset, $orderby );
		if ( empty( $list ) ) {
			return [
				'total'      => 0,
				'total_page' => 0,
				'page'       => $page,
				'data'       => [],
			];
		}

		$post_ids = $this->app->utility->array_pluck( $list, 'post_id' );
		$posts    = get_posts( [
			'include'     => $post_ids,
			'post_type'   => $instance->get_post_type(),
			'post_status' => 'any',
		] );
		$posts    = $this->app->utility->array_combine( $posts, 'ID' );

		return [
			'total'      => $total,
			'total_page' => $total_page,
			'page'       => $page,
			'data'       => array_map( function ( $d ) use ( $posts, $instance ) {
				return $instance->filter_callback( 'filter_item', [ $instance->filter_callback( 'set_post_data', [ $d, $posts[ $d['post_id'] ] ] ) ] );
			}, $list ),
		];
	}
}
