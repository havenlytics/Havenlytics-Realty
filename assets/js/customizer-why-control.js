/**
 * Customizer "Why Choose Us" repeater control.
 *
 * @package Havenlytics_Realty
 */
( function ( $ ) {
	'use strict';

	var cx = window.hvnRealtyCx || {};

	function collectItems( $root ) {
		var items = [];

		$root.find( '.hvn-realty-why-control__item' ).each( function () {
			var $item = $( this );
			var title = $.trim( $item.find( '[data-field="title"]' ).val() || '' );
			var text = $.trim( $item.find( '[data-field="text"]' ).val() || '' );

			if ( ! title && ! text ) {
				return;
			}

			items.push( {
				icon: $item.find( '[data-field="icon"]' ).val() || 'shield',
				title: title,
				text: text,
				url: $.trim( $item.find( '[data-field="url"]' ).val() || '' ),
			} );
		} );

		return items;
	}

	function syncValue( $root ) {
		var $input = $root.find( '.hvn-realty-why-control__value' ).first();

		if ( cx.syncHiddenInput ) {
			cx.syncHiddenInput( $input, function () {
				return collectItems( $root );
			} );
			return;
		}

		$input.val( JSON.stringify( collectItems( $root ) ) ).trigger( 'change' );
	}

	function refreshCards( $root ) {
		if ( cx.updateRepeaterTitles ) {
			cx.updateRepeaterTitles( $root, {
				fallbackLabel: 'Feature',
			} );
		}
	}

	function bindControl( $root ) {
		if ( ! $root.length || $root.data( 'hvnWhyBound' ) ) {
			return;
		}
		$root.data( 'hvnWhyBound', true );

		if ( cx.bindCollapse ) {
			cx.bindCollapse( $root );
		}

		if ( cx.initSortable ) {
			cx.initSortable( $root.find( '[data-hvn-realty-cx-sortable]' ).first(), {
				update: function () {
					refreshCards( $root );
					syncValue( $root );
				},
			} );
		}

		$root.on( 'input change', 'input, textarea, select', function () {
			refreshCards( $root );
			syncValue( $root );
		} );

		$root.on( 'click', '.hvn-realty-why-control__add', function ( event ) {
			event.preventDefault();
			var template = $root.siblings( '.hvn-realty-why-control__template' ).html() || '';
			var index = $root.find( '.hvn-realty-why-control__item' ).length;

			$root.find( '.hvn-realty-why-control__list' ).append(
				template.replace( /\{\{index\}\}/g, String( index ) )
			);

			refreshCards( $root );
			syncValue( $root );
		} );

		$root.on( 'click', '.hvn-realty-why-control__remove', function ( event ) {
			event.preventDefault();
			$( this ).closest( '.hvn-realty-why-control__item' ).remove();
			refreshCards( $root );
			syncValue( $root );
		} );

		$root.on( 'click', '.hvn-realty-why-control__duplicate', function ( event ) {
			event.preventDefault();
			var $item = $( this ).closest( '.hvn-realty-why-control__item' );
			var $clone = $item.clone();
			var index = $root.find( '.hvn-realty-why-control__item' ).length;

			// Preserve selected values lost by clone() on <select>.
			$item.find( 'select' ).each( function ( i ) {
				$clone.find( 'select' ).eq( i ).val( $( this ).val() );
			} );

			$clone.attr( 'data-index', String( index ) );
			$clone.removeClass( 'is-collapsed' );
			$item.after( $clone );

			refreshCards( $root );
			syncValue( $root );
		} );

		refreshCards( $root );
	}

	if ( window.wp && wp.customize && wp.customize.Control ) {
		wp.customize.controlConstructor.hvn_realty_why = wp.customize.Control.extend( {
			ready: function () {
				bindControl( this.container.find( '[data-hvn-realty-why-control]' ).first() );
			},
		} );

		wp.customize.bind( 'ready', function () {
			$( '[data-hvn-realty-why-control]' ).each( function () {
				bindControl( $( this ) );
			} );
		} );
	}
}( jQuery ) );
