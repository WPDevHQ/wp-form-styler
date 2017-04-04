<?php
namespace ElementorShow\License;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Admin {

	public static $updater = null;

	private static function get_hidden_license_key() {
		$input_string = self::get_license_key();

		$start = 5;
		$length = mb_strlen( $input_string ) - $start - 5;

		$mask_string = preg_replace( '/\S/', 'X', $input_string );
		$mask_string = mb_substr( $mask_string, $start, $length );
		$input_string = substr_replace( $input_string, $mask_string, $start, $length );

		return $input_string;
	}

	public static function get_updater_instance() {
		if ( null === self::$updater ) {
			self::$updater = new Updater();
		}

		return self::$updater;
	}

	public static function get_license_key() {
		return trim( get_option( 'elementor_show_license_key' ) );
	}

	public static function set_license_key( $license_key ) {
		return update_option( 'elementor_show_license_key', $license_key );
	}

	public function action_activate_license() {
		check_admin_referer( 'elementor-show-license' );

		if ( empty( $_POST['elementor_show_license_key'] ) ) {
			wp_die( __( 'Please enter your license key.', 'elementor-show' ), __( 'Elementor WooStore', 'elementor-show' ), [ 'back_link' => true ] );
		}

		$license_key = trim( $_POST['elementor_show_license_key'] );

		$data = API::activate_license( $license_key );
		if ( is_wp_error( $data ) ) {
			wp_die( sprintf( '%s (%s) ', $data->get_error_message(), $data->get_error_code() ), __( 'Elementor WooStore', 'elementor-show' ), [ 'back_link' => true ] );
		}

		if ( API::STATUS_VALID !== $data['license'] ) {
			$errors = [
				'no_activations_left' => __( 'You have no more activations left. Please upgrade your licence for auto updates.', 'elementor-show' ),
				'expired' => __( 'Your license has expired. Please renew your licence in order to get auto-updates.', 'elementor-show' ),
				'missing' => __( 'Your license is missing. Please check your key again.', 'elementor-show' ),
				'revoked' => __( 'Your license has been revoked.', 'elementor-show' ),
				'item_name_mismatch' => sprintf( __( 'Your license has a name mismatch. Please go to <a href="%s" target="_blank">your purchases</a> and choose the proper key.', 'elementor-show' ), 'https://my.wpdevhq.com/my-account/' ),
			];

			if ( isset( $errors[ $data['error'] ] ) ) {
				$error_msg = $errors[ $data['error'] ];
			} else {
				$error_msg = __( 'An error occurred, please try again', 'elementor-show' ) . ' (' . $data->error . ')';
			}

			wp_die( $error_msg, __( 'Elementor WooStore', 'elementor-show' ), [ 'back_link' => true ] );
		}

		self::set_license_key( $license_key );
		API::set_license_data( $data );

		wp_safe_redirect( $_POST['_wp_http_referer'] );
		die;
	}

	public function action_deactivate_license() {
		check_admin_referer( 'elementor-show-license' );

		API::deactivate_license();

		delete_option( 'elementor_show_license_key' );

		wp_safe_redirect( $_POST['_wp_http_referer'] );
		die;
	}

	public function register_menu() {
		$menu_text = __( 'License', 'elementor-show' );

		add_submenu_page(
			'elementor',
			$menu_text,
			$menu_text,
			'manage_options',
			'elementor-license',
			[ $this, 'display_page' ]
		);
	}

	public function display_page() {
		$license_key = self::get_license_key();
		?>
		<div class="wrap">
			<h2><?php _e( 'License Setting', 'elementor-show' ); ?></h2>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'elementor-show-license' ); ?>

				<p><?php _e( 'Please enter the license key that you purchased. Doing so will allow you to get automatic updates for Elementor WooStore.', 'elementor-show' ); ?></p>
	
				<h3><?php _e( 'Your License', 'elementor-show' ); ?></h3>
	
				<p><?php printf( __( 'A license key qualifies you for support and enables automatic updates straight to your dashboard. Simply enter your license key where indicated and get started. If you don\'t have a license key, and haven\'t bought Elementor WooSotre yet, <a href="%s" target="_blank">buy it in our website</a>.', 'elementor-show' ), 'https://my.wpdevhq.com/pro-license/' ); ?></p>
				
				<?php if ( empty( $license_key ) ) : ?>
					<input type="hidden" name="action" value="elementor_pro_activate_license" />
	
					<label for="elementor-pro-license-key"><?php _e( 'License Key:', 'elementor-show' ); ?></label>
	
					<input id="elementor-pro-license-key" name="elementor_pro_license_key" type="text" value="" placeholder="<?php _e( 'Place Elementor WooStore license key here', 'elementor-show' ); ?>" class="regular-text" />
	
					<input type="submit" class="button button-primary" value="<?php _e( 'Activate', 'elementor-show' ); ?>" />
	
