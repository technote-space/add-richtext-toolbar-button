<?php
/**
 * WP_Framework_Common Classes Models File Utility
 *
 * @version 0.0.29
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class File_Utility
 * @package WP_Framework_Common\Classes\Models
 */
class File_Utility implements \WP_Framework_Core\Interfaces\Singleton {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Common\Traits\Package;

	/**
	 * @return bool
	 */
	protected static function is_shared_class() {
		return true;
	}

	/**
	 * @param \WP_Framework $app
	 *
	 * @return bool
	 */
	public function delete_upload_dir( \WP_Framework $app ) {
		return $this->delete_dir( $app->define->upload_dir );
	}

	/**
	 * @see https://qiita.com/algo13/items/34bb9750f0e450109a03
	 *
	 * @param $dir
	 *
	 * @return bool
	 */
	private function delete_dir( $dir ) {
		clearstatcache( true, $dir );
		if ( is_file( $dir ) ) {
			return @unlink( $dir );
		} elseif ( is_link( $dir ) ) {
			return @unlink( $dir ) || ( '\\' === DS && @rmdir( $dir ) );
		} elseif ( $this->is_junction( $dir ) ) {
			return @rmdir( $dir );
		} elseif ( is_dir( $dir ) ) {
			$failed = false;
			foreach ( new \FilesystemIterator( $dir ) as $file ) {
				/** @var \DirectoryIterator $file */
				if ( ! $this->delete_dir( $file->getPathname() ) ) {
					$failed = true;
				}
			}

			return ! $failed && @rmdir( $dir );
		}

		return true;
	}

	/**
	 * @param string $check
	 *
	 * @return bool
	 */
	private function is_junction( $check ) {
		if ( '\\' !== DS ) {
			return false;
		}

		$stat = @lstat( $check );

		return $stat !== false && ! ( $stat['mode'] & 0xC000 );
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	public function exists( $path ) {
		return file_exists( $path );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 *
	 * @return string
	 */
	private function get_upload_file_path( \WP_Framework $app, $path ) {
		return $app->define->upload_dir . DS . ltrim( str_replace( '/', DS, $path ), DS );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 *
	 * @return string
	 */
	private function get_upload_file_link( \WP_Framework $app, $path ) {
		return $app->define->upload_url . '/' . ltrim( str_replace( DS, '/', $path ), '/' );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 *
	 * @return bool
	 */
	public function upload_file_exists( \WP_Framework $app, $path ) {
		return $this->exists( $this->get_upload_file_path( $app, $path ) );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 * @param mixed $data
	 *
	 * @throws \Exception
	 */
	public function create_upload_file( \WP_Framework $app, $path, $data ) {
		$path = $this->get_upload_file_path( $app, $path );
		@mkdir( dirname( $path ), 0700, true );
		if ( false === @file_put_contents( $path, $data, 0644 ) ) {
			throw new \Exception( 'Failed to create .htaccess file.' );
		}
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 * @param callable $generator
	 *
	 * @return bool
	 */
	public function create_upload_file_if_not_exists( \WP_Framework $app, $path, $generator ) {
		if ( ! $this->upload_file_exists( $app, $path ) ) {
			if ( isset( $generator ) && is_callable( $generator ) ) {
				try {
					$this->create_upload_file( $app, $path, $generator() );
				} catch ( \Exception $e ) {
					return false;
				}
			} else {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 *
	 * @return bool
	 */
	public function delete_upload_file( \WP_Framework $app, $path ) {
		return @unlink( $this->get_upload_file_path( $app, $path ) );
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 * @param callable|null $generator
	 *
	 * @return bool|string
	 */
	public function get_upload_file_contents( \WP_Framework $app, $path, $generator = null ) {
		if ( $this->create_upload_file_if_not_exists( $app, $path, $generator ) ) {
			return @file_get_contents( $this->get_upload_file_path( $app, $path ) );
		}

		return false;
	}

	/**
	 * @param \WP_Framework $app
	 * @param string $path
	 * @param callable|null $generator
	 *
	 * @return string|false
	 */
	public function get_upload_file_url( \WP_Framework $app, $path, $generator = null ) {
		if ( $this->create_upload_file_if_not_exists( $app, $path, $generator ) ) {
			return $this->get_upload_file_link( $app, $path );
		}

		return false;
	}
}
