<?php
/**
 * @author Technote
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
 * Class Editor
 * @package Richtext_Toolbar_Button\Classes\Models
 */
class Editor implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * enqueue css for gutenberg
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_block_editor_assets() {
		if ( ! $this->apply_filters( 'is_valid' ) ) {
			return;
		}

		$handle = 'add-richtext-toolbar-button-editor';
		$this->enqueue_style( $handle, 'gutenberg.css', [], $this->app->get_plugin_version() );
		$this->enqueue_script( $handle, 'add-richtext-toolbar-button-gutenberg.min.js', [
			'wp-editor',
			'wp-data',
			'wp-element',
			'wp-rich-text',
			'wp-components',
			'wp-url',
			'wp-i18n',
			'wp-format-library',
			'lodash',
		], $this->app->get_plugin_version() );
		$this->localize_script( $handle, 'artbParams', $this->get_editor_params() );

		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$assets->enqueue_plugin_assets( true );
	}

	/**
	 * @param array $editor_settings
	 *
	 * @return array
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function block_editor_settings( $editor_settings ) {
		if ( $this->app->isset_shared_object( '__is_doing_block_editor_settings' ) ) {
			return $editor_settings;
		}

		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );
		$styles  = $setting->get_block_editor_styles( true );
		if ( $styles ) {
			$editor_settings['styles'][] = [ 'css' => $styles ];
			$width                       = $this->apply_filters( 'block_width' );
			if ( $width > 0 ) {
				$editor_settings['styles'][] = [ 'css' => '.wp-block { max-width: ' . $width . 'px }' ];
			}
		}

		return $editor_settings;
	}

	/**
	 * @return array
	 */
	private function get_editor_params() {
		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );

		return [
			'settings'                   => $setting->get_settings( 'editor' ),
			'default_icon'               => $this->apply_filters( 'default_icon' ),
			'is_valid_contrast_checker'  => $this->apply_filters( 'is_valid_contrast_checker' ),
			'is_valid_remove_formatting' => $this->apply_filters( 'is_valid_remove_formatting' ),
			'inspector_title'            => $this->translate( 'Inline Text Settings' ),
			'translate'                  => [
				'Please select text'    => $this->translate( 'Please select text' ),
				'Remove All formatting' => $this->translate( 'Remove All formatting' ),
			],
		];
	}
}