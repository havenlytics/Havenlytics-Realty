/**
 * Havenlytics Realty — Customizer term checklist control.
 */
( function ( $ ) {
	'use strict';

	function syncChecklist( $control ) {
		var ids = [];
		$control.find( '.hvn-realty-term-checklist__box:checked' ).each( function () {
			var id = parseInt( $( this ).val(), 10 );
			if ( id > 0 ) {
				ids.push( id );
			}
		} );
		$control.find( '.hvn-realty-term-checklist__value' ).val( JSON.stringify( ids ) ).trigger( 'change' );
	}

	$( document ).on( 'change', '.hvn-realty-term-checklist__box', function () {
		var $control = $( this ).closest( '.customize-control' );
		syncChecklist( $control );
	} );
}( jQuery ) );
