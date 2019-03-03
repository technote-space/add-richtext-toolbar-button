<?php
/**
 * @version 1.0.14
 * @author Technote
 * @since 1.0.0
 * @since 1.0.9 #69
 * @since 1.0.13 #83
 * @since 1.0.14 #82
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Models;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}

/**
 * Class Assets
 * @package Richtext_Toolbar_Button\Classes\Models
 */
class Assets implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Common\Traits\Package;

	/**
	 * @var bool|null $_cleared_cache_file
	 */
	private $_cleared_cache_file = null;

	/**
	 * setup assets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_assets() {
		if ( ! $this->apply_filters( 'is_valid' ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		global $post;
		if ( ! $this->use_block_editor_for_post_type( $post->post_type ) ) {
			return;
		}

		$this->enqueue_plugin_assets( $post->post_type );
	}

	/**
	 * @param string $post_type
	 *
	 * @return bool
	 */
	private function use_block_editor_for_post_type( $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		if ( ! post_type_supports( $post_type, 'editor' ) ) {
			return false;
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( $post_type_object && ! $post_type_object->show_in_rest ) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $key
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function changed_option( $key ) {
		if ( $this->app->utility->starts_with( $key, $this->get_filter_prefix() ) ) {
			$this->clear_cache_file();
		}
	}

	/**
	 * @param string|null $post_type
	 * @param bool $editor
	 */
	public function enqueue_plugin_assets( $post_type, $editor = false ) {
		$this->enqueue_upload_style( $this->get_css_handle(), $this->get_cache_file_name( $post_type, $editor ), function () use ( $post_type, $editor ) {
			/** @var Custom_Post\Setting $setting */
			$setting = Custom_Post\Setting::get_instance( $this->app );
			$params  = [
				'settings'  => $setting->get_settings( 'front', $post_type ),
				'pre_style' => $this->get_pre_style_for_front(),
			];
			$style   = $this->get_view( 'front/style', $params );

			if ( $editor ) {
				$params['wrap']      = $this->apply_filters( 'editor_wrap_selector', '.components-tooltip .components-popover__content' );
				$params['pre_style'] = $this->get_pre_style_for_editor();
				$params['is_editor'] = true;
				$style               .= $this->get_view( 'front/style', $params );
			}

			return $style;
		} );
		$this->setup_fontawesome();
	}

	/**
	 * @return string
	 */
	public function get_css_handle() {
		return $this->get_slug( 'css-handle', '-css' );
	}

	/**
	 * @param string|null $post_type
	 * @param bool $editor
	 *
	 * @return string
	 */
	private function get_cache_file_name( $post_type, $editor ) {
		if ( empty( $post_type ) ) {
			$post_type = 'all';
			$suffix    = '.';
		} else {
			$suffix = '-';
		}

		return $editor ? $this->get_editor_cache_file_name( $post_type, $suffix ) : $this->get_front_cache_file_name( $post_type, $suffix );
	}

	/**
	 * @param string $post_type
	 * @param string $suffix
	 *
	 * @return string
	 */
	private function get_front_cache_file_name( $post_type, $suffix ) {
		return $this->apply_filters( 'cache_front_file_name', $this->app->utility->replace( $this->get_front_cache_file_base_name(), [
			'post_type' => $post_type,
			'suffix'    => $suffix,
		] ), $post_type );
	}

	/**
	 * @return string
	 */
	private function get_front_cache_file_base_name() {
		return $this->apply_filters( 'cache_front_file_base_name', 'artb${suffix}${post_type}.css' );
	}

	/**
	 * @param string $post_type
	 * @param string $suffix
	 *
	 * @return string
	 */
	private function get_editor_cache_file_name( $post_type, $suffix ) {
		return $this->apply_filters( 'cache_editor_file_name', $this->app->utility->replace( $this->get_editor_cache_file_base_name(), [
			'post_type' => $post_type,
			'suffix'    => $suffix,
		] ), $post_type );
	}

	/**
	 * @return string
	 */
	private function get_editor_cache_file_base_name() {
		return $this->apply_filters( 'cache_editor_file_base_name', 'artb.editor${suffix}${post_type}.css' );
	}

	/**
	 * @return array
	 */
	private function get_pre_style_for_front() {
		return $this->apply_filters( 'pre_style_for_front', [
			'line-height: 1;',
			'font-size: 1em;',
		] );
	}

	/**
	 * @return array
	 */
	private function get_pre_style_for_editor() {
		return $this->apply_filters( 'pre_style_for_editor', [
			'display: inline-block;',
			'background-color: white;',
		] );
	}

	/**
	 * @return bool
	 */
	public function clear_cache_file() {
		if ( isset( $this->_cleared_cache_file ) ) {
			return true;
		}
		$this->_cleared_cache_file = false;

		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );
		$deleted = false;
		foreach ( [ true, false ] as $item ) {
			$deleted |= $this->app->utility->delete_upload_file( $this->app, 'css' . DS . $this->get_cache_file_name( null, $item ) );
			foreach ( $setting->get_valid_post_types() as $post_type ) {
				$deleted |= $this->app->utility->delete_upload_file( $this->app, 'css' . DS . $this->get_cache_file_name( $post_type, $item ) );
			}
		}
		$this->_cleared_cache_file = $deleted;
		$this->app->option->set( $this->get_filter_prefix() . 'assets_version', $this->app->utility->uuid() );

		return $deleted;
	}
}