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

	function hvnThemeSetTestimonialDot( dots, activeDot ) {
		dots.forEach( function ( d ) {
			d.classList.remove( 'hvn-theme-home-active' );
			d.removeAttribute( 'aria-current' );
		} );
		activeDot.classList.add( 'hvn-theme-home-active' );
		activeDot.setAttribute( 'aria-current', 'true' );
	}

	function hvnThemeTestimonials() {
		var track = document.getElementById( 'hvn-theme-home-testimonial-track' );
		var nav = document.querySelector( '.hvn-theme-home-testimonial-nav' );
		if ( ! track || ! nav ) {
			return;
		}

		var dots = nav.querySelectorAll( '[data-hvn-theme-dot]' );
		if ( ! dots.length ) {
			return;
		}

		if ( track._hvnTestimonialCarousel ) {
			track._hvnTestimonialCarousel.destroy();
		}

		track._hvnTestimonialCarousel = hvnThemeCreateTestimonialCarousel( track, nav, dots );
		track._hvnTestimonialCarousel.init();
	}

	function hvnThemeCreateTestimonialCarousel( track, nav, dots ) {
		var slideCount = 0;
		var prefixCount = 0;
		var logicalIndex = 0;
		var physicalIndex = 0;
		var step = 0;
		var resizeTimer = null;
		var onTransitionEnd = null;
		var onNavClick = null;
		var onResize = null;
		var prefersReducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		function getOriginalSlides() {
			return Array.prototype.filter.call( track.children, function ( slide ) {
				return ! slide.hasAttribute( 'data-hvn-testimonial-clone' );
			} );
		}

		function measureStep() {
			var card = track.children[ prefixCount ];
			if ( ! card ) {
				return 0;
			}
			var styles = window.getComputedStyle( track );
			var gap = parseFloat( styles.columnGap || styles.gap || '0' ) || 0;
			return card.getBoundingClientRect().width + gap;
		}

		function setPhysicalIndex( index, animate ) {
			physicalIndex = index;
			if ( animate && ! prefersReducedMotion ) {
				track.style.transition = '';
			} else {
				track.style.transition = 'none';
			}
			track.style.transform = 'translate3d(' + ( -index * step ) + 'px,0,0)';
			if ( ! animate || prefersReducedMotion ) {
				void track.offsetHeight;
				track.style.transition = '';
			}
		}

		function normalizePhysicalIndex() {
			var maxReal = prefixCount + slideCount;
			var changed = false;

			if ( physicalIndex >= maxReal ) {
				physicalIndex -= slideCount;
				changed = true;
			} else if ( physicalIndex < prefixCount ) {
				physicalIndex += slideCount;
				changed = true;
			}

			if ( changed ) {
				setPhysicalIndex( physicalIndex, false );
			}
		}

		function syncDot( index ) {
			var dot = nav.querySelector( '[data-hvn-theme-dot="' + index + '"]' );
			if ( dot ) {
				hvnThemeSetTestimonialDot( dots, dot );
			}
		}

		function goToLogical( index ) {
			if ( index < 0 || index >= slideCount || index === logicalIndex ) {
				return;
			}
			logicalIndex = index;
			setPhysicalIndex( prefixCount + logicalIndex, true );
			syncDot( logicalIndex );
		}

		function buildClones() {
			var originals = getOriginalSlides();
			slideCount = originals.length;
			prefixCount = slideCount;

			if ( slideCount < 2 ) {
				return false;
			}

			originals.forEach( function ( slide ) {
				var appendClone = slide.cloneNode( true );
				appendClone.setAttribute( 'data-hvn-testimonial-clone', '1' );
				appendClone.setAttribute( 'aria-hidden', 'true' );
				track.appendChild( appendClone );
			} );

			for ( var i = slideCount - 1; i >= 0; i-- ) {
				var prependClone = originals[ i ].cloneNode( true );
				prependClone.setAttribute( 'data-hvn-testimonial-clone', '1' );
				prependClone.setAttribute( 'aria-hidden', 'true' );
				track.insertBefore( prependClone, track.firstChild );
			}

			return true;
		}

		function removeClones() {
			Array.prototype.slice.call( track.querySelectorAll( '[data-hvn-testimonial-clone]' ) ).forEach( function ( clone ) {
				clone.parentNode.removeChild( clone );
			} );
			prefixCount = 0;
			track.style.transition = 'none';
			track.style.transform = '';
			void track.offsetHeight;
			track.style.transition = '';
		}

		function getActiveDotIndex() {
			var active = nav.querySelector( '.hvn-theme-home-testimonial-dot.hvn-theme-home-active' );
			if ( ! active ) {
				return 0;
			}
			return parseInt( active.getAttribute( 'data-hvn-theme-dot' ), 10 ) || 0;
		}

		function bindEvents() {
			onTransitionEnd = function ( event ) {
				if ( event.target !== track || ( event.propertyName !== 'transform' && event.propertyName !== '-webkit-transform' ) ) {
					return;
				}
				normalizePhysicalIndex();
			};
			track.addEventListener( 'transitionend', onTransitionEnd );

			onNavClick = function ( event ) {
				var dot = event.target.closest( '[data-hvn-theme-dot]' );
				if ( ! dot || ! nav.contains( dot ) ) {
					return;
				}
				goToLogical( parseInt( dot.getAttribute( 'data-hvn-theme-dot' ), 10 ) || 0 );
			};
			nav.addEventListener( 'click', onNavClick );

			onResize = function () {
				window.clearTimeout( resizeTimer );
				resizeTimer = window.setTimeout( function () {
					var saved = logicalIndex;
					destroy( true );
					init( saved );
				}, 150 );
			};
			window.addEventListener( 'resize', onResize );
		}

		function unbindEvents() {
			if ( onTransitionEnd ) {
				track.removeEventListener( 'transitionend', onTransitionEnd );
				onTransitionEnd = null;
			}
			if ( onNavClick ) {
				nav.removeEventListener( 'click', onNavClick );
				onNavClick = null;
			}
			if ( onResize ) {
				window.removeEventListener( 'resize', onResize );
				onResize = null;
			}
			window.clearTimeout( resizeTimer );
		}

		function init( startIndex ) {
			removeClones();
			if ( ! buildClones() ) {
				return;
			}

			step = measureStep();
			if ( ! step ) {
				removeClones();
				return;
			}

			logicalIndex = typeof startIndex === 'number' ? startIndex : getActiveDotIndex();
			if ( logicalIndex < 0 || logicalIndex >= slideCount ) {
				logicalIndex = 0;
			}

			physicalIndex = prefixCount + logicalIndex;
			setPhysicalIndex( physicalIndex, false );
			syncDot( logicalIndex );
			bindEvents();
		}

		function destroy( resetTransform ) {
			unbindEvents();
			removeClones();
			if ( false !== resetTransform ) {
				track.style.transform = '';
			}
		}

		return {
			init: init,
			destroy: destroy,
		};
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
