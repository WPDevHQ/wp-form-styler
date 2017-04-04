<?php
namespace ElementorShow\License;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class API {

	const PRODUCT_NAME = 'Elementor WooStore';

	const STORE_URL = 'http://my.wpdevhq.com/api/v1/licenses/';

	// License Statuses
	const STATUS_VALID = 'valid';
	const STATUS_INVALID = 'invalid';
	const STATUS_EXPIRED = 'expired';
	const STATUS_DEACTIVATED = 'deactivated';
	const STATUS_SITE_INACTIVE = 'site_inactive';
	const STATUS_DISABLED = 'disabled';

	/**
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	private static function _remote_post( $body_args = [] ) {
		$body_args = wp_parse_args(
			$body_args,
			[
				'api_version' => ELEMENTOR_SHOW_VERSION,
				'item_name' => self::PRODUCT_NAME,
				'site_lang' => get_bloginfo( 'language' ),
				'url' => home_url(),
			]
		);

		$response = wp_remote_post( self::STORE_URL, [
			'sslverify' => false,
			'timeout' => 40,
			'body' => $body_args,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, __( 'HTTP Error', 'elementor-show' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'no_json', __( 'An error occurred, please try again', 'elementor-show' ) );
		}

		return $data;
	}

	public static function activate_license( $license_key ) {
		$body_args = [
			'edd_action' => 'activate_license',
			'license' => $license_key,
		];

		$license_data = self::_remote_post( $body_args );

		return $license_data;
	}

	public static function deactivate_license() {
		$body_args = [
			'edd_action' => 'deactivate_license',
			'license' => Admin::get_license_key(),
		];

		$license_data = self::_remote_post( $body_args );

		return $license_data;
	}

	public static function set_license_data( $license_data, $expiration = null ) {
		if ( null === $expiration ) {
			$expiration = 12 * HOUR_IN_SECONDS;
		}

		set_transient( 'elementor_show_license_data', $license_data, $expiration );
	}

	public static function get_license_data( $force_request = false ) {
		$license_data = get_transient( 'elementor_show_license_data' );

		if ( false === $license_data || $force_request ) {
			$body_args = [
				'edd_action' => 'check_license',
				'license' => Admin::get_license_key(),
			];

			$license_data = self::_remote_post( $body_args );

			if ( is_wp_error( $license_data ) ) {
				$license_data = [
					'license' => 'http_error',
					'payment_id' => '0',
					'license_limit' => '0',
					'site_count' => '0',
					'activations_left' => '0',
				];

				self::set_license_data( $license_data, 30 * MINUTE_IN_SECONDS );
			} else {
				self::set_license_data( $license_data );
			}
		}

		return $license_data;
	}

	public static function get_version() {
		$updater = Admin::get_updater_instance();

		$body_args = [
			'edd_action' => 'get_version',
			'name' => $updater->plugin_name,
			'slug' => $updater->plugin_slug,
			'version'  => $updater->plugin_version,
			'license' => Admin::get_license_key(),
		];

		$license_data = self::_remote_post( $body_args );

		return $license_data;
	}
}
