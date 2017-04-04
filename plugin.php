<?php
namespace ElementorWPFormStyler;

use Elementor\Utils;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {	exit; } // Exit if accessed directly

/**
 * Main class plugin
 */
class Plugin {

	/**
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * @var Manager
	 */
	private $_modules_manager;

	/**
	 * @var array
	 */
	private $_localize_settings = [];

	/**
	 * @return string
	 */
	public function get_version() {
		return ELEMENTOR_WPFS_VERSION;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-form-styler' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-form-styler' ), '1.0.0' );
	}

	/**
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	private function _includes() {
		require ELEMENTOR_WPFS_PATH . 'includes/modules-manager.php';

		if ( is_admin() ) {
			require ELEMENTOR_WPFS_PATH . 'includes/admin.php';
		}
	}

	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$filename = strtolower(
			preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class
			)
		);
		$filename = ELEMENTOR_WPFS_PATH . $filename . '.php';

		if ( is_readable( $filename ) ) {
			include( $filename );
		}
	}

	public function get_localize_settings() {
		return $this->_localize_settings;
	}

	public function add_localize_settings( $setting_key, $setting_value = null ) {
		if ( is_array( $setting_key ) ) {
			$this->_localize_settings = array_replace_recursive( $this->_localize_settings, $setting_key );

			return;
		}

		if ( ! is_array( $setting_value ) || ! isset( $this->_localize_settings[ $setting_key ] ) || ! is_array( $this->_localize_settings[ $setting_key ] ) ) {
			$this->_localize_settings[ $setting_key ] = $setting_value;

			return;
		}

		$this->_localize_settings[ $setting_key ] = array_replace_recursive( $this->_localize_settings[ $setting_key ], $setting_value );
	}

	public function enqueue_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$direction_suffix = is_rtl() ? '-rtl' : '';

		wp_enqueue_style(
			'wp-form-styler',
			ELEMENTOR_WPFS_URL . 'assets/css/frontend' . $direction_suffix . $suffix . '.css',
			[],
			Plugin::instance()->get_version()
		);

		if ( is_admin() ) {
			wp_enqueue_style(
				'elementor-show-admin',
				ELEMENTOR_WPFS_URL . 'assets/css/admin' . $direction_suffix . $suffix . '.css',
				[],
				Plugin::instance()->get_version()
			);
		}
	}

	public function enqueue_scripts() {}

	public function enqueue_panel_scripts() {}

	public function enqueue_panel_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style(
			'wp-form-styler',
			ELEMENTOR_WPFS_URL . 'assets/css/editor' . $suffix . '.css',
			[
				'elementor-editor'
			],
			Plugin::instance()->get_version()
		);
	}

	public function elementor_init() {
		$this->_modules_manager = new Manager();

		// Add element category in panel
		\Elementor\Plugin::instance()->elements_manager->add_category(
			'wpfs-elements',
			[
				'title' => __( 'WPFS Elements', 'wp-form-styler' ),
				'icon' => 'font',
			],
			1
		);
	}

	protected function add_actions() {
		add_action( 'elementor/init', [ $this, 'elementor_init' ] );

		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_panel_styles' ], 997 );
		//add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_panel_scripts' ] );

		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 998 );
	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		$this->_includes();

		$this->add_actions();

		if ( is_admin() ) {
			new Admin();
			//new License\Admin();
		}
	}
}

if ( ! defined( 'ELEMENTOR_WPFS_TESTS' ) ) {
	// In tests we run the instance manually.
	Plugin::instance();
}
