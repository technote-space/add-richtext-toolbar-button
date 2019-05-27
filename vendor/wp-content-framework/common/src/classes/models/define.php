<?php
/**
 * WP_Framework_Common Classes Models Define
 *
 * @version 0.0.49
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Singleton;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Define
 * @package WP_Framework_Common\Classes\Models
 * @property-read string $plugin_name
 * @property-read string $plugin_file
 * @property-read string $plugin_namespace
 * @property-read string $plugin_dir
 * @property-read string $plugin_dir_name
 * @property-read string $plugin_base_name
 * @property-read string $plugin_assets_dir
 * @property-read string $plugin_src_dir
 * @property-read string $plugin_configs_dir
 * @property-read string $plugin_views_dir
 * @property-read string|false $plugin_textdomain
 * @property-read string|false $plugin_languages_dir
 * @property-read string $plugin_url
 * @property-read string $plugin_assets_url
 * @property-read string $child_theme_dir
 * @property-read string $child_theme_assets_dir
 * @property-read string $child_theme_url
 * @property-read string $child_theme_assets_url
 * @property-read string $child_theme_views_dir
 * @property-read string $upload_dir
 * @property-read string $upload_url
 * @property-read int $blog_id
 */
class Define implements \WP_Framework_Core\Interfaces\Singleton {

	use Singleton, Package;

	/**
	 * @var array $readonly_properties
	 */
	protected $readonly_properties = [
		'plugin_name',
		'plugin_file',
		'plugin_namespace',
		'plugin_dir',
		'plugin_dir_name',
		'plugin_base_name',
		'plugin_assets_dir',
		'plugin_src_dir',
		'plugin_configs_dir',
		'plugin_views_dir',
		'plugin_textdomain',
		'plugin_languages_dir',
		'plugin_url',
		'plugin_assets_url',
		'child_theme_dir',
		'child_theme_url',
		'child_theme_assets_dir',
		'child_theme_assets_url',
		'child_theme_views_dir',
		'upload_dir',
		'upload_url',
		'blog_id',
	];

	/**
	 * initialize
	 */
	protected function initialize() {
		$this->plugin_name = $this->app->plugin_name;
		$this->plugin_file = $this->app->plugin_file;
		$this->plugin_dir  = $this->app->plugin_dir;

		$this->plugin_namespace = ucwords( strtolower( $this->plugin_name ), '_' );
		$this->plugin_dir_name  = basename( $this->plugin_dir );
		$this->plugin_base_name = $this->app->is_theme ? 'theme/' . $this->plugin_dir : plugin_basename( $this->plugin_file );

		$this->plugin_assets_dir  = $this->plugin_dir . DS . 'assets';
		$this->plugin_src_dir     = $this->plugin_dir . DS . 'src';
		$this->plugin_configs_dir = $this->plugin_dir . DS . 'configs';
		$this->plugin_views_dir   = $this->plugin_src_dir . DS . 'views';
		$domain_path              = trim( $this->app->get_plugin_data( 'DomainPath' ), '/' . DS );
		if ( empty( $domain_path ) || ! is_dir( $this->plugin_dir . DS . $domain_path ) ) {
			$this->plugin_textdomain    = false;
			$this->plugin_languages_dir = false;
		} else {
			$this->plugin_textdomain    = $this->app->get_plugin_data( 'TextDomain' );
			$this->plugin_languages_dir = $this->plugin_dir . DS . $domain_path;
		}

		if ( $this->app->is_theme ) {
			$this->plugin_url = get_template_directory_uri();
			if ( get_template_directory() !== get_stylesheet_directory() ) {
				$this->child_theme_dir        = get_stylesheet_directory();
				$this->child_theme_url        = get_stylesheet_directory_uri();
				$this->child_theme_assets_dir = $this->child_theme_dir . DS . 'assets';
				$this->child_theme_assets_url = $this->child_theme_url . '/assets';
				$this->child_theme_views_dir  = $this->child_theme_dir . DS . 'src' . DS . 'views';
			}
		} else {
			$this->plugin_url = plugins_url( '', $this->plugin_file );
		}
		$this->plugin_assets_url = $this->plugin_url . '/assets';

		$this->blog_id = get_current_blog_id();
		if ( is_multisite() && defined( 'BLOG_ID_CURRENT_SITE' ) && $this->blog_id != BLOG_ID_CURRENT_SITE ) {
			$this->upload_dir = WP_CONTENT_DIR . DS . 'uploads' . DS . $this->plugin_name . $this->blog_id;
			$this->upload_url = WP_CONTENT_URL . '/uploads/' . $this->plugin_name . $this->blog_id;
		} else {
			$this->upload_dir = WP_CONTENT_DIR . DS . 'uploads' . DS . $this->plugin_name;
			$this->upload_url = WP_CONTENT_URL . '/uploads/' . $this->plugin_name;
		}
	}

	/**
	 * @param int $new_blog
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function switch_blog( $new_blog ) {
		if ( $new_blog === $this->blog_id ) {
			return;
		}

		$this->set_allowed_access( true );
		$this->blog_id = $new_blog;
		if ( is_multisite() && defined( 'BLOG_ID_CURRENT_SITE' ) && $this->blog_id != BLOG_ID_CURRENT_SITE ) {
			$this->upload_dir = WP_CONTENT_DIR . DS . 'uploads' . DS . $this->plugin_name . $this->blog_id;
			$this->upload_url = WP_CONTENT_URL . '/uploads/' . $this->plugin_name . $this->blog_id;
		} else {
			$this->upload_dir = WP_CONTENT_DIR . DS . 'uploads' . DS . $this->plugin_name;
			$this->upload_url = WP_CONTENT_URL . '/uploads/' . $this->plugin_name;
		}
		$this->set_allowed_access( false );
	}
}
