<?php
/**
 * @version 1.1.2
 * @author Technote
 * @since 1.0.0
 * @since 1.0.3 #34
 * @since 1.0.12 #77
 * @since 1.1.0 wp-content-framework/admin#20, wp-content-framework/common#57
 * @since 1.1.2 trivial change
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Richtext_Toolbar_Button\Classes\Controllers\Admin;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	exit;
}

/**
 * Class Dashboard
 * @package Richtext_Toolbar_Button\Classes\Controllers\Admin
 */
class Dashboard extends \WP_Framework_Admin\Classes\Controllers\Admin\Base {

	use \WP_Framework_Admin\Traits\Dashboard;

	/**
	 * common
	 */
	protected function common_action() {
		$this->setup_dashicon_picker();
		$this->setup_media_uploader();
	}

	/**
	 * @return array
	 */
	protected function get_setting_list() {
		return [
			'is_valid',
			'is_valid_font_color',
			'font_color_icon',
			'is_valid_background_color',
			'background_color_icon',
			'is_valid_font_size',
			'font_size_icon',
			'is_valid_remove_formatting',
			'default_icon',
			'default_group',
			'test_phrase',
		];
	}

	/**
	 * @param array $detail
	 * @param string $name
	 * @param array $option
	 *
	 * @return array
	 */
	protected function filter_view_setting(
		/** @noinspection PhpUnusedParameterInspection */
		array $detail, $name, array $option
	) {
		if ( $this->app->string->ends_with( $name, '_icon' ) ) {
			$detail['form_type'] = 'icon';
		}

		return $detail;
	}
}
