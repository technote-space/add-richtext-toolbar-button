<?php
/**
 * WP_Framework_Custom_Post Configs Filter
 *
 * @version 0.0.21
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

return [

	'custom_post' => [
		'init'                         => [
			'register_post_types' => 9,
		],
		'manage_posts_columns'         => [
			'manage_posts_columns',
		],
		'manage_posts_custom_column'   => [
			'manage_posts_custom_column',
		],
		'post_row_actions'             => [
			'post_row_actions',
		],
		'wp_count_posts'               => [
			'wp_count_posts',
		],
		'posts_join'                   => [
			'posts_join',
		],
		'posts_search'                 => [
			'posts_search',
		],
		'pre_get_posts'                => [
			'setup_posts_orderby',
		],
		'save_post'                    => [
			'save_post',
			'untrash_post',
		],
		'wp_trash_post'                => [
			'wp_trash_post',
		],
		'delete_post'                  => [
			'delete_post',
		],
		'wp_insert_post_empty_content' => [
			'post_validation',
		],
		'wp_insert_post_data'          => [
			'wp_insert_post_data',
		],
		'send_email_change_email'      => [
			'send_email_change_email',
		],
		'send_password_change_email'   => [
			'send_password_change_email',
		],
		'redirect_post_location'       => [
			'redirect_post_location',
		],
		'load-edit.php'                => [
			'setup_list',
		],
		'load-post.php'                => [
			'setup_page',
			'set_admin_notices',
		],
		'load-post-new.php'            => [
			'setup_page',
		],
		'edit_form_after_title'        => [
			'edit_form_after_title',
		],
		'edit_form_after_editor'       => [
			'edit_form_after_editor',
		],
	],

];