<?php
namespace ElementorWPFormStyler\Modules\Stylers\Widgets;

use Elementor\Element_Base;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use ElementorWPFormStyler\Modules\Stylers\Skins;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Sendinblue_Emails extends Widget_Base {

	public function get_name() {
		return 'sendinblue-emails';
	}

	public function get_title() {
		return __( 'Sendinblue Emails', 'wp-form-styler' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'wpfs-elements' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'sib_form',
			[
				'label' => __( 'Form Content', 'wp-form-styler' ),
			]
		);
		
		$this->add_control(
			'sib_form_id',
			[
				'label' => __( 'Form ID', 'wp-form-styler' ),
				'type' => Controls_Manager::TEXT,
				'default' => '1',
				'title' => __( 'Paste Form ID only - Not the full shortcode!', 'wp-form-styler' ),				
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'contact_typography',
				'label' => __( 'Typography', 'wp-form-styler' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .wpcf7-form > p',
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
			'contact_styles',
			[
				'label' => __( 'Colors', 'wp-form-styler' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'form_color',
			[
				'label' => __( 'Labels', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form > p' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="submit"]' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'button_hover',
			[
				'label' => __( 'Button Text Hover', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="submit"]:hover' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'button_bg',
			[
				'label' => __( 'Button Background', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#009ee2',
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="submit"]' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'button_bg_hover',
			[
				'label' => __( 'Button BG Hover', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#009ee2',
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="submit"]:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();
		
		$selected_form = $settings['sib_form_id']; 
		
		echo do_shortcode('[sibwp_form id=' . $selected_form . ']');

	}

	protected function content_template() {}

	public function render_plain_content( $settings = [] ) {}

}