/**
 * Havenlytics Customizer UI framework.
 */
( function ( $, window ) {
	'use strict';

	var api = {
		/**
		 * Initialize sortable behavior on a list.
		 *
		 * @param {jQuery} $list List element.
		 * @param {Object} options Options.
		 * @return {void}
		 */
		initSortable: function ( $list, options ) {
			if ( ! $list.length || ! $.fn.sortable ) {
				return;
			}

			var settings = $.extend(
				{
					axis: 'y',
					handle: '.hvn-realty-cx__handle',
					placeholder: 'hvn-realty-cx__placeholder',
					forcePlaceholderSize: true,
					// Drag handles are <button> elements; jQuery UI defaults cancel button drags.
					cancel: 'input,textarea,select,option',
					update: function () {},
				},
				options || {}
			);

			$list.sortable( settings );
		},

		/**
		 * Bind collapse toggles inside a control root.
		 *
		 * @param {jQuery} $root Control root.
		 * @return {void}
		 */
		bindCollapse: function ( $root ) {
			$root.on( 'click', '.hvn-realty-cx__toggle', function ( event ) {
				event.preventDefault();

				var $button = $( this );
				var $card = $button.closest( '.hvn-realty-cx__card' );
				var collapsed = $card.toggleClass( 'is-collapsed' ).hasClass( 'is-collapsed' );

				$button.attr( 'aria-expanded', collapsed ? 'false' : 'true' );
			} );
		},

		/**
		 * Sync a hidden Customizer input.
		 *
		 * @param {jQuery}   $input Hidden input.
		 * @param {Function} collect Collect callback.
		 * @return {void}
		 */
		syncHiddenInput: function ( $input, collect ) {
			if ( ! $input.length || 'function' !== typeof collect ) {
				return;
			}

			$input.val( JSON.stringify( collect() ) ).trigger( 'change' );
		},

		/**
		 * Update repeater card titles from field values.
		 *
		 * @param {jQuery} $root Control root.
		 * @param {Object} options Options.
		 * @return {void}
		 */
		updateRepeaterTitles: function ( $root, options ) {
			options = options || {};

			$root.find( '.hvn-realty-cx__card' ).each( function ( index ) {
				var $card = $( this );
				var $title = $card.find( '[data-hvn-cx-card-title]' ).first();
				var $meta = $card.find( '[data-hvn-cx-card-meta]' ).first();
				var name = $.trim( $card.find( '[data-field="name"]' ).val() || '' );
				var position = $.trim( $card.find( '[data-field="position"]' ).val() || '' );
				var rating = parseInt( $card.find( '[data-field="rating"]' ).val(), 10 ) || 5;
				var fallback = options.fallbackLabel || 'Item';

				if ( $title.length ) {
					$title.text( name || fallback + ' ' + ( index + 1 ) );
				}

				if ( $meta.length ) {
					$meta.text( position || ( options.emptyMeta || '' ) );
				}

				var $ratingBadge = $card.find( '[data-hvn-cx-rating-badge]' ).first();
				if ( $ratingBadge.length ) {
					$ratingBadge.text( rating + '/5' );
				}
			} );
		},
	};

	window.hvnRealtyCx = api;
}( jQuery, window ) );
