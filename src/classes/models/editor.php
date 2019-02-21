<?php
/**
 * @version 1.0.7
 * @author technote-space
 * @since 1.0.0
 * @since 1.0.3 #32
 * @since 1.0.7 #61
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Models;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}

/**
 * Class Editor
 * @package Richtext_Toolbar_Button\Classes\Models
 */
class Editor implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Common\Traits\Package;

	/**
	 * enqueue css for gutenberg
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function enqueue_block_editor_assets() {
		if ( ! $this->apply_filters( 'is_valid' ) ) {
			return;
		}

		global $post;
		$handle = 'add-richtext-toolbar-button-editor';
		$this->enqueue_style( $handle, 'gutenberg.css' );
		$this->enqueue_script( $handle, 'add-richtext-toolbar-button-gutenberg.min.js', [
			'wp-editor',
			'wp-data',
			'wp-element',
			'wp-rich-text',
			'wp-components',
			'wp-url',
			'wp-i18n',
			'lodash',
		] );
		$this->localize_script( $handle, 'artb_params', $this->get_editor_params( $post->post_type ) );

		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$assets->enqueue_plugin_assets( $post->post_type, true );
	}

	/**
	 * @param string $post_type
	 *
	 * @return array
	 */
	private function get_editor_params( $post_type ) {
		/** @var Custom_Post\Setting $setting */
		$setting = Custom_Post\Setting::get_instance( $this->app );

		return [
			'settings'                  => $setting->get_settings( 'editor', $post_type ),
			'default_icon'              => $this->apply_filters( 'default_icon' ),
			'is_valid_contrast_checker' => $this->apply_filters( 'is_valid_contrast_checker' ),
			'inspector_title'           => $this->translate( 'Inline Text Settings' ),
			'translate'                 => [
				'Please select text' => $this->translate( 'Please select text' ),
			],
		];
	}
}