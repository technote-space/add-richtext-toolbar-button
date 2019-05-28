<?php
/**
 * WP_Framework_Common Classes Models User
 *
 * @version 0.0.49
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;
use WP_User;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class User
 * @package WP_Framework_Common\Classes\Models
 * @property-read int $user_id
 * @property-read WP_User $user_data
 * @property-read int $user_level
 * @property-read bool $super_admin
 * @property-read string $user_name
 * @property-read string $display_name
 * @property-read string $user_email
 * @property-read bool $logged_in
 * @property-read string|false $user_role
 * @property-read array $user_roles
 * @property-read array $user_caps
 */
class User implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Common\Interfaces\Uninstall {

	use Singleton, Hook, \WP_Framework_Common\Traits\Uninstall, Package;

	/**
	 * @var array $readonly_properties
	 */
	protected $readonly_properties = [
		'user_id',
		'user_data',
		'user_level',
		'super_admin',
		'user_name',
		'display_name',
		'user_email',
		'logged_in',
		'user_role',
		'user_roles',
		'user_caps',
	];

	/**
	 * initialize
	 */
	protected function initialize() {
		$cache = $this->app->get_shared_object( '_user_info_cache', 'all' );
		if ( ! isset( $cache ) ) {
			$cache = $this->get_user_data();
			$this->app->set_shared_object( '_user_info_cache', $cache, 'all' );
		}
		foreach ( $cache as $k => $v ) {
			$this->$k = $v;
		}
	}

	/**
	 * @return array
	 */
	private function get_user_data() {
		global $user_ID;
		$current_user = wp_get_current_user();

		$data = [];
		if ( $user_ID ) {
			$data['user_data']   = get_userdata( $user_ID );
			$data['user_level']  = $data['user_data']->user_level;
			$data['super_admin'] = is_super_admin( $user_ID );
		} else {
			$data['user_data']   = $current_user;
			$data['user_level']  = 0;
			$data['super_admin'] = false;
		}
		$data['user_id']      = $data['user_data']->ID;
		$data['user_name']    = $data['user_data']->user_login;
		$data['display_name'] = $data['user_data']->display_name;
		$data['user_email']   = $data['user_data']->user_email;
		$data['logged_in']    = is_user_logged_in();
		if ( empty( $data['user_name'] ) ) {
			$data['user_name'] = $this->app->input->ip();
		}
		if ( $data['logged_in'] && ! empty( $data['user_data']->roles ) ) {
			$roles              = array_values( $data['user_data']->roles );
			$data['user_roles'] = $roles;
			$data['user_role']  = $roles[0];
		} else {
			$data['user_roles'] = [];
			$data['user_role']  = false;
		}
		$data['user_caps'] = [];
		foreach ( $data['user_roles'] as $r ) {
			$role = get_role( $r );
			if ( $role ) {
				$data['user_caps'] = array_merge( $data['user_caps'], $role->capabilities );
			}
		}

		return $data;
	}

	/**
	 * reset
	 */
	public function reset_user_data() {
		$data = $this->get_user_data();
		$this->app->set_shared_object( '_user_info_cache', $data, 'all' );
		foreach ( $data as $k => $v ) {
			$this->$k = $v;
		}
	}

