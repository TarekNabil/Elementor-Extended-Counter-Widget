<?php
/**
 * Main plugin orchestrator class.
 *
 * Bootstraps all plugin components: autoloading, widget registration,
 * asset enqueueing, and i18n. Uses a singleton so the plugin is only
 * ever initialised once per request.
 *
 * @package ElementorExtendedCounterWidget
 */

namespace ElementorExtendedCounterWidget;

// Phase 1.2 — Main plugin orchestrator.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 *
 * Central orchestrator. All bootstrapping originates here.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $_instance = null;

	/**
	 * Return (or create) the single Plugin instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor — private to enforce singleton.
	 * Wires up all WordPress/Elementor hooks.
	 */
	private function __construct() {
		$this->_register_autoloader();
		$this->_init_hooks();
	}

	/**
	 * Register a PSR-4-style autoloader for the plugin namespace.
	 *
	 * Maps  ElementorExtendedCounterWidget\Foo\Bar
	 * →     includes/Foo/Bar.php  (with WordPress file-naming convention applied).
	 * e.g.  ElementorExtendedCounterWidget\Widgets\Counter_Widget
	 * →     includes/widgets/class-counter-widget.php
	 *
	 * @return void
	 */
	private function _register_autoloader() {
		spl_autoload_register( function ( $class ) {
			$prefix = __NAMESPACE__ . '\\';

			// Only handle classes in our namespace.
			if ( 0 !== strpos( $class, $prefix ) ) {
				return;
			}

			// Strip the namespace prefix and convert the remainder to a file path.
			$relative = substr( $class, strlen( $prefix ) );

			// Convert namespace separators to directory separators and apply
			// WordPress filename convention: Class_Name → class-name.php,
			// sub-namespace Widgets → widgets/.
			$parts    = explode( '\\', $relative );
			$dirs     = array_map( 'strtolower', array_slice( $parts, 0, -1 ) );
			$filename = 'class-' . strtolower( str_replace( '_', '-', end( $parts ) ) ) . '.php';

			$path = EECW_PLUGIN_PATH . 'includes/' . implode( '/', $dirs );
			$path = rtrim( $path, '/' ) . '/' . $filename;

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		} );
	}

	/**
	 * Wire up WordPress and Elementor action hooks.
	 *
	 * @return void
	 */
	private function _init_hooks() {
		// Load plugin text domain for i18n.
		add_action( 'init', [ $this, 'load_textdomain' ] );

		// Register our widget(s) after Elementor's widget manager is ready.
		// NOTE: must use elementor/widgets/register, NOT plugins_loaded.
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// Enqueue frontend assets.
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_assets' ] );
	}

	/**
	 * Load the plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'elementor-extended-counter-widget',
			false,
			dirname( EECW_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Register plugin widgets with Elementor's widget manager.
	 *
	 * Delegated to the dedicated loader so this class stays thin.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor's widget manager instance.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		require_once EECW_PLUGIN_PATH . 'includes/widgets/loader.php';
		Widgets\register_widgets( $widgets_manager );
	}

	/**
	 * Register (but do not enqueue) plugin scripts and styles.
	 *
	 * Assets are enqueued on demand via get_script_depends() / get_style_depends()
	 * on the widget class, so we only need to register them here.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_register_script(
			'eecw-counter-animation',
			EECW_PLUGIN_URL . 'assets/js/counter-animation.js',
			[ 'jquery' ],
			EECW_VERSION,
			true  // load in footer
		);

		wp_register_style(
			'eecw-counter-widget',
			EECW_PLUGIN_URL . 'assets/css/counter-widget.css',
			[],
			EECW_VERSION
		);
	}

	/**
	 * Prevent cloning of the singleton.
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization of the singleton.
	 *
	 * @throws \Exception Always.
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton.' );
	}
}
