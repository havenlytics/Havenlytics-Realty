/**
 * Havenlytics Realty — Homepage 3.0 behaviour.
 *
 * Vanilla JS only. Handles: sticky header, smooth scroll, counters,
 * scroll reveal, search console, property department chips,
 * testimonials slider.
 *
 * @package Havenlytics_Realty
 */
( function () {
	'use strict';

	function hvnThemeStickyHeader() {
		var header = document.getElementById( 'hvn-theme-home-header' );
		var heroBg = document.getElementById( 'hvnRealtyHeroBg' );
		if ( ! header ) {
			return;
		}
		function onScroll() {
			var y = window.scrollY || 0;
			if ( y > 40 ) {
				header.classList.add( 'hvn-theme-home-scrolled' );
			} else {
				header.classList.remove( 'hvn-theme-home-scrolled' );
			}
			/* Parallax only for static hero bg — never transform the carousel slider. */
			if ( heroBg && heroBg.classList.contains( 'hvn-realty-hero-bg' ) ) {
				heroBg.style.transform = 'scale(1.08) translateY(' + Math.min( y * 0.12, 60 ) + 'px)';
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
		var items = document.querySelectorAll( '.hvn-realty-reveal, .hvn-theme-home-reveal' );
		if ( ! items.length ) {
			return;
		}
		if ( ! ( 'IntersectionObserver' in window ) ) {
			items.forEach( function ( el ) {
				el.classList.add( 'hvn-realty-is-visible', 'hvn-theme-home-in-view' );
			} );
			return;
		}
		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'hvn-realty-is-visible', 'hvn-theme-home-in-view' );
					observer.unobserve( entry.target );
				}
			} );
		}, { threshold: 0.14, rootMargin: '0px 0px -40px 0px' } );
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
					t.classList.remove( 'hvn-theme-home-active', 'hvn-realty-is-active' );
					t.setAttribute( 'aria-selected', 'false' );
				} );
				tab.classList.add( 'hvn-theme-home-active', 'hvn-realty-is-active' );
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
			function syncMobileAdvanced() {
				var mobile = window.matchMedia( '(max-width: 980px)' ).matches;
				if ( mobile ) {
					advanced.hidden = false;
					advanced.classList.add( 'hvn-realty-is-expanded', 'is-open' );
					moreBtn.hidden = true;
					moreBtn.setAttribute( 'aria-expanded', 'true' );
					return;
				}
				moreBtn.hidden = false;
				advanced.hidden = true;
				advanced.classList.remove( 'hvn-realty-is-expanded', 'is-open' );
				moreBtn.classList.remove( 'hvn-realty-is-expanded' );
				moreBtn.setAttribute( 'aria-expanded', 'false' );
			}

			moreBtn.addEventListener( 'click', function () {
				if ( window.matchMedia( '(max-width: 980px)' ).matches ) {
					return;
				}
				var isOpen = advanced.classList.toggle( 'hvn-realty-is-expanded' );
				advanced.classList.toggle( 'is-open', isOpen );
				advanced.hidden = ! isOpen;
				moreBtn.classList.toggle( 'hvn-realty-is-expanded', isOpen );
				moreBtn.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			} );

			syncMobileAdvanced();
			window.addEventListener( 'resize', syncMobileAdvanced );
		}
	}

	function hvnThemePropertyChips() {
		var chips = document.querySelectorAll( '.hvn-realty-chip-row .hvn-realty-chip' );
		var cards = document.querySelectorAll( '#hvnRealtyPropGrid .hvn-realty-prop-card' );
		var grid = document.getElementById( 'hvnRealtyPropGrid' );
		if ( ! chips.length || ! cards.length ) {
			return;
		}

		var chipList = Array.prototype.slice.call( chips );

		function cardMatches( card, filter ) {
			if ( filter === 'all' ) {
				return true;
			}
			var cats = ( card.getAttribute( 'data-cat' ) || '' ).split( /\s+/ );
			return cats.indexOf( filter ) !== -1;
		}

		function setCardVisible( card, show ) {
			card.style.display = show ? '' : 'none';
			if ( show ) {
				card.removeAttribute( 'hidden' );
			} else {
				card.setAttribute( 'hidden', 'hidden' );
			}
		}

		function activateChip( chipEl, focus ) {
			chipList.forEach( function ( c ) {
				var on = c === chipEl;
				c.classList.toggle( 'hvn-realty-is-active', on );
				c.setAttribute( 'aria-selected', on ? 'true' : 'false' );
				c.setAttribute( 'tabindex', on ? '0' : '-1' );
			} );
			var filter = chipEl.getAttribute( 'data-filter' ) || 'all';
			cards.forEach( function ( card ) {
				setCardVisible( card, cardMatches( card, filter ) );
			} );
			if ( focus ) {
				chipEl.focus();
			}
		}

		chipList.forEach( function ( chipEl, index ) {
			chipEl.addEventListener( 'click', function () {
				activateChip( chipEl, false );
			} );

			chipEl.addEventListener( 'keydown', function ( event ) {
				var key = event.key;
				var nextIndex = index;

				if ( key === 'ArrowRight' || key === 'ArrowDown' ) {
					event.preventDefault();
					nextIndex = ( index + 1 ) % chipList.length;
				} else if ( key === 'ArrowLeft' || key === 'ArrowUp' ) {
					event.preventDefault();
					nextIndex = ( index - 1 + chipList.length ) % chipList.length;
				} else if ( key === 'Home' ) {
					event.preventDefault();
					nextIndex = 0;
				} else if ( key === 'End' ) {
					event.preventDefault();
					nextIndex = chipList.length - 1;
				} else if ( key === 'Enter' || key === ' ' ) {
					event.preventDefault();
					activateChip( chipEl, false );
					return;
				} else {
					return;
				}

				activateChip( chipList[ nextIndex ], true );
			} );
		} );

		// Apply initial filter from server (Active Tab = First Department).
		var initial = grid ? ( grid.getAttribute( 'data-hvn-initial-filter' ) || 'all' ) : 'all';
		var initialChip = chipList.filter( function ( c ) {
			return ( c.getAttribute( 'data-filter' ) || '' ) === initial;
		} )[ 0 ] || chipList[ 0 ];
		if ( initialChip ) {
			activateChip( initialChip, false );
		}
	}

	function hvnThemeTestimonials() {
		var track = document.getElementById( 'hvnRealtyTestiTrack' );
		var nextBtn = document.getElementById( 'hvnRealtyTestiNext' );
		var prevBtn = document.getElementById( 'hvnRealtyTestiPrev' );
		if ( ! track ) {
			return;
		}

		var wrap = track.parentElement;
		var slides = Array.prototype.slice.call( track.children );
		if ( ! slides.length || ! wrap ) {
			return;
		}

		var autoplayOn = '1' === track.getAttribute( 'data-autoplay' );
		var speed = parseInt( track.getAttribute( 'data-speed' ), 10 );
		if ( isNaN( speed ) || speed < 2000 ) {
			speed = 5000;
		}
		if ( speed > 15000 ) {
			speed = 15000;
		}

		var page = 0;
		var timer = null;
		var reduceMotion =
			window.matchMedia &&
			window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		function perView() {
			return window.matchMedia( '(max-width: 980px)' ).matches ? 1 : 3;
		}

		function pageCount() {
			var pv = perView();
			return Math.max( 1, Math.ceil( slides.length / pv ) );
		}

		function go( nextPage ) {
			var pages = pageCount();
			if ( pages < 2 ) {
				page = 0;
				track.style.transform = 'translate3d(0,0,0)';
				return;
			}

			page = ( ( nextPage % pages ) + pages ) % pages;
			var pv = perView();
			var index = page * pv;
			if ( index >= slides.length ) {
				index = Math.max( 0, slides.length - pv );
			}

			var first = slides[ index ];
			if ( ! first ) {
				track.style.transform = 'translate3d(0,0,0)';
				return;
			}

			/*
			 * Pixel offset of the page's first slide relative to the track.
			 * Avoids translateX(%) against an oversized flex track (upgrade overflow).
			 */
			var offset = Math.max( 0, first.offsetLeft - track.offsetLeft );
			track.style.transform = 'translate3d(-' + offset + 'px,0,0)';
		}

		function stop() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		}

		function start() {
			stop();
			if ( ! autoplayOn || reduceMotion || pageCount() < 2 ) {
				return;
			}
			timer = window.setInterval( function () {
				go( page + 1 );
			}, speed );
		}

		if ( nextBtn && prevBtn && pageCount() > 1 ) {
			nextBtn.addEventListener( 'click', function () {
				go( page + 1 );
				start();
			} );
			prevBtn.addEventListener( 'click', function () {
				go( page - 1 );
				start();
			} );
		} else if ( nextBtn && prevBtn ) {
			nextBtn.hidden = true;
			prevBtn.hidden = true;
		}

		wrap.addEventListener( 'mouseenter', stop );
		wrap.addEventListener( 'mouseleave', start );

		window.addEventListener( 'resize', function () {
			go( page );
			start();
		} );

		go( 0 );
		start();
	}

	function hvnThemeHome() {
		hvnThemeStickyHeader();
		hvnThemeSmoothScroll();
		hvnThemeCounters();
		hvnThemeReveal();
		hvnThemeSearch();
		hvnThemePropertyChips();
		hvnThemeTestimonials();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', hvnThemeHome );
	} else {
		hvnThemeHome();
	}

	window.hvnRealtyHomeReinit = function () {
		hvnThemeCounters();
		hvnThemeReveal();
		hvnThemeSearch();
		hvnThemePropertyChips();
		hvnThemeTestimonials();
		if ( typeof window.hvnRealtyHomeMapInit === 'function' ) {
			window.hvnRealtyHomeMapInit();
		}
	};
} )();
