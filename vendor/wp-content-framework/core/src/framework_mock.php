<?php
/**
 * WP_Framework mock
 *
 * @version 0.0.54
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}
define( 'WP_FRAMEWORK_IS_MOCK', true );

/**
 * Class WP_Framework
 */
class WP_Framework {

	/**
	 * @var array
	 */
	private static $_instances = [];

	/**
	 * @var bool $_framework_textdomain_loaded
	 */
	private static $_framework_textdomain_loaded = false;

	/**
	 * @var string $original_plugin_name
	 */
	private $original_plugin_name;

	/**
	 * @var string $plugin_name
	 */
	private $plugin_name;

	/**
	 * @var string $plugin_file
	 */
	private $plugin_file;

	/**
	 * @var string $plugin_dir
	 */
	private $plugin_dir;

	/**
	 * @var string $plugin_configs_dir
	 */
	private $plugin_configs_dir;

	/**
	 * @var string $textdomain
	 */
	private $textdomain;

	/**
	 * @var array $plugin_data
	 */
	private $plugin_data;

	/**
	 * @return mixed
	 */
	private function is_not_enough_php_version() {
		return version_compare( phpversion(), WP_FRAMEWORK_REQUIRED_PHP_VERSION, '<' );
	}

	/**
	 * @return mixed
	 */
	private function is_not_enough_wp_version() {
		global $wp_version;

		return version_compare( $wp_version, WP_FRAMEWORK_REQUIRED_WP_VERSION, '<' );
	}

	/**
	 * WP_Framework constructor.
	 *
	 * @param string $plugin_name
	 * @param string $plugin_file
	 */
	private function __construct( $plugin_name, $plugin_file ) {
		$this->original_plugin_name = $plugin_name;
		$this->plugin_name          = strtolower( $plugin_name );
		$this->plugin_file          = $plugin_file;
		$this->plugin_dir           = dirname( $this->plugin_file );
		$this->plugin_configs_dir   = $this->plugin_dir . DS . 'configs';

		if ( ! ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) ) {
			if ( $this->is_not_enough_php_version() || $this->is_not_enough_wp_version() ) {
				$this->set_unsupported();
			}
		}
	}

	/**
	 * @return string
	 */
	private function get_textdomain() {
		if ( ! isset( $this->textdomain ) ) {
			$domain_path = trim( $this->plugin_data['DomainPath'], '/' . DS );
			if ( empty( $domain_path ) ) {
				$this->textdomain          = false;
				$plugin_languages_rel_path = false;
			} else {
				$this->textdomain          = $this->plugin_data['TextDomain'];
				$plugin_languages_rel_path = ltrim( str_replace( WP_PLUGIN_DIR, '', $this->plugin_dir . DS . $domain_path ), DS );
			}

			if ( ! self::$_framework_textdomain_loaded ) {
				self::$_framework_textdomain_loaded = true;
				$framework_languages_rel_path       = ltrim( str_replace( WP_PLUGIN_DIR, '', dirname( dirname( WP_FRAMEWORK_BOOTSTRAP ) ) . DS . 'common' . DS . 'languages' ), DS );
				load_plugin_textdomain( 'wp_framework-common', false, $framework_languages_rel_path );
			}
			if ( ! empty( $this->textdomain ) ) {
				load_plugin_textdomain( $this->textdomain, false, $plugin_languages_rel_path );
			}
		}

		return $this->textdomain;
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	private function translate( $value ) {
		$textdomain = $this->get_textdomain();
		if ( ! empty( $textdomain ) ) {
			$translated = __( $value, $textdomain );
			if ( $value !== $translated ) {
				return $translated;
			}
		}

		return __( $value, 'wp_framework-common' );
	}

	/**
	 * @param string $plugin_name
	 * @param string $plugin_file
	 *
	 * @return WP_Framework
	 */
	public static function get_instance( $plugin_name, $plugin_file ) {
		if ( ! isset( self::$_instances[ $plugin_name ] ) ) {
			self::$_instances[ $plugin_name ] = new static( $plugin_name, $plugin_file );
		}

		return self::$_instances[ $plugin_name ];
	}

	/**
	 * set unsupported
	 */
	private function set_unsupported() {
		add_action( 'init', function () {
			$this->init();
		} );
		add_action( 'admin_notices', function () {
			$this->admin_notices();
		} );
	}

	/**
	 * init
	 */
	private function init() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->plugin_data = get_plugin_data( $this->plugin_file, false, false );
	}

	/**
	 * @return string
	 */
	private function get_unsupported_php_version_message() {
		$messages   = [];
		$messages[] = sprintf( $this->translate( 'Your PHP version is %s.' ), phpversion() );
		$messages[] = $this->translate( 'Please update your PHP.' );
		$messages[] = sprintf( $this->translate( '<strong>%s</strong> requires PHP version %s or above.' ), $this->translate( $this->original_plugin_name ), WP_FRAMEWORK_REQUIRED_PHP_VERSION );

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
		$messages[] = sprintf( $this->translate( '<strong>%s</strong> requires WordPress version %s or above.' ), $this->translate( $this->original_plugin_name ), WP_FRAMEWORK_REQUIRED_WP_VERSION );

		return implode( '<br>', $messages );
	}

	/**
	 * admin_notices
	 */
	private function admin_notices() {
		?>
        <div class="notice error notice-error">
			<?php if ( $this->is_not_enough_php_version() ): ?>
                <p><?php echo $this->get_unsupported_php_version_message(); ?></p>
			<?php endif; ?>
			<?php if ( $this->is_not_enough_wp_version() ): ?>
                <p><?php echo $this->get_unsupported_wp_version_message(); ?></p>
			<?php endif; ?>
        </div>
		<?php
	}
}