	/**
	 * @return string
	 */
	private function get_user_prefix() {
		return $this->get_slug( 'user_prefix', '_user' ) . '-';
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_meta_key( $key ) {
		return $this->get_user_prefix() . $key;
	}

	/**
	 * @param string $key
	 * @param int|null $user_id
	 * @param bool $single
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $user_id = null, $single = true, $default = '' ) {
		if ( ! isset( $user_id ) ) {
			$user_id = $this->user_id;
		}
		if ( $user_id <= 0 ) {
			return $this->apply_filters( 'get_user_meta', $default, $key, $user_id, $single, $default );
		}

		return $this->apply_filters( 'get_user_meta', get_user_meta( $user_id, $this->get_meta_key( $key ), $single ), $key, $user_id, $single, $default, $this->get_user_prefix() );
	}

	/**
	 * @param $key
	 * @param $value
	 * @param int|null $user_id
	 *
	 * @return bool|int
	 */
	public function set( $key, $value, $user_id = null ) {
		if ( ! isset( $user_id ) ) {
			$user_id = $this->user_id;
		}
		if ( $user_id <= 0 ) {
			return false;
		}

		return update_user_meta( $user_id, $this->get_meta_key( $key ), $value );
	}

	/**
	 * @param string $key
	 * @param int|null $user_id
	 * @param mixed $meta_value
	 *
	 * @return bool
	 */
	public function delete( $key, $user_id = null, $meta_value = '' ) {
		if ( ! isset( $user_id ) ) {
			$user_id = $this->user_id;
		}
		if ( $user_id <= 0 ) {
			return false;
		}

		return delete_user_meta( $user_id, $this->get_meta_key( $key ), $meta_value );
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function set_all( $key, $value ) {
		/** @noinspection SqlResolve */
		$query = $this->wpdb()->prepare( "UPDATE {$this->get_wp_table('usermeta')} SET meta_value = %s WHERE meta_key LIKE %s", $value, $this->get_meta_key( $key ) );
		$this->wpdb()->query( $query );
	}

	/**
	 * @param string $key
	 */
	public function delete_all( $key ) {
		/** @noinspection SqlResolve */
		$query = $this->wpdb()->prepare( "DELETE FROM {$this->get_wp_table('usermeta')} WHERE meta_key LIKE %s", $this->get_meta_key( $key ) );
		$this->wpdb()->query( $query );
	}

	/**
	 * @param string $key
	 * @param string $value
	 *
	 * @return bool
	 */
	public function delete_matched( $key, $value ) {
		$user_ids = $this->find( $key, $value );
		if ( empty( $user_ids ) ) {
			return true;
		}
		foreach ( $user_ids as $user_id ) {
			$this->delete( $key, $user_id );
		}

		return true;
	}

	/**
	 * @param string $key
	 * @param string $value
	 *
	 * @return array
	 */
	public function find( $key, $value ) {
		/** @noinspection SqlResolve */
		$query   = <<< SQL
			SELECT * FROM {$this->get_wp_table( 'usermeta' )}
			WHERE meta_key LIKE %s
			AND   meta_value LIKE %s
SQL;
		$results = $this->wpdb()->get_results( $this->wpdb()->prepare( $query, $this->get_meta_key( $key ), $value ) );

		return $this->apply_filters( 'find_user_meta', $this->app->array->pluck( $results, 'user_id' ), $key, $value );
	}

	/**
	 * @param string $key
	 * @param string $value
	 *
	 * @return false|int
	 */
	public function first( $key, $value ) {
		$user_ids = $this->find( $key, $value );
		if ( empty( $user_ids ) ) {
			return false;
		}

		return reset( $user_ids );
	}

	/**
	 * @param string $key
	 *
	 * @return array
	 */
	public function get_meta_user_ids( $key ) {
		/** @noinspection SqlResolve */
		$query   = <<< SQL
		SELECT user_id FROM {$this->get_wp_table( 'usermeta' )}
		WHERE meta_key LIKE %s
SQL;
		$results = $this->wpdb()->get_results( $this->wpdb()->prepare( $query, $this->get_meta_key( $key ) ) );

		return $this->apply_filters( 'get_meta_user_ids', $this->app->array->pluck( $results, 'user_id' ), $key );
	}

	/**
	 * @param null|string|false $capability
	 *
	 * @return bool
	 */
	public function user_can( $capability = null ) {
		if ( ! isset( $capability ) ) {
			$capability = $this->app->get_config( 'capability', 'default_user', 'manage_options' );
		}
		if ( false === $capability ) {
			return true;
		}
		if ( '' === $capability ) {
			return false;
		}

		return $this->has_cap( $capability ) || in_array( $capability, $this->user_roles );
	}

	/**
	 * @param string $capability
	 *
	 * @return bool
	 */
	public function has_cap( $capability ) {
		return ! empty( $this->user_caps[ $capability ] );
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		/** @noinspection SqlResolve */
		$query = $this->wpdb()->prepare( "DELETE FROM {$this->get_wp_table('usermeta')} WHERE meta_key LIKE %s", $this->get_user_prefix() . '%' );
		$this->wpdb()->query( $query );
	}

	/**
	 * @return int
	 */
	public function get_uninstall_priority() {
		return 100;
	}
}