					<p class="description"><?php printf( __( 'Please enter your license key. You can find your key in <a href="%s" target="_blank">your purchases</a>. License key looks similar to this: fb351f05958872E193feb37a505a84be', 'elementor-show' ), 'http://my.wpdevhq.com/my-account/' ); ?></p>

				<?php else :

					$license_data = API::get_license_data( true );
					?>
					<input type="hidden" name="action" value="elementor_pro_deactivate_license" />

					<label for="elementor-pro-license-key"><?php _e( 'License Key:', 'elementor-show' ); ?></label>

					<input id="elementor-pro-license-key" type="text" value="<?php echo esc_attr( self::get_hidden_license_key() ); ?>" class="regular-text" disabled />

					<input type="submit" class="button button-primary" value="<?php _e( 'Deactivate', 'elementor-show' ); ?>" />

					<p>
						<?php _e( 'Status', 'elementor-show' ); ?>:
						<?php if ( API::STATUS_EXPIRED === $license_data['license'] ) : ?>
							<span style="color: #ff0000; font-style: italic;"><?php _e( 'Expired', 'elementor-show' ); ?></span>
						<?php elseif ( API::STATUS_SITE_INACTIVE === $license_data['license'] ) : ?>
							<span style="color: #ff0000; font-style: italic;"><?php _e( 'No Match', 'elementor-show' ); ?></span>
						<?php else : ?>
							<span style="color: #008000; font-style: italic;"><?php _e( 'Active', 'elementor-show' ); ?></span>
						<?php endif; ?>
					</p>

					<?php if ( API::STATUS_EXPIRED === $license_data['license'] ) : ?>
						<p><?php _e( '<strong>Your license key has expired!</strong> Please visit <a href="http://my.wpdevhq.com/purchases/" target="_blank">to renew it or purchase a new one</a> in order to get updates and support.', 'elementor-show' ); ?></p>
				<?php endif; ?>
					
				<?php endif; ?>
			</form>
		</div>
		<?php
	}

	public function admin_license_details() {
		$license_page_link = add_query_arg( [ 'page' => 'elementor-license' ], admin_url( 'admin.php' ) );

		$license_key = self::get_license_key();
		if ( empty( $license_key ) ) {
			$msg = sprintf( __( '<strong>Welcome to Elementor WooStore!</strong> Please <a href="%s">activate your license key</a> to enable automatic updates.', 'elementor-show' ), $license_page_link );
			printf( '<div class="error"><p>%s</p></div>', $msg );
			return;
		}

		$license_data = API::get_license_data();
		if ( empty( $license_data['license'] ) ) {
			return;
		}

		$errors = [
			API::STATUS_DISABLED => __( 'License is disabled', 'elementor-show' ),
			API::STATUS_EXPIRED => __( 'License has expired', 'elementor-show' ),
			API::STATUS_INVALID => sprintf( __( 'Something went wrong with license key\'s activation on your site. Please go to <a href="%s">your account at WPDevHQ</a> to get an updated license key.', 'elementor-show' ), 'http://my.wpdevhq.com/my-account/' ),
			API::STATUS_SITE_INACTIVE => sprintf( __( 'The entered license key does not match the domain registered in our system, please <a href="%s">deactivate the license</a> and try reinserting it to receive updates.', 'elementor-show' ), $license_page_link ),
		];

		if ( isset( $errors[ $license_data['license'] ] ) ) {
			printf( '<div class="error"><p>%s</p></div>', $errors[ $license_data['license'] ] );
			return;
		}

		if ( API::STATUS_VALID === $license_data['license'] ) {
			$expires_time = strtotime( $license_data['expires'] );
			$notification_expires_time = strtotime( '-30 days', $expires_time );

			if ( $notification_expires_time <= current_time( 'timestamp' ) ) {
				$msg = sprintf( __( '<strong>Note:</strong> Your license key will expire in %s.', 'elementor-show' ), human_time_diff( current_time( 'timestamp' ), $expires_time ) );
				printf( '<div class="update-nag">%s</div>', $msg );
			}
		}
	}

	public function filter_library_get_templates_args( $body_args ) {
		$license_key = self::get_license_key();

		if ( ! empty( $license_key ) ) {
			$body_args['license'] = $license_key;
		}

		return $body_args;
	}

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu' ], 800 );
		add_action( 'admin_post_elementor_show_activate_license', [ $this, 'action_activate_license' ] );
		add_action( 'admin_post_elementor_show_deactivate_license', [ $this, 'action_deactivate_license' ] );

		add_action( 'admin_notices', [ $this, 'admin_license_details' ], 20 );

		// Add the licence key to Templates Library requests
		add_filter( 'elementor/api/get_templates/body_args', [ $this, 'filter_library_get_templates_args' ] );

		self::get_updater_instance();
	}
}
