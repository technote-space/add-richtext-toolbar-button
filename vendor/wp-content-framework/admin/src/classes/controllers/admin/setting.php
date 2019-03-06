<?php
/**
 * WP_Framework_Admin Classes Controller Admin Setting
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Admin\Classes\Controllers\Admin;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Setting
 * @package WP_Framework_Admin\Classes\Controllers\Admin
 */
class Setting extends Base {

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return $this->apply_filters( 'setting_page_priority', $this->app->get_config( 'config', 'setting_page_priority' ) );
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return $this->apply_filters( 'setting_page_title', $this->app->get_config( 'config', 'setting_page_title' ) );
	}

	/**
	 * post action
	 */
	protected function post_action() {
		foreach ( $this->app->setting->get_groups() as $group ) {
			foreach ( $this->app->setting->get_settings( $group ) as $setting ) {
				$this->app->option->set_post_value( $this->app->utility->array_get( $this->app->setting->get_setting( $setting, true ), 'name', '' ) );
			}
		}
		$this->app->add_message( 'Settings updated.', 'setting' );
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {
		$settings = [];
		foreach ( $this->app->setting->get_groups() as $group ) {
			foreach ( $this->app->setting->get_settings( $group ) as $setting ) {
				$settings[ $group ][ $setting ] = $this->app->setting->get_setting( $setting, true );
			}
		}

		return [
			'settings' => $settings,
		];
	}

	/**
	 * @return array
	 */
	protected function get_help_contents() {
		if ( $this->app->get_config( 'config', 'suppress_setting_help_contents' ) ) {
			return [];
		}

		return [
			[
				'title' => 'The procedure for editing the help of dashboard',
				'view'  => 'setting1',
			],
			[
				'title' => 'The procedure for deleting this help if it is not needed',
				'view'  => 'setting2',
			],
			[
				'title' => 'The procedure for editing the sidebar',
				'view'  => 'setting3',
			],
		];
	}

	/**
	 * @return array
	 */
	protected function get_help_content_params() {
		return [
			'prefix' => $this->get_filter_prefix(),
		];
	}

	/**
	 * @return false|string
	 */
	protected function get_help_sidebar() {
		if (
			! empty( $this->app->get_config( 'config', 'contact_url' ) ) ||
			! empty( $this->app->get_config( 'config', 'twitter' ) ) ||
			! empty( $this->app->get_config( 'config', 'github' ) )
		) {
			return 'setting';
		}

		return false;
	}
}
