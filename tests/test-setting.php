<?php
/**
 * Class SettingTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Richtext_Toolbar_Button\Classes\Models\Assets;
use Richtext_Toolbar_Button\Classes\Models\Custom_Post\Setting;

/**
 * @noinspection PhpUndefinedClassInspection
 * Setting test case.
 *
 * @mixin TestCase
 * @SuppressWarnings(TooManyPublicMethods)
 */
class SettingTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework|Phake_IMock
	 */
	protected static $app;

	/**
	 * @var Setting $setting
	 */
	private static $setting;

	/**
	 * @var Assets $assets
	 */
	private static $assets;

	/**
	 * @SuppressWarnings(StaticAccess)
	 * @throws ReflectionException
	 */
	public static function setUpBeforeClass() {
		static::$app     = WP_Framework::get_instance( ADD_RICHTEXT_TOOLBAR_BUTTON );
		static::$setting = Setting::get_instance( static::$app );
		static::$assets  = Assets::get_instance( static::$app );
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
		static::$app->db->table( 'setting' )->truncate();
		static::$app->db->wp_table( 'posts' )->truncate();
		static::$app->option->delete( 'has_inserted_presets' );
		wp_dequeue_style( static::$assets->get_css_handle() );

		$handle = static::$app->get_config( 'config', 'fontawesome_handle' );
		wp_dequeue_style( $handle );
		static::set_property( static::$setting, '_setup_fontawesome', [] );
		static::set_property( static::$setting, 'cache_settings', [] );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_insert_presets() {
		$this->assertEmpty( static::$app->get_option( 'has_inserted_presets' ) );
		$this->assertEmpty( static::$setting->get_list_data( null, false )['data'] );
		static::$app->filter->do_action( 'app_activated' );
		$this->assertNotEmpty( static::$app->get_option( 'has_inserted_presets' ) );
		$this->assertNotEmpty( static::$setting->get_list_data( null, false )['data'] );

		static::reset();
		static::$app->option->set( 'has_inserted_presets', true );
		$this->assertNotEmpty( static::$app->get_option( 'has_inserted_presets' ) );
		$this->assertEmpty( static::$setting->get_list_data( null, false )['data'] );
		static::$app->filter->do_action( 'app_activated' );
		$this->assertNotEmpty( static::$app->get_option( 'has_inserted_presets' ) );
		$this->assertEmpty( static::$setting->get_list_data( null, false )['data'] );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_setup_assets() {
		$this->get_output_js();
		$this->get_output_css();

		global $typenow;
		wp_dequeue_style( static::$assets->get_css_handle() );
		$typenow = 'post';
		do_action( 'load-edit.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		$this->assertFalse( wp_style_is( static::$assets->get_css_handle() ) );
		$this->assertEmpty( $this->get_output_js() );
		$this->assertEmpty( $this->get_output_css() );

		wp_dequeue_style( static::$assets->get_css_handle() );
		$typenow = 'artb-setting';
		do_action( 'load-edit.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		$this->assertTrue( wp_style_is( static::$assets->get_css_handle() ) );
		$this->assertNotEmpty( $this->get_output_js() );
		$this->assertNotEmpty( $this->get_output_css() );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_setup_page() {
		static::reset();
		$handle = static::$app->get_config( 'config', 'fontawesome_handle' );
		$this->assertFalse( wp_style_is( $handle ) );
		static::$setting->setup_page();
		$this->assertTrue( wp_style_is( $handle ) );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_output_edit_form() {
		static::insert_settings();

		static::$app->setting->edit_setting( 'default_group', 'default', null );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertNull( static::$app->filter->apply_filters( 'default_group' ) );
		ob_start();
		static::$setting->output_edit_form( get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertContains( '<div class="block form custom-post">', $contents );
		$this->assertContains( 'id="artb-tag_name"', $contents );
		$this->assertContains( 'id="artb-class_name"', $contents );
		$this->assertContains( 'id="artb-group_name"', $contents );
		$this->assertContains( 'id="artb-icon"', $contents );
		$this->assertContains( 'id="artb-style"', $contents );
		$this->assertContains( 'id="artb-is_valid_toolbar_button"', $contents );
		$this->assertContains( 'id="artb-priority"', $contents );
		$this->assertContains( '<legend>preset</legend>', $contents );
		$this->assertContains( 'Font Awesome Icons', $contents );

		static::$app->setting->edit_setting( 'default_group', 'default', 'default_test' );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertEquals( 'default_test', static::$app->filter->apply_filters( 'default_group' ) );
		ob_start();
		static::$setting->output_edit_form( get_post( 2 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertContains( '<div class="block form custom-post">', $contents );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_manage_posts_columns() {
		static::insert_settings();

		$columns = static::$setting->manage_posts_columns( [ 'title' => 'タイトル' ] );
		$this->assertArrayHasKey( 'title', $columns );
		$this->assertArrayHasKey( 'artb-setting-preview', $columns );
		$this->assertArrayHasKey( 'artb-setting-display', $columns );
		$this->assertArrayHasKey( 'artb-setting-is_valid_toolbar_button', $columns );
		$this->assertArrayHasKey( 'artb-setting-priority', $columns );
		$this->assertEquals( 'Setting name', $columns['title'] );

		ob_start();
		static::$setting->manage_posts_custom_column( 'artb-setting-preview', get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( '<iframe class="preview-iframe" data-tag_name="span" data-class_name="test1"></iframe>', str_replace( [ "\r", "\n" ], '', $contents ) );

		ob_start();
		static::$setting->manage_posts_custom_column( 'artb-setting-display', get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertContains( '<table class="widefat striped">', $contents );
		$this->assertContains( '<th>tag name</th>', $contents );
		$this->assertContains( '<th>class name</th>', $contents );
		$this->assertContains( '<th>group name</th>', $contents );
		$this->assertContains( '<th>icon</th>', $contents );

		ob_start();
		static::$setting->manage_posts_custom_column( 'artb-setting-is_valid_toolbar_button', get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( 'Valid', $contents );

		ob_start();
		static::$setting->manage_posts_custom_column( 'artb-setting-is_valid_toolbar_button', get_post( 2 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( 'Invalid', $contents );

		ob_start();
		static::$setting->manage_posts_custom_column( 'artb-setting-priority', get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( '25', $contents );
	}

	public function test_get_error_messages() {
		$errors = static::$setting->get_error_messages( 'post_title', [ 'test1', 'test2' ] );
		static::assertCount( 2, $errors );
		static::assertEquals( 'test1: [Setting name]', $errors[0] );
		static::assertEquals( 'test2: [Setting name]', $errors[1] );
	}

	public function test_call_clear_cache_file() {
		$post = get_post( 1 );
		static::$setting->data_updated( 1, $post, [], [] );
		static::$setting->data_inserted( 1, $post, [] );
		static::$setting->untrash_post( 1, $post );
		static::$setting->trash_post( 1 );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_delete() {
		static::insert_settings();
		static::assertEquals( 1, static::$setting->delete_data( 2 ) );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_get_settings() {
		static::insert_settings();

		static::$app->setting->edit_setting( 'default_group', 'default', null );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertNull( static::$app->filter->apply_filters( 'default_group' ) );
		$settings = static::$setting->get_settings( 'editor' );
		static::assertCount( 4, $settings );
		static::assertArrayHasKey( 'icon', $settings[0] );
		static::assertArrayHasKey( 'title', $settings[0] );
		static::assertArrayHasKey( 'name', $settings[0] );
		static::assertArrayHasKey( 'selector', $settings[0] );
		static::assertArrayHasKey( 'tagName', $settings[0] );
		static::assertArrayHasKey( 'groupName', $settings[0] );
		static::assertArrayHasKey( 'isValidToolbarButton', $settings[0] );
		static::assertArrayHasKey( 'isValid', $settings[0] );

		static::$app->setting->edit_setting( 'default_group', 'default', 'default_test' );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertEquals( 'default_test', static::$app->filter->apply_filters( 'default_group' ) );
		$settings = static::$setting->get_settings( 'front' );
		static::assertCount( 4, $settings );
		static::assertArrayHasKey( 'icon', $settings[0] );
		static::assertArrayHasKey( 'title', $settings[0] );
		static::assertArrayHasKey( 'name', $settings[0] );
		static::assertArrayHasKey( 'selector', $settings[0] );
		static::assertArrayHasKey( 'tag_name', $settings[0] );
		static::assertArrayHasKey( 'group_name', $settings[0] );
		static::assertArrayHasKey( 'is_valid_toolbar_button', $settings[0] );
		static::assertArrayHasKey( 'is_valid', $settings[0] );
	}

	public function test_get_valid_post_types() {
		$post_types = static::$setting->get_valid_post_types();
		static::assertContains( 'post', $post_types );
		static::assertContains( 'page', $post_types );
		static::assertContains( 'wp_block', $post_types );
	}

	/**
	 * @return false|string
	 * @throws ReflectionException
	 */
	private function get_output_js() {
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
	private function get_output_css() {
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

	/**
	 * @throws ReflectionException
	 */
	private static function insert_settings() {
		static::reset();
		foreach (
			[
				[
					'post_title'              => 'test title1',
					'group_name'              => 'test group1',
					'class_name'              => 'test1',
					'style'                   => '',
					'is_valid_toolbar_button' => 1,
					'priority'                => 25,
				],
				[
					'post_title'              => 'test title2',
					'group_name'              => 'test group2',
					'class_name'              => 'test2',
					'style'                   => '',
					'is_valid_toolbar_button' => 0,
					'priority'                => 50,
				],
				[
					'post_title'              => 'test title3',
					'group_name'              => 'test group2',
					'class_name'              => 'test3-1 test3-2',
					'style'                   => '',
					'is_valid_toolbar_button' => 1,
				],
				[
					'post_title'              => 'test title4',
					'class_name'              => 'test4',
					'style'                   => '',
					'is_valid_toolbar_button' => 1,
				],
			] as $item
		) {
			static::$setting->insert( $item );
		}
	}
}
