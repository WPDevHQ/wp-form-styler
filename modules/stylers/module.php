<?php
namespace ElementorWPFormStyler\Modules\Stylers;

use Elementor\Plugin;
use ElementorWPFormStyler\Base\Module_Base;

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		//$this->add_actions();
	}
	
	public function get_name() {
		return 'wp-form-styler';
	}

	public function get_widgets() {
		return [
			'Contact_Form_7', // What is it goes here.
			'Formidable_Forms', // What is it goes here.
			'Gravity_Forms', // What is it goes here.
			//'WPForms_Widget', // What is it goes here.
			'Sendinblue_Emails', // Email Marketing.
		];
	}
	
}