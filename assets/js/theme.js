/**
 * Havenlytics Realty theme interactions (vanilla JS).
 */
(function () {
	'use strict';

	var keyboardTimeout;

	function qs(selector, context) {
		return (context || document).querySelector(selector);
	}

	function qsa(selector, context) {
		return Array.prototype.slice.call((context || document).querySelectorAll(selector));
	}

	function on(el, event, handler) {
		if (el) {
			el.addEventListener(event, handler);
		}
	}

	function trapFocus(container, event) {
		if (event.key !== 'Tab' || !container) {
			return;
		}

		var focusable = qsa('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])', container)
			.filter(function (node) {
				return !node.disabled && node.offsetParent !== null;
			});

		if (!focusable.length) {
			return;
		}

		var first = focusable[0];
		var last = focusable[focusable.length - 1];

		if (event.shiftKey && document.activeElement === first) {
			event.preventDefault();
			last.focus();
		} else if (!event.shiftKey && document.activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	}

	function handleActivation(event) {
		if (event.key === 'Enter' || event.key === ' ') {
			event.preventDefault();
			event.currentTarget.click();
		}
	}

	function handleFirstTab(event) {
		if (event.key === 'Tab') {
			document.body.classList.remove('mouse-user');
			document.body.classList.add('keyboard-user');
			clearTimeout(keyboardTimeout);
			keyboardTimeout = setTimeout(function () {
				document.body.classList.remove('keyboard-user');
				document.body.classList.add('mouse-user');
			}, 1000);
		}
	}

	function initKeyboardMode() {
		window.addEventListener('keydown', handleFirstTab);
		document.body.classList.add('mouse-user');
	}

	function initDesktopNavigation() {
		var desktopMenu = qs('#site-navigation');
		if (!desktopMenu) {
			return;
		}

		var subMenus = qsa('.menu-item-has-children', desktopMenu);
		var closeTimers = new WeakMap();

		function setDropdownExpanded( item, expanded ) {
			var parentLink = qs( ':scope > a', item );

			if ( parentLink ) {
				parentLink.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
			}

			item.classList.toggle( 'is-dropdown-open', !! expanded );
		}

		subMenus.forEach(function (item) {
			item.setAttribute('aria-haspopup', 'true');

			var parentLink = qs(':scope > a', item);
			if (parentLink) {
				parentLink.setAttribute('aria-expanded', 'false');

				on(item, 'mouseenter', function () {
					var timer = closeTimers.get(item);
					if (timer) {
						clearTimeout(timer);
						closeTimers.delete(item);
					}
					setDropdownExpanded(item, true);
				});

				on(item, 'mouseleave', function () {
					var timer = setTimeout(function () {
						if (!item.contains(document.activeElement)) {
							setDropdownExpanded(item, false);
						}
					}, 160);
					closeTimers.set(item, timer);
				});

				on(parentLink, 'keydown', function (event) {
					if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') {
						if (event.key === 'Escape') {
							setDropdownExpanded(item, false);
							item.classList.remove('focus');
						}
						return;
					}
					event.preventDefault();
					var subMenu = qs('.sub-menu', item);
					if (subMenu && subMenu.children.length) {
						item.classList.add('focus');
						setDropdownExpanded(item, true);
						var firstLink = qs('a', subMenu);
						if (firstLink) {
							firstLink.focus();
						}
					}
				});
			}
		});

		qsa('.sub-menu a', desktopMenu).forEach(function (link) {
			on(link, 'keydown', function (event) {
				if (event.key === 'ArrowLeft') {
					var parent = link.closest('.sub-menu');
					var parentItem = parent ? parent.closest('.menu-item-has-children') : null;
					if (parentItem) {
						var parentLink = qs(':scope > a', parentItem);
						parentItem.classList.remove('focus');
						setDropdownExpanded(parentItem, false);
						if (parentLink) {
							parentLink.focus();
						}
					}
				}

				if (event.key === 'ArrowRight') {
					var currentItem = link.closest('.menu-item-has-children');
					if (currentItem) {
						var childSubMenu = qs('.sub-menu', currentItem);
						if (childSubMenu && childSubMenu.children.length) {
							currentItem.classList.add('focus');
							setDropdownExpanded(currentItem, true);
							var firstChildLink = qs('a', childSubMenu);
							if (firstChildLink) {
								firstChildLink.focus();
							}
						}
					}
				}

				if (event.key === 'Escape') {
					var closestMenu = link.closest('.hvn-theme-nav-menu');
					if (closestMenu) {
						qsa('.menu-item-has-children.focus, .menu-item-has-children.is-dropdown-open', closestMenu).forEach(function (openItem) {
							openItem.classList.remove('focus');
							setDropdownExpanded(openItem, false);
						});
						var mainLinks = qsa('a', closestMenu);
						if (mainLinks[0]) {
							mainLinks[0].focus();
						}
					}
				}
			});
		});

		qsa('a', desktopMenu).forEach(function (link) {
			on(link, 'focus', function () {
				var node = link.parentNode;
				while (node && node !== desktopMenu) {
					if (node.tagName === 'LI' && node.classList.contains('menu-item-has-children')) {
						node.classList.add('focus');
						setDropdownExpanded(node, true);
					}
					node = node.parentNode;
				}
			});

			on(link, 'blur', function () {
				var parent = link.parentNode;
				setTimeout(function () {
					if (parent && parent.classList && parent.classList.contains('menu-item-has-children')) {
						if (!parent.contains(document.activeElement)) {
							parent.classList.remove('focus');
							setDropdownExpanded(parent, false);
						}
					}
				}, 10);
			});
		});
	}

	/**
	 * Global mobile menu controller.
	 *
	 * One implementation used across the entire theme. It drives the shared
	 * slide-in panel (#hvn-theme-home-mobile) and binds whichever header burger
	 * is present on the current page: the homepage burger (#hvn-theme-home-burger)
	 * and/or the internal-header toggle (.hvn-theme-menu-toggle). Desktop headers
	 * are untouched — each header keeps its own burger button.
	 */
	function initMobileMenu() {
		var panel = qs('#hvn-theme-home-mobile');
		var overlay = qs('#hvn-theme-home-mobile-overlay');
		var closeBtn = qs('#hvn-theme-home-mobile-close');
		var toggles = qsa('#hvn-theme-home-burger, .hvn-theme-menu-toggle, [aria-controls="hvn-theme-home-mobile"]');

		if (!panel || !toggles.length) {
			return;
		}

		var lastToggle = toggles[0];

		function openMenu(trigger) {
			if (trigger) {
				lastToggle = trigger;
			}
			panel.classList.add('hvn-theme-home-open');
			panel.setAttribute('aria-hidden', 'false');
			if (overlay) {
				overlay.hidden = false;
				overlay.classList.add('hvn-theme-home-open');
				overlay.setAttribute('aria-hidden', 'false');
			}
			toggles.forEach(function (btn) {
				btn.classList.add('active');
				btn.setAttribute('aria-expanded', 'true');
			});
			document.body.style.overflow = 'hidden';
			setTimeout(function () {
				if (closeBtn) {
					closeBtn.focus();
				}
			}, 100);
		}

		function closeMenu() {
			panel.classList.remove('hvn-theme-home-open');
			panel.setAttribute('aria-hidden', 'true');
			if (overlay) {
				overlay.classList.remove('hvn-theme-home-open');
				overlay.setAttribute('aria-hidden', 'true');
				setTimeout(function () {
					overlay.hidden = true;
				}, 450);
			}
			toggles.forEach(function (btn) {
				btn.classList.remove('active');
				btn.setAttribute('aria-expanded', 'false');
			});
			document.body.style.overflow = '';
			qsa('.sub-menu.hvn-theme-home-open', panel).forEach(function (subMenu) {
				subMenu.classList.remove('hvn-theme-home-open');
			});
			qsa('.hvn-theme-home-mobile__submenu-toggle[aria-expanded="true"]', panel).forEach(function (btn) {
				btn.setAttribute('aria-expanded', 'false');
			});
			if (lastToggle && typeof lastToggle.focus === 'function') {
				lastToggle.focus();
			}
		}

		toggles.forEach(function (btn) {
			on(btn, 'click', function (event) {
				event.preventDefault();
				if (panel.classList.contains('hvn-theme-home-open')) {
					closeMenu();
				} else {
					openMenu(btn);
				}
			});
			on(btn, 'keydown', handleActivation);
		});

		if (closeBtn) {
			on(closeBtn, 'click', function (event) {
				event.preventDefault();
				closeMenu();
			});
			on(closeBtn, 'keydown', handleActivation);
		}

		if (overlay) {
			on(overlay, 'click', closeMenu);
		}

		qsa('a', panel).forEach(function (link) {
			on(link, 'click', function () {
				closeMenu();
			});
		});

		on(document, 'keydown', function (event) {
			if (event.key === 'Escape' && panel.classList.contains('hvn-theme-home-open')) {
				closeMenu();
			}
		});

		on(window, 'resize', function () {
			if (window.innerWidth > 991 && panel.classList.contains('hvn-theme-home-open')) {
				closeMenu();
			}
		});

		on(panel, 'keydown', function (event) {
			trapFocus(panel, event);
		});

		// Build accessible submenu toggles inside the mobile panel.
		qsa('.menu-item-has-children', panel).forEach(function (item, index) {
			var link = qs(':scope > a', item);
			var subMenu = qs(':scope > .sub-menu', item);
			if (!link || !subMenu || qs('.hvn-theme-home-mobile__submenu-toggle', item)) {
				return;
			}

			var subMenuId = subMenu.id || ('hvn-theme-home-submenu-' + index);
			subMenu.id = subMenuId;

			var toggle = document.createElement('button');
			toggle.type = 'button';
			toggle.className = 'hvn-theme-home-mobile__submenu-toggle';
			toggle.setAttribute('aria-expanded', 'false');
			toggle.setAttribute('aria-controls', subMenuId);
			toggle.setAttribute('aria-label', 'Toggle submenu');
			item.appendChild(toggle);

			on(toggle, 'click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				var isOpen = subMenu.classList.toggle('hvn-theme-home-open');
				toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			});
			on(toggle, 'keydown', handleActivation);
		});
	}

	function initSkipLink() {
		var skipLink = qs('.skip-link');
		if (!skipLink) {
			return;
		}

		on(skipLink, 'keydown', function (event) {
			if (event.key === 'Enter') {
				var target = skipLink.getAttribute('href');
				if (target) {
					var node = qs(target);
					if (node) {
						node.setAttribute('tabindex', '-1');
						node.focus();
					}
				}
			}
		});
	}

	function initScrollTop() {
		var scrollTopBtn = document.getElementById('hvn-scroll-top') || document.querySelector('.hvn-theme-back-to-top');
		if (!scrollTopBtn) {
			return;
		}

		on(window, 'scroll', function () {
			if (window.scrollY > 300) {
				scrollTopBtn.classList.add('show');
			} else {
				scrollTopBtn.classList.remove('show');
			}
		});

		function scrollToTop(event) {
			if (event) {
				event.preventDefault();
			}
			window.scrollTo({ top: 0, behavior: 'smooth' });
		}

		on(scrollTopBtn, 'click', scrollToTop);
		on(scrollTopBtn, 'keydown', function (event) {
			if (event.key === 'Enter' || event.key === ' ') {
				scrollToTop(event);
			}
		});
	}

	function initHeaderPropertySearch() {
		var panel = qs('#hvn-property-search-panel');
		var openBtn = qs('[data-hvn-search-open]');

		if (!panel || !openBtn) {
			return;
		}

		var dialog = qs('.hvn-theme-property-search-panel__dialog', panel);
		var closeTriggers = qsa('[data-hvn-search-close]', panel);
		var keywordField = qs('#hvn-header-address-keyword', panel);

		function openPanel() {
			panel.hidden = false;
			panel.setAttribute('aria-hidden', 'false');
			openBtn.setAttribute('aria-expanded', 'true');
			document.body.classList.add('hvn-search-panel-open');
			document.body.style.overflow = 'hidden';

			requestAnimationFrame(function () {
				requestAnimationFrame(function () {
					panel.classList.add('is-open');
				});
			});

			window.setTimeout(function () {
				if (keywordField) {
					keywordField.focus();
				}
			}, 320);
		}

		function closePanel() {
			panel.classList.remove('is-open');
			panel.setAttribute('aria-hidden', 'true');
			openBtn.setAttribute('aria-expanded', 'false');
			document.body.classList.remove('hvn-search-panel-open');
			document.body.style.overflow = '';
			openBtn.focus();

			window.setTimeout(function () {
				panel.hidden = true;
			}, 320);
		}

		on(openBtn, 'click', function (event) {
			event.preventDefault();
			if (panel.classList.contains('is-open')) {
				closePanel();
			} else {
				openPanel();
			}
		});

		closeTriggers.forEach(function (trigger) {
			on(trigger, 'click', function (event) {
				event.preventDefault();
				closePanel();
			});
		});

		on(document, 'keydown', function (event) {
			if (event.key === 'Escape' && panel.classList.contains('is-open')) {
				closePanel();
			}
		});

		if (dialog) {
			on(dialog, 'keydown', function (event) {
				trapFocus(dialog, event);
			});
		}
	}

	function initPreloader() {
		var preloader = qs('#hvn-theme-preloader');
		if (!preloader) {
			return;
		}

		function hidePreloader() {
			preloader.classList.add('is-hidden');
			preloader.setAttribute('aria-hidden', 'true');
			document.body.classList.remove('hvn-theme-is-loading');
			document.body.classList.add('hvn-theme-is-loaded');

			setTimeout(function () {
				if (preloader.parentNode) {
					preloader.parentNode.removeChild(preloader);
				}
			}, 450);
		}

		if (document.readyState === 'complete') {
			hidePreloader();
		} else {
			on(window, 'load', hidePreloader);
		}
	}

	function init() {
		initPreloader();
		initKeyboardMode();
		initDesktopNavigation();
		initMobileMenu();
		initHeaderPropertySearch();
		initSkipLink();
		initScrollTop();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();