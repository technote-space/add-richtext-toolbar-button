<?php
/**
 * Class EditorTest
 *
 * @package Tests
 */

namespace Richtext_Toolbar_Button\Tests\Models;

use PHPUnit\Framework\TestCase;
use Richtext_Toolbar_Button\Classes\Models\Assets;
use Richtext_Toolbar_Button\Classes\Models\Editor;
use WP_Framework;
use WP_UnitTestCase;

/**
 * @noinspection PhpUndefinedClassInspection
 * Editor test case.
 *
 * @mixin TestCase
 */
class EditorTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework
	 */
	protected static $app;

	/**
	 * @var Editor $editor
	 */
	private static $editor;

	/**
	 * @var Assets $assets
	 */
	private static $assets;

	/**
	 * @var bool $is_ci
	 */
	private static $is_ci;

	/**
	 * @SuppressWarnings(StaticAccess)
	 */
	public static function setUpBeforeClass() {
		static::$app    = WP_Framework::get_instance( ADD_RICHTEXT_TOOLBAR_BUTTON );
		static::$editor = Editor::get_instance( static::$app );
		static::$assets = Assets::get_instance( static::$app );
		static::$is_ci  = ! empty( getenv( 'CI' ) );
		static::reset();
	}

	public static function tearDownAfterClass() {
		static::reset();
	}

	private static function reset() {
		wp_dequeue_script( 'add-richtext-toolbar-button-editor' );
		wp_dequeue_style( static::$assets->get_css_handle() );
		if ( static::$is_ci ) {
			static::$app->file->delete( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'index.min.js' );
		}
	}

	public function test_enqueue_block_editor_assets() {
		wp_dequeue_script( 'add-richtext-toolbar-button-editor' );
		wp_dequeue_style( static::$assets->get_css_handle() );
		if ( static::$is_ci ) {
			static::$app->file->put_contents( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'index.min.js', '' );
		}

		static::$app->setting->edit_setting( 'is_valid', 'default', false );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertFalse( static::$app->filter->apply_filters( 'is_valid' ) );
		do_action( 'enqueue_block_editor_assets' );
		$this->assertFalse( wp_script_is( 'add-richtext-toolbar-button-editor' ) );
		$this->assertFalse( wp_style_is( static::$assets->get_css_handle() ) );

		static::$app->setting->edit_setting( 'is_valid', 'default', true );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertTrue( static::$app->filter->apply_filters( 'is_valid' ) );
		do_action( 'enqueue_block_editor_assets' );
		$this->assertTrue( wp_script_is( 'add-richtext-toolbar-button-editor' ) );
		$this->assertTrue( wp_style_is( static::$assets->get_css_handle() ) );
	}
}
