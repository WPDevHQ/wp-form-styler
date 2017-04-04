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

class Contact_Form_7 extends Widget_Base {

	public function get_name() {
		return 'contact-form-7';
	}

	public function get_title() {
		return __( 'Contact Form 7', 'wp-form-styler' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'wpfs-elements' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'contact_form',
			[
				'label' => __( 'Form Content', 'wp-form-styler' ),
			]
		);

		$this->add_control(
			'select_form',
			[
				'label' => __( 'Contact Form 7', 'wp-form-styler' ),
				'type' => Controls_Manager::SELECT,
				'default' => [],
				'options' => actions_cf7_temp(),
				'description' => __( 'List of your available contact form 7 templates.', 'wp-form-styler' ),
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label' => __( 'Alignment', 'wp-form-styler' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'wp-form-styler' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wp-form-styler' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'wp-form-styler' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form > p' => 'text-align: {{VALUE}};',
				],
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
			'form_options',
			[
				'label' => __( 'Form', 'wp-form-styler' ),
			]
		);
		
		$this->add_control(
			'form_padding',
			[
				'label' => __( 'Padding', 'wp-form-styler' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpcf7' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
				'label' => __( 'Border', 'wp-form-styler' ),
				'default' => '1px',
				'selector' => '{{WRAPPER}} .wpcf7',
			]
		);

		$this->add_control(
			'form_radius',
			[
				'label' => __( 'Radius', 'wp-form-styler' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpcf7' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'fields_options',
			[
				'label' => __( 'Form Fields', 'wp-form-styler' ),
			]
		);
		
		$this->add_responsive_control(
			'fields_width',
			[
				'label'          => __( 'Width', 'wp-form-styler' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 10,
						'max' => 600,
					],
				],
				'default'        => [
					'size' => 50,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => '',
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'size_units'     => [ '%', 'px' ],
				'selectors'      => [
					'{{WRAPPER}} .wpcf7 input[type="text"],.wpcf7 input[type="email"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'field_border',
				'label' => __( 'Input Fields: Border', 'wp-form-styler' ),
				'default' => '1px',
				'selector' => '{{WRAPPER}} .wpcf7 input[type="text"],.wpcf7 input[type="email"]',
			]
		);

		$this->add_control(
			'field_radius',
			[
				'label' => __( 'Input Fields: Radius', 'wp-form-styler' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="text"],.wpcf7 input[type="email"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'textarea_options',
			[
				'label' => __( 'Form Textarea', 'wp-form-styler' ),
			]
		);
		
		$this->add_responsive_control(
			'textarea_width',
			[
				'label'          => __( 'Width', 'wp-form-styler' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 10,
						'max' => 600,
					],
				],
				'default'        => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => '',
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'size_units'     => [ '%', 'px' ],
				'selectors'      => [
					'{{WRAPPER}} .wpcf7 textarea' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'textarea_height',
			[
				'label'          => __( 'Height', 'wp-form-styler' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
					'px' => [
						'min' => 10,
						'max' => 600,
					],
				],
				'default'        => [
					'size' => 200,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => '',
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 200,
					'unit' => 'px',
				],
				'size_units'     => [ '%', 'px' ],
				'selectors'      => [
					'{{WRAPPER}} .wpcf7 textarea' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'textarea_border',
				'label' => __( 'Textarea: Border', 'wp-form-styler' ),
				'default' => '1px',
				'selector' => '{{WRAPPER}} .wpcf7 textarea',
			]
		);

		$this->add_control(
			'textarea_radius',
			[
				'label' => __( 'Textarea: Radius', 'wp-form-styler' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpcf7 textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'button_options',
			[
				'label' => __( 'Form Button', 'wp-form-styler' ),
			]
		);		
		
		$this->add_control(
			'button_padding',
			[
				'label' => __( 'Padding', 'wp-form-styler' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'label' => __( 'Border', 'wp-form-styler' ),
				'default' => '1px',
				'selector' => '{{WRAPPER}} .wpcf7 input[type="submit"]',
			]
		);

		$this->add_control(
			'button_radius',
			[
				'label' => __( 'Radius', 'wp-form-styler' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'message_options',
			[
				'label' => __( 'Success/Error Message', 'wp-form-styler' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'message_ok_border',
				'label' => __( 'Success: Border', 'wp-form-styler' ),
				'default' => '1px',
				'selector' => '{{WRAPPER}} div.wpcf7-mail-sent-ok',
			]
		);

		$this->add_control(
			'message_ok_radius',
			[
				'label' => __( 'Success: Radius', 'wp-form-styler' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} div.wpcf7-mail-sent-ok' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'message_error_border',
				'label' => __( 'Error: Border', 'wp-form-styler' ),
				'default' => '1px',
				'selector' => '{{WRAPPER}} div.wpcf7-validation-errors',
			]
		);

		$this->add_control(
			'message_error_radius',
			[
				'label' => __( 'Error: Radius', 'wp-form-styler' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} div.wpcf7-validation-errors' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'message_typography',
				'label' => __( 'Typography', 'wp-form-styler' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} div.wpcf7-response-output',
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'form_styles',
			[
				'label' => __( 'Form Outer', 'wp-form-styler' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'form_bg',
			[
				'label' => __( 'Background', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wpcf7' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'contact_styles',
			[
				'label' => __( 'Labels & Fileds', 'wp-form-styler' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'labels_color',
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
			'fields_color',
			[
				'label' => __( 'Fields Color', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="text"],.wpcf7 input[type="email"]' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'fields_bg',
			[
				'label' => __( 'Fields BG', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wpcf7 input[type="text"],.wpcf7 input[type="email"]' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'textarea_styles',
			[
				'label' => __( 'Teaxtarea', 'wp-form-styler' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'textarea_color',
			[
				'label' => __( 'Text Color', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .wpcf7 textarea' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'textarea_bg',
			[
				'label' => __( 'Background', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .wpcf7 textarea' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();
		
		$this->start_controls_section(
			'button_styles',
			[
				'label' => __( 'Button', 'wp-form-styler' ),
				'tab' => Controls_Manager::TAB_STYLE,
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
		
		$this->start_controls_section(
			'message_styles',
			[
				'label' => __( 'Message', 'wp-form-styler' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'message_text',
			[
				'label' => __( 'Color', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} div.wpcf7-response-output' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'message_bg',
			[
				'label' => __( 'Background', 'wp-form-styler' ),
				'type' => Controls_Manager::COLOR,	
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} div.wpcf7-response-output' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();
		
		$selected_form = $settings['select_form']; 

		echo do_shortcode('[contact-form-7 id="' . $selected_form . '"]');

	}

	protected function content_template() {}

	public function render_plain_content( $settings = [] ) {}

}