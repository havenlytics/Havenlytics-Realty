/**
 * Havenlytics Realty — Customizer controls panel (parent frame).
 */
( function ( $ ) {
	'use strict';

	var config = window.hvnRealtyCustomizerControls || {};
	var homeSections = config.homeSections || {};

	function scrollPreviewToSection( selector ) {
		if ( ! selector || ! wp.customize.previewer ) {
			return;
		}

		wp.customize.previewer.send( 'hvn-realty-scroll-to-section', {
			selector: selector,
		} );
	}

	function bindHomepageSectionScroll() {
		if ( ! Object.keys( homeSections ).length ) {
			return;
		}

		$.each( homeSections, function ( sectionId, selector ) {
			wp.customize.section( sectionId, function ( section ) {
				section.expanded.bind( function ( isExpanded ) {
					if ( isExpanded ) {
						scrollPreviewToSection( selector );
					}
				} );
			} );
		} );
	}

	wp.customize.bind( 'ready', bindHomepageSectionScroll );
}( jQuery ) );
