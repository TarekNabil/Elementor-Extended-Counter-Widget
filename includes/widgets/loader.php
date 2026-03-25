<?php
/**
 * Widget registration loader.
 *
 * Requires each widget class file and registers the widget instances
 * with Elementor's widget manager. Keeping registration separate from
 * the Plugin orchestrator makes it easy to add more widgets later
 * without touching class-plugin.php.
 *
 * @package ElementorExtendedCounterWidget
 */

namespace ElementorExtendedCounterWidget\Widgets;

// Phase 1.4 — Widget loader / registration.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all plugin widgets with Elementor.
 *
 * Called from Plugin::register_widgets() on the
 * `elementor/widgets/register` action hook.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widget manager.
 * @return void
 */
function register_widgets( $widgets_manager ) {
	require_once EECW_PLUGIN_PATH . 'includes/widgets/class-counter-widget.php';

	$widgets_manager->register( new Counter_Widget() );
}
