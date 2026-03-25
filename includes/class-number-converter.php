<?php
/**
 * Number converter utility.
 *
 * Handles all numeral-system conversions used by the counter widget.
 * Uses a static registry pattern so future versions can add new formats
 * (Persian, Hebrew, Roman, etc.) without modifying this core class.
 *
 * @package ElementorExtendedCounterWidget
 */

namespace ElementorExtendedCounterWidget;

// Phase 1.3 — Number conversion utility.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Number_Converter
 *
 * Converts numbers between supported numeral systems.
 * All public methods are static — no instantiation needed.
 */
class Number_Converter {

	/**
	 * Registry of available numeral formats.
	 *
	 * Each entry maps a format key (string) to a human-readable label and
	 * an optional callable converter. Built-in formats use dedicated static
	 * methods and don't need a callable entry here.
	 *
	 * Registry pattern allows future versions to register custom formats
	 * without modifying this class:
	 *
	 *   Number_Converter::register_format( 'persian', __( 'Persian', '...' ), 'my_converter' );
	 *
	 * @var array<string, array{label: string, converter: callable|null}>
	 */
	private static $_registry = [];

	/**
	 * Arabic-Indic digit map (Latin digit index → Arabic-Indic Unicode char).
	 *
	 * Unicode U+0660–U+0669 are the Eastern Arabic-Indic digits used in
	 * Arabic-script languages (Arabic, Urdu, etc.).
	 *
	 * @var string[]
	 */
	private static $_arabic_digits = [ '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ];

	// -------------------------------------------------------------------------
	// Public API
	// -------------------------------------------------------------------------

	/**
	 * Convert a number string to the requested numeral format.
	 *
	 * This is the single entry-point for all format conversions. The widget
	 * calls this method on every animation frame with the current display value.
	 *
	 * @param int|float|string $number The number to convert.
	 * @param string           $format Target format key (e.g. 'arabic', 'latin').
	 * @return string Converted number string. Falls back to Latin on unknown format.
	 */
	public static function convert( $number, $format ) {
		$number = (string) $number;

		switch ( $format ) {
			case 'arabic':
				return self::to_arabic( $number );

			case 'latin':
			default:
				// Latin is the raw numeric string — no conversion needed.
				return $number;
		}
	}

	/**
	 * Convert a Latin numeral string to Arabic-Indic digits (٠١٢٣٤٥٦٧٨٩).
	 *
	 * Replaces each ASCII digit 0-9 with its Unicode Arabic-Indic equivalent
	 * while leaving non-digit characters (commas, dots, minus, spaces) intact
	 * so formatters and separators survive the conversion.
	 *
	 * @param string $number Latin numeral string (e.g. "1,234.56").
	 * @return string Arabic-Indic representation (e.g. "١٬٢٣٤٫٥٦").
	 */
	public static function to_arabic( $number ) {
		$number = (string) $number;

		// Replace each digit 0-9 with its Arabic-Indic counterpart.
		return strtr( $number, [
			'0' => self::$_arabic_digits[0],
			'1' => self::$_arabic_digits[1],
			'2' => self::$_arabic_digits[2],
			'3' => self::$_arabic_digits[3],
			'4' => self::$_arabic_digits[4],
			'5' => self::$_arabic_digits[5],
			'6' => self::$_arabic_digits[6],
			'7' => self::$_arabic_digits[7],
			'8' => self::$_arabic_digits[8],
			'9' => self::$_arabic_digits[9],
		] );
	}

	/**
	 * Return the list of supported numeral formats for use in widget controls.
	 *
	 * Built-in formats are listed first; registered formats are appended.
	 *
	 * @return array<string, string> Format key → human-readable label pairs.
	 */
	public static function get_supported_formats() {
		$built_in = [
			'latin'  => __( 'Latin (1, 2, 3…)', 'elementor-extended-counter-widget' ),
			'arabic' => __( 'Arabic (١, ٢, ٣…)', 'elementor-extended-counter-widget' ),
		];

		// Merge any formats added via register_format().
		$registered = [];
		foreach ( self::$_registry as $key => $entry ) {
			$registered[ $key ] = $entry['label'];
		}

		return array_merge( $built_in, $registered );
	}

	/**
	 * Register a custom numeral format.
	 *
	 * Intended for future use by child plugins or third-party extensions.
	 * The $converter callable must accept a single string (the Latin number)
	 * and return the converted string.
	 *
	 * @param string   $key       Unique format identifier (e.g. 'persian').
	 * @param string   $label     Human-readable label shown in the widget panel.
	 * @param callable $converter Conversion function: ( string $number ) → string.
	 * @return void
	 */
	public static function register_format( $key, $label, callable $converter ) {
		self::$_registry[ sanitize_key( $key ) ] = [
			'label'     => sanitize_text_field( $label ),
			'converter' => $converter,
		];
	}
}
