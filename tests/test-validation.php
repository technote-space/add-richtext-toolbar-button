<?php
/**
 * Class ValidationTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Richtext_Toolbar_Button\Classes\Models\Validation;
use Richtext_Toolbar_Button\Classes\Models\Custom_Post\Setting;

/**
 * @noinspection PhpUndefinedClassInspection
 * Validation test case.
 *
 * @mixin TestCase
 */
class ValidationTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework|Phake_IMock
	 */
	protected static $app;

	/**
	 * @var Validation $validation
	 */
	private static $validation;

	/**
	 * @var Setting $setting ;
	 */
	private static $setting;

	/**
	 * @var int $inserted
	 */
	private static $inserted;

	/**
	 * @SuppressWarnings(StaticAccess)
	 */
	public static function setUpBeforeClass() {
		static::$app        = WP_Framework::get_instance( ADD_RICHTEXT_TOOLBAR_BUTTON );
		static::$validation = Validation::get_instance( static::$app );
		static::$setting    = Setting::get_instance( static::$app );

		static::$app->db->table( 'setting' )->truncate();
		static::$app->db->wp_table( 'posts' )->truncate();

		$item                            = [];
		$item['post_title']              = 'test title';
		$item['group_name']              = 'test group';
		$item['class_name']              = 'test';
		$item['style']                   = 'background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #f69 75%); font-weight: bold;';
		$item['is_valid_toolbar_button'] = 1;
		$item['priority']                = 50;

		$setting_id       = static::$setting->insert( $item );
		$data             = static::$setting->get_data( $setting_id );
		static::$inserted = $data['post_id'];
	}

	/**
	 * @dataProvider validate_class_name_data_provider
	 *
	 * @param $class_name
	 * @param $priority
	 * @param $post_array
	 * @param $errors
	 * @param $callback
	 */
	public function test_validate_class_name( $class_name, $priority, $post_array, $errors, $callback ) {
		if ( ! empty( $post_array['ID'] ) ) {
			$post_array['ID'] = static::$inserted;
		}
		$errors = static::$validation->validate_class_name( $class_name, $priority, $post_array, $errors );
		$callback( $errors );
	}

	public function validate_class_name_data_provider() {
		return [
			[
				'artb-test',
				10,
				[],
				[],
				function ( $errors ) {
					$this->assertArrayHasKey( 'class_name', $errors );
					$this->assertCount( 1, $errors['class_name'] );
					$this->assertContains( static::$validation->translate( 'The value is unusable.' ), $errors['class_name'] );
				},
			],
			[
				'0abc',
				10,
				[],
				[ 'class_name' => [ 'a' ] ],
				function ( $errors ) {
					$this->assertArrayHasKey( 'class_name', $errors );
					$this->assertCount( 3, $errors['class_name'] );
					$this->assertContains( 'a', $errors['class_name'] );
					$this->assertContains( static::$validation->translate( 'Invalid format.' ), $errors['class_name'] );
					$this->assertContains( static::$validation->translate( 'A class name must begin with a letter, followed by any number of hyphens, letters, or numbers.' ), $errors['class_name'] );
				},
			],
			[
				'test',
				10,
				[],
				[],
				function ( $errors ) {
					$this->assertArrayHasKey( 'class_name', $errors );
					$this->assertCount( 1, $errors['class_name'] );
					$this->assertContains( static::$validation->translate( 'The value has already been used.' ), $errors['class_name'] );
				},
			],
			[
				'test test2',
				100,
				[ 'ID' => 0 ],
				[],
				function ( $errors ) {
					$this->assertArrayHasKey( 'class_name', $errors );
					$this->assertCount( 1, $errors['class_name'] );
					$this->assertContains( static::$validation->translate( 'The value is included in the class name of other settings.' ), $errors['class_name'] );
				},
			],
			[
				'test test2',
				10,
				[ 'ID' => 0 ],
				[],
				function ( $errors ) {
					$this->assertEmpty( $errors );
				},
			],
			[
				'test test2',
				100,
				[ 'ID' => 1 ],
				[],
				function ( $errors ) {
					$this->assertEmpty( $errors );
				},
			],
		];
	}

	/**
	 * @dataProvider validate_tag_name_data_provider
	 *
	 * @param $tag_name
	 * @param $errors
	 * @param $callback
	 */
	public function test_validate_tag_name( $tag_name, $errors, $callback ) {
		$errors = static::$validation->validate_tag_name( $tag_name, $errors );
		$callback( $errors );
	}

	public function validate_tag_name_data_provider() {
		return [
			[
				'div',
				[],
				function ( $errors ) {
					$this->assertArrayHasKey( 'tag_name', $errors );
					$this->assertCount( 1, $errors['tag_name'] );
					$this->assertContains( static::$validation->translate( 'This tag name is unusable.' ), $errors['tag_name'] );
				},
			],
			[
				'0abc',
				[ 'tag_name' => [ 'a' ] ],
				function ( $errors ) {
					$this->assertArrayHasKey( 'tag_name', $errors );
					$this->assertCount( 2, $errors['tag_name'] );
					$this->assertContains( 'a', $errors['tag_name'] );
					$this->assertContains( static::$validation->translate( 'Invalid format.' ), $errors['tag_name'] );
				},
			],
		];
	}
}
