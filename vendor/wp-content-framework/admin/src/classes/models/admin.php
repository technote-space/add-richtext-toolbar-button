<?php
/**
 * WP_Framework_Admin Classes Models Admin
 *
 * @version 0.0.10
 * @author technote-space
 * @copyright technote-space All Rights Reserved
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
 * @property-read \WP_Framework_Admin\Classes\Controllers\Admin\Base $page
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
	 * @var array $readonly_properties
	 */
	protected $readonly_properties = [
		'page',
	];

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
	 * @return \WP_Framework_Admin\Classes\Controllers\Admin\Base|null
	 */
	private function load_page() {
		try {
			$prefix  = $this->get_page_prefix();
			$pattern = "#\A{$prefix}(.+)#";
			$_page   = $this->app->input->get( 'page' );
			if ( ! empty( $_page ) && is_string( $_page ) && preg_match( $pattern, $_page, $matches ) ) {
				$page          = $matches[1];
				$exploded      = explode( '-', $page );
				$page          = array_pop( $exploded );
				$add_namespace = implode( '\\', array_map( 'ucfirst', $exploded ) );
				! empty( $add_namespace ) and $add_namespace .= '\\';
				$instance = $this->get_class_instance( $this->get_class_setting( $page, $add_namespace ), '\WP_Framework_Admin\Classes\Controllers\Admin\Base' );
				if ( false !== $instance ) {
					/** @var \WP_Framework_Admin\Classes\Controllers\Admin\Base $instance */
					$this->do_action( 'pre_load_admin_page', $instance );

					return $instance;
				}
				$this->app->log( sprintf( $this->translate( '%s not found.' ), $_page ), [
					'$_GET[\'page\']' => $_page,
					'$page'           => $page,
					'$add_namespace'  => $add_namespace,
				] );
			}
		} catch ( \Exception $e ) {
			$this->app->log( $e );
		}

		return null;
	}

	/**
	 * add menu
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function add_menu() {
		$capability = $this->app->get_config( 'capability', 'admin_menu', 'manage_options' );
		if ( ! $this->app->user_can( $capability ) ) {
			return;
		}

		$this->set_readonly_property( 'page', $this->load_page() );
		if ( isset( $this->page ) && $this->app->user_can( $this->apply_filters( 'admin_menu_capability', $this->page->get_capability(), $this->page ) ) ) {
			$this->page->action();
			$this->do_action( 'post_load_admin_page', $this->page );
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
		$hook  = add_menu_page(
			$title,
			$title,
			$capability,
			$slug,
			function () {
			},
			$this->get_img_url( $this->app->get_config( 'config', 'menu_image' ), '' ),
			$this->get_admin_menu_position( $slug, $title )
		);

		if ( isset( $this->page ) && $this->app->user_can( $this->page->get_capability() ) ) {
			add_action( "load-$hook", function () {
				$this->page->setup_help();
			} );
		}

		if ( ! $this->app->is_theme ) {
			add_filter( 'plugin_action_links_' . $this->app->define->plugin_base_name, function ( array $actions ) {
				return $this->plugin_action_links( $actions );
			} );
		}

		/** @var \WP_Framework_Admin\Classes\Controllers\Admin\Base $page */
		foreach ( $this->_pages as $page ) {
			$hook = add_submenu_page(
				$this->get_menu_slug(),
				$this->translate( $page->get_page_title() ),
				$this->translate( $page->get_menu_name() ),
				$capability,
				$this->get_page_prefix() . $page->get_page_slug(),
				function () {
					$this->load();
				}
			);
			if ( $this->page ) {
				add_action( "load-$hook", function () {
					$this->page->setup_help();
				} );
			}
		}
	}

	/**
	 * @param string $menu_slug
	 * @param string $menu_title
	 *
	 * @return float|string
	 */
	private function get_admin_menu_position( $menu_slug, $menu_title ) {
		$position = $this->apply_filters( 'admin_menu_position' );

		global $wp_version, $menu;
		if ( isset( $menu["$position"] ) && version_compare( $wp_version, '4.4', '<' ) ) {
			$position = $position + substr( base_convert( md5( $menu_slug . $menu_title ), 16, 10 ), - 5 ) * 0.00001;
			$position = "$position";
		}

		return $position;
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

		$pages = $this->app->utility->array_map( $this->_pages, function ( $p ) {
			/** @var \WP_Framework_Admin\Classes\Controllers\Admin\Base $p */
			return $this->get_page_prefix() . $p->get_page_slug();
		} );
		$pages = array_combine( $pages, $this->_pages );

		/** @var \WP_Framework_Custom_Post\Classes\Models\Custom_Post $custom_post */
		$custom_post = \WP_Framework_Custom_Post\Classes\Models\Custom_Post::get_instance( $this->app );
		$types       = $custom_post->get_custom_posts();
		$types       = array_combine( $this->app->utility->array_map( $types, function ( $p ) {
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
	 * @param array $actions
	 *
	 * @return array
	 */
	private function plugin_action_links( array $actions ) {
		$link = $this->get_view( 'admin/include/action_links', [
			'url' => menu_page_url( $this->get_menu_slug(), false ),
		] );
		array_unshift( $actions, $link );

		return $actions;
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
	 * load
	 */
	private function load() {
		if ( isset( $this->page ) ) {
			if ( $this->app->user_can( $this->page->get_capability() ) ) {
				$this->get_view( 'admin/include/layout', [
					'page' => $this->page,
					'slug' => $this->page->get_page_slug(),
				], true );
			} else {
				$this->get_view( 'admin/include/error', [ 'message' => 'Forbidden.' ], true );
			}
		} else {
			$this->get_view( 'admin/include/error', [ 'message' => 'Page not found.' ], true );
		}
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
	 * @param string $message
	 * @param string $group
	 * @param bool $escape
	 * @param bool $error
	 */
	public function add_message( $message, $group = '', $error = false, $escape = true ) {
		$this->_messages[ $group ][ $error ? 'error' : 'updated' ][] = [ $message, $escape ];
	}
}
