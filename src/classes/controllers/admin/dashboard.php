<?php
/**
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
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

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return 0;
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return 'Dashboard';
	}

	/**
	 * post
	 */
	protected function post_action() {
		if ( $this->app->input->post( 'reset' ) ) {
			foreach ( $this->get_setting_list() as $name ) {
				$this->app->option->delete( $this->get_filter_prefix() . $name );
				$this->delete_hook_cache( $name );
			}
			$this->app->add_message( 'Settings have been reset.', 'setting' );
		} elseif ( $this->app->input->post( 'update' ) ) {
			foreach ( $this->get_setting_list() as $name ) {
				$this->update_setting( $name );
			}
			$this->app->add_message( 'Settings have been updated.', 'setting' );
		}
	}

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
	protected function get_view_args() {
		$args = [];
		foreach ( $this->get_setting_list() as $name ) {
			$args['settings'][ $name ] = $this->get_view_setting( $name );
		}

		return $args;
	}

	/**
	 * @return array
	 */
	private function get_setting_list() {
		return [
			'is_valid',
			'default_icon',
			'default_group',
			'test_phrase',
		];
	}

	/**
	 * @param string $name
	 *
	 * @return array
	 */
	private function get_view_setting( $name ) {
		$detail          = $this->app->setting->get_setting( $name, true );
		$detail['id']    = str_replace( '/', '-', $detail['name'] );
		$detail['form']  = $this->get_form_by_type( $detail['type'], false );
		$detail['label'] = $this->translate( $detail['label'] );
		if ( $this->app->utility->array_get( $detail, 'type' ) === 'bool' ) {
			if ( $detail['value'] ) {
				$detail['checked'] = true;
			}
			$detail['value'] = 1;
		}
		'default_icon' === $name and $detail['form_type'] = 'icon';

		return $detail;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	private function update_setting( $name ) {
		$detail  = $this->app->setting->get_setting( $name, true );
		$default = null;
		if ( $this->app->utility->array_get( $detail, 'type' ) === 'bool' ) {
			$default = 0;
		}

		return $this->app->option->set_post_value( $detail['name'], $default );
	}
}
