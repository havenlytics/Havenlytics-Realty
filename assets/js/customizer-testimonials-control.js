/**

 * Customizer testimonials repeater control.

 */

( function ( $ ) {

	'use strict';



	var cx = window.hvnRealtyCx || {};



	function collectItems( $root ) {

		var items = [];



		$root.find( '.hvn-realty-testimonials-control__item' ).each( function () {

			var $item = $( this );

			var name = $.trim( $item.find( '[data-field="name"]' ).val() || '' );

			var text = $.trim( $item.find( '[data-field="text"]' ).val() || '' );



			if ( ! name || ! text ) {

				return;

			}



			items.push( {

				name: name,

				position: $.trim( $item.find( '[data-field="position"]' ).val() || '' ),

				text: text,

				rating: parseInt( $item.find( '[data-field="rating"]' ).val(), 10 ) || 5,

				avatar_id: parseInt( $item.find( '[data-field="avatar_id"]' ).val(), 10 ) || 0,

			} );

		} );



		return items;

	}



	function syncValue( $root ) {

		var $input = $root.find( '.hvn-realty-testimonials-control__value' ).first();



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

				fallbackLabel: 'Testimonial',

			} );

		}

	}



	function bindControl( $root ) {

		if ( $root.data( 'hvnTestimonialsBound' ) ) {

			return;

		}



		$root.data( 'hvnTestimonialsBound', true );



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



		$root.on( 'click', '.hvn-realty-testimonials-control__add', function ( event ) {

			event.preventDefault();

			var template = $root.siblings( '.hvn-realty-testimonials-control__template' ).html() || '';

			var index = $root.find( '.hvn-realty-testimonials-control__item' ).length;



			$root.find( '.hvn-realty-testimonials-control__list' ).append(

				template.replace( /\{\{index\}\}/g, String( index ) )

			);



			refreshCards( $root );

			syncValue( $root );

		} );



		$root.on( 'click', '.hvn-realty-testimonials-control__remove', function ( event ) {

			event.preventDefault();

			$( this ).closest( '.hvn-realty-testimonials-control__item' ).remove();

			refreshCards( $root );

			syncValue( $root );

		} );

		$root.on( 'click', '.hvn-realty-testimonials-control__duplicate', function ( event ) {

			event.preventDefault();

			var $item = $( this ).closest( '.hvn-realty-testimonials-control__item' );
			var $clone = $item.clone();
			var index = $root.find( '.hvn-realty-testimonials-control__item' ).length;

			$clone.attr( 'data-index', String( index ) );
			$clone.removeClass( 'is-collapsed' );
			$item.after( $clone );

			refreshCards( $root );

			syncValue( $root );

		} );



		$root.on( 'click', '.hvn-realty-testimonials-control__upload', function ( event ) {

			event.preventDefault();



			var $item = $( this ).closest( '.hvn-realty-testimonials-control__item' );

			var $field = $item.find( '[data-field="avatar_id"]' );

			var frame = wp.media( {

				title: 'Select avatar',

				button: { text: 'Use image' },

				multiple: false,

			} );



			frame.on( 'select', function () {

				var attachment = frame.state().get( 'selection' ).first().toJSON();

				$field.val( attachment.id || 0 );

				$item.find( '.hvn-realty-testimonials-control__preview' ).html(

					attachment.url ? '<img src="' + attachment.url + '" alt="" />' : ''

				);

				$item.find( '.hvn-realty-testimonials-control__clear-avatar' ).prop( 'hidden', ! attachment.url );

				syncValue( $root );

			} );



			frame.open();

		} );



		$root.on( 'click', '.hvn-realty-testimonials-control__clear-avatar', function ( event ) {

			event.preventDefault();

			var $item = $( this ).closest( '.hvn-realty-testimonials-control__item' );

			$item.find( '[data-field="avatar_id"]' ).val( 0 );

			$item.find( '.hvn-realty-testimonials-control__preview' ).empty();

			$( this ).prop( 'hidden', true );

			syncValue( $root );

		} );



		refreshCards( $root );

	}



	if ( wp.customize && wp.customize.Control ) {
		wp.customize.controlConstructor.hvn_realty_testimonials = wp.customize.Control.extend( {
			ready: function () {
				bindControl( this.container.find( '[data-hvn-realty-testimonials-control]' ).first() );
			},
		} );
	}

	wp.customize.bind( 'ready', function () {
		$( '[data-hvn-realty-testimonials-control]' ).each( function () {
			bindControl( $( this ) );
		} );
	} );

}( jQuery ) );


