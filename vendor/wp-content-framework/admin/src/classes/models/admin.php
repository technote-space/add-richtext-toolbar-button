<?php
/**
 * WP_Framework_Admin Classes Models Admin
 *
 * @version 0.0.24
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Admin\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Admin
 * @package WP_Framework_Admin\Classes\Models
 */
class Admin implements \WP_Framework_Core\Interfaces\Loader, \WP_Framework_Presenter\Interfaces\Presenter, \WP_Framework_Core\Interfaces\Nonce {

	use \WP_Framework_Core\Traits\Loader, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Core\Traits\Nonce, \WP_Framework_Admin\Traits\Package;

	/**
	 * @var array $_messages
	 */
	private $_messages = [];

	/**
	 * @var \WP_Framework_Admin\Classes\Controllers\Admin\Base[] $_pages
	 */
	private $_pages = [];

	/**
	 * @var \WP_Framework_Admin\Classes\Controllers\Admin\Base[] $_hooks
	 */
	private $_hooks = [];

	/**
	 * add menu
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function add_menu() {
		$capability = $this->app->get_config( 'capability', 'admin_menu', 'manage_options' );
		if ( ! $this->app->user_can( $capability ) ) {
			return;
		}

		$this->_pages = [];
		foreach ( $this->get_class_list() as $page ) {
			/** @var \WP_Framework_Admin\Classes\Controllers\Admin\Base $page */
			if ( $this->app->user_can( $this->apply_filters( 'admin_menu_capability', $page->get_capability(), $page ) ) ) {
				$this->_pages[] = $page;
			}
		}

		$title = $this->get_main_menu_title();
		$slug  = $this->get_menu_slug();
		add_menu_page(
			$title,
			$title,
			$capability,
			$slug,
			function () {
			},
			$this->get_img_url( $this->app->get_config( 'config', 'menu_image' ), '' ),
			$this->get_admin_menu_position( $slug, $title )
		);

		/** @var \WP_Framework_Admin\Classes\Controllers\Admin\Base $_page */
		foreach ( $this->_pages as $_page ) {
			$hook = add_submenu_page(
				$slug,
				$this->translate( $_page->get_page_title() ),
				$this->translate( $_page->get_menu_name() ),
				$capability,
				$this->get_page_prefix() . $_page->get_page_slug(),
				function () use ( $_page ) {
					$this->load( $_page );
				}
			);
			false !== $hook && $this->_hooks[ $hook ] = $_page;
		}
	}

	/**
	 * setup help
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_help() {
		global $hook_suffix;
		isset( $this->_hooks[ $hook_suffix ] ) && $this->_hooks[ $hook_suffix ]->setup_help();
	}

	/**
	 * do page action
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function do_page_action() {
		global $hook_suffix;
		if ( isset( $this->_hooks[ $hook_suffix ] ) ) {
			$page = $this->_hooks[ $hook_suffix ];
			$this->do_action( 'pre_load_admin_page', $page );
			$page->action();
			$this->do_action( 'post_load_admin_page', $page );
		}
	}

	/**
	 * sort menu
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function sort_menu() {
		if ( ! $this->is_valid_package( 'custom_post' ) ) {
			return;
		}

		global $submenu;
		$slug = $this->get_menu_slug();
		if ( empty( $submenu[ $slug ] ) ) {
			return;
		}

		$pages = $this->app->array->map( $this->_pages, function ( $p ) {
			/** @var \WP_Framework_Admin\Classes\Controllers\Admin\Base $p */
			return $this->get_page_prefix() . $p->get_page_slug();
		} );
		$pages = array_combine( $pages, $this->_pages );

		/** @var \WP_Framework_Custom_Post\Classes\Models\Custom_Post $custom_post */
		$custom_post = \WP_Framework_Custom_Post\Classes\Models\Custom_Post::get_instance( $this->app );
		$types       = $custom_post->get_custom_posts();
		$types       = array_combine( $this->app->array->map( $types, function ( $p ) {
			/** @var \WP_Framework_Custom_Post\Interfaces\Custom_Post $p */
			return "edit.php?post_type={$p->get_post_type()}";
		} ), $types );

		$sort = [];
		foreach ( $submenu[ $slug ] as $item ) {
			if ( isset( $pages[ $item[2] ] ) ) {
				/** @var \WP_Framework_Admin\Classes\Controllers\Admin\Base $p */
				$p = $pages[ $item[2] ];
				if ( method_exists( $p, 'get_load_priority' ) ) {
					$priority = $p->get_load_priority();
				} else {
					$priority = 10;
				}
				$sort[] = $priority;
			} elseif ( isset( $types[ $item[2] ] ) ) {
				/** @var \WP_Framework_Custom_Post\Interfaces\Custom_Post $p */
				$p = $types[ $item[2] ];
				if ( method_exists( $p, 'get_load_priority' ) ) {
					$priority = $p->get_load_priority();
				} else {
					$priority = 10;
				}
				$sort[] = $priority;
			} else {
				$sort[] = 10;
			}
		}
		if ( count( $sort ) !== count( $submenu[ $slug ] ) ) {
			return;
		}
		array_multisort( $sort, $submenu[ $slug ] );
	}

	/**
	 * admin notice
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function admin_notice() {
		if ( $this->app->user_can( $this->app->get_config( 'capability', 'admin_notice_capability', 'manage_options' ) ) ) {
			$this->get_view( 'admin/include/notice', [
				'messages' => $this->_messages,
			], true );
		}
	}

	/**
	 * @param string[] $actions
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $context
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function plugin_action_links( array $actions, $plugin_file, array $plugin_data, $context ) {
		if ( $this->app->is_theme || $plugin_file !== $this->app->define->plugin_base_name ) {
			return $actions;
		}

		$action_links = $this->parse_config_links( $this->app->get_config( 'config', 'action_links' ), $plugin_data, $context );
		! empty( $action_links ) and $actions = array_merge( $action_links, $actions );

		return $this->apply_filters( 'plugin_action_links', $actions );
	}

	/**
	 * @param string[] $plugin_meta
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $status
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function plugin_row_meta( array $plugin_meta, $plugin_file, array $plugin_data, $status ) {
		if ( $this->app->is_theme || $plugin_file !== $this->app->define->plugin_base_name ) {
			return $plugin_meta;
		}

		$plugin_row_meta = $this->parse_config_links( $this->app->get_config( 'config', 'plugin_row_meta' ), $plugin_data, $status );
		! empty( $plugin_row_meta ) and $plugin_meta = array_merge( $plugin_meta, $plugin_row_meta );

		return $this->apply_filters( 'plugin_row_meta', $plugin_meta );
	}

	/**
	 * @param array $links
	 * @param $plugin_data
	 * @param $status
	 *
	 * @return array
	 */
	private function parse_config_links( array $links, array $plugin_data, $status ) {
		if ( is_array( $links ) && ! empty( $links ) ) {
			return array_filter( $this->app->array->map( $links, function ( $setting ) use ( $plugin_data, $status ) {
				if ( empty( $setting['url'] ) || ! isset( $setting['label'] ) ) {
					return false;
				}

				return $this->url(
					$this->app->utility->value( $setting['url'], $this, $plugin_data, $status ),
					$this->app->utility->value( $setting['label'], $this, $plugin_data, $status ),
					true, ! empty( $setting['new_tab'] ), [], false
				);
			} ) );
		}

		return [];
	}

	/**
	 * @return string
	 */
	protected function get_setting_slug() {
		return $this->apply_filters( 'get_setting_slug', $this->app->get_config( 'config', 'setting_page_slug' ) );
	}

	/**
	 * @return string
	 */
	public function get_menu_slug() {
		return $this->get_page_prefix() . $this->apply_filters( 'get_setting_menu_slug', $this->get_setting_slug() );
	}

	/**
	 * @return string
	 */
	private function get_main_menu_title() {
		$main_menu_title = $this->app->get_config( 'config', 'main_menu_title' );
		empty( $main_menu_title ) and $main_menu_title = str_replace( '_', ' ', $this->app->original_plugin_name );

		return $this->apply_filters( 'get_main_menu_title', $this->translate( $main_menu_title ) );
	}

	/**
	 * @param string $menu_slug
	 * @param string $menu_title
	 *
	 * @return float|string
	 */
	private function get_admin_menu_position( $menu_slug, $menu_title ) {
		$position = $this->apply_filters( 'admin_menu_position' );

		global $menu;
		if ( isset( $menu["$position"] ) && $this->compare_wp_version( '4.4', '<' ) ) {
			$position = $position + substr( base_convert( md5( $menu_slug . $menu_title ), 16, 10 ), -5 ) * 0.00001;
			$position = "$position";
		}

		return $position;
	}

	/**
	 * @param \WP_Framework_Admin\Classes\Controllers\Admin\Base $page
	 */
	private function load( $page ) {
		$this->get_view( 'admin/include/layout', [
			'page' => $page,
			'slug' => $page->get_page_slug(),
		], true );
	}

	/**
	 * @return string
	 */
	public function get_nonce_slug() {
		return '_admin_main';
	}

	/**
	 * @return string
	 */
	private function get_page_prefix() {
		return $this->apply_filters( 'get_page_prefix', $this->get_slug( 'page_prefix', '' ) ) . '-';
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		$namespaces = [ $this->app->define->plugin_namespace . '\\Classes\\Controllers\\Admin\\' ];
		foreach ( $this->app->get_packages() as $package ) {
			foreach ( $package->get_admin_namespaces() as $namespace ) {
				$namespaces[] = $namespace;
			}
		}

		return $namespaces;
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\WP_Framework_Admin\Classes\Controllers\Admin\Base';
	}

	/**
	 * @param string $message
	 * @param string $group
	 * @param bool $escape
	 * @param bool $error
	 * @param null|array $override_allowed_html
	 */
	public function add_message( $message, $group = '', $error = false, $escape = true, $override_allowed_html = null ) {
		if ( ! $escape ) {
			$message = preg_replace_callback( '#\[([^()]+?)\]\s*\((https?://([\w\-]+\.)+[\w\-]+(/[\w\-\./\?%&=\#]*)?)\)#', function ( $matches ) {
				return $this->url( $matches[2], $matches[1], false, ! $this->app->utility->is_admin_url( $matches[2] ), [], false );
			}, $message );
			$message = $this->app->string->strip_tags( $message, $override_allowed_html );
		}
		$this->_messages[ $group ][ $error ? 'error' : 'updated' ][] = [ $message, $escape ];
	}
}
