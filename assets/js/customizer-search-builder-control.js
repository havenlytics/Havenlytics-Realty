/**
 * Customizer Hero Search Builder control.
 */
( function ( $, window ) {
	'use strict';

	function collectFields( $root ) {
		var fields = [];

		$root.find( '.hvn-realty-search-builder-control__item' ).each( function () {
			var $item = $( this );
			var id = $item.attr( 'data-field-id' );

			if ( ! id || '{{id}}' === id ) {
				return;
			}

			fields.push( {
				id: id,
				enabled: $item.find( '[data-field="enabled"]' ).is( ':checked' ),
				zone: $item.find( '[data-field="zone"]' ).val() || 'primary',
				label: $.trim( $item.find( '[data-field="label"]' ).val() || '' ),
				placeholder: $.trim( $item.find( '[data-field="placeholder"]' ).val() || '' ),
				default: $.trim( $item.find( '[data-field="default"]' ).val() || '' ),
				required: $item.find( '[data-field="required"]' ).is( ':checked' ),
				width: $item.find( '[data-field="width"]' ).val() || '1',
			} );
		} );

		return fields;
	}

	function syncFields( $root ) {
		var $input = $root.find( '.hvn-realty-search-builder-control__value' ).first();
		var cx = window.hvnRealtyCx || {};

		if ( cx.syncHiddenInput ) {
			cx.syncHiddenInput( $input, function () {
				return collectFields( $root );
			} );
		} else if ( $input.length ) {
			$input.val( JSON.stringify( collectFields( $root ) ) ).trigger( 'change' );
		}
	}

	function updateCardState( $item ) {
		var enabled = $item.find( '[data-field="enabled"]' ).is( ':checked' );
		var label = $.trim( $item.find( '[data-field="label"]' ).val() || '' );
		var title = $item.find( '[data-hvn-cx-card-title]' ).first();
		var fallback = $item.attr( 'data-field-id' ) || '';

		$item.toggleClass( 'is-disabled', ! enabled );

		if ( title.length ) {
			title.text( label || fallback );
		}
	}

	function bindControl( $root ) {
		if ( ! $root.length || $root.data( 'hvnSearchBuilderBound' ) ) {
			return;
		}
		$root.data( 'hvnSearchBuilderBound', true );

		var $list = $root.find( '[data-hvn-realty-cx-sortable]' ).first();
		var cx = window.hvnRealtyCx || {};

		if ( cx.initSortable ) {
			cx.initSortable( $list, {
				handle: '.hvn-realty-cx__handle',
				update: function () {
					syncFields( $root );
				},
			} );
		} else if ( $list.length && $.fn.sortable ) {
			$list.sortable( {
				axis: 'y',
				handle: '.hvn-realty-cx__handle',
				cancel: 'input,textarea,select,option,label',
				update: function () {
					syncFields( $root );
				},
			} );
		}

		if ( cx.bindCollapse ) {
			cx.bindCollapse( $root );
		}

		$root.on( 'change input', '[data-field]', function () {
			updateCardState( $( this ).closest( '.hvn-realty-search-builder-control__item' ) );
			syncFields( $root );
		} );

		$root.find( '.hvn-realty-search-builder-control__item' ).each( function () {
			updateCardState( $( this ) );
		} );
	}

	if ( window.wp && wp.customize && wp.customize.Control ) {
		wp.customize.controlConstructor.hvn_realty_search_builder = wp.customize.Control.extend( {
			ready: function () {
				bindControl( this.container.find( '[data-hvn-realty-search-builder-control]' ).first() );
			},
		} );

		wp.customize.bind( 'ready', function () {
			$( '[data-hvn-realty-search-builder-control]' ).each( function () {
				bindControl( $( this ) );
			} );
		} );
	}
}( jQuery, window ) );
