<?php
/**
 * WP_Framework_Common Classes Models System
 *
 * @version 0.0.50
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Common\Classes\Models;

use WP_Framework_Common\Traits\Package;
use WP_Framework_Core\Traits\Hook;
use WP_Framework_Core\Traits\Singleton;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class System
 * @package WP_Framework_Common\Classes\Models
 * @property-read string $required_php_version
 * @property-read string $required_wordpress_version
 * @property-read bool $not_enough_php_version
 * @property-read bool $not_enough_wordpress_version
 */
class System implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook {

	use Singleton, Hook, Package;

	/**
	 * @var bool $_setup_initialized_action
	 */
	private static $_setup_initialized_action = false;

	/**
	 * @var array $readonly_properties
	 */
	protected $readonly_properties = [
		'required_php_version',
		'required_wordpress_version',
		'not_enough_php_version',
		'not_enough_wordpress_version',
	];

	/**
	 * initialize
	 */
	protected function initialize() {
		global $wp_version;
		$this->required_php_version         = $this->app->get_config( 'config', 'required_php_version' );
		$this->required_wordpress_version   = $this->app->get_config( 'config', 'required_wordpress_version' );
		$this->not_enough_php_version       = version_compare( phpversion(), $this->required_php_version, '<' );
		$this->not_enough_wordpress_version = empty( $wp_version ) || version_compare( $wp_version, $this->required_wordpress_version, '<' );
		if ( ! $this->is_enough_version() ) {
			$this->set_unsupported();
			if ( $this->app->is_theme ) {
				$this->deactivate_theme();
			}
		} elseif ( $this->app->is_uninstall() ) {
			$this->app_initialized();
		} elseif ( ! self::$_setup_initialized_action ) {
			self::$_setup_initialized_action = true;
			add_action( 'init', function () {
				$this->do_framework_action( 'initialize' );
			}, 9 );
		}
	}

	/**
	 * app initialized
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function app_initialized() {
		if ( ! $this->is_enough_version() ) {
			return;
		}

		if ( $this->app->is_uninstall() ) {
			$this->app->load_all_packages();
			$this->app->uninstall->get_class_list();
		}
		$this->do_action( 'app_initialized', $this->app );

		if ( ! $this->app->option->is_app_activated() ) {
			$this->do_action( 'app_activated', $this->app );
		}
	}

	/**
	 * deactivate theme
	 */
	private function deactivate_theme() {
		add_action( 'init', function () {
			switch_theme( WP_DEFAULT_THEME );
			unset( $_GET['activated'] );
		}, 999 );
	}

	/**
	 * @return bool
	 */
	public function is_enough_version() {
		return ! $this->not_enough_php_version && ! $this->not_enough_wordpress_version;
	}

	/**
	 * set unsupported
	 */
	private function set_unsupported() {
		add_action( 'admin_notices', function () {
			?>
            <div class="notice error notice-error">
				<?php if ( $this->not_enough_php_version ): ?>
                    <p>
						<?php $this->e( $this->get_unsupported_php_version_message() ); ?>
                    </p>
				<?php endif; ?>
				<?php if ( $this->not_enough_wordpress_version ): ?>
                    <p>
						<?php $this->e( $this->get_unsupported_wp_version_message() ); ?>
                    </p>
				<?php endif; ?>
				<?php if ( ! $this->app->is_theme ): ?>
                    <p>
						<?php $this->e( $this->get_deactivate_message() ); ?>
                    </p>
				<?php endif; ?>
            </div>
			<?php
		} );
	}

	/**
	 * @return string
	 */
	private function get_unsupported_php_version_message() {
		$messages   = [];
		$messages[] = sprintf( $this->translate( 'Your PHP version is %s.' ), phpversion() );
		$messages[] = $this->translate( 'Please update your PHP.' );
		$messages[] = sprintf( $this->translate( '<strong>%s</strong> requires PHP version %s or above.' ), $this->translate( $this->app->original_plugin_name ), $this->required_php_version );

		return implode( '<br>', $messages );
	}

	/**
	 * @return string
	 */
	private function get_unsupported_wp_version_message() {
		global $wp_version;
		$messages   = [];
		$messages[] = sprintf( $this->translate( 'Your WordPress version is %s.' ), $wp_version );
		$messages[] = $this->translate( 'Please update your WordPress.' );
		$messages[] = sprintf( $this->translate( '<strong>%s</strong> requires WordPress version %s or above.' ), $this->translate( $this->app->original_plugin_name ), $this->required_wordpress_version );

		return implode( '<br>', $messages );
	}

	/**
	 * @return string
	 */
	private function get_deactivate_message() {
		$url   = wp_nonce_url( add_query_arg( [
			'action' => 'deactivate',
			'plugin' => urlencode( $this->app->define->plugin_base_name ),
		], 'plugins.php' ), 'deactivate-plugin_' . $this->app->define->plugin_base_name );
		$label = $this->translate( 'Deactivate plugin' );

		return '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label ) . '">' . $label . '</a>';
	}

	/**
	 * @param string $string
	 */
	private function e( $string ) {
		echo $this->app->string->strip_tags( $string );
	}
}
