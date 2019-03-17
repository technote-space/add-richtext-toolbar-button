<?php
/**
 * WP_Framework_Custom_Post Interfaces Custom Post
 *
 * @version 0.0.29
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Custom_Post\Interfaces;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Custom_Post
 * @package WP_Framework_Custom_Post\Interfaces
 */
interface Custom_Post extends \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter, \WP_Framework_Core\Interfaces\Helper\Data_Helper, \WP_Framework_Core\Interfaces\Helper\Validate {

	/**
	 * @param array $data
	 * @param bool $convert_name
	 * @param bool $wp_error
	 *
	 * @return array|bool|int
	 */
	public function insert( array $data, $convert_name = true, $wp_error = false );

	/**
	 * @param array $data
	 * @param array $where
	 * @param bool $convert_name
	 *
	 * @return array|bool|int
	 */
	public function update( array $data, array $where, $convert_name = true );

	/**
	 * @param array $data
	 * @param bool $convert_name
	 *
	 * @return array
	 */
	public function validate_insert( array $data, $convert_name = true );

	/**
	 * @return string
	 */
	public function get_post_type_slug();

	/**
	 * @return string
	 */
	public function get_related_table_name();

	/**
	 * @return string
	 */
	public function get_post_type();

	/**
	 * @return \WP_Post_Type|\WP_Error
	 */
	public function get_post_type_object();

	/**
	 * @param string $capability
	 *
	 * @return bool
	 */
	public function user_can( $capability );

	/**
	 * @param array|null $capabilities
	 *
	 * @return array
	 */
	public function get_post_type_args( $capabilities = null );

	/**
	 * @return array
	 */
	public function get_post_type_labels();

	/**
	 * @return string
	 */
	public function get_post_type_single_name();

	/**
	 * @return string
	 */
	public function get_post_type_plural_name();

	/**
	 * @return string|array
	 */
	public function get_post_type_capability_type();

	/**
	 * @return array
	 */
	public function get_post_type_supports();

	/**
	 * @return string
	 */
	public function get_post_type_menu_icon();

	/**
	 * @return int|null
	 */
	public function get_post_type_position();

	/**
	 * @param string $search
	 * @param \WP_Query $wp_query
	 *
	 * @return string
	 */
	public function posts_search( $search, \WP_Query $wp_query );

	/**
	 * @param string $join
	 * @param \WP_Query|string $wp_query
	 *
	 * @return string
	 */
	public function posts_join( $join, $wp_query );

	/**
	 * @param \WP_Query $wp_query
	 */
	public function setup_posts_orderby( $wp_query );

	/**
	 * @return bool
	 */
	public function is_support_io();

	/**
	 * @param array $actions
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	public function post_row_actions( array $actions, \WP_Post $post );

	/**
	 * @param mixed $data
	 *
	 * @return array {
	 *  int $result
	 *  string $message
	 *  int $success
	 *  int $fail
	 * }
	 */
	public function import( $data );

	/**
	 * @param array $columns
	 * @param bool $sortable
	 *
	 * @return array
	 */
	public function manage_posts_columns( array $columns, $sortable = false );

	/**
	 * @param string $column_name
	 * @param \WP_Post $post
	 */
	public function manage_posts_custom_column( $column_name, \WP_Post $post );

	/**
	 * @param int $post_id
	 *
	 * @return array|false
	 */
	public function get_related_data( $post_id );

	/**
	 * @param int $id
	 * @param bool $is_valid
	 *
	 * @return array|false
	 */
	public function get_data( $id, $is_valid = true );

	/**
	 * @param \Closure|null $callback
	 * @param bool $is_valid
	 * @param int|null $per_page
	 * @param int $page
	 *
	 * @return array
	 */
	public function get_list_data( $callback = null, $is_valid = true, $per_page = null, $page = 1 );

	/**
	 * @param int $per_page
	 * @param int $page
	 * @param \Closure|null $callback
	 * @param bool $is_valid
	 *
	 * @return array
	 */
	public function pagination( $per_page, $page, $callback = null, $is_valid = true );

	/**
	 * @param bool $only_publish
	 *
	 * @return int
	 */
	public function count( $only_publish = false );

	/**
	 * @param bool $only_publish
	 *
	 * @return bool
	 */
	public function is_empty( $only_publish = false );

	/**
	 * @param array $params
	 * @param array $where
	 * @param \WP_Post $post
	 * @param bool $update
	 *
	 * @return int
	 */
	public function update_data( array $params, array $where, \WP_Post $post, $update );

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param array $old
	 * @param array $new
	 */
	public function data_updated( $post_id, \WP_Post $post, array $old, array $new );

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param array $data
	 */
	public function data_inserted( $post_id, \WP_Post $post, array $data );

	/**
	 * @param \WP_Post $post
	 * @param bool $update
	 *
	 * @return array
	 */
	public function get_update_data_params( \WP_Post $post, $update );

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function untrash_post( $post_id, \WP_Post $post );

	/**
	 * @param int $post_id
	 */
	public function trash_post( $post_id );

	/**
	 * @param int $post_id
	 *
	 * @return bool|false|int
	 */
	public function delete_data( $post_id );

	/**
	 * @return string
	 */
	public function get_post_field_name_prefix();

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_post_field_name( $key );

	/**
	 * @return array
	 */
	public function get_data_field_settings();

	/**
	 * @param \WP_Post $post
	 */
	public function output_edit_form( \WP_Post $post );

	/**
	 * @param \WP_Post $post
	 */
	public function output_after_editor( \WP_Post $post );

	/**
	 * @param array|null $post_array
	 *
	 * @return array
	 */
	public function validate_input( $post_array = null );

	/**
	 * @param array $data
	 * @param array $post_array
	 *
	 * @return array
	 */
	public function filter_post_data( array $data, array $post_array );

	/**
	 * setup list
	 */
	public function setup_list();

	/**
	 * setup page
	 */
	public function setup_page();

	/**
	 * @param string $key
	 * @param array $errors
	 *
	 * @return array
	 */
	public function get_error_messages( $key, array $errors );

	/**
	 * @return string
	 */
	public function get_post_type_link();

	/**
	 * @param int $post_id
	 *
	 * @return string
	 */
	public function get_edit_post_link( $post_id );

	/**
	 * @return int
	 */
	public function get_load_priority();
}
