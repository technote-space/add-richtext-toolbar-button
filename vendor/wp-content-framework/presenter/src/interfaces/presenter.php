<?php
/**
 * WP_Framework_Presenter Interfaces Presenter
 *
 * @version 0.0.19
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Presenter\Interfaces;

use WP_Framework;
use WP_Framework_Core\Traits\Translate;
use WP_Post;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Interface Presenter
 * @package WP_Framework_Presenter\Interfaces
 * @property WP_Framework $app
 * @mixin Translate
 */
interface Presenter {

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function view_exists( $name );

	/**
	 * @param string $name
	 * @param array $args
	 * @param bool $echo
	 * @param bool $error
	 * @param bool $remove_nl
	 *
	 * @return string
	 */
	public function get_view( $name, array $args = [], $echo = false, $error = true, $remove_nl = false );

	/**
	 * @param string $name
	 * @param array $args
	 * @param array $overwrite
	 * @param bool $echo
	 * @param bool $error
	 *
	 * @return string
	 */
	public function form( $name, array $args = [], array $overwrite = [], $echo = true, $error = true );

	/**
	 * @param string $name
	 * @param mixed $data
	 * @param string|null $key
	 * @param string $default
	 * @param bool $checkbox
	 *
	 * @return mixed
	 */
	public function old( $name, $data, $key = null, $default = '', $checkbox = false );

	/**
	 * @param mixed $data
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function dump( $data, $echo = true );

	/**
	 * @param string $script
	 * @param int $priority
	 */
	public function add_script( $script, $priority = 10 );

	/**
	 * @param string $style
	 * @param int $priority
	 */
	public function add_style( $style, $priority = 10 );

	/**
	 * @param string $name
	 * @param array $args
	 * @param int $priority
	 */
	public function add_script_view( $name, array $args = [], $priority = 10 );

	/**
	 * @param string $name
	 * @param array $args
	 * @param int $priority
	 */
	public function add_style_view( $name, array $args = [], $priority = 10 );

	/**
	 * @param string $value
	 * @param bool $translate
	 * @param bool $echo
	 * @param bool $escape
	 * @param array $args
	 *
	 * @return string
	 */
	public function h( $value, $translate = false, $echo = true, $escape = true, ...$args );

	/**
	 * @param mixed $value
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function json( $value, $echo = true );

	/**
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function id( $echo = true );

	/**
	 * @param array $data
	 * @param bool $echo
	 *
	 * @return int
	 */
	public function n( array $data, $echo = true );

	/**
	 * @param string $url
	 * @param string $contents
	 * @param bool $translate
	 * @param bool $new_tab
	 * @param array $args
	 * @param bool $echo
	 * @param bool $escape
	 *
	 * @return string
	 */
	public function url( $url, $contents, $translate = false, $new_tab = false, array $args = [], $echo = true, $escape = true );

	/**
	 * @param string $path
	 * @param string|null $default
	 * @param bool $append_version
	 *
	 * @return string
	 */
	public function get_assets_url( $path, $default = null, $append_version = true );

	/**
	 * @param string $path
	 * @param string|null $default
	 * @param bool $append_version
	 *
	 * @return string
	 */
	public function get_upload_assets_url( $path, $default = null, $append_version = true );

	/**
	 * @param string $path
	 * @param string|null $default
	 * @param bool $append_version
	 *
	 * @return string
	 */
	public function get_img_url( $path, $default = null, $append_version = true );

	/**
	 * @param string $url
	 * @param string $view
	 * @param array $args
	 * @param string $field
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function assets( $url, $view, array $args, $field, $echo = true );

	/**
	 * @param string $path
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function img( $path, array $args = [], $echo = true );

	/**
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function loading( array $args = [], $echo = true );

	/**
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function no_img( array $args = [], $echo = true );

	/**
	 * @param null|int|WP_Post $post
	 * @param array $args
	 * @param string|array $size
	 *
	 * @return string
	 */
	public function get_thumbnail( $post = null, array $args = [], $size = 'post-thumbnail' );

	/**
	 * @param string $path
	 * @param int $priority
	 * @param bool $use_upload_dir
	 *
	 * @return bool
	 */
	public function css( $path, $priority = 10, $use_upload_dir = false );

	/**
	 * @param string $path
	 * @param int $priority
	 * @param bool $use_upload_dir
	 *
	 * @return bool
	 */
	public function js( $path, $priority = 10, $use_upload_dir = false );

	/**
	 * @param string $handle
	 * @param string $file
	 * @param array $depends
	 * @param string|bool|null $ver
	 * @param string $media
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function enqueue_style( $handle, $file, array $depends = [], $ver = false, $media = 'all', $dir = 'css' );

	/**
	 * @param string $handle
	 * @param string $file
	 * @param callable $generator
	 * @param array $depends
	 * @param string|bool|null $ver
	 * @param string $media
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function enqueue_upload_style( $handle, $file, $generator, array $depends = [], $ver = false, $media = 'all', $dir = 'css' );

	/**
	 * @param string $handle
	 * @param string $file
	 * @param array $depends
	 * @param string|bool|null $ver
	 * @param bool $in_footer
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function enqueue_script( $handle, $file, array $depends = [], $ver = false, $in_footer = true, $dir = 'js' );

	/**
	 * @param string $handle
	 * @param string $file
	 * @param callable $generator
	 * @param array $depends
	 * @param string|bool|null $ver
	 * @param bool $in_footer
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function enqueue_upload_script( $handle, $file, $generator, array $depends = [], $ver = false, $in_footer = true, $dir = 'js' );

	/**
	 * @param string $handle
	 * @param string $name
	 * @param array $data
	 *
	 * @return bool
	 */
	public function localize_script( $handle, $name, array $data );

	/**
	 * setup modal
	 */
	public function setup_modal();

	/**
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function modal_class( $echo = true );

	/**
	 * setup color picker
	 */
	public function setup_color_picker();

	/**
	 * @return string
	 */
	public function get_color_picker_class();

	/**
	 * setup dashicon picker
	 */
	public function setup_dashicon_picker();

	/**
	 * @return string
	 */
	public function get_dashicon_picker_class();

	/**
	 * setup media uploader
	 */
	public function setup_media_uploader();

	/**
	 * @return string
	 */
	public function get_media_uploader_class();

	/**
	 * @param string $handle
	 */
	public function set_script_translations( $handle );

	/**
	 * @param string $type
	 * @param bool $parse_db_type
	 *
	 * @return string
	 */
	public function get_form_by_type( $type, $parse_db_type = true );

	/**
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function convert_select_value( $value );

}
