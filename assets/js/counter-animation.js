/* global elementorFrontend */
/**
 * EECW Counter Animation
 *
 * Animates the counter number from 0 to a target value using
 * requestAnimationFrame for smooth 60 fps performance. Applies an
 * ease-out-quad curve and converts the current value to the chosen
 * numeral format on every frame.
 *
 * On the public frontend the animation is gated behind an
 * IntersectionObserver so it only starts when the widget scrolls into view.
 * In the Elementor editor it runs immediately so the live preview feels
 * responsive.
 *
 * Phase 3.2
 *
 * @package ElementorExtendedCounterWidget
 */
( function () {
	'use strict';

	/**
	 * Arabic-Indic digit map (index 0–9 → Unicode U+0660–U+0669).
	 *
	 * Mirrors class-number-converter.php so the same conversion is
	 * available to every animation frame without a PHP round-trip.
	 *
	 * @type {string[]}
	 */
	var ARABIC_DIGITS = [ '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ];

	// -------------------------------------------------------------------------
	// Utilities
	// -------------------------------------------------------------------------

	/**
	 * Convert an integer to the requested numeral-format string.
	 *
	 * @param {number} number Integer to convert (will be rounded).
	 * @param {string} format Target format key ('latin' | 'arabic').
	 * @returns {string} Formatted number string.
	 */
	function convertToFormat( number, format ) {
		var str = String( Math.round( number ) );

		if ( 'arabic' === format ) {
			return str.replace( /[0-9]/g, function ( digit ) {
				return ARABIC_DIGITS[ parseInt( digit, 10 ) ];
			} );
		}

		// 'latin' (default) — the raw digit string needs no conversion.
		return str;
	}

	/**
	 * Ease-out quad — fast start, gentle deceleration at the end.
	 *
	 * @param {number} t Progress value in [0, 1].
	 * @returns {number} Eased progress value.
	 */
	function easeOutQuad( t ) {
		return t * ( 2 - t );
	}

	// -------------------------------------------------------------------------
	// Core animation
	// -------------------------------------------------------------------------

	/**
	 * Start the counter animation for a single .eecw-counter element.
	 *
	 * Reads data attributes set by the PHP render() method:
	 *   data-target   — final integer value
	 *   data-format   — numeral format key
	 *   data-duration — animation duration in milliseconds
	 *   data-delay    — pause before the animation starts (milliseconds)
	 *
	 * @param {HTMLElement} counterEl The .eecw-counter wrapper element.
	 * @returns {void}
	 */
	function animateCounter( counterEl ) {
		var target   = parseInt( counterEl.getAttribute( 'data-target' ), 10 );
		var format   = counterEl.getAttribute( 'data-format' ) || 'latin';
		var duration = parseInt( counterEl.getAttribute( 'data-duration' ), 10 ) || 2000;
		var delay    = parseInt( counterEl.getAttribute( 'data-delay' ), 10 )    || 0;
		var numberEl = counterEl.querySelector( '.eecw-counter-number' );

		if ( ! numberEl || isNaN( target ) ) {
			return;
		}

		// Reset to the zero equivalent before (re)starting — handles editor
		// re-renders where the element already shows a previous value.
		numberEl.textContent = convertToFormat( 0, format );

		var startTimestamp = null;

		/**
		 * Single frame callback driven by requestAnimationFrame.
		 *
		 * @param {DOMHighResTimeStamp} timestamp Current frame timestamp (ms).
		 * @returns {void}
		 */
		function tick( timestamp ) {
			if ( null === startTimestamp ) {
				startTimestamp = timestamp;
			}

			var elapsed  = timestamp - startTimestamp;
			var progress = Math.min( elapsed / duration, 1 );
			var eased    = easeOutQuad( progress );

			numberEl.textContent = convertToFormat( eased * target, format );

			if ( progress < 1 ) {
				requestAnimationFrame( tick );
			}
		}

		if ( delay > 0 ) {
			setTimeout( function () {
				requestAnimationFrame( tick );
			}, delay );
		} else {
			requestAnimationFrame( tick );
		}
	}

	// -------------------------------------------------------------------------
	// Viewport gating (IntersectionObserver)
	// -------------------------------------------------------------------------

	/**
	 * Observe a counter element and start its animation once at least 20 % of
	 * the element has entered the viewport. Falls back to immediate animation
	 * in browsers that do not support IntersectionObserver (e.g. IE 11).
	 *
	 * @param {HTMLElement} counterEl The .eecw-counter wrapper element.
	 * @returns {void}
	 */
	function observeCounter( counterEl ) {
		if ( ! ( 'IntersectionObserver' in window ) ) {
			// Fallback: animate immediately.
			animateCounter( counterEl );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries, obs ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						animateCounter( entry.target );
						// Animate only once per page load; stop watching afterwards.
						obs.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.2 }
		);

		observer.observe( counterEl );
	}

	// -------------------------------------------------------------------------
	// Elementor frontend integration
	// -------------------------------------------------------------------------

	/**
	 * Register the widget handler after the Elementor frontend has initialised.
	 *
	 * 'frontend/element_ready/eecw_counter.default' fires for every instance
	 * of our widget, passing the widget's root jQuery element as $scope.
	 * This handler is called on both the public frontend and the editor
	 * live-preview iframe.
	 */
	window.addEventListener( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/eecw_counter.default',
			/**
			 * @param {jQuery} $scope The widget wrapper element.
			 * @returns {void}
			 */
			function ( $scope ) {
				var counterEl = $scope[ 0 ].querySelector( '.eecw-counter' );

				if ( ! counterEl ) {
					return;
				}

				// In the editor, animate right away so the preview is snappy.
				// On the public frontend, wait until the element is in view.
				if ( elementorFrontend.isEditMode() ) {
					animateCounter( counterEl );
				} else {
					observeCounter( counterEl );
				}
			}
		);
	} );

} )();
