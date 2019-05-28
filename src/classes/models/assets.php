<?php
/**
 * @version 1.1.6
 * @author Technote
 * @since 1.0.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_Framework_Presenter\Traits\Presenter;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}

/**
 * Class Assets
 * @package Richtext_Toolbar_Button\Classes\Models
 */
class Assets implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * @var bool|null $_cleared_cache_file
	 */
	private $_cleared_cache_file = null;

	/**
	 * remove setting
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function remove_setting() {
		$this->app->setting->remove_setting( 'assets_version' );

		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );
		if ( $setting->is_support_gutenberg() ) {
			$this->app->setting->remove_setting( 'support_block_editor_styles' );
			$this->app->setting->remove_setting( 'block_width' );
		} elseif ( ! $this->apply_filters( 'support_block_editor_styles' ) ) {
			$this->app->setting->remove_setting( 'block_width' );
		}
	}

	/**
	 * setup assets
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_assets() {
		if ( ! $this->apply_filters( 'is_valid' ) ) {
			return;
		}
		$this->enqueue_plugin_assets();
	}

	/**
	 * @param string $key
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function changed_option( $key ) {
		if ( $this->app->string->starts_with( $key, $this->get_filter_prefix() ) ) {
			$this->clear_cache_file();
		}
	}

	/**
	 * @param bool $is_editor
	 */
	public function enqueue_plugin_assets( $is_editor = false ) {
		$this->enqueue_upload_style( $this->get_css_handle(), $this->get_cache_file_name( $is_editor ), function () use ( $is_editor ) {
			/** @var Custom_Post\Setting $setting */
			$setting = Custom_Post\Setting::get_instance( $this->app );
			$params  = [
				'settings'  => $setting->get_settings( 'front' ),
				'pre_style' => $this->get_pre_style_for_front(),
			];
			$style   = $this->get_view( 'front/style', $params );

			if ( $is_editor ) {
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
	 * @param bool $is_editor
	 *
	 * @return string
	 */
	private function get_cache_file_name( $is_editor ) {
		return $is_editor ? $this->get_editor_cache_file_name() : $this->get_front_cache_file_name();
	}

	/**
	 * @return string
	 */
	private function get_front_cache_file_name() {
		return $this->apply_filters( 'cache_front_file_name', 'artb.css' );
	}

	/**
	 * @return string
	 */
	private function get_editor_cache_file_name() {
		return $this->apply_filters( 'cache_editor_file_name', 'artb.editor.css' );
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
		return $this->apply_filters( 'pre_style_for_editor', [] );
	}

	/**
	 * @return bool
	 */
	public function clear_cache_file() {
		if ( isset( $this->_cleared_cache_file ) ) {
			return $this->_cleared_cache_file;
		}
		$this->_cleared_cache_file = false;

		$deleted = false;
		foreach ( [ true, false ] as $is_editor ) {
			$deleted |= $this->app->file->delete_upload_file( $this->app, 'css' . DS . $this->get_cache_file_name( $is_editor ) );
		}
		$this->_cleared_cache_file = $deleted;
		$this->app->option->set( $this->get_filter_prefix() . 'assets_version', $this->app->utility->uuid() );

		return $deleted;
	}
}