/**
 * Havenlytics Realty — Realty admin onboarding tutorial modal.
 */
( function ( $ ) {
	'use strict';

	var config = window.hvnRealtyOnboarding || {};
	var $modal = $( '#hvn-realty-onboarding-modal' );
	var $iframe = $( '#hvn-realty-onboarding-video' );
	var isOpen = false;

	if ( ! $modal.length ) {
		return;
	}

	function buildEmbedSrc( autoplay ) {
		var base = $iframe.attr( 'data-src-base' ) || '';

		if ( ! base ) {
			return '';
		}

		var params = [
			'rel=0',
			'modestbranding=1',
			'playsinline=1',
		];

		if ( autoplay ) {
			params.push( 'autoplay=1' );
			params.push( 'mute=1' );
		}

		return base + ( base.indexOf( '?' ) > -1 ? '&' : '?' ) + params.join( '&' );
	}

	function setIframeSrc( autoplay ) {
		var src = buildEmbedSrc( autoplay );
		if ( src ) {
			$iframe.attr( 'src', src );
		}
	}

	function clearIframe() {
		$iframe.attr( 'src', '' );
	}

	function markSeen() {
		if ( ! config.ajaxUrl || ! config.nonce ) {
			return;
		}

		$.post( config.ajaxUrl, {
			action: 'hvn_realty_mark_onboarding_video_seen',
			nonce: config.nonce,
		} );
	}

	function openModal( autoplay ) {
		if ( isOpen ) {
			return;
		}

		isOpen = true;
		$modal.removeAttr( 'hidden' ).attr( 'aria-hidden', 'false' ).addClass( 'is-visible' );
		$( 'body' ).addClass( 'hvn-realty-onboarding-open' );
		setIframeSrc( !! autoplay );

		window.setTimeout( function () {
			$modal.find( '[data-hvn-onboarding-dismiss]' ).first().focus();
		}, 50 );
	}

	function closeModal( shouldMarkSeen ) {
		if ( ! isOpen ) {
			return;
		}

		isOpen = false;
		$modal.attr( 'aria-hidden', 'true' ).removeClass( 'is-visible' );
		$( 'body' ).removeClass( 'hvn-realty-onboarding-open' );
		clearIframe();

		window.setTimeout( function () {
			$modal.attr( 'hidden', 'hidden' );
		}, 220 );

		if ( shouldMarkSeen ) {
			markSeen();
		}
	}

	$( document ).on( 'click', '[data-hvn-onboarding-open]', function ( event ) {
		event.preventDefault();
		openModal( true );
	} );

	$( document ).on( 'click', '[data-hvn-onboarding-dismiss]', function ( event ) {
		event.preventDefault();
		closeModal( true );
	} );

	$( document ).on( 'keydown', function ( event ) {
		if ( ! isOpen ) {
			return;
		}

		if ( 'Escape' === event.key ) {
			event.preventDefault();
			closeModal( true );
		}
	} );

	if ( config.autoShow ) {
		window.setTimeout( function () {
			openModal( true );
		}, parseInt( config.delayMs, 10 ) || 2000 );
	}
}( jQuery ) );
