<?php
/**
 * Plugin Name: WP Form Styler
 * Description: WP Form Styler is a feature packed WordPress popular forms styler for the Elementor Page Builder plugin. Style Contact Form 7, Gravity Forms, WPForms e.t.c right inside Elementor.
 * Plugin URI: https://wpdevhq.com/
 * Author: WPDevHQ
 * Version: 1.0.0
 * Author URI: https://wpdevhq.com/
 *
 * Text Domain: wp-form-styler
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'ELEMENTOR_WPFS_VERSION', '1.0.1' );

define( 'ELEMENTOR_WPFS__FILE__', __FILE__ );
define( 'ELEMENTOR_WPFS_PLUGIN_BASE', plugin_basename( ELEMENTOR_WPFS__FILE__ ) );
define( 'ELEMENTOR_WPFS_PATH', plugin_dir_path( ELEMENTOR_WPFS__FILE__ ) );
define( 'ELEMENTOR_WPFS_MODULES_PATH', ELEMENTOR_WPFS_PATH . 'modules/' );
define( 'ELEMENTOR_WPFS_URL', plugins_url( '/', ELEMENTOR_WPFS__FILE__ ) );
define( 'ELEMENTOR_WPFS_ASSETS_URL', ELEMENTOR_WPFS_URL . 'assets/' );
define( 'ELEMENTOR_WPFS_MODULES_URL', ELEMENTOR_WPFS_URL . 'modules/' );

/**
 * Load gettext translate for our text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementor_wpfs_load_plugin() {
	load_plugin_textdomain( 'wp-form-styler' );

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'elementor_wpfs_fail_load' );
		return;
	}

	$elementor_version_required = '1.0.6';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'elementor_wpfs_fail_load_out_of_date' );
		return;
	}

	require( ELEMENTOR_WPFS_PATH . 'plugin.php' );
}
add_action( 'plugins_loaded', 'elementor_wpfs_load_plugin' );

/**
 * Show in WP Dashboard notice about the plugin is not activated.
 *
 * @since 1.0.0
 *
 * @return void
 */
function elementor_wpfs_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}

	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

		$message = '<p>' . __( 'Elementor WP Form Styler is not working because you need to activate the Elementor plugin.', 'wp-form-styler' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, __( 'Activate Elementor Now', 'wp-form-styler' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

		$message = '<p>' . __( 'Elementor WP Form Styler is not working because you need to install the Elemenor plugin', 'wp-form-styler' ) . '</p>';
		$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Elementor Now', 'wp-form-styler' ) ) . '</p>';
	}

	echo '<div class="error"><p>' . $message . '</p></div>';
}

function elementor_wpfs_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = '<p>' . __( 'Elementor WP Form Styler is not working because you are using an old version of Elementor.', 'wp-form-styler' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'wp-form-styler' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}

if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}

function actions_cf7_temp() {
    $wpcf7_array = array();

        $args = array(
            'post_type' => 'wpcf7_contact_form',
        );
        
        $wpcf7 = get_posts($args);

        foreach( $wpcf7 as $post ) { setup_postdata( $post );
            $wpcf7_array[$post->ID] = $post->post_title;
        } 

        return $wpcf7_array;

    wp_reset_postdata();
}

function actions_caldera_temp() {
    $caldera_array = array();
	Caldera_Forms_Forms::get_forms( true );
    $forms = Caldera_Forms_Forms::get_forms( 'ID' );
		if(!empty($forms)){
		//$wpcf7 = ;
			foreach($forms as $formid=>$form){
				$caldera_array[$form->ID] = $formid;
			}
		}

        return $caldera_array;
		
}

function actions_wpforms_temp() {
	$wpforms_array = array();
	
	$forms = wpforms()->form->get();
	
	if ( !empty( $forms ) ) {
		foreach ( $forms as $form ) {
			$wpforms_array[$form->ID] = $form->post_title;
		}		
	}
	return $wpforms_array;
}