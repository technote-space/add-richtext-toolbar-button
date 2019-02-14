<?php
/**
 * WP_Framework_Log Classes Models Log
 *
 * @version 0.0.7
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Log\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Log
 * @package WP_Framework_Log\Classes\Models
 */
class Log implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Log\Traits\Package;

	/**
	 * setup shutdown
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_shutdown() {
		if ( $this->apply_filters( 'capture_shutdown_error' ) && $this->is_valid() ) {
			add_action( 'shutdown', function () {
				$this->shutdown();
			}, 0 );
		}
	}

	/**
	 * setup settings
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_settings() {
		if ( ! $this->is_valid() ) {
			$this->app->setting->remove_setting( 'save_log_term' );
			$this->app->setting->remove_setting( 'delete_log_interval' );
			$this->app->setting->remove_setting( 'capture_shutdown_error' );
		}
	}

	/**
	 * shutdown
	 */
	private function shutdown() {
		$error = error_get_last();
		if ( $error === null ) {
			return;
		}

		if ( $error['type'] & $this->app->get_config( 'config', 'target_shutdown_error' ) ) {
			$suppress = $this->app->get_config( 'config', 'suppress_log_messages' );
			$message  = str_replace( [ "\r\n", "\r", "\n" ], "\n", $error['message'] );
			$messages = explode( "\n", $message );
			$message  = reset( $messages );
			if ( empty( $suppress ) || ( is_array( $suppress ) && ! in_array( $message, $suppress ) ) ) {
				$this->app->log( $message, $error, 'error' );
			}
		}
	}

	/**
	 * @since 0.0.2 #2
	 * @return bool
	 */
	public function is_valid() {
		return $this->apply_filters( 'is_valid_log' );
	}

	/**
	 * @param string $message
	 * @param mixed $context
	 * @param string $level
	 *
	 * @return bool
	 */
	public function log( $message, $context = null, $level = '' ) {
		if ( ! $this->is_valid() ) {
			return false;
		}

		$log_level = $this->app->get_config( 'config', 'log_level' );
		$level     = $this->get_log_level( $level, $log_level );
		if ( empty( $log_level[ $level ] ) ) {
			return false;
		}

		global $wp_version;
		$data                       = $this->get_called_info();
		$data['message']            = is_string( $message ) ? $this->translate( $message ) : json_encode( $message );
		$data['framework_version']  = $this->app->get_framework_version();
		$data['plugin_version']     = $this->app->get_plugin_version();
		$data['php_version']        = phpversion();
		$data['wordpress_version']  = $wp_version;
		$data['level']              = $level;
		$data['framework_packages'] = json_encode( $this->app->utility->array_combine( array_map( function ( $package ) {
			/** @var \WP_Framework\Package_Base $package */
			return [
				'version' => $package->get_version(),
				'package' => $package->get_package(),
			];
		}, $this->app->get_packages() ), 'package', 'version' ) );
		if ( isset( $context ) ) {
			$data['context'] = json_encode( $context );
		}

		$this->send_mail( $level, $log_level, $message, $data );
		$this->insert_log( $level, $log_level, $data );

		return true;
	}

	/**
	 * @param string $level
	 * @param array $log_level
	 *
	 * @return string
	 */
	private function get_log_level( $level, array $log_level ) {
		if ( ! isset( $log_level[ $level ] ) && ! isset( $log_level[''] ) ) {
			return 'info';
		}
		'' === $level || ! isset( $log_level[ $level ] ) and $level = $log_level[''];
		if ( empty( $log_level[ $level ] ) ) {
			return 'info';
		}

		return $level;
	}

	/**
	 * @param string $level
	 * @param array $log_level
	 * @param array $data
	 */
	private function insert_log( $level, array $log_level, array $data ) {
		if ( empty( $log_level[ $level ]['is_valid_log'] ) ) {
			return;
		}
		if ( $this->apply_filters( 'save_log_term' ) <= 0 ) {
			return;
		}
		$db = $this->app->db;
		if ( $db ) {
			$db->insert( '__log', $data );
		}
	}

	/**
	 * @param string $level
	 * @param array $log_level
	 * @param string $message
	 * @param array $data
	 */
	private function send_mail( $level, array $log_level, $message, array $data ) {
		if ( empty( $log_level[ $level ]['is_valid_mail'] ) ) {
			return;
		}

		$level   = $log_level[ $level ];
		$roles   = $this->app->utility->array_get( $level, 'roles', [] );
		$emails  = $this->app->utility->array_get( $level, 'emails', [] );
		$filters = $this->app->utility->array_get( $level, 'filters', [] );
		empty( $roles ) and $roles = [];
		empty( $emails ) and $emails = [];
		empty( $filters ) and $filters = [];
		if ( empty( $roles ) && empty( $emails ) && empty( $filters ) ) {
			return;
		}

		! is_array( $roles ) and $roles = [ $roles ];
		! is_array( $emails ) and $emails = [ $emails ];
		! is_array( $filters ) and $filters = [ $filters ];
		$emails = array_unique( $emails );
		$emails = array_combine( $emails, $emails );
		foreach ( $roles as $role ) {
			foreach ( get_users( [ 'role' => $role ] ) as $user ) {
				/** @var \WP_User $user */
				! empty( $user->user_email ) and $emails[ $user->user_email ] = $user->user_email;
			}
		}
		foreach ( $filters as $filter ) {
			$items = $this->apply_filters( $filter );
			if ( empty( $items ) ) {
				continue;
			}
			! is_array( $items ) and $items = $this->app->utility->explode( $items );
			foreach ( $items as $item ) {
				if ( ! empty( $item ) && is_string( $item ) && is_email( $item ) ) {
					$emails[ $item ] = $item;
				}
			}
		}

		foreach ( $emails as $email ) {
			$this->app->send_mail( $email, $message, $this->dump( $data, false ) );
		}
	}

	/**
	 * @return array
	 */
	private function get_called_info() {
		$next = false;
		foreach ( $this->app->utility->get_debug_backtrace() as $item ) {
			if ( $next ) {
				$return = [];
				isset( $item['file'] ) and $return['file'] = preg_replace( '/' . preg_quote( ABSPATH, '/' ) . '/A', '', $item['file'] );
				isset( $item['line'] ) and $return['line'] = $item['line'];

				return $return;
			}
			if ( ! empty( $item['class'] ) && __CLASS__ === $item['class'] && $item['function'] === 'log' ) {
				$next = true;
			}
		}

		return [];
	}

	/**
	 * @return int
	 */
	public function delete_old_logs() {
		$count = 0;
		$term  = $this->apply_filters( 'save_log_term' );
		foreach (
			$this->app->db->select( '__log', [
				'created_at' => [ '<', 'NOW() - INTERVAL ' . (int) $term . ' SECOND', true ],
			] ) as $log
		) {
			$this->app->db->delete( '__log', [
				'id' => $log['id'],
			] );
			$count ++;
		}

		return $count;
	}
}
