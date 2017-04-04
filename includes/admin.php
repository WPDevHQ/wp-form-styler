<?php
namespace ElementorWPFormStyler;

use Elementor\Settings;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Admin {

	/**
	 * Enqueue admin styles.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		$suffix = Utils::is_script_debug() ? '' : '.min';

		$direction_suffix = is_rtl() ? '-rtl' : '';

		wp_register_style(
			'elementor-show-admin',
			ELEMENTOR_SHOW_ASSETS_URL . 'css/admin' . $direction_suffix . $suffix . '.css',
			Plugin::instance()->get_version()
		);

		wp_enqueue_style( 'elementor-show-admin' );
	}

	public function enqueue_scripts() {
		$suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			'elementor-show-admin',
			ELEMENTOR_SHOW_URL . 'assets/js/admin' . $suffix . '.js',
			[],
			Plugin::instance()->get_version(),
			true
		);

		wp_localize_script(
			'elementor-show-admin',
			'ElementorWPFormStylerConfig',
			Plugin::instance()->get_localize_settings()
		);
	}

	public function remove_go_woostore_menu() {
		remove_action( 'admin_menu', [ \Elementor\Plugin::instance()->settings, 'register_woostore_menu' ], Settings::MENU_PRIORITY_GO_PRO );
	}

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_menu', [ $this, 'remove_go_woostore_menu' ], 0 );
	}
}
