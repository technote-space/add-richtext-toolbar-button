<?php
/**
 * WP_Framework_Common Deprecated Classes Models Utility
 *
 * @version 0.0.43
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Deprecated\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Utility
 * @package WP_Framework_Common\Deprecated\Classes\Models
 */
class Utility implements \WP_Framework_Core\Interfaces\Singleton {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Common\Traits\Package;

	/**
	 * @param string $name
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function __call( $name, array $args ) {
		array_shift( $args );
		switch ( $name ) {
			case 'flatten':
				return $this->app->array->flatten( ...$args );
			case 'get_array_value':
				return $this->app->array->to_array( ...$args );
			case 'array_wrap':
			case 'array_get':
			case 'array_search':
			case 'array_set':
			case 'array_pluck':
			case 'array_map':
			case 'array_pluck_unique':
			case 'array_combine':
				$name = substr( $name, 6 );

				return $this->app->array->{$name}( ...$args );
			case 'replace':
			case 'replace_time':
			case 'explode':
			case 'starts_with':
			case 'ends_with':
			case 'contains':
			case 'lower':
			case 'camel':
			case 'studly':
			case 'snake':
			case 'kebab':
			case 'strip_tags':
				return $this->app->string->{$name}( ...$args );
			case 'delete_upload_dir':
			case 'upload_file_exists':
			case 'create_upload_file':
			case 'create_upload_file_if_not_exists':
			case 'delete_upload_file':
			case 'get_upload_file_contents':
			case 'get_upload_file_url':
			case 'scan_dir_namespace_class':
				return $this->app->file->{$name}( ...$args );
			case 'file_exists':
				return $this->app->file->exists( ...$args );
		}

		\WP_Framework::wp_die( sprintf( 'you cannot access utility->%s', $name ), __FILE__, __LINE__ );

		return null;
	}
}
