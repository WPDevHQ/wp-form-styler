<?php
namespace ElementorShow\License;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Updater {

	public $plugin_version;
	public $plugin_name;
	public $plugin_slug;

	private $response_transient_key;

	public function __construct() {
		$this->plugin_version = ELEMENTOR_SHOW_VERSION;
		$this->plugin_name = ELEMENTOR_SHOW_PLUGIN_BASE;
		$this->plugin_slug = basename( ELEMENTOR_SHOW__FILE__, '.php' );
		$this->response_transient_key = md5( sanitize_key( $this->plugin_name ) . 'response_transient' );

		$this->setup_hooks();
		$this->maybe_delete_transients();
	}

	private function setup_hooks() {
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
		add_filter( 'plugins_api', [ $this, 'plugins_api_filter' ], 10, 3 );

		remove_action( 'after_plugin_row_' . $this->plugin_name, 'wp_plugin_update_row' );
		add_action( 'after_plugin_row_' . $this->plugin_name, [ $this, 'show_update_notification' ], 10, 2 );
	}

	private function maybe_delete_transients() {
		global $pagenow;

		if ( 'update-core.php' === $pagenow && isset( $_GET['force-check'] ) ) {
			delete_transient( $this->response_transient_key );
		}
	}

	private function check_transient_data( $_transient_data ) {
		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new \stdClass;
		}

		$version_info = get_transient( $this->response_transient_key . 'ss' );
		if ( false === $version_info ) {
			$version_info = API::get_version();

			if ( is_wp_error( $version_info ) ) {
				$version_info = new \stdClass();
				$version_info->error = true;
			}

			set_transient( $this->response_transient_key, $version_info, 12 * HOUR_IN_SECONDS );
		}

		if ( ! empty( $version_info->error ) ) {
			return $_transient_data;
		}

		if ( version_compare( $this->plugin_version, $version_info['new_version'], '<' ) ) {
			$_transient_data->response[ $this->plugin_name ] = (object) $version_info;
		}

		$_transient_data->last_checked = current_time( 'timestamp' );
		$_transient_data->checked[ $this->plugin_name ] = $this->plugin_version;

		return $_transient_data;
	}

	public function check_update( $_transient_data ) {
		global $pagenow;

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new \stdClass;
		}

		if ( 'plugins.php' === $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		return $this->check_transient_data( $_transient_data );
	}

	public function plugins_api_filter( $_data, $_action = '', $_args = null ) {
		if ( 'plugin_information' !== $_action ) {
			return $_data;
		}

		if ( ! isset( $_args->slug ) || ( $_args->slug !== $this->plugin_slug ) ) {
			return $_data;
		}

		$cache_key = 'elementor_show_api_request_' . substr( md5( serialize( $this->plugin_slug ) ), 0, 15 );

		$api_request_transient = get_site_transient( $cache_key );

		if ( empty( $api_request_transient ) ) {
			$api_response = API::get_version();

			$_data = new \stdClass();

			$_data->name = 'Elementor WooStore';
			$_data->slug = $this->plugin_slug;
			$_data->author = '<a href="http://wpdevhq.com/">WPDevHQ</a>';
			$_data->homepage = 'http://wpdevhq.com/';

			$_data->version = $api_response['new_version'];
			$_data->last_updated = $api_response['last_updated'];
			$_data->download_link = $api_response['download_link'];
			$_data->banners = [];

			$_data->sections = unserialize( $api_response['sections'] );

			//Expires in 1 day
			set_site_transient( $cache_key, $_data, DAY_IN_SECONDS );
		}

		$_data = $api_request_transient;

		return $_data;
	}

	public function show_update_notification( $file, $plugin ) {
		if ( is_network_admin() ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		if ( ! is_multisite() ) {
			return;
		}

		if ( $this->plugin_name !== $file ) {
			return;
		}

		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );

		$update_cache = get_site_transient( 'update_plugins' );
		$update_cache = $this->check_transient_data( $update_cache );
		set_site_transient( 'update_plugins', $update_cache );

		// Restore our filter
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
	}
}
