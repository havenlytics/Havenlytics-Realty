/**
 * Havenlytics Realty — Homepage hero background carousel.
 *
 * Soft cross-fade + optional Ken Burns / soft-scale.
 * Left-rail dots + prev/next, pause-on-hover, reduced-motion support.
 *
 * @package Havenlytics_Realty
 */
( function () {
	'use strict';

	function hvnRealtyHeroCarousel() {
		var slider = document.querySelector( '.hvn-realty-hero-slider' );
		if ( ! slider ) {
			return;
		}

		var slides = Array.prototype.slice.call(
			slider.querySelectorAll( '.hvn-realty-hero-slide' )
		);
		if ( slides.length < 2 ) {
			return;
		}

		var dotsRoot = document.getElementById( 'hvnRealtyHeroDots' );
		var dots = dotsRoot
			? Array.prototype.slice.call( dotsRoot.querySelectorAll( '.hvn-realty-hero-dot' ) )
			: [];
		var prevBtn = document.getElementById( 'hvnRealtyHeroPrev' );
		var nextBtn = document.getElementById( 'hvnRealtyHeroNext' );

		var reduceMotion =
			window.matchMedia &&
			window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		var autoplayMs = parseInt( slider.getAttribute( 'data-autoplay' ), 10 );
		if ( isNaN( autoplayMs ) ) {
			autoplayMs = 5000;
		}
		var autoplayEnabled = autoplayMs >= 2000;

		var transitionMs = parseInt( slider.getAttribute( 'data-transition' ), 10 );
		if ( isNaN( transitionMs ) || transitionMs < 400 ) {
			transitionMs = 1200;
		}

		var loop = '0' !== slider.getAttribute( 'data-loop' );
		var pauseHover = '0' !== slider.getAttribute( 'data-pause-hover' );
		var zoomEnabled = '0' !== slider.getAttribute( 'data-zoom' );
		var effect = slider.getAttribute( 'data-effect' ) || 'fade_zoom';

		slider.style.setProperty( '--hvn-realty-hero-transition', transitionMs + 'ms' );
		slider.classList.add( 'hvn-realty-hero-effect--' + effect );

		var index = 0;
		var timer = null;
		var paused = false;
		var transitioning = false;
		var transitionTimer = null;

		function wantsMotionClass() {
			if ( reduceMotion || 'fade' === effect || ! zoomEnabled ) {
				return false;
			}
			return (
				'fade_zoom' === effect ||
				'ken_burns' === effect ||
				'soft_scale' === effect
			);
		}

		function motionClassName() {
			return 'soft_scale' === effect ? 'hvn-realty-hero-soft-scale' : 'hvn-realty-hero-ken-burns';
		}

		function applyMotion( slide, on ) {
			if ( ! slide ) {
				return;
			}
			if ( on && wantsMotionClass() ) {
				if (
					! slide.classList.contains( 'hvn-realty-hero-ken-burns' ) &&
					! slide.classList.contains( 'hvn-realty-hero-soft-scale' )
				) {
					slide.classList.add( motionClassName() );
				}
				return;
			}
			if ( ! on ) {
				slide.classList.remove( 'hvn-realty-hero-ken-burns', 'hvn-realty-hero-soft-scale' );
			}
		}

		function syncDots( activeIndex ) {
			dots.forEach( function ( dot, i ) {
				var active = i === activeIndex;
				dot.classList.toggle( 'hvn-realty-is-active', active );
				dot.setAttribute( 'aria-current', active ? 'true' : 'false' );
			} );
		}

		slides.forEach( function ( slide, i ) {
			slide.style.transitionDuration = transitionMs + 'ms';
			slide.classList.toggle( 'hvn-realty-is-active', i === 0 );
			slide.classList.remove( 'hvn-realty-is-outgoing' );
			applyMotion( slide, i === 0 );
		} );
		syncDots( 0 );

		function goTo( next, fromUser ) {
			if ( next === index || next < 0 || next >= slides.length ) {
				return;
			}
			if ( transitioning && ! fromUser ) {
				return;
			}

			if ( transitionTimer ) {
				window.clearTimeout( transitionTimer );
				transitionTimer = null;
			}

			transitioning = true;

			var outgoing = slides[ index ];
			var incoming = slides[ next ];

			/* Overlap fades; keep outgoing motion until opacity finishes (avoids scale jump). */
			incoming.classList.remove( 'hvn-realty-is-outgoing' );
			applyMotion( incoming, true );

			outgoing.classList.remove( 'hvn-realty-is-active' );
			outgoing.classList.add( 'hvn-realty-is-outgoing' );
			incoming.classList.add( 'hvn-realty-is-active' );

			index = next;
			syncDots( index );

			transitionTimer = window.setTimeout( function () {
				outgoing.classList.remove( 'hvn-realty-is-outgoing' );
				applyMotion( outgoing, false );
				transitioning = false;
				transitionTimer = null;
			}, transitionMs + 40 );

			if ( fromUser && autoplayEnabled && ! paused && ! reduceMotion ) {
				start();
			}
		}

		function nextSlide() {
			var next = index + 1;
			if ( next >= slides.length ) {
				if ( ! loop ) {
					stop();
					return;
				}
				next = 0;
			}
			goTo( next, false );
		}

		function prevSlide() {
			var prev = index - 1;
			if ( prev < 0 ) {
				if ( ! loop ) {
					return;
				}
				prev = slides.length - 1;
			}
			goTo( prev, true );
		}

		function stop() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		}

		function start() {
			stop();
			if ( ! autoplayEnabled || reduceMotion || paused ) {
				return;
			}
			timer = window.setInterval( nextSlide, autoplayMs );
		}

		dots.forEach( function ( dot ) {
			dot.addEventListener( 'click', function () {
				var target = parseInt( dot.getAttribute( 'data-hvn-hero-slide' ), 10 );
				if ( isNaN( target ) ) {
					return;
				}
				goTo( target, true );
			} );
		} );

		if ( nextBtn ) {
			nextBtn.addEventListener( 'click', function () {
				var next = index + 1;
				if ( next >= slides.length ) {
					next = loop ? 0 : index;
				}
				goTo( next, true );
			} );
		}
		if ( prevBtn ) {
			prevBtn.addEventListener( 'click', function () {
				prevSlide();
			} );
		}

		if ( pauseHover && autoplayEnabled && ! reduceMotion ) {
			var hero = slider.closest( '.hvn-realty-hero' ) || slider;
			hero.addEventListener( 'mouseenter', function () {
				paused = true;
				stop();
			} );
			hero.addEventListener( 'mouseleave', function () {
				paused = false;
				start();
			} );
		}

		start();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', hvnRealtyHeroCarousel );
	} else {
		hvnRealtyHeroCarousel();
	}
} )();
