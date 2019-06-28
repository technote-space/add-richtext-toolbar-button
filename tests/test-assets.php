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

	public function test_remove_setting() {
		$this->assertEquals( false, static::$app->setting->is_setting_removed( 'assets_version' ) );
		static::$app->filter->do_action( 'post_load_admin_page' );
		$this->assertEquals( true, static::$app->setting->is_setting_removed( 'assets_version' ) );
	}
}
