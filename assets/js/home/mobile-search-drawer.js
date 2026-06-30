/**
 * Homepage mobile search drawer — dock visibility and filter panel.
 *
 * @package Havenlytics_Realty
 */
( function () {
	'use strict';

	var config = window.hvnRealtyMobileSearchDrawer || {};
	var mobileQuery = window.matchMedia( '(max-width: ' + ( config.mobileMaxWidth || 991 ) + 'px)' );
	var activeInstance = null;
	var resizeTimer = null;

	/**
	 * @return {boolean}
	 */
	function isMobileViewport() {
		return mobileQuery.matches;
	}

	/**
	 * @param {HTMLElement|null} root
	 * @return {void}
	 */
	function setRootDesktopHidden( root ) {
		if ( ! root ) {
			return;
		}
		root.hidden = true;
		root.setAttribute( 'aria-hidden', 'true' );
	}

	/**
	 * @param {HTMLElement|null} root
	 * @return {void}
	 */
	function setRootMobileVisible( root ) {
		if ( ! root ) {
			return;
		}
		root.hidden = false;
		root.setAttribute( 'aria-hidden', 'false' );
	}

	/**
	 * Destroy the active drawer instance and release listeners/observers.
	 *
	 * @return {void}
	 */
	function destroyMobileSearchDrawer() {
		if ( ! activeInstance ) {
			return;
		}
		activeInstance.destroy();
		activeInstance = null;
	}

	/**
	 * Initialize or refresh the mobile search drawer on mobile viewports.
	 *
	 * @return {void}
	 */
	function initializeMobileSearchDrawer() {
		if ( activeInstance ) {
			activeInstance.syncVisibility();
			activeInstance.updatePillsFade();
			return;
		}

		var instance = createMobileSearchDrawerInstance();
		if ( ! instance ) {
			return;
		}

		instance.initialize();
		activeInstance = instance;
	}

	/**
	 * Respond to viewport crossing the mobile breakpoint.
	 *
	 * @return {void}
	 */
	function handleViewportChange() {
		if ( isMobileViewport() ) {
			initializeMobileSearchDrawer();
			return;
		}

		destroyMobileSearchDrawer();
		setRootDesktopHidden( document.getElementById( 'hvn-theme-home-msd-root' ) );
	}

	/**
	 * Debounced resize/orientation sync for mobile drawer state.
	 *
	 * @return {void}
	 */
	function handleResponsiveRecalc() {
		if ( ! isMobileViewport() ) {
			return;
		}

		window.clearTimeout( resizeTimer );
		resizeTimer = window.setTimeout( function () {
			if ( ! isMobileViewport() ) {
				return;
			}

			if ( ! activeInstance ) {
				initializeMobileSearchDrawer();
				return;
			}

			activeInstance.syncVisibility();
			activeInstance.updatePillsFade();
		}, 120 );
	}

	/**
	 * Build a drawer instance with initialize/destroy/sync lifecycle.
	 *
	 * @return {object|null}
	 */
	function createMobileSearchDrawerInstance() {
		var root = document.getElementById( 'hvn-theme-home-msd-root' );
		var heroSearch = document.querySelector( config.heroSearchSelector || '#hvn-theme-home-search' );
		var dockWrap = document.getElementById( 'hvn-theme-home-msd-dock-wrap' );
		var pillsContainer = document.getElementById( 'hvn-theme-home-msd-pills' );
		var pillsScroll = document.getElementById( 'hvn-theme-home-msd-pills-scroll' );
		var drawer = document.getElementById( 'hvn-theme-home-msd-drawer' );
		var scrim = document.getElementById( 'hvn-theme-home-msd-scrim' );
		var closeBtn = document.getElementById( 'hvn-theme-home-msd-close' );
		var form = document.getElementById( 'hvn-theme-home-msd-form' );
		var departmentInput = document.getElementById( 'hvn-theme-home-msd-department' );
		var deptLabel = document.getElementById( 'hvn-theme-home-msd-drawer-dept' );
		var dragHandle = document.getElementById( 'hvn-theme-home-msd-drag-handle' );

		if ( ! root || ! dockWrap || ! pillsContainer || ! drawer || ! scrim ) {
			return null;
		}

		var abortController = new AbortController();
		var signal = abortController.signal;
		var useHeroVisibility = !! config.useHeroVisibility && !! heroSearch;
		var scrollShowOffset = parseInt( config.scrollShowOffset, 10 );
		if ( isNaN( scrollShowOffset ) || scrollShowOffset < 150 ) {
			scrollShowOffset = 200;
		}
		if ( scrollShowOffset > 250 ) {
			scrollShowOffset = 250;
		}

		var isExpanded = false;
		var heroVisible = useHeroVisibility;
		var dockShouldShow = false;
		var lastFocused = null;
		var heroObserver = null;
		var dragStartY = 0;
		var currentY = 0;
		var isDragging = false;
		var autoCenterPills = false !== config.autoCenterPills;
		var edgeFade = false !== config.edgeFade;
		var dragClose = false !== config.dragClose;
		var swipeGestures = false !== config.swipeGestures;
		var initialized = false;

		/**
		 * @param {HTMLElement} pill
		 * @param {ScrollBehavior} behavior
		 */
		function scrollPillIntoView( pill, behavior ) {
			if ( ! pill || ! autoCenterPills ) {
				return;
			}

			var scrollBehavior = behavior || 'smooth';

			if ( 'function' === typeof pill.scrollIntoView ) {
				pill.scrollIntoView( {
					behavior: scrollBehavior,
					inline: 'center',
					block: 'nearest',
				} );
			}
		}

		function updatePillsFade() {
			if ( ! pillsScroll || ! edgeFade ) {
				if ( pillsScroll ) {
					pillsScroll.classList.remove( 'hvn-theme-home-msd-can-scroll-start', 'hvn-theme-home-msd-can-scroll-end' );
				}
				return;
			}

			var maxScroll = pillsContainer.scrollWidth - pillsContainer.clientWidth;

			if ( maxScroll <= 2 ) {
				pillsScroll.classList.remove( 'hvn-theme-home-msd-can-scroll-start', 'hvn-theme-home-msd-can-scroll-end' );
				return;
			}

			pillsScroll.classList.toggle( 'hvn-theme-home-msd-can-scroll-start', pillsContainer.scrollLeft > 4 );
			pillsScroll.classList.toggle( 'hvn-theme-home-msd-can-scroll-end', pillsContainer.scrollLeft < maxScroll - 4 );
		}

		function centerActivePill( behavior ) {
			var activePill = pillsContainer.querySelector( '.hvn-theme-home-msd-pill-active' );
			if ( activePill ) {
				scrollPillIntoView( activePill, behavior );
			}
		}

		function hideDock() {
			if ( isExpanded ) {
				closeDrawer();
			}
			dockWrap.classList.remove( 'hvn-theme-home-msd-dock-visible' );
		}

		function showDock() {
			if ( dockShouldShow && ! isExpanded ) {
				dockWrap.classList.add( 'hvn-theme-home-msd-dock-visible' );
			}
		}

		function syncDockVisibility() {
			if ( dockShouldShow ) {
				showDock();
			} else {
				hideDock();
			}
		}

		function updateScrollDockVisibility() {
			var scrollY = window.scrollY || document.documentElement.scrollTop || 0;
			dockShouldShow = scrollY >= scrollShowOffset;
			syncDockVisibility();
		}

		function syncHeroVisibility() {
			if ( ! useHeroVisibility || ! heroSearch ) {
				return;
			}

			var offset = parseInt( config.heroTriggerOffset, 10 ) || 0;
			var rect = heroSearch.getBoundingClientRect();
			var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;

			heroVisible = rect.bottom > offset && rect.top < viewportHeight;
			dockShouldShow = ! heroVisible;
			syncDockVisibility();
		}

		function syncVisibility() {
			if ( useHeroVisibility && heroSearch ) {
				syncHeroVisibility();
				return;
			}

			updateScrollDockVisibility();
		}

		/**
		 * @param {string} department
		 * @param {string} label
		 * @param {HTMLElement|null} pill
		 */
		function setActiveDepartment( department, label, pill ) {
			if ( departmentInput ) {
				departmentInput.value = department || '';
			}
			if ( deptLabel && label ) {
				deptLabel.textContent = label;
			}

			pillsContainer.querySelectorAll( '.hvn-theme-home-msd-pill' ).forEach( function ( activePill ) {
				var isActive = activePill.getAttribute( 'data-hvn-msd-department' ) === department;
				activePill.classList.toggle( 'hvn-theme-home-msd-pill-active', isActive );
				activePill.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
			} );

			if ( pill ) {
				scrollPillIntoView( pill, 'smooth' );
			} else {
				centerActivePill( 'smooth' );
			}

			window.requestAnimationFrame( updatePillsFade );
		}

		function handleKeydown( event ) {
			if ( 'Escape' === event.key || 'Esc' === event.key ) {
				event.preventDefault();
				closeDrawer();
				return;
			}

			if ( 'Tab' === event.key ) {
				var focusable = dockWrap.querySelectorAll(
					'button:not([disabled]), input:not([disabled]):not(.hvn-theme-home-msd-visually-hidden), select:not([disabled]):not(.hvn-theme-home-msd-visually-hidden), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
				);
				if ( ! focusable.length ) {
					return;
				}
				var first = focusable[ 0 ];
				var last = focusable[ focusable.length - 1 ];
				if ( event.shiftKey && document.activeElement === first ) {
					event.preventDefault();
					last.focus();
				} else if ( ! event.shiftKey && document.activeElement === last ) {
					event.preventDefault();
					first.focus();
				}
			}
		}

		/**
		 * @param {string} department
		 * @param {string} label
		 * @param {HTMLElement|null} pill
		 */
		function openDrawer( department, label, pill ) {
			setActiveDepartment( department, label, pill );

			if ( isExpanded ) {
				return;
			}

			isExpanded = true;
			lastFocused = document.activeElement;
			document.body.classList.add( 'hvn-theme-home-msd-no-scroll' );
			dockWrap.classList.add( 'hvn-theme-home-msd-dock-visible' );
			drawer.hidden = false;
			scrim.hidden = false;

			window.requestAnimationFrame( function () {
				dockWrap.classList.add( 'hvn-theme-home-msd-drawer-open' );
				scrim.classList.add( 'hvn-theme-home-msd-scrim-visible' );
			} );

			document.addEventListener( 'keydown', handleKeydown, { signal: signal } );
			window.setTimeout( function () {
				if ( closeBtn && initialized ) {
					closeBtn.focus();
				}
			}, 420 );
		}

		function closeDrawer() {
			if ( ! isExpanded ) {
				return;
			}

			isExpanded = false;
			dockWrap.classList.remove( 'hvn-theme-home-msd-drawer-open' );
			scrim.classList.remove( 'hvn-theme-home-msd-scrim-visible' );
			document.body.classList.remove( 'hvn-theme-home-msd-no-scroll' );

			window.setTimeout( function () {
				if ( ! isExpanded ) {
					drawer.hidden = true;
					scrim.hidden = true;
				}
			}, 400 );

			if ( ! dockShouldShow ) {
				dockWrap.classList.remove( 'hvn-theme-home-msd-dock-visible' );
			}

			if ( lastFocused && 'function' === typeof lastFocused.focus ) {
				lastFocused.focus();
			}
		}

		function bindSteppers() {
			document.querySelectorAll( '[data-hvn-msd-stepper]' ).forEach( function ( stepper ) {
				var selectId = stepper.getAttribute( 'data-hvn-msd-stepper-for' );
				var select = selectId ? document.getElementById( selectId ) : null;
				var valueEl = stepper.querySelector( '[data-hvn-msd-value]' );

				if ( ! select || ! valueEl ) {
					return;
				}

				var index = 0;

				function syncFromSelect() {
					var options = Array.prototype.slice.call( select.options );
					index = Math.max( 0, select.selectedIndex );
					if ( options[ index ] ) {
						valueEl.textContent = options[ index ].textContent;
					}
				}

				syncFromSelect();

				stepper.querySelectorAll( '[data-hvn-msd-action]' ).forEach( function ( btn ) {
					btn.addEventListener(
						'click',
						function () {
							var options = select.options;
							if ( ! options.length ) {
								return;
							}
							var action = btn.getAttribute( 'data-hvn-msd-action' );
							if ( 'inc' === action ) {
								index = Math.min( options.length - 1, index + 1 );
							} else {
								index = Math.max( 0, index - 1 );
							}
							select.selectedIndex = index;
							syncFromSelect();
						},
						{ signal: signal }
					);
				} );
			} );
		}

		function bindChips() {
			document.querySelectorAll( '[data-hvn-msd-chip-target]' ).forEach( function ( group ) {
				var targetId = group.getAttribute( 'data-hvn-msd-chip-target' );
				var input = targetId ? document.getElementById( targetId ) : null;

				if ( ! input ) {
					return;
				}

				group.querySelectorAll( '[data-hvn-msd-chip-value]' ).forEach( function ( chip ) {
					chip.addEventListener(
						'click',
						function () {
							var value = chip.getAttribute( 'data-hvn-msd-chip-value' ) || '';
							var isActive = chip.classList.contains( 'hvn-theme-home-msd-chip-active' );

							group.querySelectorAll( '.hvn-theme-home-msd-chip' ).forEach( function ( item ) {
								item.classList.remove( 'hvn-theme-home-msd-chip-active' );
							} );

							if ( isActive ) {
								input.value = '';
							} else {
								chip.classList.add( 'hvn-theme-home-msd-chip-active' );
								input.value = value;
							}
						},
						{ signal: signal }
					);
				} );
			} );
		}

		function bindFormSubmit() {
			if ( ! form ) {
				return;
			}

			form.addEventListener(
				'submit',
				function ( event ) {
					if ( ! form.checkValidity() ) {
						event.preventDefault();
						form.reportValidity();
						return;
					}

					form.querySelectorAll( 'input, select, textarea' ).forEach( function ( field ) {
						if ( field.disabled || 'submit' === field.type || 'button' === field.type ) {
							return;
						}
						if ( ! field.name ) {
							return;
						}
						if ( ( field.value || '' ).trim() === '' ) {
							field.disabled = true;
						}
					} );
				},
				{ signal: signal }
			);
		}

		function onPillsClick( event ) {
			var pill = event.target.closest( '.hvn-theme-home-msd-pill' );
			if ( ! pill ) {
				return;
			}
			openDrawer(
				pill.getAttribute( 'data-hvn-msd-department' ) || '',
				pill.getAttribute( 'data-hvn-msd-label' ) || '',
				pill
			);
		}

		function onDragStart( event ) {
			if ( ! isExpanded || ! dragClose ) {
				return;
			}
			isDragging = true;
			dragStartY = event.touches ? event.touches[ 0 ].clientY : event.clientY;
			dockWrap.classList.add( 'hvn-theme-home-msd-dragging' );
		}

		function onDragMove( event ) {
			if ( ! isDragging ) {
				return;
			}
			var y = event.touches ? event.touches[ 0 ].clientY : event.clientY;
			currentY = Math.max( 0, y - dragStartY );
			dockWrap.style.transform = 'translate3d(0, ' + currentY + 'px, 0)';
		}

		function onDragEnd() {
			if ( ! isDragging ) {
				return;
			}
			isDragging = false;
			dockWrap.classList.remove( 'hvn-theme-home-msd-dragging' );
			dockWrap.style.transform = '';
			if ( currentY > 110 && dragClose ) {
				closeDrawer();
			}
			currentY = 0;
		}

		function startHeroObserver() {
			if ( ! useHeroVisibility || ! heroSearch || ! ( 'IntersectionObserver' in window ) ) {
				return;
			}

			if ( heroObserver ) {
				heroObserver.disconnect();
			}

			var offset = parseInt( config.heroTriggerOffset, 10 ) || 0;
			var rootMargin = '0px 0px -' + Math.max( 0, offset ) + 'px 0px';

			heroObserver = new IntersectionObserver(
				function ( entries ) {
					entries.forEach( function ( entry ) {
						heroVisible = entry.isIntersecting;
						dockShouldShow = ! heroVisible;
						syncDockVisibility();
					} );
				},
				{ threshold: 0, rootMargin: rootMargin }
			);
			heroObserver.observe( heroSearch );
			syncHeroVisibility();
		}

		function stopHeroObserver() {
			if ( heroObserver ) {
				heroObserver.disconnect();
				heroObserver = null;
			}
		}

		function resetDomState() {
			closeDrawer();
			dockWrap.classList.remove( 'hvn-theme-home-msd-dock-visible', 'hvn-theme-home-msd-drawer-open', 'hvn-theme-home-msd-dragging' );
			dockWrap.style.transform = '';
			scrim.classList.remove( 'hvn-theme-home-msd-scrim-visible' );
			drawer.hidden = true;
			scrim.hidden = true;
			document.body.classList.remove( 'hvn-theme-home-msd-no-scroll' );
		}

		function initialize() {
			if ( initialized ) {
				syncVisibility();
				return;
			}

			initialized = true;
			setRootMobileVisible( root );

			pillsContainer.addEventListener( 'click', onPillsClick, { signal: signal } );
			pillsContainer.addEventListener( 'scroll', updatePillsFade, { passive: true, signal: signal } );

			if ( closeBtn ) {
				closeBtn.addEventListener( 'click', closeDrawer, { signal: signal } );
			}

			scrim.addEventListener( 'click', closeDrawer, { signal: signal } );

			if ( dragHandle && ( dragClose || swipeGestures ) ) {
				dragHandle.addEventListener( 'touchstart', onDragStart, { passive: true, signal: signal } );
				dragHandle.addEventListener( 'touchmove', onDragMove, { passive: true, signal: signal } );
				dragHandle.addEventListener( 'touchend', onDragEnd, { signal: signal } );
				dragHandle.addEventListener( 'mousedown', onDragStart, { signal: signal } );
				document.addEventListener( 'mousemove', onDragMove, { signal: signal } );
				document.addEventListener( 'mouseup', onDragEnd, { signal: signal } );
			}

			if ( useHeroVisibility && heroSearch ) {
				startHeroObserver();
			} else {
				window.addEventListener( 'scroll', updateScrollDockVisibility, { passive: true, signal: signal } );
				updateScrollDockVisibility();
			}

			bindSteppers();
			bindChips();
			bindFormSubmit();

			window.requestAnimationFrame( function () {
				centerActivePill( 'auto' );
				updatePillsFade();
			} );

			syncVisibility();
		}

		function destroy() {
			if ( ! initialized ) {
				return;
			}

			initialized = false;
			stopHeroObserver();
			resetDomState();
			abortController.abort();
			abortController = new AbortController();
			signal = abortController.signal;
		}

		return {
			initialize: initialize,
			destroy: destroy,
			syncVisibility: syncVisibility,
			updatePillsFade: updatePillsFade,
			refreshConfig: function ( nextConfig ) {
				config = nextConfig || config;
				autoCenterPills = false !== config.autoCenterPills;
				edgeFade = false !== config.edgeFade;
				dragClose = false !== config.dragClose;
				swipeGestures = false !== config.swipeGestures;
				useHeroVisibility = !! config.useHeroVisibility && !! document.querySelector( config.heroSearchSelector || '#hvn-theme-home-search' );
				scrollShowOffset = parseInt( config.scrollShowOffset, 10 );
				if ( isNaN( scrollShowOffset ) || scrollShowOffset < 150 ) {
					scrollShowOffset = 200;
				}
				if ( scrollShowOffset > 250 ) {
					scrollShowOffset = 250;
				}
				syncVisibility();
				updatePillsFade();
			},
		};
	}

	function boot() {
		if ( 'function' === typeof mobileQuery.addEventListener ) {
			mobileQuery.addEventListener( 'change', handleViewportChange );
		} else if ( 'function' === typeof mobileQuery.addListener ) {
			mobileQuery.addListener( handleViewportChange );
		}

		window.addEventListener( 'resize', handleResponsiveRecalc, { passive: true } );
		window.addEventListener(
			'orientationchange',
			function () {
				window.setTimeout( handleResponsiveRecalc, 150 );
			},
			{ passive: true }
		);

		handleViewportChange();

		window.hvnRealtyMobileSearchDrawerApi = {
			initialize: initializeMobileSearchDrawer,
			destroy: destroyMobileSearchDrawer,
			reinitialize: function () {
				destroyMobileSearchDrawer();
				initializeMobileSearchDrawer();
			},
			syncVisibility: function () {
				if ( activeInstance ) {
					activeInstance.syncVisibility();
				}
			},
			updatePillsFade: function () {
				if ( activeInstance ) {
					activeInstance.updatePillsFade();
				}
			},
			refreshConfig: function ( nextConfig ) {
				config = nextConfig || config;
				if ( activeInstance ) {
					activeInstance.refreshConfig( config );
				}
			},
		};
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
