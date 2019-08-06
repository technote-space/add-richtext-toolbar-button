<?php
/**
 * Class DashboardTest
 *
 * @package Tests
 */

namespace Richtext_Toolbar_Button\Tests\Controllers\Admin;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use Richtext_Toolbar_Button\Classes\Controllers\Admin\Dashboard;
use WP_Framework;
use WP_UnitTestCase;

/**
 * @noinspection PhpUndefinedClassInspection
 * Dashboard test case.
 *
 * @mixin TestCase
 */
class DashboardTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework
	 */
	protected static $app;

	/**
	 * @var Dashboard $dashboard
	 */
	private static $dashboard;

	/**
	 * @SuppressWarnings(StaticAccess)
	 * @throws ReflectionException
	 */
	public static function setUpBeforeClass() {
		static::$app       = WP_Framework::get_instance( ADD_RICHTEXT_TOOLBAR_BUTTON );
		static::$dashboard = Dashboard::get_instance( static::$app );
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
		wp_dequeue_script( 'media-upload' );
		wp_dequeue_script( 'thickbox' );
		wp_dequeue_style( 'thickbox' );
		static::get_output_js();
		static::get_output_css();
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_action() {
		static::reset();
		$this->assertFalse( wp_script_is( 'media-upload' ) );
		$this->assertFalse( wp_script_is( 'thickbox' ) );
		$this->assertFalse( wp_style_is( 'thickbox' ) );

		static::$dashboard->action();
		$this->assertTrue( wp_script_is( 'media-upload' ) );
		$this->assertTrue( wp_script_is( 'thickbox' ) );
		$this->assertTrue( wp_style_is( 'thickbox' ) );
		$this->assertNotEmpty( static::get_output_js() );
		$this->assertNotEmpty( static::get_output_css() );
	}

	public function test_presenter() {
		ob_start();
		static::$dashboard->presenter();
		$contents = ob_get_contents();
		ob_end_clean();

		$this->assertContains( 'name="artb_nonce_admin_dashboard"', $contents );
		$this->assertContains( 'id="artb-is_valid"', $contents );
		$this->assertContains( 'id="artb-is_valid_font_color"', $contents );
		$this->assertContains( 'id="artb/font_color_icon"', $contents );
		$this->assertContains( 'id="artb-is_valid_background_color"', $contents );
		$this->assertContains( 'id="artb/background_color_icon"', $contents );
		$this->assertContains( 'id="artb-is_valid_font_size"', $contents );
		$this->assertContains( 'id="artb/font_size_icon"', $contents );
		$this->assertContains( 'id="artb-is_valid_remove_formatting"', $contents );
		$this->assertContains( 'id="artb/default_icon"', $contents );
		$this->assertContains( 'id="artb-default_group"', $contents );
		$this->assertContains( 'id="artb-test_phrase"', $contents );
		$this->assertContains( 'name="update"', $contents );
		$this->assertContains( 'name="reset"', $contents );
		$this->assertContains( 'id="artb-info-wrap"', $contents );
	}

	/**
	 * @return false|string
	 * @throws ReflectionException
	 */
	private static function get_output_js() {
		ob_start();
		static::$app->minify->output_js( true );
		$contents = ob_get_contents();
		ob_end_clean();

		static::set_property( static::$app->minify, '_has_output_script', false );

		return $contents;
	}

	/**
	 * @return false|string
	 * @throws ReflectionException
	 */
	private static function get_output_css() {
		ob_start();
		static::$app->minify->output_css( true );
		$contents = ob_get_contents();
		ob_end_clean();

		static::set_property( static::$app->minify, '_end_footer', false );

		return $contents;
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
