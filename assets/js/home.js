/**
 * Havenlytics Realty — Homepage 2.0.0 behaviour.
 *
 * Vanilla JS only. No dependencies. Handles: sticky/transparent header,
 * mobile navigation, animated statistics, scroll reveal, search tabs,
 * testimonials slider and back-to-top.
 *
 * @package Havenlytics_Realty
 */
( function () {
	'use strict';

	function hvnThemeStickyHeader() {
		var header = document.getElementById( 'hvn-theme-home-header' );
		if ( ! header ) {
			return;
		}
		function onScroll() {
			if ( window.scrollY > 40 ) {
				header.classList.add( 'hvn-theme-home-scrolled' );
			} else {
				header.classList.remove( 'hvn-theme-home-scrolled' );
			}
		}
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	}

	function hvnThemeSmoothScroll() {
		document.querySelectorAll( 'a[href^="#"]' ).forEach( function ( link ) {
			link.addEventListener( 'click', function ( e ) {
				var id = link.getAttribute( 'href' );
				if ( ! id || id.length < 2 ) {
					return;
				}
				var target = document.querySelector( id );
				if ( ! target ) {
					return;
				}
				e.preventDefault();
				var top = target.getBoundingClientRect().top + window.pageYOffset - 90;
				window.scrollTo( { top: top, behavior: 'smooth' } );
			} );
		} );
	}

	function hvnThemeCounters() {
		var counters = document.querySelectorAll( '[data-hvn-theme-counter]' );
		if ( ! counters.length || ! ( 'IntersectionObserver' in window ) ) {
			counters.forEach( function ( el ) {
				var t = parseInt( el.getAttribute( 'data-hvn-theme-counter' ), 10 ) || 0;
				el.textContent = t.toLocaleString() + ( el.getAttribute( 'data-hvn-theme-suffix' ) || '' );
			} );
			return;
		}
		function animate( el ) {
			var target = parseInt( el.getAttribute( 'data-hvn-theme-counter' ), 10 ) || 0;
			var suffix = el.getAttribute( 'data-hvn-theme-suffix' ) || '';
			var duration = 1400;
			var start = null;
			function step( timestamp ) {
				if ( null === start ) {
					start = timestamp;
				}
				var progress = Math.min( ( timestamp - start ) / duration, 1 );
				var eased = 1 - Math.pow( 1 - progress, 3 );
				el.textContent = Math.floor( eased * target ).toLocaleString() + suffix;
				if ( progress < 1 ) {
					requestAnimationFrame( step );
				} else {
					el.textContent = target.toLocaleString() + suffix;
				}
			}
			requestAnimationFrame( step );
		}
		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					animate( entry.target );
					observer.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.5 } );
		counters.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	function hvnThemeReveal() {
		var items = document.querySelectorAll( '.hvn-theme-home-reveal' );
		if ( ! items.length ) {
			return;
		}
		if ( ! ( 'IntersectionObserver' in window ) ) {
			items.forEach( function ( el ) {
				el.classList.add( 'hvn-theme-home-in-view' );
			} );
			return;
		}
		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry, i ) {
				if ( entry.isIntersecting ) {
					setTimeout( function () {
						entry.target.classList.add( 'hvn-theme-home-in-view' );
					}, ( i % 4 ) * 80 );
					observer.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.15 } );
		items.forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	function hvnThemeSearch() {
		var tabs = document.querySelectorAll( '[data-hvn-theme-tab]' );
		var departmentInput = document.getElementById( 'hvn-theme-home-search-department' );
		var countEl = document.querySelector( '[data-hvn-theme-search-count]' );

		function hvnThemeUpdateCount( tab ) {
			if ( ! countEl ) {
				return;
			}
			var raw = tab.getAttribute( 'data-hvn-theme-count' );
			if ( null === raw ) {
				return;
			}
			var value = parseInt( raw, 10 );
			if ( isNaN( value ) ) {
				return;
			}
			countEl.textContent = value.toLocaleString();
		}

		tabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				tabs.forEach( function ( t ) {
					t.classList.remove( 'hvn-theme-home-active' );
					t.setAttribute( 'aria-selected', 'false' );
				} );
				tab.classList.add( 'hvn-theme-home-active' );
				tab.setAttribute( 'aria-selected', 'true' );
				if ( departmentInput ) {
					departmentInput.value = tab.getAttribute( 'data-hvn-theme-department' ) || '';
				}
				hvnThemeUpdateCount( tab );
			} );
		} );

		var form = document.getElementById( 'hvn-theme-home-search-form' );
		if ( form ) {
			form.addEventListener( 'submit', function ( event ) {
				if ( ! form.checkValidity() ) {
					event.preventDefault();
					form.reportValidity();
					return;
				}

				form.querySelectorAll( 'input, select, textarea' ).forEach( function ( field ) {
					if ( field.disabled || field.type === 'submit' || field.type === 'button' ) {
						return;
					}
					if ( ! field.name ) {
						return;
					}
					if ( ( field.value || '' ).trim() === '' ) {
						field.disabled = true;
					}
				} );
			} );
		}

		var moreBtn = document.getElementById( 'hvn-theme-home-search-more' );
		var advanced = document.getElementById( 'hvn-theme-home-search-advanced' );
		if ( moreBtn && advanced ) {
			moreBtn.addEventListener( 'click', function () {
				var isOpen = advanced.classList.toggle( 'is-open' );
				advanced.hidden = ! isOpen;
				moreBtn.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			} );
		}

		document.querySelectorAll( '[data-hvn-theme-wishlist]' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				btn.classList.toggle( 'hvn-theme-home-active' );
			} );
		} );
	}

	function hvnThemeTestimonials() {
		var track = document.getElementById( 'hvn-theme-home-testimonial-track' );
		var dots = document.querySelectorAll( '[data-hvn-theme-dot]' );
		if ( ! track || ! dots.length || ! track.children.length ) {
			return;
		}
		dots.forEach( function ( dot ) {
			dot.addEventListener( 'click', function () {
				var index = parseInt( dot.getAttribute( 'data-hvn-theme-dot' ), 10 ) || 0;
				var first = track.children[ 0 ];
				var gap = parseFloat( getComputedStyle( track ).columnGap || getComputedStyle( track ).gap || '0' ) || 22.4;
				var cardWidth = first.getBoundingClientRect().width + gap;
				track.style.transform = 'translateX(-' + ( index * cardWidth ) + 'px)';
				dots.forEach( function ( d ) {
					d.classList.remove( 'hvn-theme-home-active' );
				} );
				dot.classList.add( 'hvn-theme-home-active' );
			} );
		} );
	}

	function hvnThemeHome() {
		hvnThemeStickyHeader();
		hvnThemeSmoothScroll();
		hvnThemeCounters();
		hvnThemeReveal();
		hvnThemeSearch();
		hvnThemeTestimonials();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', hvnThemeHome );
	} else {
		hvnThemeHome();
	}

	/*
	 * Re-initialise section-scoped behaviours after a Customizer selective-refresh
	 * partial replaces a homepage section. Only behaviours bound to elements inside
	 * the re-rendered markup are re-run; global once-bound listeners (sticky header,
	 * smooth scroll, back-to-top) are intentionally skipped to avoid duplicates.
	 */
	window.hvnRealtyHomeReinit = function () {
		hvnThemeCounters();
		hvnThemeReveal();
		hvnThemeSearch();
		hvnThemeTestimonials();
	};
} )();
