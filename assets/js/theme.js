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

	function initMobileMenu() {
		var menuToggle = qs('.hvn-theme-menu-toggle');
		var mobileMenu = qs('#hvn-mobile-menu');
		var mobileOverlay = qs('.hvn-theme-mobile-overlay');
		var mobileClose = qs('.hvn-theme-mobile-menu-close');

		if (!menuToggle || !mobileMenu) {
			return;
		}

		function openMobileMenu() {
			mobileMenu.classList.add('active');
			mobileMenu.setAttribute('aria-hidden', 'false');
			if (mobileOverlay) {
				mobileOverlay.classList.add('active');
				mobileOverlay.setAttribute('aria-hidden', 'false');
			}
			menuToggle.classList.add('active');
			menuToggle.setAttribute('aria-expanded', 'true');
			document.body.style.overflow = 'hidden';
			setTimeout(function () {
				if (mobileClose) {
					mobileClose.focus();
				}
			}, 100);
		}

		function closeMobileMenu() {
			mobileMenu.classList.remove('active');
			mobileMenu.setAttribute('aria-hidden', 'true');
			if (mobileOverlay) {
				mobileOverlay.classList.remove('active');
				mobileOverlay.setAttribute('aria-hidden', 'true');
			}
			menuToggle.classList.remove('active');
			menuToggle.setAttribute('aria-expanded', 'false');
			document.body.style.overflow = '';
			qsa('.hvn-theme-mobile-nav-menu .sub-menu.toggled').forEach(function (subMenu) {
				subMenu.classList.remove('toggled');
				subMenu.setAttribute('aria-hidden', 'true');
			});
			qsa('.hvn-theme-mobile-dropdown-toggle.active').forEach(function (btn) {
				btn.classList.remove('active');
				btn.setAttribute('aria-expanded', 'false');
			});
			qsa('.hvn-theme-mobile-nav-menu .menu-item-has-children > a[aria-expanded="true"]').forEach(function (link) {
				link.setAttribute('aria-expanded', 'false');
			});
			menuToggle.focus();
		}

		on(menuToggle, 'click', function (event) {
			event.preventDefault();
			if (mobileMenu.classList.contains('active')) {
				closeMobileMenu();
			} else {
				openMobileMenu();
			}
		});
		on(menuToggle, 'keydown', handleActivation);

		if (mobileClose) {
			on(mobileClose, 'click', function (event) {
				event.preventDefault();
				closeMobileMenu();
			});
			on(mobileClose, 'keydown', handleActivation);
		}

		if (mobileOverlay) {
			on(mobileOverlay, 'click', closeMobileMenu);
		}

		on(document, 'keydown', function (event) {
			if (event.key === 'Escape' && mobileMenu.classList.contains('active')) {
				closeMobileMenu();
			}
		});

		on(window, 'resize', function () {
			if (window.innerWidth > 991) {
				closeMobileMenu();
			}
		});

		on(mobileMenu, 'keydown', function (event) {
			trapFocus(mobileMenu, event);
		});

		// Initialize mobile dropdown toggles
		qsa('.hvn-theme-mobile-nav-menu .menu-item-has-children').forEach(function (item, index) {
			var subMenu = qs(':scope > .sub-menu', item);
			if (!subMenu || qs('.hvn-theme-mobile-dropdown-toggle', item)) {
				return;
			}

			var subMenuId = subMenu.id || ('hvn-mobile-submenu-' + index);
			subMenu.id = subMenuId;
			subMenu.setAttribute('aria-hidden', 'true');

			var toggle = document.createElement('button');
			toggle.className = 'hvn-theme-mobile-dropdown-toggle';
			toggle.type = 'button';
			toggle.setAttribute('aria-expanded', 'false');
			toggle.setAttribute('aria-controls', subMenuId);
			toggle.setAttribute('aria-label', 'Toggle submenu');
			item.appendChild(toggle);

			on(toggle, 'click', function (event) {
				event.preventDefault();
				event.stopPropagation();
				var expanded = toggle.getAttribute('aria-expanded') === 'true';
				var isExpanded = !expanded;
				toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
				toggle.classList.toggle('active', isExpanded);

				var parentLink = qs(':scope > a', item);
				if (parentLink) {
					parentLink.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
				}

				subMenu.setAttribute('aria-hidden', isExpanded ? 'false' : 'true');
				subMenu.classList.toggle('toggled', isExpanded);
			});

			on(toggle, 'keydown', handleActivation);
		});

		// Handle submenu toggle from parent link
		qsa('.hvn-theme-mobile-nav-menu .menu-item-has-children > a').forEach(function (link) {
			var item = link.parentNode;
			var subMenu = qs(':scope > .sub-menu', item);
			var toggle = qs('.hvn-theme-mobile-dropdown-toggle', item);

			// Allow enter/space on the parent link to toggle if submenu exists
			on(link, 'keydown', function (event) {
				if ((event.key === 'Enter' || event.key === ' ') && subMenu && toggle) {
					event.preventDefault();
					toggle.click();
				}
			});
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