/**
 * Floating contact action menu — toggle, Escape, focus return, reverse close.
 *
 * @package Havenlytics_Realty
 */
( function () {
	'use strict';

	function initFab() {
		var root = document.getElementById( 'hvnRealtyFab' );
		var toggle = document.getElementById( 'hvnRealtyFabToggle' );
		var menu = document.getElementById( 'hvnRealtyFabMenu' );

		if ( ! root || ! toggle || ! menu ) {
			return;
		}

		var items = Array.prototype.slice.call(
			menu.querySelectorAll( '.hvn-realty-fab__item' )
		);
		var links = Array.prototype.slice.call(
			menu.querySelectorAll( '[role="menuitem"]' )
		);
		var openLabel = toggle.getAttribute( 'data-hvn-realty-fab-open-label' ) || 'Open contact menu';
		var closeLabel = toggle.getAttribute( 'data-hvn-realty-fab-close-label' ) || 'Close contact menu';
		var closingTimer = null;
		var reduceMotion =
			window.matchMedia &&
			window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		root.style.setProperty( '--hvn-realty-fab-count', String( items.length || 1 ) );

		function isOpen() {
			return root.classList.contains( 'hvn-realty-is-open' );
		}

		function setExpanded( open ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			toggle.setAttribute( 'aria-label', open ? closeLabel : openLabel );
		}

		function openMenu() {
			if ( closingTimer ) {
				window.clearTimeout( closingTimer );
				closingTimer = null;
			}
			root.classList.remove( 'hvn-realty-is-closing' );
			menu.hidden = false;
			root.classList.add( 'hvn-realty-is-open' );
			setExpanded( true );
			if ( links[0] ) {
				window.requestAnimationFrame( function () {
					links[0].focus();
				} );
			}
		}

		function closeMenu( returnFocus ) {
			if ( ! isOpen() && ! root.classList.contains( 'hvn-realty-is-closing' ) ) {
				return;
			}

			root.classList.add( 'hvn-realty-is-closing' );
			root.classList.remove( 'hvn-realty-is-open' );
			setExpanded( false );

			var duration = reduceMotion ? 0 : 45 * Math.max( items.length, 1 ) + 320;

			if ( closingTimer ) {
				window.clearTimeout( closingTimer );
			}

			closingTimer = window.setTimeout( function () {
				root.classList.remove( 'hvn-realty-is-closing' );
				menu.hidden = true;
				closingTimer = null;
				if ( false !== returnFocus ) {
					toggle.focus();
				}
			}, duration );
		}

		function toggleMenu() {
			if ( isOpen() ) {
				closeMenu( true );
			} else {
				openMenu();
			}
		}

		toggle.addEventListener( 'click', function ( event ) {
			event.preventDefault();
			toggleMenu();
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' !== event.key && 'Esc' !== event.key ) {
				return;
			}
			if ( ! isOpen() && ! root.classList.contains( 'hvn-realty-is-closing' ) ) {
				return;
			}
			event.preventDefault();
			closeMenu( true );
		} );

		document.addEventListener( 'click', function ( event ) {
			if ( ! isOpen() ) {
				return;
			}
			if ( root.contains( event.target ) ) {
				return;
			}
			closeMenu( false );
		} );

		toggle.addEventListener( 'keydown', function ( event ) {
			if ( 'ArrowUp' === event.key && ! isOpen() ) {
				event.preventDefault();
				openMenu();
			}
		} );

		menu.addEventListener( 'keydown', function ( event ) {
			if ( ! links.length ) {
				return;
			}
			var current = document.activeElement;
			var index = links.indexOf( current );
			if ( index < 0 ) {
				return;
			}

			if ( 'ArrowUp' === event.key ) {
				event.preventDefault();
				links[ ( index + 1 ) % links.length ].focus();
			} else if ( 'ArrowDown' === event.key ) {
				event.preventDefault();
				links[ ( index - 1 + links.length ) % links.length ].focus();
			} else if ( 'Home' === event.key ) {
				event.preventDefault();
				links[ links.length - 1 ].focus();
			} else if ( 'End' === event.key ) {
				event.preventDefault();
				links[0].focus();
			} else if ( 'Tab' === event.key ) {
				closeMenu( false );
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initFab );
	} else {
		initFab();
	}
} )();
