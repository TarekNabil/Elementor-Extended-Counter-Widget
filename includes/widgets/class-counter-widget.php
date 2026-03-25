<?php
/**
 * Extended Counter Widget for Elementor.
 *
 * A standalone widget (not extending Elementor's built-in counter)
 * that animates numbers from 0 to a target value and can display them
 * in non-Latin numeral systems (Arabic, and more in future versions).
 *
 * @package ElementorExtendedCounterWidget
 */

namespace ElementorExtendedCounterWidget\Widgets;

use ElementorExtendedCounterWidget\Number_Converter;

// Phase 1.4 — Widget class stub. Controls and render are added in Phase 2 & 3.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Counter_Widget
 *
 * Extends Elementor's Widget_Base to provide an animated counter with
 * configurable numeral format and full RTL language support.
 */
class Counter_Widget extends \Elementor\Widget_Base {

	// -------------------------------------------------------------------------
	// Identity
	// -------------------------------------------------------------------------

	/**
	 * Unique machine-readable widget name used internally by Elementor.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'eecw_counter';
	}

	/**
	 * Human-readable widget title shown in the Elementor widget panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Extended Counter', 'elementor-extended-counter-widget' );
	}

	/**
	 * Dashicons icon shown next to the widget title in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-counter';
	}

	/**
	 * Widget panel category.
	 *
	 * 'general' places the widget in Elementor's default General section.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Search keywords so users can find the widget by typing related terms.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return [ 'counter', 'number', 'arabic', 'count', 'animate', 'stats' ];
	}

	/**
	 * JavaScript handle(s) this widget depends on.
	 *
	 * Elementor will enqueue these on both the editor preview and the frontend
	 * whenever this widget is present on the page.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return [ 'eecw-counter-animation' ];
	}

	/**
	 * CSS handle(s) this widget depends on.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return [ 'eecw-counter-widget' ];
	}

	// -------------------------------------------------------------------------
	// Controls  (Phase 2)
	// -------------------------------------------------------------------------

	/**
	 * Register widget controls (settings panel).
	 *
	 * Phase 2 implementation:
	 * - Content controls (number, prefix, suffix, numeral format)
	 * - Animation controls (duration, delay)
	 * - Style controls with responsive typography and font sizes
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_counter',
			[
				'label' => __( 'Counter', 'elementor-extended-counter-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'number',
			[
				'label'   => __( 'Number', 'elementor-extended-counter-widget' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 100,
				'min'     => 0,
			]
		);

		$this->add_control(
			'prefix',
			[
				'label'       => __( 'Prefix', 'elementor-extended-counter-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'e.g. $', 'elementor-extended-counter-widget' ),
			]
		);

		$this->add_control(
			'suffix',
			[
				'label'       => __( 'Suffix', 'elementor-extended-counter-widget' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'e.g. +', 'elementor-extended-counter-widget' ),
			]
		);

		$this->add_control(
			'number_format',
			[
				'label'   => __( 'Number Format', 'elementor-extended-counter-widget' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => Number_Converter::get_supported_formats(),
				'default' => 'latin',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_animation',
			[
				'label' => __( 'Animation', 'elementor-extended-counter-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'duration',
			[
				'label'       => __( 'Duration (ms)', 'elementor-extended-counter-widget' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 2000,
				'min'         => 100,
				'step'        => 100,
				'description' => __( 'Total animation duration in milliseconds.', 'elementor-extended-counter-widget' ),
			]
		);

		$this->add_control(
			'delay',
			[
				'label'       => __( 'Delay (ms)', 'elementor-extended-counter-widget' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'step'        => 100,
				'description' => __( 'Delay before animation starts.', 'elementor-extended-counter-widget' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_number',
			[
				'label' => __( 'Number', 'elementor-extended-counter-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'number_color',
			[
				'label'     => __( 'Color', 'elementor-extended-counter-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eecw-counter-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .eecw-counter-number',
			]
		);

		$this->add_responsive_control(
			'number_font_size',
			[
				'label'      => __( 'Font Size', 'elementor-extended-counter-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eecw-counter-number' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_prefix_suffix',
			[
				'label' => __( 'Prefix/Suffix', 'elementor-extended-counter-widget' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'prefix_suffix_color',
			[
				'label'     => __( 'Color', 'elementor-extended-counter-widget' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eecw-counter-prefix' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eecw-counter-suffix' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'prefix_suffix_typography',
				'selector' => '{{WRAPPER}} .eecw-counter-prefix, {{WRAPPER}} .eecw-counter-suffix',
			]
		);

		$this->add_responsive_control(
			'prefix_suffix_font_size',
			[
				'label'      => __( 'Font Size', 'elementor-extended-counter-widget' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 120,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eecw-counter-prefix' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eecw-counter-suffix' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	// -------------------------------------------------------------------------
	// Rendering  (Phase 3)
	// -------------------------------------------------------------------------

	/**
	 * Render widget HTML on the frontend and in the editor preview.
	 *
	 * Implemented in Phase 3. Outputs a placeholder div so the widget is
	 * visible and selectable in the editor during Phase 1 testing.
	 *
	 * @return void
	 */
	protected function render() {
		// Phase 3 — full render implementation coming.
		// Placeholder output keeps the widget visible in the editor preview.
		echo '<div class="eecw-counter-placeholder" style="padding:20px;text-align:center;border:2px dashed #ccc;">';
		echo '<span style="font-size:2rem;font-weight:bold;">0</span>';
		echo '<p style="margin:4px 0 0;color:#999;font-size:12px;">' . esc_html__( 'Extended Counter (Phase 1)', 'elementor-extended-counter-widget' ) . '</p>';
		echo '</div>';
	}
}
