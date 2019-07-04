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

// @codeCoverageIgnoreStart
if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}
// @codeCoverageIgnoreEnd

/**
 * Class Editor
 * @package Richtext_Toolbar_Button\Classes\Models
 */
class Editor implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use Singleton, Hook, Presenter, Package;

	/**
	 * enqueue assets
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function enqueue_block_editor_assets() {
		if ( ! $this->apply_filters( 'is_valid' ) ) {
			return;
		}

		$handle  = 'add-richtext-toolbar-button-editor';
		$depends = [
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-compose',
			'wp-core-data',
			'wp-data',
			'wp-editor',
			'wp-element',
			'wp-format-library',
			'wp-hooks',
			'wp-i18n',
			'wp-rich-text',
			'wp-server-side-render',
			'wp-url',
		];
		foreach ( $depends as $key => $depend ) {
			if ( ! $this->app->editor->is_support_editor_package( $depend ) ) {
				unset( $depends[ $key ] );
			}
		}
		$depends[] = 'lodash';
		$this->enqueue_script( $handle, 'index.min.js', $depends, $this->app->get_plugin_version() );
		$this->localize_script( $handle, 'artbParams', $this->get_editor_params() );

		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$assets->enqueue_plugin_assets( true );
	}

	/**
	 * @return array
	 */
	private function get_editor_params() {
		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );

		return [
			'settings'                => $setting->get_settings( 'editor' ),
			'defaultIcon'             => $this->apply_filters( 'default_icon' ),
			'isValidContrastChecker'  => $this->apply_filters( 'is_valid_contrast_checker' ),
			'isValidRemoveFormatting' => $this->apply_filters( 'is_valid_remove_formatting' ),
			'translate'               => $this->get_translate_data( [
				'Please select text',
				'Remove All formatting',
				'Inline Text Settings',
			] ),
			'defaultButtons'          => [
				'font-color'       => [
					'name'      => 'font-color',
					'title'     => $this->translate( 'font color' ),
					'icon'      => $this->apply_filters( 'font_color_icon' ),
					'className' => $setting->get_default_class_name( 'font-color' ),
					'style'     => 'color',
					'isValid'   => $this->apply_filters( 'is_valid_font_color' ),
				],
				'background-color' => [
					'name'      => 'background-color',
					'title'     => $this->translate( 'background color' ),
					'icon'      => $this->apply_filters( 'background_color_icon' ),
					'className' => $setting->get_default_class_name( 'background-color' ),
					'style'     => 'background-color',
					'isValid'   => $this->apply_filters( 'is_valid_background_color' ),
				],
				'font-size'        => [
					'name'      => 'font-size',
					'title'     => $this->translate( 'font size' ),
					'icon'      => $this->apply_filters( 'font_size_icon' ),
					'className' => $setting->get_default_class_name( 'font-size' ),
					'isValid'   => $this->apply_filters( 'is_valid_font_size' ),
				],
			],
		];
	}
}
