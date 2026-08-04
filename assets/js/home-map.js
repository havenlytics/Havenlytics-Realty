/**

 * Havenlytics Realty — Homepage interactive property map (Leaflet).

 *

 * Uses the custom tooltip from the Homepage 3.0 design spec.

 * Never uses the default Leaflet popup.

 *

 * @package Havenlytics_Realty

 */

( function () {

	'use strict';



	var cfg = window.hvnRealtyHomeMap || {};

	var PROPERTIES = Array.isArray( cfg.markers ) ? cfg.markers : [];

	var i18n = cfg.i18n || {};

	var FAV_STORAGE_KEY = 'hvnly_guest_favorites';

	var TOOLTIP_FOCUSABLE_IDS = [

		'hvnRealtyMapTooltipClose',

		'hvnRealtyMapTooltipFav',

		'hvnRealtyMapTooltipLink'

	];



	function formatI18n( template, value ) {

		if ( ! template ) {

			return String( value || '' );

		}

		return String( template ).replace( '%s', value ).replace( '%d', value );

	}



	function markerAriaLabel( title ) {

		var trimmed = ( title || '' ).toString().trim();

		if ( trimmed ) {

			return formatI18n(

				i18n.openPreviewNamed || 'Open property preview: %s',

				trimmed

			);

		}

		return i18n.openPreview || 'Open property preview';

	}



	function clusterAriaLabel( count ) {

		return formatI18n(

			i18n.clusterLabel || 'Show %d properties in this area',

			count

		);

	}



	function mapPinIcon( status ) {

		var cls = 'hvn-realty-map-pin' + ( status === 'open' ? ' hvn-realty-map-pin--open' : '' );

		var html = '<div class="' + cls + '" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10h14V10"/></svg></div>';

		return window.L.divIcon( { className: 'hvn-realty-map-pin-wrap', html: html, iconSize: [ 40, 40 ], iconAnchor: [ 20, 38 ] } );

	}



	function mapClusterIcon( cluster ) {

		var count = cluster.getChildCount();

		return window.L.divIcon( {

			className: 'hvn-realty-map-cluster-wrap',

			html: '<div class="hvn-realty-map-cluster" aria-hidden="true">' + count + '</div>',

			iconSize: [ 52, 52 ]

		} );

	}



	function mapMetaRow( p ) {

		var parts = [];

		if ( p.beds ) {

			parts.push( '<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 9v9M2 13h20M2 9h18a2 2 0 0 1 2 2v7M6 13V7a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v6"/></svg>' + p.beds + ' ' + ( i18n.beds || 'bd' ) + '</span>' );

		}

		if ( p.baths ) {

			parts.push( '<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 12h16M6 12V6a2 2 0 0 1 2-2h2m6 8v6M4 12v6M20 12v6"/></svg>' + p.baths + ' ' + ( i18n.baths || 'ba' ) + '</span>' );

		}

		if ( p.area ) {

			parts.push( '<span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>' + p.area + '</span>' );

		}

		return parts.join( '' );

	}



	function getFavoriteIds() {

		try {

			if ( window.hvnlyFavorites && typeof window.hvnlyFavorites.getIds === 'function' ) {

				return window.hvnlyFavorites.getIds().map( function ( id ) {

					return parseInt( id, 10 );

				} ).filter( function ( id ) {

					return id > 0;

				} );

			}

			if ( window.hvnlyFavoritesData && Array.isArray( window.hvnlyFavoritesData.ids ) ) {

				return window.hvnlyFavoritesData.ids.map( function ( id ) {

					return parseInt( id, 10 );

				} ).filter( function ( id ) {

					return id > 0;

				} );

			}

			var raw = window.localStorage.getItem( FAV_STORAGE_KEY );

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



	function isPropertyFavorited( propertyId ) {

		var id = parseInt( propertyId, 10 );

		if ( ! id ) {

			return false;

		}

		return getFavoriteIds().indexOf( id ) !== -1;

	}



	function syncFavButtonState( favBtn, propertyId ) {

		if ( ! favBtn ) {

			return;

		}

		var active = isPropertyFavorited( propertyId );

		favBtn.classList.toggle( 'hvn-realty-is-active', active );

		favBtn.classList.toggle( 'is-favorited', active );

		favBtn.setAttribute( 'aria-pressed', active ? 'true' : 'false' );

		favBtn.setAttribute(

			'aria-label',

			active ? ( i18n.removeFavorite || 'Remove property from favorites' ) : ( i18n.saveFavorite || 'Save property' )

		);

		var svg = favBtn.querySelector( 'svg' );

		if ( svg ) {

			svg.setAttribute( 'fill', active ? 'currentColor' : 'none' );

			svg.setAttribute( 'aria-hidden', 'true' );

		}

	}



	function getTooltipControls() {

		return TOOLTIP_FOCUSABLE_IDS.map( function ( id ) {

			return document.getElementById( id );

		} ).filter( Boolean );

	}



	function getTooltipFocusables() {

		return getTooltipControls().filter( function ( el ) {

			return el.getAttribute( 'tabindex' ) !== '-1' && ! el.disabled;

		} );

	}



	function tooltipContainsFocus() {

		return !!( hvnTooltipEl && hvnTooltipEl.contains( document.activeElement ) );

	}



	function setTooltipOpenState( open ) {

		if ( ! hvnTooltipEl ) {

			return;

		}



		var controls = getTooltipControls();



		if ( open ) {

			hvnTooltipEl.removeAttribute( 'inert' );

			hvnTooltipEl.setAttribute( 'aria-hidden', 'false' );

			controls.forEach( function ( el ) {

				el.removeAttribute( 'tabindex' );

			} );

			return;

		}



		hvnTooltipEl.setAttribute( 'aria-hidden', 'true' );

		hvnTooltipEl.setAttribute( 'inert', '' );

		controls.forEach( function ( el ) {

			el.setAttribute( 'tabindex', '-1' );

		} );

	}



	function focusMarkerIcon( marker ) {

		if ( ! marker || typeof marker.getElement !== 'function' ) {

			return;

		}

		var el = marker.getElement();

		if ( el && typeof el.focus === 'function' ) {

			try {

				el.focus( { preventScroll: true } );

			} catch ( err ) {

				el.focus();

			}

		}

	}



	function labelMarkerElement( marker, title ) {

		var el = marker && typeof marker.getElement === 'function' ? marker.getElement() : null;

		if ( ! el ) {

			return;

		}

		el.setAttribute( 'aria-label', markerAriaLabel( title ) );

		if ( ! el.getAttribute( 'role' ) ) {

			el.setAttribute( 'role', 'button' );

		}

		if ( ! el.hasAttribute( 'tabindex' ) ) {

			el.setAttribute( 'tabindex', '0' );

		}

	}



	function labelClusterIcons( root ) {

		if ( ! root ) {

			return;

		}

		var icons = root.querySelectorAll( '.leaflet-marker-icon' );

		Array.prototype.forEach.call( icons, function ( el ) {

			var cluster = el.querySelector( '.hvn-realty-map-cluster' );

			if ( ! cluster ) {

				return;

			}

			var count = parseInt( cluster.textContent, 10 );

			if ( isNaN( count ) || count < 1 ) {

				count = 1;

			}

			el.setAttribute( 'aria-label', clusterAriaLabel( count ) );

			if ( ! el.getAttribute( 'role' ) ) {

				el.setAttribute( 'role', 'button' );

			}

		} );

	}



	function refreshMarkerLabels( clusterGroup ) {

		if ( ! clusterGroup || typeof clusterGroup.eachLayer !== 'function' ) {

			return;

		}

		clusterGroup.eachLayer( function ( layer ) {

			if ( ! layer || typeof layer.getLatLng !== 'function' ) {

				return;

			}

			var latLng = layer.getLatLng();

			var match = null;

			for ( var i = 0; i < PROPERTIES.length; i++ ) {

				if ( PROPERTIES[ i ].lat === latLng.lat && PROPERTIES[ i ].lng === latLng.lng ) {

					match = PROPERTIES[ i ];

					break;

				}

			}

			if ( match ) {

				labelMarkerElement( layer, match.title );

			}

		} );

	}



	var hvnMap;

	var hvnTooltipEl;

	var hvnActiveMarker;

	var hvnHideTimer;

	var hvnActiveProperty;

	var hvnTooltipKeyHandler;



	function hvnPositionTooltip( marker ) {

		var stage = document.getElementById( 'hvnRealtyMapStage' );

		if ( ! stage || ! hvnMap || ! hvnTooltipEl ) {

			return;

		}



		var stageW = stage.clientWidth;

		var stageH = stage.clientHeight;

		var point = hvnMap.latLngToContainerPoint( marker.getLatLng() );

		var ttW = hvnTooltipEl.offsetWidth || 296;

		var ttH = hvnTooltipEl.offsetHeight || 280;

		var pad = 12;

		var gap = 22;

		var isNarrow = stageW < 768;



		var spaceAbove = point.y - pad;

		var spaceBelow = stageH - point.y - pad;

		var preferBelow = isNarrow || spaceAbove < ( ttH + gap );

		var centerInStage = false;



		if ( preferBelow ) {

			if ( spaceBelow < ( ttH + gap ) ) {

				if ( spaceAbove >= ( ttH + gap ) ) {

					preferBelow = false;

				} else {

					centerInStage = true;

				}

			}

		} else if ( spaceAbove < ( ttH + gap ) ) {

			preferBelow = true;

			if ( spaceBelow < ( ttH + gap ) ) {

				centerInStage = true;

			}

		}



		hvnTooltipEl.classList.toggle( 'hvn-realty-map-tooltip--below', preferBelow && ! centerInStage );

		hvnTooltipEl.classList.toggle( 'hvn-realty-map-tooltip--centered', centerInStage );



		var left;

		var top;



		if ( centerInStage ) {

			left = stageW / 2;

			top = stageH / 2;

		} else {

			left = point.x;

			var minX = pad + ( ttW / 2 );

			var maxX = Math.max( minX, stageW - pad - ( ttW / 2 ) );

			left = Math.min( Math.max( left, minX ), maxX );



			top = point.y;

			if ( preferBelow ) {

				var maxTop = Math.max( pad, stageH - pad - ttH - gap );

				top = Math.min( Math.max( top, pad ), maxTop );

			} else {

				var minTop = pad + ttH + gap;

				top = Math.min( Math.max( top, minTop ), stageH - pad );

			}

		}



		hvnTooltipEl.style.left = left + 'px';

		hvnTooltipEl.style.top = top + 'px';

	}



	function scheduleHideTooltip() {

		if ( hvnHideTimer ) {

			clearTimeout( hvnHideTimer );

		}

		hvnHideTimer = setTimeout( function () {

			if ( tooltipContainsFocus() ) {

				return;

			}

			hvnHideTooltip( { returnFocus: false } );

		}, 260 );

	}



	function bindTooltipKeyboard() {

		if ( hvnTooltipKeyHandler ) {

			return;

		}

		hvnTooltipKeyHandler = function ( e ) {

			if ( ! hvnTooltipEl || ! hvnTooltipEl.classList.contains( 'hvn-realty-is-visible' ) ) {

				return;

			}



			if ( e.key === 'Escape' || e.keyCode === 27 ) {

				e.preventDefault();

				hvnHideTooltip( { returnFocus: true } );

				return;

			}



			if ( e.key !== 'Tab' && e.keyCode !== 9 ) {

				return;

			}



			var focusables = getTooltipFocusables();

			if ( ! focusables.length ) {

				return;

			}



			var first = focusables[ 0 ];

			var last = focusables[ focusables.length - 1 ];

			var active = document.activeElement;



			if ( e.shiftKey ) {

				if ( active === first || ! hvnTooltipEl.contains( active ) ) {

					e.preventDefault();

					last.focus();

				}

			} else if ( active === last || ! hvnTooltipEl.contains( active ) ) {

				e.preventDefault();

				first.focus();

			}

		};

		document.addEventListener( 'keydown', hvnTooltipKeyHandler, true );

	}



	function hvnShowTooltip( marker, p, opts ) {

		opts = opts || {};



		if ( hvnActiveMarker && hvnActiveMarker !== marker ) {

			var prevEl = hvnActiveMarker.getElement();

			if ( prevEl ) {

				var prevPin = prevEl.querySelector( '.hvn-realty-map-pin' );

				if ( prevPin ) {

					prevPin.classList.remove( 'hvn-realty-is-active' );

				}

			}

		}

		hvnActiveMarker = marker;

		hvnActiveProperty = p;

		var el = marker.getElement();

		if ( el ) {

			var pin = el.querySelector( '.hvn-realty-map-pin' );

			if ( pin ) {

				pin.classList.add( 'hvn-realty-is-active' );

			}

			labelMarkerElement( marker, p && p.title );

		}



		var img = document.getElementById( 'hvnRealtyMapTooltipImg' );

		var price = document.getElementById( 'hvnRealtyMapTooltipPrice' );

		var title = document.getElementById( 'hvnRealtyMapTooltipTitle' );

		var address = document.getElementById( 'hvnRealtyMapTooltipAddress' );

		var meta = document.getElementById( 'hvnRealtyMapTooltipMeta' );

		var link = document.getElementById( 'hvnRealtyMapTooltipLink' );

		var favBtn = document.getElementById( 'hvnRealtyMapTooltipFav' );

		var closeBtn = document.getElementById( 'hvnRealtyMapTooltipClose' );



		var thumbUrl = p.image || p.thumb || '';

		var titleText = ( p.title || '' ).toString().trim();

		var propertyId = p.id ? String( p.id ) : '';



		if ( img ) {

			img.src = thumbUrl;

			img.alt = titleText || ( i18n.openPreview || 'Property preview' );

		}

		if ( price ) {

			price.textContent = p.price || '';

		}

		if ( title ) {

			title.textContent = titleText;

		}

		if ( address ) {

			address.textContent = p.address || '';

		}

		if ( meta ) {

			meta.innerHTML = mapMetaRow( p );

		}

		if ( link ) {

			link.href = p.url || '#';

			link.textContent = i18n.view || 'View Property';

			link.setAttribute(

				'aria-label',

				titleText

					? formatI18n( i18n.viewNamed || 'View property: %s', titleText )

					: ( i18n.view || 'View Property' )

			);

		}

		if ( favBtn ) {

			favBtn.setAttribute( 'data-hvnly-favorite', '1' );

			favBtn.setAttribute( 'data-property-id', propertyId );

			favBtn.setAttribute( 'data-property-title', titleText );

			favBtn.setAttribute( 'data-property-thumb', thumbUrl );

			syncFavButtonState( favBtn, propertyId );

		}

		var cmpBtn = document.getElementById( 'hvnRealtyMapTooltipCmp' );
		if ( cmpBtn ) {
			cmpBtn.hidden = false;
			cmpBtn.setAttribute( 'data-hvnly-compare', '1' );
			cmpBtn.setAttribute( 'data-hvnly-compare-native', '1' );
			cmpBtn.setAttribute( 'data-property-id', propertyId );
			cmpBtn.setAttribute( 'data-property-title', titleText );
			cmpBtn.setAttribute( 'data-property-thumb', thumbUrl );
			cmpBtn.setAttribute( 'aria-pressed', 'false' );
			cmpBtn.setAttribute( 'aria-label', i18n.compare || 'Add to compare' );
		}

		if ( closeBtn ) {

			closeBtn.setAttribute( 'aria-label', i18n.closePreview || 'Close property preview' );

		}



		hvnTooltipEl.classList.add( 'hvn-realty-is-visible' );

		setTooltipOpenState( true );

		hvnPositionTooltip( marker );

		bindTooltipKeyboard();



		if ( opts.moveFocus && closeBtn ) {

			window.requestAnimationFrame( function () {

				try {

					closeBtn.focus( { preventScroll: true } );

				} catch ( err ) {

					closeBtn.focus();

				}

			} );

		}

	}



	function hvnHideTooltip( opts ) {

		opts = opts || {};

		if ( ! hvnTooltipEl ) {

			return;

		}



		var markerToFocus = hvnActiveMarker;

		var returnFocus = opts.returnFocus !== false;



		hvnTooltipEl.classList.remove( 'hvn-realty-is-visible' );

		hvnTooltipEl.classList.remove( 'hvn-realty-map-tooltip--centered' );

		setTooltipOpenState( false );



		if ( hvnActiveMarker ) {

			var el = hvnActiveMarker.getElement();

			if ( el ) {

				var pin = el.querySelector( '.hvn-realty-map-pin' );

				if ( pin ) {

					pin.classList.remove( 'hvn-realty-is-active' );

				}

			}

			hvnActiveMarker = null;

		}

		hvnActiveProperty = null;



		if ( returnFocus && markerToFocus ) {

			window.requestAnimationFrame( function () {

				focusMarkerIcon( markerToFocus );

			} );

		}

	}



	function initMap() {

		var mapEl = document.getElementById( 'hvnRealtyPropertyMap' );

		if ( ! mapEl || typeof window.L === 'undefined' ) {

			return;

		}



		hvnTooltipEl = document.getElementById( 'hvnRealtyMapTooltip' );

		if ( ! hvnTooltipEl ) {

			return;

		}



		setTooltipOpenState( false );



		var center = [ 30.305, -97.755 ];

		if ( PROPERTIES.length ) {

			center = [ PROPERTIES[ 0 ].lat, PROPERTIES[ 0 ].lng ];

		}



		hvnMap = window.L.map( mapEl, {

			center: center,

			zoom: 11,

			minZoom: 3,

			maxZoom: 17,

			zoomControl: false,

			scrollWheelZoom: false,

			attributionControl: true,

			zoomAnimation: true

		} );

		window.L.control.zoom( { position: 'bottomright' } ).addTo( hvnMap );

		window.L.tileLayer( 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {

			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions" target="_blank" rel="noopener">CARTO</a>',

			subdomains: 'abcd',

			maxZoom: 19

		} ).addTo( hvnMap );



		var clusterGroup = window.L.markerClusterGroup( {

			iconCreateFunction: mapClusterIcon,

			showCoverageOnHover: false,

			spiderfyOnMaxZoom: true,

			maxClusterRadius: 56

		} );



		var bounds = [];

		PROPERTIES.forEach( function ( p ) {

			if ( ! p.lat || ! p.lng ) {

				return;

			}

			var marker = window.L.marker( [ p.lat, p.lng ], {

				icon: mapPinIcon( p.status ),

				keyboard: true,

				title: ( p.title || '' ).toString(),

				alt: markerAriaLabel( p.title )

			} );



			marker.on( 'add', function () {

				labelMarkerElement( marker, p.title );

			} );



			marker.on( 'mouseover', function () {

				if ( hvnHideTimer ) {

					clearTimeout( hvnHideTimer );

				}

				hvnShowTooltip( marker, p, { moveFocus: false } );

			} );

			marker.on( 'mouseout', scheduleHideTooltip );

			marker.on( 'click', function () {

				if ( hvnHideTimer ) {

					clearTimeout( hvnHideTimer );

				}

				// Leaflet maps Enter/Space to click when keyboard:true.

				hvnShowTooltip( marker, p, { moveFocus: true } );

			} );



			clusterGroup.addLayer( marker );

			bounds.push( [ p.lat, p.lng ] );

		} );

		hvnMap.addLayer( clusterGroup );



		function refreshMapA11yLabels() {

			window.requestAnimationFrame( function () {

				labelClusterIcons( mapEl );

				refreshMarkerLabels( clusterGroup );

			} );

		}



		clusterGroup.on( 'animationend clusteringend spiderfied unspiderfied', refreshMapA11yLabels );

		refreshMapA11yLabels();



		if ( bounds.length > 1 ) {

			hvnMap.fitBounds( bounds, { padding: [ 40, 40 ], maxZoom: 13 } );

		} else if ( bounds.length === 1 ) {

			hvnMap.setView( bounds[ 0 ], 12 );

		}



		hvnMap.on( 'move zoom', function () {

			if ( hvnActiveMarker ) {

				hvnPositionTooltip( hvnActiveMarker );

			}

			labelClusterIcons( mapEl );

		} );

		hvnMap.on( 'click', function () {

			hvnHideTooltip( { returnFocus: false } );

		} );



		hvnTooltipEl.addEventListener( 'mouseenter', function () {

			if ( hvnHideTimer ) {

				clearTimeout( hvnHideTimer );

			}

		} );

		hvnTooltipEl.addEventListener( 'mouseleave', scheduleHideTooltip );



		var closeBtn = document.getElementById( 'hvnRealtyMapTooltipClose' );

		if ( closeBtn ) {

			closeBtn.addEventListener( 'click', function () {

				hvnHideTooltip( { returnFocus: true } );

			} );

		}



		// Favorite clicks are handled by the plugin on [data-hvnly-favorite].

		// Do not stopPropagation — plugin may use document-level delegation.

		// Re-sync visual state after plugin updates storage/DOM.

		var favBtn = document.getElementById( 'hvnRealtyMapTooltipFav' );

		if ( favBtn ) {

			favBtn.addEventListener( 'click', function () {

				window.setTimeout( function () {

					syncFavButtonState( favBtn, favBtn.getAttribute( 'data-property-id' ) );

				}, 0 );

				window.setTimeout( function () {

					syncFavButtonState( favBtn, favBtn.getAttribute( 'data-property-id' ) );

				}, 250 );

			} );

		}



		window.addEventListener( 'resize', function () {

			if ( hvnMap ) {

				hvnMap.invalidateSize();

			}

			if ( hvnActiveMarker ) {

				hvnPositionTooltip( hvnActiveMarker );

			}

		} );

	}



	if ( document.readyState === 'loading' ) {

		document.addEventListener( 'DOMContentLoaded', initMap );

	} else {

		initMap();

	}



	window.hvnRealtyHomeMapInit = initMap;

} )();


