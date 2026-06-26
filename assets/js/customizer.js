/**
 * Havenlytics Realty — Customizer live preview (preview iframe).
 */
( function ( $ ) {
	'use strict';

	var config = window.hvnRealtyCustomizer || {};
	var fontStacks = config.fontStacks || {};
	var googleFonts = config.googleFonts || {};
	var defaultCopyright = config.defaultCopyright || '';
	var homeSectionVisibility = config.homeSectionVisibility || {};

	function setStyle( id, css ) {
		var existing = document.getElementById( id );
		if ( existing ) {
			existing.remove();
		}
		var style = document.createElement( 'style' );
		style.id = id;
		style.textContent = css;
		document.head.appendChild( style );
	}

	function setText( selector, value ) {
		var node = document.querySelector( selector );
		if ( node ) {
			node.textContent = value;
		}
	}

	function resolveThemeLink( value, fallback ) {
		var link = ( value || '' ).trim();
		var homeUrl = ( config.homeUrl || '/' ).replace( /\/$/, '' );

		if ( ! link ) {
			return fallback || homeUrl + '/';
		}

		if ( /^https?:\/\//i.test( link ) ) {
			return link;
		}

		if ( link.charAt( 0 ) === '/' ) {
			return homeUrl + link;
		}

		return homeUrl + '/' + link.replace( /^\/+|\/+$/g, '' ) + '/';
	}

	function setLink( selector, href, text ) {
		var node = document.querySelector( selector );
		if ( ! node ) {
			return;
		}
		if ( typeof href === 'string' && href ) {
			node.setAttribute( 'href', href );
		}
		if ( typeof text === 'string' ) {
			node.textContent = text;
		}
	}

	function setHtml( selector, value ) {
		var node = document.querySelector( selector );
		if ( node ) {
			node.innerHTML = value;
		}
	}

	function toggleBodyClass( className, enabled ) {
		document.body.classList.toggle( className, !! enabled );
	}

	function replaceBodyClass( prefix, value ) {
		Array.prototype.slice.call( document.body.classList ).forEach( function ( className ) {
			if ( className.indexOf( prefix ) === 0 ) {
				document.body.classList.remove( className );
			}
		} );
		document.body.classList.add( prefix + value );
	}

	function bindColorVar( settingId, cssVar ) {
		wp.customize( settingId, function ( value ) {
			value.bind( function ( to ) {
				if ( ! to ) {
					return;
				}
				setStyle( 'hvn-realty-dynamic-' + settingId, ':root{' + cssVar + ':' + to + ';}' );
			} );
		} );
	}

	function bindRootVar( settingId, cssVar, transform ) {
		wp.customize( settingId, function ( value ) {
			value.bind( function ( to ) {
				var output = transform ? transform( to ) : to;
				setStyle( 'hvn-realty-dynamic-' + settingId, ':root{' + cssVar + ':' + output + ';}' );
			} );
		} );
	}

	function bindFontFamily( settingId, cssVar ) {
		wp.customize( settingId, function ( value ) {
			value.bind( function ( to ) {
				var stack = fontStacks[ to ] || fontStacks.inter || "'Inter', sans-serif";
				loadGoogleFont( to );
				setStyle( 'hvn-realty-dynamic-' + settingId, ':root{' + cssVar + ':' + stack + ';}' );
			} );
		} );
	}

	function loadGoogleFont( slug ) {
		if ( ! slug || ! googleFonts[ slug ] ) {
			return;
		}

		var linkId = 'hvn-realty-font-' + slug;
		if ( document.getElementById( linkId ) ) {
			return;
		}

		var link = document.createElement( 'link' );
		link.id = linkId;
		link.rel = 'stylesheet';
		link.href = 'https://fonts.googleapis.com/css2?family=' + googleFonts[ slug ] + '&display=swap';
		document.head.appendChild( link );
	}

	function toggleSectionVisibility( selector, enabled ) {
		if ( ! selector ) {
			return;
		}

		document.querySelectorAll( selector ).forEach( function ( section ) {
			section.style.display = enabled ? '' : 'none';
		} );
	}

	function updateFooterColumns( columns ) {
		columns = parseInt( columns, 10 );
		if ( isNaN( columns ) || columns < 1 ) {
			columns = 1;
		}
		if ( columns > 4 ) {
			columns = 4;
		}

		replaceBodyClass( 'hvn-footer-cols-', columns );

		document.querySelectorAll( '.hvn-theme-footer-widgets' ).forEach( function ( grid ) {
			grid.classList.remove( 'hvn-cols-1', 'hvn-cols-2', 'hvn-cols-3', 'hvn-cols-4' );
			grid.classList.add( 'hvn-cols-' + columns );
		} );
	}

	function bindTextPreview( settingId, selector, options ) {
		options = options || {};

		wp.customize( settingId, function ( value ) {
			function update( to ) {
				var nodes = document.querySelectorAll( selector );
				if ( ! nodes.length ) {
					return;
				}

				var text = ( null === to || typeof to === 'undefined' ) ? '' : String( to );

				nodes.forEach( function ( node ) {
					if ( options.html ) {
						node.innerHTML = text;
					} else {
						node.textContent = text;
					}

					if ( options.toggleHidden ) {
						node.hidden = '' === text;
					}
				} );
			}

			value.bind( update );
			update( value.get() );
		} );
	}

	function bindCheckboxPreview( settingId, selector, enabledWhenChecked ) {
		wp.customize( settingId, function ( value ) {
			function update( to ) {
				var enabled = !! to && '0' !== to && 0 !== to;
				var show = enabledWhenChecked ? enabled : ! enabled;

				document.querySelectorAll( selector ).forEach( function ( node ) {
					node.hidden = ! show;
				} );
			}

			value.bind( update );
			update( value.get() );
		} );
	}

	function bindHomeTextFields() {
		var bindings = {
			hvn_realty_home_featured_title: '#hvn-realty-featured-title',
			hvn_realty_home_featured_subtitle: '#hvn-realty-section-featured .hvn-realty-section__subtitle',
			hvn_realty_home_department_title: '#hvn-realty-departments-title',
			hvn_realty_home_department_subtitle: '#hvn-realty-section-departments .hvn-realty-section__subtitle',
			hvn_realty_home_taxonomies_title: '#hvn-realty-taxonomies-title',
			hvn_realty_home_taxonomies_subtitle: '#hvn-realty-section-taxonomies .hvn-realty-section__subtitle',
			hvn_realty_home_locations_title: '#hvn-realty-taxonomies-title',
			hvn_realty_home_locations_subtitle: '#hvn-realty-section-taxonomies .hvn-realty-section__subtitle',
			hvn_realty_home_agents_title: '#hvn-realty-agents-title',
			hvn_realty_home_agents_subtitle: '#hvn-realty-section-agents .hvn-realty-section__subtitle',
			hvn_realty_home_agencies_title: '#hvn-realty-agencies-title',
			hvn_realty_home_agencies_subtitle: '#hvn-realty-section-agencies .hvn-realty-section__subtitle',
			hvn_realty_home_blog_title: '#hvn-realty-blog-title',
			hvn_realty_home_blog_subtitle: '#hvn-realty-section-blog .hvn-realty-section__subtitle',
			hvn_realty_home_property_types_title: '#hvn-realty-property-types-title',
			hvn_realty_home_property_types_subtitle: '#hvn-realty-section-property-types .hvn-realty-section__subtitle',
			hvn_realty_home_testimonials_title: '#hvn-realty-testimonials-title',
			hvn_realty_home_testimonials_subtitle: '#hvn-realty-section-testimonials .hvn-realty-section__subtitle',
			hvn_realty_home_footer_cta_text: '#hvn-realty-footer-cta-title',
		};

		Object.keys( bindings ).forEach( function ( settingId ) {
			bindTextPreview( settingId, bindings[ settingId ] );
		} );
	}

	function bindHeroSearchFields() {
		bindCheckboxPreview( 'hvn_realty_show_hero_department_tabs', '[data-hvn-realty-hero-search-tabs]', true );
	}

	function bindLocalizedTextPreviews() {
		var bindings = config.textPreviewBindings || {};

		Object.keys( bindings ).forEach( function ( settingId ) {
			var binding = bindings[ settingId ];
			if ( ! binding || ! binding.selector ) {
				return;
			}

			bindTextPreview( settingId, binding.selector, {
				toggleHidden: !! binding.toggleHidden,
				html: !! binding.html,
			} );
		} );
	}

	function bindHomeDepartmentButton() {
		if ( ! wp.customize( 'hvn_realty_home_department_button_text' ) || ! wp.customize( 'hvn_realty_home_department_button_url' ) ) {
			return;
		}

		var fallback = config.propertySearchUrl || config.homeUrl || '/';

		function refreshDepartmentButton() {
			var text = wp.customize( 'hvn_realty_home_department_button_text' ).get();
			var urlValue = wp.customize( 'hvn_realty_home_department_button_url' ).get();
			setLink( '#hvn-realty-dept-view-all', resolveThemeLink( urlValue, fallback ), text );
		}

		wp.customize( 'hvn_realty_home_department_button_text', function ( value ) {
			value.bind( refreshDepartmentButton );
		} );

		wp.customize( 'hvn_realty_home_department_button_url', function ( value ) {
			value.bind( refreshDepartmentButton );
		} );
	}

	function bindCustomLogoPreview() {
		if ( ! wp.customize( 'custom_logo' ) ) {
			return;
		}

		wp.customize( 'custom_logo', function ( value ) {
			value.bind( function ( to ) {
				var logoId = parseInt( to, 10 );

				if ( ! logoId ) {
					if ( wp.customize.preview ) {
						wp.customize.preview.send( 'refresh' );
					}
					return;
				}

				var attachment = wp.media.attachment( logoId );
				attachment.fetch().then( function () {
					var url = attachment.get( 'url' );
					if ( ! url ) {
						return;
					}

					var alt = attachment.get( 'alt' ) || '';
					var width = attachment.get( 'width' );
					var height = attachment.get( 'height' );

					document.querySelectorAll( '.hvn-theme-custom-logo img, img.custom-logo' ).forEach( function ( img ) {
						img.src = url;
						img.alt = alt;
						if ( width ) {
							img.width = width;
						}
						if ( height ) {
							img.height = height;
						}
					} );
				} );
			} );
		} );
	}

	function bindSiteIconPreview() {
		if ( ! wp.customize( 'site_icon' ) ) {
			return;
		}

		wp.customize( 'site_icon', function ( value ) {
			value.bind( function ( to ) {
				var iconId = parseInt( to, 10 );
				if ( ! iconId ) {
					return;
				}

				var attachment = wp.media.attachment( iconId );
				attachment.fetch().then( function () {
					var url = attachment.get( 'url' );
					if ( ! url ) {
						return;
					}

					document.querySelectorAll( 'link[rel="icon"], link[rel="shortcut icon"], link[rel="apple-touch-icon"]' ).forEach( function ( link ) {
						link.href = url;
					} );
				} );
			} );
		} );
	}

	function updateBlogColumns( columns ) {
		columns = parseInt( columns, 10 );
		if ( isNaN( columns ) || columns < 1 ) {
			columns = 1;
		}
		if ( columns > 4 ) {
			columns = 4;
		}

		document.body.setAttribute( 'data-blog-cols', String( columns ) );
		replaceBodyClass( 'hvn-posts-cols-', columns );

		document.querySelectorAll( '.hvn-theme-content-area > .hvn-blog-grid' ).forEach( function ( grid ) {
			grid.classList.remove( 'hvn-cols-1', 'hvn-cols-2', 'hvn-cols-3', 'hvn-cols-4' );
			grid.classList.add( 'hvn-cols-' + columns );
			grid.style.setProperty( '--hvn-blog-columns', String( columns ) );
		} );

		var tablet = columns > 2 ? 2 : columns;
		var sidebarCols = Math.min( 2, columns );
		setStyle(
			'hvn-realty-blog-cols-preview',
			'.hvn-layout-blog .hvn-blog-grid{--hvn-blog-columns:' + columns + ';}' +
			'@media (max-width:991px){.hvn-layout-blog .hvn-blog-grid{--hvn-blog-columns:' + tablet + ';}}' +
			'@media (min-width:992px){.hvn-layout-blog.hvn-has-sidebar .hvn-blog-grid,' +
			'body.hvn-theme-has-sidebar .hvn-layout-blog.hvn-has-sidebar .hvn-blog-grid{--hvn-blog-columns:' + sidebarCols + ';}}'
		);
	}

	function updateBlogLayout( layout ) {
		var isList = 'list' === layout;

		document.body.setAttribute( 'data-blog-layout', layout );
		toggleBodyClass( 'hvn-blog-view-list', isList );
		toggleBodyClass( 'hvn-blog-view-grid', ! isList );

		if ( isList ) {
			updateBlogColumns( 1 );
		} else {
			wp.customize( 'hvn_realty_blog_columns', function ( setting ) {
				updateBlogColumns( setting.get() );
			} );
		}
	}

	function updateSidebarPosition( position ) {
		var layouts = document.querySelectorAll( '.hvn-theme-layout' );

		toggleBodyClass( 'hvn-theme-has-sidebar', 'none' !== position );
		toggleBodyClass( 'hvn-theme-no-sidebar', 'none' === position );
		toggleBodyClass( 'hvn-theme-sidebar-left', 'left' === position );
		toggleBodyClass( 'hvn-theme-sidebar-right', 'right' === position );
		toggleBodyClass( 'hvn-theme-sidebar-none', 'none' === position );

		layouts.forEach( function ( layout ) {
			layout.classList.remove( 'hvn-has-sidebar', 'hvn-sidebar-left', 'hvn-sidebar-right' );
			if ( 'none' !== position ) {
				layout.classList.add( 'hvn-has-sidebar', 'hvn-sidebar-' + position );
			}
		} );

		document.querySelectorAll( '.hvn-theme-sidebar-area' ).forEach( function ( sidebar ) {
			sidebar.style.display = 'none' === position ? 'none' : '';
		} );
	}

	function registerPreviewBindings() {
		wp.customize( 'blogname', function ( value ) {
			value.bind( function ( to ) {
				setText( '.hvn-theme-site-title a', to );
			} );
		} );

		wp.customize( 'blogdescription', function ( value ) {
			value.bind( function ( to ) {
				setText( '.hvn-theme-site-description', to );
			} );
		} );

		wp.customize( 'header_textcolor', function ( value ) {
			value.bind( function ( to ) {
				var selectors = '.hvn-theme-site-title, .hvn-theme-site-description, .hvn-theme-nav .hvn-theme-nav-menu > li > a, .hvn-theme-main-navigation .hvn-theme-nav-menu > li > a, .hvn-theme-search-toggle, .hvn-theme-menu-toggle .hamburger, .hvn-theme-menu-toggle .hamburger::before, .hvn-theme-menu-toggle .hamburger::after';

				if ( 'blank' === to ) {
					setStyle( 'hvn-realty-header-color-style', selectors + '{clip:rect(1px,1px,1px,1px);position:absolute;}' );
					return;
				}

				var colorValue = String( to ).replace( '#', '' );
				setStyle(
					'hvn-realty-header-color-style',
					'.hvn-theme-site-title a, .hvn-theme-site-description, .hvn-theme-nav .hvn-theme-nav-menu > li > a, .hvn-theme-main-navigation .hvn-theme-nav-menu > li > a, .hvn-theme-search-toggle{color:#' + colorValue + ' !important;}' +
					'.hvn-theme-nav-menu .menu-item-has-children > a::after{color:#' + colorValue + ' !important;}' +
					'.hvn-theme-menu-toggle .hamburger, .hvn-theme-menu-toggle .hamburger::before, .hvn-theme-menu-toggle .hamburger::after{background-color:#' + colorValue + ' !important;}'
				);
			} );
		} );

		bindColorVar( 'hvn_realty_primary_color', '--hvn-primary' );
		bindColorVar( 'hvn_realty_secondary_color', '--hvn-secondary' );
		bindColorVar( 'hvn_realty_accent_color', '--hvn-accent' );
		bindColorVar( 'hvn_realty_text_color', '--hvn-text' );
		bindColorVar( 'hvn_realty_background_color', '--hvn-bg' );
		bindColorVar( 'hvn_realty_border_color', '--hvn-border' );
		bindColorVar( 'hvn_realty_footer_bg_color', '--hvn-footer-bg' );
		bindColorVar( 'hvn_realty_footer_text_color', '--hvn-footer-text' );
		bindColorVar( 'hvn_realty_footer_link_color', '--hvn-footer-link' );

		bindRootVar( 'hvn_realty_border_radius', '--hvn-radius', function ( to ) {
			return parseInt( to, 10 ) + 'px';
		} );

		bindRootVar( 'hvn_realty_container_width', '--hvn-container', function ( to ) {
			return parseInt( to, 10 ) + 'px';
		} );

		bindRootVar( 'hvn_realty_body_font_size', '--hvn-font-size', function ( to ) {
			return parseInt( to, 10 ) + 'px';
		} );

		bindRootVar( 'hvn_realty_line_height', '--hvn-line-height', function ( to ) {
			return parseFloat( to );
		} );

		bindFontFamily( 'hvn_realty_body_font_family', '--hvn-font-body' );
		bindFontFamily( 'hvn_realty_heading_font_family', '--hvn-font-heading' );

		wp.customize( 'hvn_realty_heading_scale', function ( value ) {
			value.bind( function ( to ) {
				var map = { small: 0.9, medium: 1, large: 1.12 };
				var scale = map[ to ] || 1;
				setStyle( 'hvn-realty-heading-scale-css', ':root{--hvn-heading-scale:' + scale + ';}' );
			} );
		} );

		var fontWeightTokens = config.fontWeightTokens || {};
		[ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ].forEach( function ( level ) {
			var settingId = 'hvn_realty_' + level + '_weight';
			var cssVar = '--hvn-realty-' + level + '-weight';

			wp.customize( settingId, function ( value ) {
				value.bind( function ( to ) {
					var output = fontWeightTokens[ String( to ) ] || to;
					setStyle( 'hvn-realty-dynamic-' + settingId, ':root{' + cssVar + ':' + output + ';}' );
				} );
			} );
		} );

		wp.customize( 'hvn_realty_copyright_text', function ( value ) {
			function updateCopyright( to ) {
				setHtml( '.hvn-theme-copyright', to || defaultCopyright );
			}

			value.bind( updateCopyright );
			updateCopyright( value.get() );
		} );

		wp.customize( 'hvn_realty_header_cta_text', function ( value ) {
			function updateHeaderCtaText( to ) {
				document.querySelectorAll( '.hvn-theme-header-cta' ).forEach( function ( cta ) {
					cta.textContent = to;
				} );
			}

			value.bind( updateHeaderCtaText );
			updateHeaderCtaText( value.get() );
		} );

		wp.customize( 'hvn_realty_header_cta_url', function ( value ) {
			value.bind( function ( to ) {
				var href = to;
				if ( ! href && wp.customize( 'home' ) ) {
					href = wp.customize( 'home' ).get();
				}
				if ( ! href ) {
					href = '/';
				}
				document.querySelectorAll( '.hvn-theme-header-cta, .hvn-theme-mobile-header-cta' ).forEach( function ( cta ) {
					cta.setAttribute( 'href', href );
				} );
			} );
		} );

		wp.customize( 'hvn_realty_footer_columns', function ( value ) {
			value.bind( function ( to ) {
				updateFooterColumns( to );
			} );
		} );

		wp.customize( 'hvn_realty_show_back_to_top', function ( value ) {
			value.bind( function ( to ) {
				toggleBodyClass( 'hvn-has-back-to-top', to );
				var btn = document.getElementById( 'hvn-scroll-top' );
				if ( btn ) {
					btn.style.display = to ? '' : 'none';
				} else if ( to && wp.customize.preview ) {
					wp.customize.preview.send( 'refresh' );
				}
			} );
		} );

		Object.keys( homeSectionVisibility ).forEach( function ( settingId ) {
			wp.customize( settingId, function ( value ) {
				value.bind( function ( to ) {
					toggleSectionVisibility( homeSectionVisibility[ settingId ], !! to );
				} );
			} );
		} );

		bindHomeTextFields();
		bindHeroSearchFields();
		bindLocalizedTextPreviews();
		bindHomeDepartmentButton();

		if ( wp.customize( 'hvn_realty_home_show_taxonomy_counts' ) ) {
			wp.customize( 'hvn_realty_home_show_taxonomy_counts', function ( value ) {
				value.bind( function ( to ) {
					document.querySelectorAll( '.hvn-realty-taxonomies__count' ).forEach( function ( el ) {
						el.style.display = to ? '' : 'none';
					} );
				} );
			} );
		}

		bindCustomLogoPreview();
		bindSiteIconPreview();

		wp.customize( 'hvn_realty_blog_columns', function ( value ) {
			value.bind( function ( to ) {
				wp.customize( 'hvn_realty_blog_layout', function ( layoutSetting ) {
					if ( 'list' !== layoutSetting.get() ) {
						updateBlogColumns( to );
					}
				} );
			} );
		} );

		wp.customize( 'hvn_realty_blog_layout', function ( value ) {
			value.bind( function ( to ) {
				updateBlogLayout( to );
			} );
		} );

		wp.customize( 'hvn_realty_container_mode', function ( value ) {
			value.bind( function ( to ) {
				toggleBodyClass( 'hvn-container-full', 'full' === to );
				toggleBodyClass( 'hvn-container-boxed', 'boxed' === to );
				setStyle(
					'hvn-realty-container-mode-preview',
					'full' === to ? '.hvn-container-full .hvn-theme-container{max-width:100%;}' : ''
				);
			} );
		} );

		wp.customize( 'hvn_realty_sidebar_position', function ( value ) {
			value.bind( function ( to ) {
				updateSidebarPosition( to );
				if ( 'none' !== to && ! document.querySelector( '.hvn-theme-sidebar-area' ) && wp.customize.preview ) {
					wp.customize.preview.send( 'refresh' );
				}
			} );
		} );

		wp.customize( 'hvn_realty_sticky_header', function ( value ) {
			value.bind( function ( to ) {
				toggleBodyClass( 'hvn-theme-sticky-header', to );
			} );
		} );

		wp.customize( 'hvn_realty_show_header_search', function ( value ) {
			value.bind( function ( to ) {
				toggleBodyClass( 'hvn-header-no-search', ! to );
			} );
		} );

		wp.customize( 'hvn_realty_show_header_cta', function ( value ) {
			value.bind( function ( to ) {
				toggleBodyClass( 'hvn-header-no-cta', ! to );
			} );
		} );

		wp.customize( 'hvn_realty_header_layout', function ( value ) {
			value.bind( function ( to ) {
				document.body.classList.remove( 'hvn-header-layout-1', 'hvn-header-layout-2', 'hvn-header-layout-3' );
				document.body.classList.add( 'hvn-header-layout-' + to );
			} );
		} );

		wp.customize( 'hvn_realty_home_cta_headline', function ( value ) {
			value.bind( function ( to ) {
				setText( '#hvn-realty-cta-title', to );
			} );
		} );

		wp.customize( 'hvn_realty_home_cta_primary_text', function ( value ) {
			value.bind( function ( to ) {
				setText( '.hvn-realty-home-cta__primary', to );
			} );
		} );

		wp.customize( 'hvn_realty_home_cta_subtext', function ( value ) {
			value.bind( function ( to ) {
				setText( '.hvn-realty-cta__text', to );
			} );
		} );

		wp.customize( 'hvn_realty_home_hero_height', function ( value ) {
			value.bind( function ( to ) {
				var height = parseInt( to, 10 );
				if ( isNaN( height ) ) {
					return;
				}
				height = Math.max( 40, Math.min( 100, height ) );
				setStyle(
					'hvn-realty-hero-height',
					'.hvn-realty-section--hero{--hvn-realty-hero-height:' + height + 'vh;}'
				);
				if ( typeof window.hvnRealtyInvalidateHomeMap === 'function' ) {
					window.setTimeout( window.hvnRealtyInvalidateHomeMap, 50 );
					window.setTimeout( window.hvnRealtyInvalidateHomeMap, 350 );
				}
			} );
		} );

		wp.customize( 'hvn_realty_home_hero_height_mobile', function ( value ) {
			value.bind( function ( to ) {
				var height = parseInt( to, 10 );
				if ( isNaN( height ) ) {
					return;
				}
				height = Math.max( 40, Math.min( 100, height ) );
				setStyle(
					'hvn-realty-hero-height-mobile',
					'.hvn-realty-section--hero{--hvn-realty-hero-height-mobile:' + height + 'vh;}'
				);
				if ( typeof window.hvnRealtyInvalidateHomeMap === 'function' ) {
					window.setTimeout( window.hvnRealtyInvalidateHomeMap, 50 );
					window.setTimeout( window.hvnRealtyInvalidateHomeMap, 350 );
				}
			} );
		} );

		function heroSearchFlexValue( axis, alignment ) {
			var horizontal = { left: 'flex-start', center: 'center', right: 'flex-end' };
			var vertical = { top: 'flex-start', center: 'center', bottom: 'flex-end' };

			if ( axis === 'horizontal' ) {
				return horizontal[ alignment ] || 'flex-start';
			}

			return vertical[ alignment ] || 'center';
		}

		function clampHeroSearchOffset( value ) {
			var offset = parseInt( value, 10 );
			if ( isNaN( offset ) ) {
				return 0;
			}
			return Math.max( 0, Math.min( 100, offset ) );
		}

		function clampHeroSearchWidth( value ) {
			var width = parseInt( value, 10 );
			if ( isNaN( width ) ) {
				return 400;
			}
			return Math.max( 400, Math.min( 700, width ) );
		}

		function updateHeroSearchPosition() {
			var horizontalSetting = wp.customize( 'hvn_realty_hero_search_horizontal' );
			var verticalSetting = wp.customize( 'hvn_realty_hero_search_vertical' );
			var offsetXSetting = wp.customize( 'hvn_realty_hero_search_offset_x' );
			var offsetYSetting = wp.customize( 'hvn_realty_hero_search_offset_y' );
			var widthSetting = wp.customize( 'hvn_realty_hero_search_width' );

			if ( ! horizontalSetting || ! verticalSetting || ! offsetXSetting || ! offsetYSetting || ! widthSetting ) {
				return;
			}

			var horizontal = horizontalSetting.get();
			var vertical = verticalSetting.get();
			var offsetX = clampHeroSearchOffset( offsetXSetting.get() );
			var offsetY = clampHeroSearchOffset( offsetYSetting.get() );
			var width = clampHeroSearchWidth( widthSetting.get() );
			var justify = heroSearchFlexValue( 'horizontal', horizontal );
			var align = heroSearchFlexValue( 'vertical', vertical );

			setStyle(
				'hvn-realty-hero-search-position',
				'.hvn-realty-section--hero-has-search{'
					+ '--hvn-realty-hero-search-justify:' + justify + ';'
					+ '--hvn-realty-hero-search-align:' + align + ';'
					+ '--hvn-realty-hero-search-offset-x:' + offsetX + 'px;'
					+ '--hvn-realty-hero-search-offset-y:' + offsetY + 'px;'
					+ '--hvn-realty-hero-search-width:' + width + 'px;'
					+ '}'
			);
		}

		[
			'hvn_realty_hero_search_horizontal',
			'hvn_realty_hero_search_vertical',
			'hvn_realty_hero_search_offset_x',
			'hvn_realty_hero_search_offset_y',
			'hvn_realty_hero_search_width',
		].forEach( function ( settingId ) {
			wp.customize( settingId, function ( value ) {
				value.bind( updateHeroSearchPosition );
			} );
		} );

		if ( wp.customize.preview ) {
			wp.customize.preview.bind( 'ready', updateHeroSearchPosition );
		}
	}

	function scrollToHomeSection( selector ) {
		if ( ! selector ) {
			return;
		}

		var target = document.querySelector( selector );
		if ( ! target ) {
			return;
		}

		target.scrollIntoView( { block: 'start', behavior: 'auto' } );
		target.classList.add( 'hvn-realty-customizer-highlight' );
		window.setTimeout( function () {
			target.classList.remove( 'hvn-realty-customizer-highlight' );
		}, 1600 );

		if ( '#hvn-realty-section-hero' === selector && typeof window.hvnRealtyInvalidateHomeMap === 'function' ) {
			window.setTimeout( window.hvnRealtyInvalidateHomeMap, 350 );
		}
	}

	function registerHomepageSectionScroll() {
		if ( typeof wp === 'undefined' || ! wp.customize || ! wp.customize.preview ) {
			return;
		}

		wp.customize.preview.bind( 'hvn-realty-scroll-to-section', function ( data ) {
			if ( data && data.selector ) {
				scrollToHomeSection( data.selector );
			}
		} );

		// Section expanded → scroll is registered in customizer-controls.js (controls frame).
		// wp.customize.section() is not available in the preview iframe.
	}

	// Deferred setting API — registers when the preview messenger syncs each setting.
	registerPreviewBindings();
	wp.customize.bind( 'preview-ready', registerHomepageSectionScroll );

}( jQuery ) );
