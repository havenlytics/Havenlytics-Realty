/**
 * Hero search panel — department tab selection.
 */
( function ( $ ) {
	'use strict';

	function initHeroSearchTabs( $scope ) {
		var $wrap = ( $scope || $( document ) ).find( '[data-hvn-realty-hero-search-tabs]' );

		$wrap.each( function () {
			var $tabs = $( this );

			if ( $tabs.data( 'hvnHeroSearchTabsBound' ) ) {
				return;
			}

			$tabs.data( 'hvnHeroSearchTabsBound', true );

			var $form = $tabs.closest( '.hvn-realty-hero-search__card' ).find( '.hvn-realty-hero-search__form' );
			var $department = $form.find( '#hvn-hero-department' );

			if ( ! $department.length ) {
				return;
			}

			$tabs.on( 'click', '.hvn-realty-search-tab', function () {
				var $button = $( this );
				var slug = $button.attr( 'data-department' ) || '';

				$tabs.find( '.hvn-realty-search-tab' ).removeClass( 'active' ).attr( 'aria-selected', 'false' );
				$button.addClass( 'active' ).attr( 'aria-selected', 'true' );
				$department.val( slug );
			} );
		} );
	}

	$( function () {
		initHeroSearchTabs( $( document ) );
	} );

	window.hvnRealtyInitHeroSearchTabs = initHeroSearchTabs;
}( jQuery ) );
