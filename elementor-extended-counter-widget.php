<?php
/**
 * Plugin Name:       Elementor Extended Counter Widget
 * Plugin URI:        https://github.com/TarekNabil/Elementor-Extended-Counter-Widget
 * Description:       Extends Elementor's counter widget to support non-Latin numerals (Arabic ٠١٢٣٤٥٦٧٨٩) with full RTL language support.
 * Version:           1.0.0
 * Author:            Tarek Nabil
 * Author URI:        https://github.com/TarekNabil
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       elementor-extended-counter-widget
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 *
 * @package ElementorExtendedCounterWidget
 */

// Phase 1.1 — Plugin entry point.
// Prevents direct execution outside WordPress.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin version and path constants used throughout the codebase.
define( 'EECW_VERSION', '1.0.0' );
define( 'EECW_PLUGIN_FILE', __FILE__ );
define( 'EECW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'EECW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EECW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Load the plugin after all plugins are loaded so we can safely check
 * for Elementor's presence before registering anything.
 */
function eecw_init() {
	// Bail early if Elementor has not been loaded — avoids fatal errors
	// when Elementor is deactivated while our plugin is still active.
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'eecw_missing_elementor_notice' );
		return;
	}

	// Require the main plugin orchestrator and hand off control.
	require_once EECW_PLUGIN_PATH . 'includes/class-plugin.php';
	\ElementorExtendedCounterWidget\Plugin::instance();
}
add_action( 'plugins_loaded', 'eecw_init' );

/**
 * Display an admin notice when Elementor is not active.
 *
 * @return void
 */
function eecw_missing_elementor_notice() {
	$message = sprintf(
		/* translators: %s: Elementor plugin name */
		esc_html__( 'Elementor Extended Counter Widget requires %s to be installed and activated.', 'elementor-extended-counter-widget' ),
		'<strong>' . esc_html__( 'Elementor', 'elementor-extended-counter-widget' ) . '</strong>'
	);
	printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
}
