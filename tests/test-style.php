<?php
/**
 * Class StyleTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Richtext_Toolbar_Button\Classes\Models\Style;

/**
 * @noinspection PhpUndefinedClassInspection
 * Style test case.
 *
 * @mixin TestCase
 */
class StyleTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework|Phake_IMock
	 */
	protected static $app;

	/**
	 * @var Style $style
	 */
	private static $style;

	/**
	 * @SuppressWarnings(StaticAccess)
	 */
	public static function setUpBeforeClass() {
		static::$app   = WP_Framework::get_instance( ADD_RICHTEXT_TOOLBAR_BUTTON );
		static::$style = Style::get_instance( static::$app );
	}

	/**
	 * @dataProvider encode_style_data_provider
	 *
	 * @param $expected
	 * @param $style
	 */
	public function test_encode_style( $expected, $style ) {
		$this->assertEquals( $expected, static::$style->encode_style( $style ) );
	}

	public function encode_style_data_provider() {
		return [
			[
				'[]',
				'',
			],
			[
				'[]',
				null,
			],
			[
				'{"":["background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #f69 75%);","font-weight: bold;"]}',
				'background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #f69 75%);
font-weight: bold;',
			],
			[
				'{"":["font-weight: bold;","color: red;"]}',
				'!#$%&"abc: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #f69 75%);
font-weight: bold;;;color: red',
			],
			[
				'{"":["padding: 6px;"],"after":["color: red;"],"before":["font-weight: bold;","display: block;"]}',
				'!#$%&"abc: linear-gradient(to bottom, rgba(255, 255, 255, 0) 60%, #f69 75%);
[before]font-weight: bold;;;[after]color: red
[before]display:block
padding:     6px
[after]color: red',
			],
		];
	}

	/**
	 * @dataProvider decode_style_data_provider
	 *
	 * @param $expected
	 * @param $style
	 * @param $is_editor
	 */
	public function test_decode_style( $expected, $style, $is_editor ) {
		$this->assertEquals( $expected, static::$style->decode_style( $style, $is_editor ) );
	}

	public function decode_style_data_provider() {
		return [
			[
				[],
				'',
				false,
			],
			[
				[ '' => [ 'padding: 6px;' ] ],
				'{"":["padding: 6px;"]}',
				false,
			],
			[
				"padding: 6px;\r\n",
				'{"":["padding: 6px;"]}',
				true,
			],
			[
				"padding: 6px\r\n\r\n[after] color: red;\r\n\r\n[before] font-weight: bold\r\n[before] display: block;\r\n",
				'{"":["padding: 6px"],"after":["color: red;"],"before":["font-weight: bold","display: block;"]}',
				true,
			],
		];
	}
}
