/**
 * Header account + favorites dropdown behaviour.
 *
 * Lightweight: open/close, Escape, outside click, guest count / list from
 * localStorage + on-page favorite button metadata (no extra queries).
 * Heart visibility follows favorite count (hidden when empty).
 *
 * @package Havenlytics_Realty
 */
( function () {
	'use strict';

	var cfg = window.hvnRealtyHeaderAccount || {};
	var STORAGE_KEY = cfg.storageKey || 'hvnly_guest_favorites';
	var HOME_URL = cfg.homeUrl || '/';
	var I18N = cfg.i18n || {};

	function getGuestIds() {
		try {
			if ( window.hvnlyFavorites && typeof window.hvnlyFavorites.getIds === 'function' ) {
				if ( ! ( window.hvnlyFavoritesData && window.hvnlyFavoritesData.isLoggedIn ) ) {
					return window.hvnlyFavorites.getIds();
				}
			}
			var raw = window.localStorage.getItem( STORAGE_KEY );
			if ( ! raw ) {
				return [];
			}
			var parsed = JSON.parse( raw );
			if ( ! Array.isArray( parsed ) ) {
				return [];
			}
			return parsed.map( function ( id ) {
				return parseInt( id, 10 );
			} ).filter( function ( id ) {
				return id > 0;
			} );
		} catch ( e ) {
			return [];
		}
	}

	function getFavoriteCount() {
		var loggedIn = window.hvnlyFavoritesData && window.hvnlyFavoritesData.isLoggedIn;

		if ( loggedIn ) {
			if ( window.hvnlyFavorites && typeof window.hvnlyFavorites.getIds === 'function' ) {
				return window.hvnlyFavorites.getIds().length;
			}
			return Array.isArray( window.hvnlyFavoritesData.ids ) ? window.hvnlyFavoritesData.ids.length : 0;
		}

		return getGuestIds().length;
	}

	function getMetaForId( id ) {
		var btn = document.querySelector( '[data-hvnly-favorite][data-property-id="' + id + '"]' );
		if ( ! btn ) {
			return { title: '', thumb: '', url: HOME_URL.replace( /\/?$/, '/' ) + '?p=' + id };
		}
		return {
			title: btn.getAttribute( 'data-property-title' ) || '',
			thumb: btn.getAttribute( 'data-property-thumb' ) || '',
			url: HOME_URL.replace( /\/?$/, '/' ) + '?p=' + id,
		};
	}

	function closePanel( root, toggleSel, panelSel ) {
		var toggle = root.querySelector( toggleSel );
		var panel = root.querySelector( panelSel );
		if ( ! toggle || ! panel ) {
			return;
		}
		toggle.setAttribute( 'aria-expanded', 'false' );
		panel.hidden = true;
	}

	function openPanel( root, toggleSel, panelSel ) {
		var toggle = root.querySelector( toggleSel );
		var panel = root.querySelector( panelSel );
		if ( ! toggle || ! panel ) {
			return;
		}
		toggle.setAttribute( 'aria-expanded', 'true' );
		panel.hidden = false;
	}

	function closeAll( except ) {
		document.querySelectorAll( '[data-hvn-header-account]' ).forEach( function ( root ) {
			if ( except && root === except ) {
				return;
			}
			closePanel( root, '[data-hvn-header-fav-toggle]', '[data-hvn-header-fav-panel]' );
			closePanel( root, '[data-hvn-header-user-toggle]', '[data-hvn-header-user-menu]' );
		} );
	}

	function syncFavVisibility( count ) {
		document.querySelectorAll( '[data-hvn-header-fav]' ).forEach( function ( el ) {
			var root = el.closest( '[data-hvn-header-account]' );
			if ( count > 0 ) {
				el.hidden = false;
			} else {
				el.hidden = true;
				if ( root ) {
					closePanel( root, '[data-hvn-header-fav-toggle]', '[data-hvn-header-fav-panel]' );
				}
			}
		} );

		document.querySelectorAll( '[data-hvn-header-fav-count]' ).forEach( function ( el ) {
			if ( count > 0 ) {
				el.textContent = String( count );
				el.hidden = false;
				el.classList.remove( 'is-empty' );
			} else {
				el.textContent = '';
				el.hidden = true;
				el.classList.add( 'is-empty' );
			}
		} );
	}

	function syncGuestLists() {
		var loggedIn = window.hvnlyFavoritesData && window.hvnlyFavoritesData.isLoggedIn;
		document.querySelectorAll( '[data-hvn-header-account]' ).forEach( function ( root ) {
			if ( root.getAttribute( 'data-hvn-logged-in' ) === '1' || loggedIn ) {
				return;
			}
			var list = root.querySelector( '[data-hvn-header-fav-list]' );
			if ( ! list ) {
				return;
			}
			var ids = getGuestIds().slice( 0, 3 );
			list.innerHTML = '';
			if ( ! ids.length ) {
				return;
			}
			ids.forEach( function ( id ) {
				var meta = getMetaForId( id );
				var li = document.createElement( 'li' );
				var a = document.createElement( 'a' );
				a.href = meta.url;
				if ( meta.thumb ) {
					var img = document.createElement( 'img' );
					img.src = meta.thumb;
					img.alt = '';
					img.width = 40;
					img.height = 40;
					img.loading = 'lazy';
					img.decoding = 'async';
					a.appendChild( img );
				}
				var span = document.createElement( 'span' );
				span.textContent = meta.title || I18N.property || 'Saved property';
				a.appendChild( span );
				li.appendChild( a );
				list.appendChild( li );
			} );
		} );
	}

	function bindRoot( root ) {
		var favToggle = root.querySelector( '[data-hvn-header-fav-toggle]' );
		var userToggle = root.querySelector( '[data-hvn-header-user-toggle]' );

		if ( favToggle ) {
			favToggle.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				var wrap = root.querySelector( '[data-hvn-header-fav]' );
				if ( wrap && wrap.hidden ) {
					return;
				}
				var panel = root.querySelector( '[data-hvn-header-fav-panel]' );
				var open = panel && ! panel.hidden;
				closeAll();
				if ( ! open ) {
					syncGuestLists();
					openPanel( root, '[data-hvn-header-fav-toggle]', '[data-hvn-header-fav-panel]' );
				}
			} );
		}

		var favPanel = root.querySelector( '[data-hvn-header-fav-panel]' );
		if ( favPanel ) {
			favPanel.addEventListener( 'click', function ( e ) {
				e.stopPropagation();
			} );
		}

		if ( userToggle ) {
			userToggle.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				var menu = root.querySelector( '[data-hvn-header-user-menu]' );
				var open = menu && ! menu.hidden;
				closeAll();
				if ( ! open ) {
					openPanel( root, '[data-hvn-header-user-toggle]', '[data-hvn-header-user-menu]' );
				}
			} );
		}

		var userMenu = root.querySelector( '[data-hvn-header-user-menu]' );
		if ( userMenu ) {
			userMenu.addEventListener( 'click', function ( e ) {
				e.stopPropagation();
			} );
		}
	}

	function refresh() {
		var count = getFavoriteCount();
		syncFavVisibility( count );
		if ( count > 0 ) {
			syncGuestLists();
		}
	}

	function init() {
		document.querySelectorAll( '[data-hvn-header-account]' ).forEach( bindRoot );
		refresh();

		document.addEventListener( 'click', function () {
			closeAll();
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				closeAll();
			}
		} );

		document.addEventListener( 'hvnly:favorites:changed', refresh );
		document.addEventListener( 'hvnly:favorites:merged', refresh );
		document.addEventListener( 'hvnly:favorites:refresh', refresh );
		window.addEventListener( 'storage', function ( e ) {
			if ( e.key === STORAGE_KEY ) {
				refresh();
			}
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
