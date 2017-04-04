<?php
namespace ElementorWPFormStyler;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Manager {

	private $_modules = null;

	private function is_module_active( $module_id ) {
		$module_data = $this->get_module_data( $module_id );
		if ( $module_data['required'] ) {
			return true;
		}

		$options = get_option( 'elementor_wpfs_active_modules', [] );
		if ( ! isset( $options[ $module_id ] ) ) {
			return $module_data['default_activation'];
		}

		return 'true' === $options[ $module_id ];
	}

	private function get_module_data( $module_id ) {
		return isset( $this->_modules[ $module_id ] ) ? $this->_modules[ $module_id ] : false;
	}

	public function __construct() {
		$modules = [
			'stylers',
		];

		// Fetch all modules data
		foreach ( $modules as $module ) {
			$this->_modules[ $module ] = require ELEMENTOR_WPFS_MODULES_PATH . $module . '/module.info.php';
		}

		foreach ( $this->_modules as $module_id => $module_data ) {
			if ( ! $this->is_module_active( $module_id ) ) {
				continue;
			}

			$class_name = str_replace( '-', ' ', $module_id );
			$class_name = str_replace( ' ', '', ucwords( $class_name ) );
			$class_name = __NAMESPACE__ . '\\Modules\\' . $class_name . '\Module';

			$class_name::instance();
		}
	}
}
