/**

 * Customizer homepage section order control.

 */

( function ( $ ) {

	'use strict';



	var cx = window.hvnRealtyCx || {};



	function collectOrder( $root ) {

		var order = [];



		$root.find( '.hvn-realty-section-order-control__item' ).each( function () {

			var slug = $( this ).attr( 'data-slug' );

			if ( slug ) {

				order.push( slug );

			}

		} );



		return order;

	}



	function syncValue( $root ) {

		var $input = $root.find( '.hvn-realty-section-order-control__value' ).first();



		if ( cx.syncHiddenInput ) {

			cx.syncHiddenInput( $input, function () {

				return collectOrder( $root );

			} );

			return;

		}



		$input.val( JSON.stringify( collectOrder( $root ) ) ).trigger( 'change' );

	}



	function bindControl( $root ) {

		if ( $root.data( 'hvnSectionOrderBound' ) ) {

			return;

		}



		$root.data( 'hvnSectionOrderBound', true );



		if ( cx.initSortable ) {

			cx.initSortable( $root.find( '[data-hvn-realty-cx-sortable]' ).first(), {

				update: function () {

					syncValue( $root );

				},

			} );

		}

	}



	if ( wp.customize && wp.customize.Control ) {
		wp.customize.controlConstructor.hvn_realty_section_order = wp.customize.Control.extend( {
			ready: function () {
				bindControl( this.container.find( '[data-hvn-realty-section-order-control]' ).first() );
			},
		} );
	}

	wp.customize.bind( 'ready', function () {
		$( '[data-hvn-realty-section-order-control]' ).each( function () {
			bindControl( $( this ) );
		} );
	} );

}( jQuery ) );


