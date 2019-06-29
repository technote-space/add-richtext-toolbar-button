<?php
/**
 * Class AssetsTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Richtext_Toolbar_Button\Classes\Models\Assets;

/**
 * @noinspection PhpUndefinedClassInspection
 * Assets test case.
 *
 * @mixin TestCase
 */
class AssetsTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework|Phake_IMock
	 */
	protected static $app;

	/**
	 * @var Assets $assets
	 */
	private static $assets;

	/**
	 * @SuppressWarnings(StaticAccess)
	 */
	public static function setUpBeforeClass() {
		static::$app    = WP_Framework::get_instance( ADD_RICHTEXT_TOOLBAR_BUTTON );
		static::$assets = Assets::get_instance( static::$app );
	}

	public static function tearDownAfterClass() {
		wp_dequeue_style( static::$assets->get_css_handle() );
		wp_dequeue_style( 'artb-css' );
	}

	public function test_remove_setting() {
		$this->assertEquals( false, static::$app->setting->is_setting_removed( 'assets_version' ) );
		static::$app->filter->do_action( 'post_load_admin_page' );
		$this->assertEquals( true, static::$app->setting->is_setting_removed( 'assets_version' ) );
	}

	public function test_setup_assets() {
		wp_dequeue_style( static::$assets->get_css_handle() );

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

		$this->reset_cleared_cache_file();
		static::$app->filter->do_action( 'changed_option', 'test' );
		$this->assertTrue( static::$app->file->upload_file_exists( static::$app, $path ) );

		$this->reset_cleared_cache_file();
		static::$app->filter->do_action( 'changed_option', 'artb/test' );
		$this->assertFalse( static::$app->file->upload_file_exists( static::$app, $path ) );
	}

	/**
	 * @throws ReflectionException
	 */
	private function reset_cleared_cache_file() {
		$reflection = new ReflectionClass( static::$assets );
		$property   = $reflection->getProperty( 'cleared_cache_file' );
		$property->setAccessible( true );
		$property->setValue( static::$assets, null );
		$property->setAccessible( false );
	}

	public function test_enqueue_plugin_assets() {
		wp_dequeue_style( 'artb-css' );

		$this->assertFalse( wp_style_is( 'artb-css' ) );
		static::$assets->enqueue_plugin_assets( true );
		$this->assertTrue( wp_style_is( 'artb-css' ) );
	}
}
