<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Models;

use stdClass;
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
	 * @var string $theme_key_cache
	 */
	private $theme_key_cache;

	/**
	 * enqueue css for gutenberg
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
		$this->enqueue_style( $handle, 'gutenberg.css', [], $this->app->get_plugin_version() );
		$this->enqueue_script( $handle, 'add-richtext-toolbar-button-gutenberg.min.js', $depends, $this->app->get_plugin_version() );
		$this->localize_script( $handle, 'artbParams', $this->get_editor_params() );

		/** @var Assets $assets */
		$assets = Assets::get_instance( $this->app );
		$assets->enqueue_plugin_assets( true );
	}

	/**
	 * @noinspection PhpUnusedPrivateMethodInspection
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 *
	 * @param array $editor_settings
	 *
	 * @return array
	 */
	private function block_editor_settings( $editor_settings ) {
		if ( $this->app->isset_shared_object( '__is_doing_block_editor_settings' ) ) {
			return $editor_settings;
		}

		$styles = $this->get_block_editor_styles( true );
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
			'translate'                  => $this->get_translate_data( [
				'Please select text',
				'Remove All formatting',
				'Inline Text Settings',
			] ),
			'default_buttons'            => [
				'font-color'       => [
					'name'       => 'font-color',
					'title'      => $this->translate( 'font color' ),
					'icon'       => $this->apply_filters( 'font_color_icon' ),
					'class_name' => $setting->get_default_class_name( 'font-color' ),
					'style'      => 'color',
					'is_valid'   => $this->apply_filters( 'is_valid_font_color' ),
				],
				'background-color' => [
					'name'       => 'background-color',
					'title'      => $this->translate( 'background color' ),
					'icon'       => $this->apply_filters( 'background_color_icon' ),
					'class_name' => $setting->get_default_class_name( 'background-color' ),
					'style'      => 'background-color',
					'is_valid'   => $this->apply_filters( 'is_valid_background_color' ),
				],
				'font-size'        => [
					'name'       => 'font-size',
					'title'      => $this->translate( 'font size' ),
					'icon'       => $this->apply_filters( 'font_size_icon' ),
					'class_name' => $setting->get_default_class_name( 'font-size' ),
					'is_valid'   => $this->apply_filters( 'is_valid_font_size' ),
				],
			],
		];
	}

	/**
	 * @return bool
	 */
	public function is_support_gutenberg() {
		if ( ! isset( $this->theme_key_cache ) ) {
			$theme                 = wp_get_theme();
			$this->theme_key_cache = $theme['Name'] . '/' . $theme['Version'];
		}

		$cache = $this->cache_get( 'is_support_gutenberg', null, $this->theme_key_cache );
		if ( isset( $cache ) ) {
			return $cache;
		}

		$result = $this->check_support_gutenberg();
		$this->cache_set( 'is_support_gutenberg', $result, $this->theme_key_cache );

		return $result;
	}

	/**
	 * @return bool
	 */
	private function check_support_gutenberg() {
		if ( current_theme_supports( 'editor-styles' ) ) {
			return true;
		}

		$editor_settings = [
			'alignWide'              => true,
			'availableTemplates'     => [],
			'allowedBlockTypes'      => true,
			'disableCustomColors'    => false,
			'disableCustomFontSizes' => false,
			'disablePostFormats'     => true,
			'titlePlaceholder'       => '',
			'bodyPlaceholder'        => '',
			'isRTL'                  => false,
			'autosaveInterval'       => 60,
			'maxUploadFileSize'      => 2097152,
			'allowedMimeTypes'       => [],
			'styles'                 => [],
			'imageSizes'             => [],
			'richEditingEnabled'     => true,
			'postLock'               => [],
			'postLockUtils'          => [],
			'enableCustomFields'     => false,
		];

		$editor_settings = $this->apply_block_editor_settings( $editor_settings );

		return ! empty( $editor_settings['styles'] );
	}

	/**
	 * @param array $editor_settings
	 *
	 * @return array
	 */
	public function apply_block_editor_settings( $editor_settings ) {
		$this->app->set_shared_object( '__is_doing_block_editor_settings', true );
		if ( function_exists( 'gutenberg_extend_block_editor_styles' ) ) {
			remove_filter( 'block_editor_settings', 'gutenberg_extend_block_editor_styles' );
		}
		$editor_settings = apply_filters( 'block_editor_settings', $editor_settings, is_singular() ? get_post() : new stdClass() );
		if ( function_exists( 'gutenberg_extend_block_editor_styles' ) ) {
			add_filter( 'block_editor_settings', 'gutenberg_extend_block_editor_styles' );
		}
		$this->app->delete_shared_object( '__is_doing_block_editor_settings' );

		return $editor_settings;
	}

	/**
	 * @param bool $is_editor
	 *
	 * @return string
	 */
	public function get_block_editor_styles( $is_editor = false ) {
		if ( $this->is_support_gutenberg() ) {
			if ( $is_editor ) {
				return '';
			}
		} elseif ( ! $this->apply_filters( 'support_block_editor_styles' ) ) {
			return '';
		}

		$styles = [];
		foreach ( $this->get_editor_styles() as $style ) {
			$styles = preg_match( '~^(https?:)?//~', $style ) ? $this->get_remote_style( $style, $styles ) : $this->get_local_style( $style, $styles );
		}

		$editor_settings = [
			'alignWide'              => true,
			'availableTemplates'     => [],
			'allowedBlockTypes'      => true,
			'disableCustomColors'    => false,
			'disableCustomFontSizes' => false,
			'disablePostFormats'     => true,
			'titlePlaceholder'       => '',
			'bodyPlaceholder'        => '',
			'isRTL'                  => false,
			'autosaveInterval'       => 60,
			'maxUploadFileSize'      => 2097152,
			'allowedMimeTypes'       => [],
			'styles'                 => $styles,
			'imageSizes'             => [],
			'richEditingEnabled'     => true,
			'postLock'               => [],
			'postLockUtils'          => [],
			'enableCustomFields'     => false,
		];

		$editor_settings = $this->apply_block_editor_settings( $editor_settings );
		$css             = implode( ' ', $this->app->array->pluck_unique( $editor_settings['styles'], 'css' ) );
		$css             = preg_replace( '/\/\*[\s\S]*?\*\//', '', $css );
		$css             = str_replace( [ "\r", "\n" ], ' ', $css );
		if ( ! $is_editor ) {
			$css = addslashes( $css );
		}

		return $css;
	}

	/**
	 * @return array
	 */
	private function get_editor_styles() {
		global $editor_styles;
		$_editor_styles = $editor_styles;
		if ( empty( $_editor_styles ) ) {
			$_editor_styles = [];
			if ( ! current_theme_supports( 'editor-styles' ) ) {
				$_editor_styles[] = get_template_directory_uri() . '/style.css';
				if ( is_child_theme() ) {
					$_editor_styles[] = get_stylesheet_uri();
				}
			}
		}

		return $_editor_styles;
	}

	/**
	 * @param string $style
	 * @param array $styles
	 *
	 * @return array
	 */
	private function get_remote_style( $style, $styles ) {
		$response = wp_remote_get( $style, [ 'sslverify' => false ] );
		if ( ! is_wp_error( $response ) ) {
			$styles[] = [
				'css' => wp_remote_retrieve_body( $response ),
			];
		}

		return $styles;
	}

	/**
	 * @param string $style
	 * @param array $styles
	 *
	 * @return array
	 */
	private function get_local_style( $style, $styles ) {
		$file = get_theme_file_path( $style );
		if ( $this->app->file->is_readable( $file ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$css = $this->app->file->get_contents( $file );

			// urlの相対パス⇒絶対パス置換（管理画面から読み込むため）
			$base   = dirname( get_theme_file_uri( $style ) );
			$parent = dirname( $base );
			$css    = preg_replace( "#url\([\"']?(\./)?(\w[^\"']+?)[\"']\)#", "url({$base}/$2)", $css );
			$css    = preg_replace( "#url\([\"']?(\../)?([^\"']+?)[\"']\)#", "url({$parent}/$2)", $css );

			// importの解析（面倒なので再帰的には読みこまない）
			if ( preg_match_all( '#@import\s*url\(["\']?((https?:)?//([\w\-]+\.)+[\w\-]+(/[\w\-\./\?%&=\#]*)?)["\']?\);?#', $css, $matches, PREG_SET_ORDER ) > 0 ) {
				foreach ( $matches as $match ) {
					$css    = str_replace( $match[0], '', $css );
					$styles = $this->get_remote_style( $match[1], $styles );
				}
			}

			$styles[] = [
				'css' => $css,
			];
		}

		return $styles;
	}
}
