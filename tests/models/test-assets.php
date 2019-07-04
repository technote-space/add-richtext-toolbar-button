<?php
/**
 * Class AssetsTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Richtext_Toolbar_Button\Classes\Models\Assets;
use Richtext_Toolbar_Button\Classes\Models\Custom_Post\Setting;

/**
 * @noinspection PhpUndefinedClassInspection
 * Assets test case.
 *
 * @mixin TestCase
 */
class AssetsTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework
	 */
	protected static $app;

	/**
	 * @var Assets $assets
	 */
	private static $assets;

	/**
	 * @var Setting $setting
	 */
	private static $setting;

	/**
	 * @SuppressWarnings(StaticAccess)
	 * @throws ReflectionException
	 */
	public static function setUpBeforeClass() {
		static::$app     = WP_Framework::get_instance( ADD_RICHTEXT_TOOLBAR_BUTTON );
		static::$assets  = Assets::get_instance( static::$app );
		static::$setting = Setting::get_instance( static::$app );
		static::reset();
	}

	/**
	 * @throws ReflectionException
	 */
	public static function tearDownAfterClass() {
		static::reset();
	}

	/**
	 * @throws ReflectionException
	 */
	private static function reset() {
		wp_dequeue_style( static::$assets->get_css_handle() );
		wp_dequeue_style( 'artb-css' );

		$handle = static::$app->get_config( 'config', 'fontawesome_handle' );
		wp_dequeue_style( $handle );
		static::set_property( static::$setting, '_setup_fontawesome', [] );
	}

	public function test_remove_setting() {
		$this->assertEquals( false, static::$app->setting->is_setting_removed( 'assets_version' ) );
		static::$app->filter->do_action( 'post_load_admin_page' );
		$this->assertEquals( true, static::$app->setting->is_setting_removed( 'assets_version' ) );
	}

	public function test_setup_assets() {
		wp_dequeue_style( static::$assets->get_css_handle() );
		$this->assertFalse( wp_style_is( static::$assets->get_css_handle() ) );

		static::$app->setting->edit_setting( 'is_valid', 'default', false );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertFalse( static::$app->filter->apply_filters( 'is_valid' ) );
		ob_start();
		do_action( 'wp_head' );
		ob_end_clean();
		$this->assertFalse( wp_style_is( static::$assets->get_css_handle() ) );

		static::$app->setting->edit_setting( 'is_valid', 'default', true );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertTrue( static::$app->filter->apply_filters( 'is_valid' ) );
		ob_start();
		do_action( 'wp_head' );
		ob_end_clean();
		$this->assertTrue( wp_style_is( static::$assets->get_css_handle() ) );
	}

	/**
	 * @throws Exception
	 */
	public function test_changed_option() {
		$path = 'css' . DS . 'artb.css';
		static::$app->file->create_upload_file( static::$app, $path, '' );
		$this->assertTrue( static::$app->file->upload_file_exists( static::$app, $path ) );

		static::set_property( static::$assets, 'cleared_cache_file', null );
		static::$app->filter->do_action( 'changed_option', 'test' );
		$this->assertTrue( static::$app->file->upload_file_exists( static::$app, $path ) );

		static::set_property( static::$assets, 'cleared_cache_file', null );
		static::$app->filter->do_action( 'changed_option', 'artb/test' );
		$this->assertFalse( static::$app->file->upload_file_exists( static::$app, $path ) );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_enqueue_plugin_assets() {
		$handle = static::$app->get_config( 'config', 'fontawesome_handle' );

		static::reset();
		static::$app->setting->edit_setting( 'is_valid_fontawesome', 'default', true );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertTrue( static::$app->filter->apply_filters( 'is_valid_fontawesome' ) );
		$this->assertFalse( wp_style_is( 'artb-css' ) );
		$this->assertFalse( wp_style_is( $handle ) );
		static::$assets->enqueue_plugin_assets( true );
		$this->assertTrue( wp_style_is( 'artb-css' ) );
		$this->assertTrue( wp_style_is( $handle ) );

		static::reset();
		static::$app->setting->edit_setting( 'is_valid_fontawesome', 'default', false );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertFalse( static::$app->filter->apply_filters( 'is_valid_fontawesome' ) );
		$this->assertFalse( wp_style_is( 'artb-css' ) );
		$this->assertFalse( wp_style_is( $handle ) );
		static::$assets->enqueue_plugin_assets( true );
		$this->assertTrue( wp_style_is( 'artb-css' ) );
		$this->assertFalse( wp_style_is( $handle ) );
	}

	/**
	 * @param $target
	 * @param $name
	 * @param $value
	 *
	 * @throws ReflectionException
	 */
	private static function set_property( $target, $name, $value ) {
		$reflection = new ReflectionClass( $target );
		$property   = $reflection->getProperty( $name );
		$property->setAccessible( true );
		$property->setValue( $target, $value );
		$property->setAccessible( false );
	}
}
