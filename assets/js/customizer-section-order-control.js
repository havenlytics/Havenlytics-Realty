/**
 * Customizer homepage section order control.
 */
( function ( $, window ) {
	'use strict';

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

	function syncOrder( $root ) {
		var $input = $root.find( '.hvn-realty-section-order-control__value' ).first();
		if ( ! $input.length ) {
			return;
		}
		$input.val( JSON.stringify( collectOrder( $root ) ) ).trigger( 'change' );
	}

	function bindControl( $root ) {
		if ( ! $root.length || $root.data( 'hvnSectionOrderBound' ) ) {
			return;
		}
		$root.data( 'hvnSectionOrderBound', true );

		var $list = $root.find( '[data-hvn-realty-cx-sortable]' ).first();
		var cx = window.hvnRealtyCx || {};

		if ( cx.initSortable ) {
			cx.initSortable( $list, {
				handle: '.hvn-realty-cx__handle',
				update: function () {
					syncOrder( $root );
				},
			} );
		} else if ( $list.length && $.fn.sortable ) {
			$list.sortable( {
				axis: 'y',
				handle: '.hvn-realty-cx__handle',
				cancel: 'input,textarea,select,option,label',
				update: function () {
					syncOrder( $root );
				},
			} );
		}

		$root.on( 'change', '.hvn-realty-section-order-control__visible', function () {
			var $item = $( this ).closest( '.hvn-realty-section-order-control__item' );
			var settingId = $item.attr( 'data-visibility-setting' );
			var enabled = $( this ).is( ':checked' );

			if ( settingId && wp.customize( settingId ) ) {
				wp.customize( settingId ).set( enabled );
			}
		} );
	}

	if ( window.wp && wp.customize && wp.customize.Control ) {
		// Register the constructor immediately so its `ready` fires when the
		// control is embedded (controls inside panels embed lazily on open).
		wp.customize.controlConstructor.hvn_realty_section_order = wp.customize.Control.extend( {
			ready: function () {
				bindControl( this.container.find( '[data-hvn-realty-section-order-control]' ).first() );
			},
		} );

		wp.customize.bind( 'ready', function () {
			$( '[data-hvn-realty-section-order-control]' ).each( function () {
				bindControl( $( this ) );
			} );
		} );
	}
}( jQuery, window ) );
