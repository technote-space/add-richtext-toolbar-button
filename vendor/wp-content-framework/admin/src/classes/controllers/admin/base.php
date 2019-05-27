<?php
/**
 * WP_Framework_Admin Classes Controller Admin Base
 *
 * @version 0.0.32
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Admin\Classes\Controllers\Admin;

use WP_Framework_Admin\Traits\Admin;
use WP_Framework_Admin\Traits\Package;
use WP_Framework_Core\Traits\Nonce;
use WP_Screen;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Base
 * @package WP_Framework_Admin\Classes\Controllers\Admin
 */
abstract class Base extends \WP_Framework_Controller\Classes\Controllers\Base implements \WP_Framework_Core\Interfaces\Nonce, \WP_Framework_Admin\Interfaces\Admin {

	use Admin, Nonce, Package;

	/**
	 * @var string $_relative_namespace
	 */
	private $_relative_namespace;

	/**
	 * @return string
	 */
	abstract public function get_page_title();

	/**
	 * @return string
	 */
	public function get_menu_name() {
		return $this->get_page_title();
	}

	/**
	 * @param string $relative_namespace
	 */
	public function set_relative_namespace( $relative_namespace ) {
		$this->_relative_namespace = $relative_namespace;
	}

	/**
	 * @return string
	 */
	public function get_page_slug() {
		return str_replace( '\\', '-', strtolower( $this->_relative_namespace ) ) . $this->get_file_slug();
	}

	/**
	 * get
	 */
	protected function get_action() {

	}

	/**
	 * post
	 */
	protected function post_action() {

	}

	/**
	 * common
	 */
	protected function common_action() {

	}

	/**
	 * action
	 */
	public final function action() {
		$is_valid_update = $this->is_post() && $this->nonce_check();
		if ( $is_valid_update ) {
			$this->post_action();
		} else {
			$this->get_action();
		}
		$this->common_action();
		$this->do_action( 'controller_action', $is_valid_update );
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {
		return [];
	}

	/**
	 * presenter
	 */
	public function presenter() {
		$args = $this->get_view_args();
		$slug = $this->get_page_slug();
		$this->add_style_view( 'admin/style/' . $slug, $args );
		$this->add_script_view( 'admin/script/' . $slug, $args );
		$this->get_view( 'admin/' . $this->get_page_slug(), $args, true );
	}

	/**
	 * @return string
	 */
	public function get_nonce_slug() {
		return 'admin_' . $this->get_file_slug();
	}

	/**
	 * @param int $index
	 *
	 * @return string
	 */
	private function get_help_tab_id( $index ) {
		$slug = $this->get_page_slug();

		return $this->apply_filters( 'get_help_tab_id', $slug . '_help_' . $index, $slug, $index );
	}

	/**
	 * @param string $slug
	 *
	 * @return string
	 */
	private function get_help_content( $slug ) {
		if ( empty( $slug ) ) {
			return '';
		}

		return $this->get_view( 'admin/help/' . $slug, $this->get_help_content_params(), false, false );
	}

	/**
	 * @return array
	 */
	protected function get_help_content_params() {
		return [];
	}

	/**
	 * @param string $slug
	 *
	 * @return string
	 */
	private function get_sidebar_content( $slug ) {
		if ( empty( $slug ) ) {
			return '';
		}

		return $this->get_view( 'admin/sidebar/' . $slug, $this->get_sidebar_content_params(), false, false );
	}

	/**
	 * @return array
	 */
	protected function get_sidebar_content_params() {
		return [];
	}

	/**
	 * setup help
	 */
	public function setup_help() {
		$slug     = $this->get_page_slug();
		$contents = $this->apply_filters( 'get_help_contents', $this->get_help_contents(), $slug );
		if ( ! empty( $contents ) && is_array( $contents ) ) {
			/** @var WP_Screen|null $current_screen */
			$current_screen = get_current_screen();
			if ( isset( $current_screen ) ) {
				$index = 0;
				if ( isset( $contents['content'] ) || isset( $contents['view'] ) ) {
					$contents = [ $contents ];
				}
				foreach ( $contents as $content ) {
					if ( ! is_array( $content ) ) {
						continue;
					}

					$id      = $this->apply_filters( 'help_tag_id', $this->app->array->get( $content, 'id', function () use ( $index ) {
						return $this->get_help_tab_id( $index );
					} ), $content, $slug, $index );
					$title   = $this->apply_filters( 'help_tag_title', $this->app->array->get( $content, 'title', 'Help Tab' ), $content, $slug, $index );
					$content = $this->apply_filters( 'help_tag_content', $this->app->array->get( $content, 'content', function () use ( $content ) {
						return $this->get_help_content( $this->app->array->get( $content, 'view' ) );
					} ), $content, $slug, $index );

					if ( ! empty( $content ) ) {
						$current_screen->add_help_tab( [
							'id'      => $id,
							'title'   => $this->translate( $title ),
							'content' => $content,
						] );
					}
					$index++;
				}

				$sidebar = $this->apply_filters( 'get_help_sidebar', $this->get_help_sidebar(), $slug );
				if ( is_string( $sidebar ) && ! empty( $sidebar ) ) {
					$content = $this->get_sidebar_content( $sidebar );
					if ( ! empty( $content ) ) {
						$current_screen->set_help_sidebar( $content );
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	protected function get_help_contents() {
		return [];
	}

	/**
	 * @return false|string
	 */
	protected function get_help_sidebar() {
		return false;
	}

}
