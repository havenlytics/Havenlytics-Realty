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

	function bindHeroTitlePreview() {
		var ids = [
			'hvn_realty_home_hero_title_before',
			'hvn_realty_home_hero_title_highlight',
			'hvn_realty_home_hero_title_after',
		];

		function updateHeroTitle() {
			var h1 = document.querySelector( '#hvn-theme-home-hero h1' );
			if ( ! h1 ) {
				return;
			}

			var before = wp.customize( 'hvn_realty_home_hero_title_before' ) ? wp.customize( 'hvn_realty_home_hero_title_before' ).get() : '';
			var highlight = wp.customize( 'hvn_realty_home_hero_title_highlight' ) ? wp.customize( 'hvn_realty_home_hero_title_highlight' ).get() : '';
			var after = wp.customize( 'hvn_realty_home_hero_title_after' ) ? wp.customize( 'hvn_realty_home_hero_title_after' ).get() : '';

			h1.textContent = '';
			h1.appendChild( document.createTextNode( before || '' ) );
			if ( highlight ) {
				var em = document.createElement( 'em' );
				em.textContent = highlight;
				h1.appendChild( em );
			}
			h1.appendChild( document.createTextNode( after || '' ) );
		}

		ids.forEach( function ( settingId ) {
			if ( ! wp.customize( settingId ) ) {
				return;
			}
			wp.customize( settingId, function ( value ) {
				value.bind( updateHeroTitle );
			} );
		} );

		updateHeroTitle();
	}

	function bindHeroButtonsPreview() {
		function refreshHeroButtons() {
			var primaryLabel = wp.customize( 'hvn_realty_home_hero_primary_label' );
			var primaryUrl = wp.customize( 'hvn_realty_home_hero_primary_url' );
			var ghostLabel = wp.customize( 'hvn_realty_home_hero_ghost_label' );
			var ghostUrl = wp.customize( 'hvn_realty_home_hero_ghost_url' );

			if ( primaryLabel && primaryUrl ) {
				setLink(
					'#hvn-theme-home-hero .hvn-theme-home-hero__actions .hvn-theme-home-btn--gold',
					resolveThemeLink( primaryUrl.get(), '#hvn-theme-home-search' ),
					primaryLabel.get()
				);
			}
			if ( ghostLabel && ghostUrl ) {
				setLink(
					'#hvn-theme-home-hero .hvn-theme-home-hero__actions .hvn-theme-home-btn--ghost',
					resolveThemeLink( ghostUrl.get(), '#hvn-theme-home-agents' ),
					ghostLabel.get()
				);
			}
		}

		[
			'hvn_realty_home_hero_primary_label',
			'hvn_realty_home_hero_primary_url',
			'hvn_realty_home_hero_ghost_label',
			'hvn_realty_home_hero_ghost_url',
		].forEach( function ( settingId ) {
			if ( ! wp.customize( settingId ) ) {
				return;
			}
			wp.customize( settingId, function ( value ) {
				value.bind( refreshHeroButtons );
			} );
		} );

		refreshHeroButtons();
	}

	function bindHeroFloatPreview() {
		bindTextPreview( 'hvn_realty_home_hero_float_title', '#hvn-theme-home-hero .hvn-theme-home-hero__float strong' );
		bindTextPreview( 'hvn_realty_home_hero_float_subtitle', '#hvn-theme-home-hero .hvn-theme-home-hero__float span' );
	}

	function bindHeroStatLabelsPreview() {
		[ 1, 2, 3 ].forEach( function ( n ) {
			bindTextPreview(
				'hvn_realty_home_hero_stat' + n + '_label',
				'#hvn-theme-home-hero .hvn-theme-home-hero__stat:nth-child(' + n + ') span'
			);
			if ( wp.customize( 'hvn_realty_home_hero_stat' + n + '_suffix' ) ) {
				wp.customize( 'hvn_realty_home_hero_stat' + n + '_suffix', function ( value ) {
					value.bind( function ( to ) {
						var strong = document.querySelector( '#hvn-theme-home-hero .hvn-theme-home-hero__stat:nth-child(' + n + ') strong' );
						if ( strong ) {
							strong.setAttribute( 'data-hvn-theme-suffix', to || '' );
						}
					} );
				} );
			}
		} );
	}

	function bindCtaButtonsPreview() {
		function refreshCtaButtons() {
			var primaryLabel = wp.customize( 'hvn_realty_home_cta_primary_label' );
			var primaryUrl = wp.customize( 'hvn_realty_home_cta_primary_url' );
			var secondaryLabel = wp.customize( 'hvn_realty_home_cta_secondary_label' );
			var secondaryUrl = wp.customize( 'hvn_realty_home_cta_secondary_url' );

			if ( primaryLabel && primaryUrl ) {
				setLink(
					'.hvn-theme-home-cta__actions .hvn-theme-home-btn--gold',
					resolveThemeLink( primaryUrl.get(), '#hvn-theme-home-footer' ),
					primaryLabel.get()
				);
			}
			if ( secondaryLabel && secondaryUrl ) {
				setLink(
					'.hvn-theme-home-cta__actions .hvn-theme-home-btn--ghost',
					resolveThemeLink( secondaryUrl.get(), '#hvn-theme-home-agents' ),
					secondaryLabel.get()
				);
			}
		}

		[
			'hvn_realty_home_cta_primary_label',
			'hvn_realty_home_cta_primary_url',
			'hvn_realty_home_cta_secondary_label',
			'hvn_realty_home_cta_secondary_url',
		].forEach( function ( settingId ) {
			if ( ! wp.customize( settingId ) ) {
				return;
			}
			wp.customize( settingId, function ( value ) {
				value.bind( refreshCtaButtons );
			} );
		} );

		refreshCtaButtons();
	}

	function bindHomeTextFields() {
		var bindings = {
			hvn_realty_home_hero_eyebrow: '#hvn-theme-home-hero .hvn-theme-home-eyebrow',
			hvn_realty_home_hero_subtitle: '#hvn-theme-home-hero .hvn-theme-home-hero__copy > p',
			hvn_realty_home_featured_title: '#hvn-theme-home-properties-title',
			hvn_realty_home_featured_subtitle: '#hvn-theme-home-properties .hvn-theme-home-eyebrow',
			hvn_realty_home_why_eyebrow: '#hvn-theme-home-why .hvn-theme-home-eyebrow',
			hvn_realty_home_why_title: '#hvn-theme-home-why-title',
			hvn_realty_home_why_subtitle: '#hvn-theme-home-why .hvn-theme-home-head p',
			hvn_realty_home_locations_title: '#hvn-theme-home-locations-title',
			hvn_realty_home_locations_subtitle: '#hvn-theme-home-locations .hvn-theme-home-eyebrow',
			hvn_realty_home_locations_text: '#hvn-theme-home-locations .hvn-theme-home-head p',
			hvn_realty_home_agents_title: '#hvn-theme-home-agents-title',
			hvn_realty_home_agents_subtitle: '#hvn-theme-home-agents .hvn-theme-home-eyebrow',
			hvn_realty_home_blog_title: '#hvn-theme-home-blog-title',
			hvn_realty_home_blog_subtitle: '#hvn-theme-home-blog .hvn-theme-home-eyebrow',
			hvn_realty_home_property_types_title: '#hvn-theme-home-types-title',
			hvn_realty_home_property_types_subtitle: '#hvn-theme-home-types .hvn-theme-home-eyebrow',
			hvn_realty_home_testimonials_title: '#hvn-theme-home-testimonials-title',
			hvn_realty_home_testimonials_subtitle: '#hvn-theme-home-testimonials .hvn-theme-home-eyebrow',
			hvn_realty_home_cta_title: '#hvn-theme-home-cta-title',
			hvn_realty_home_cta_subtitle: '#hvn-theme-home-cta .hvn-theme-home-cta__copy p',
		};

		Object.keys( bindings ).forEach( function ( settingId ) {
			if ( wp.customize( settingId ) ) {
				bindTextPreview( settingId, bindings[ settingId ] );
			}
		} );

		bindHeroTitlePreview();
		bindHeroButtonsPreview();
		bindHeroFloatPreview();
		bindHeroStatLabelsPreview();
		bindCtaButtonsPreview();
	}

	function bindHomeSectionStyles() {
		var slugs = config.homeSectionSlugs || [];
		slugs.forEach( function ( slug ) {
			var prefix = 'hvn_realty_home_style_' + slug;
			var selector = ( config.homeSectionSelectors && config.homeSectionSelectors[ slug ] ) || '';

			if ( ! selector ) {
				return;
			}

			// Use the deferred wp.customize( id, cb ) form (no premature existence
			// guard). registerPreviewBindings() runs at script-parse time, before
			// customize-preview.js instantiates the settings; the immediate
			// wp.customize( id ) check would return undefined and the binding would
			// never be registered, which is why these previews did not update live.
			[ 'bg', 'text' ].forEach( function ( token ) {
				var settingId = prefix + '_' + token;
				wp.customize( settingId, function ( value ) {
					value.bind( function ( to ) {
						var cssProp = 'bg' === token ? 'background-color' : 'color';
						var extra = 'bg' === token ? 'background-image:none;' : '';
						var rules = 'body.hvn-theme-home ' + selector + '{' + cssProp + ':' + ( to || 'inherit' ) + ';' + extra + '}';
						setStyle( 'hvn-realty-home-style-' + slug + '-' + token, rules );
					} );
				} );
			} );

			[ 'pad_top', 'pad_bottom' ].forEach( function ( token ) {
				var settingId = prefix + '_' + token;
				wp.customize( settingId, function ( value ) {
					value.bind( function ( to ) {
						var cssProp = 'pad_top' === token ? 'padding-top' : 'padding-bottom';
						var px = parseInt( to, 10 );
						var val = isNaN( px ) || px <= 0 ? '' : px + 'px';
						var rules = val ? 'body.hvn-theme-home ' + selector + '{' + cssProp + ':' + val + ';}' : '';
						setStyle( 'hvn-realty-home-style-' + slug + '-' + token, rules );
					} );
				} );
			} );

			wp.customize( prefix + '_animate', function ( value ) {
				value.bind( function ( to ) {
					var rules = to ? '' : 'body.hvn-theme-home ' + selector + ' .hvn-theme-home-reveal{opacity:1;transform:none}';
					setStyle( 'hvn-realty-home-style-' + slug + '-animate', rules );
				} );
			} );
		} );
	}

	function bindHeroGradient() {
		var ids = {
			top: 'hvn_realty_home_style_hero_grad_top',
			mid: 'hvn_realty_home_style_hero_grad_mid',
			bottom: 'hvn_realty_home_style_hero_grad_bottom',
		};

		function valueOf( settingId, fallback ) {
			var setting = wp.customize( settingId );
			var current = setting ? setting.get() : '';
			return current || fallback;
		}

		function applyGradient() {
			var top = valueOf( ids.top, '#151a1f' );
			var mid = valueOf( ids.mid, '#1f3a3a' );
			var bottom = valueOf( ids.bottom, '#2a4c4a' );
			var rules = 'body.hvn-theme-home #hvn-theme-home-hero{background:linear-gradient(180deg,' +
				top + ' 0%,' + mid + ' 64%,' + bottom + ' 100%);}';
			setStyle( 'hvn-realty-home-hero-gradient', rules );
		}

		Object.keys( ids ).forEach( function ( key ) {
			// Deferred form so the binding registers even though the gradient
			// settings are not yet instantiated when registerPreviewBindings() runs.
			wp.customize( ids[ key ], function ( value ) {
				value.bind( applyGradient );
			} );
		} );
	}

	function pluginBridgeComponentCss( primary, secondary ) {
		if ( ! primary || ! secondary ) {
			return '';
		}
		return '.hvnly-btn-primary,.hvnly-button-primary,.hvnly-submit-btn,button.hvnly-property-single__action-btn--primary{background-color:' + primary + ';border-color:' + primary + '}' +
			'.hvnly-btn-primary:hover,.hvnly-button-primary:hover,.hvnly-submit-btn:hover,button.hvnly-property-single__action-btn--primary:hover{background-color:' + secondary + ';border-color:' + secondary + '}' +
			'a,.hvnly-link{color:' + primary + '}' +
			'a:hover,.hvnly-link:hover{color:' + secondary + '}';
	}

	function refreshPluginBridgeComponentPreview() {
		var primarySetting = wp.customize( 'hvn_realty_primary_color' );
		var secondarySetting = wp.customize( 'hvn_realty_secondary_color' );
		if ( ! primarySetting || ! secondarySetting ) {
			return;
		}
		var css = pluginBridgeComponentCss( primarySetting.get(), secondarySetting.get() );
		setStyle( 'hvn-realty-plugin-bridge-components', css );
	}

	function bindPluginColorBridgePreview() {
		var bridgeVars = [
			'--hvnly-brand-primary',
			'--hvnly-primary-color',
			'--hvnly-button-bg',
			'--hvnly-input-focus',
			'--hvnly-price-color',
			'--hvnly-pagination-active-bg',
			'--hvnly-map-marker-bg',
			'--hvnly-slider-thumb',
			'--hvnly-link-color',
			'--hvnly-status-sale',
		];
		var bridgeSecondaryVars = [
			'--hvnly-brand-secondary',
			'--hvnly-secondary-color',
			'--hvnly-button-bg-hover',
			'--hvnly-slider-range',
			'--hvnly-link-hover-color',
		];

		if ( wp.customize( 'hvn_realty_primary_color' ) ) {
			wp.customize( 'hvn_realty_primary_color', function ( value ) {
				value.bind( function ( to ) {
					if ( ! to ) {
						return;
					}
					var decl = bridgeVars.map( function ( v ) {
						return v + ':' + to;
					} ).join( ';' );
					setStyle( 'hvn-realty-plugin-bridge-primary', ':root{' + decl + '}' );
					refreshPluginBridgeComponentPreview();
				} );
			} );
		}

		if ( wp.customize( 'hvn_realty_secondary_color' ) ) {
			wp.customize( 'hvn_realty_secondary_color', function ( value ) {
				value.bind( function ( to ) {
					if ( ! to ) {
						return;
					}
					var decl = bridgeSecondaryVars.map( function ( v ) {
						return v + ':' + to;
					} ).join( ';' );
					setStyle( 'hvn-realty-plugin-bridge-secondary', ':root{' + decl + '}' );
					refreshPluginBridgeComponentPreview();
				} );
			} );
		}
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
		bindFontFamily( 'hvn_realty_nav_font_family', '--hvn-font-nav' );

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
		bindHomeSectionStyles();
		bindHeroGradient();
		bindPluginColorBridgePreview();
		bindLocalizedTextPreviews();

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

		bindMobileSearchDrawerPreview();
	}

	function bindMobileSearchDrawerPreview() {
		var defaults = config.msdDefaults || {};
		var settingIds = config.msdPreviewSettings || [];

		if ( ! settingIds.length ) {
			return;
		}

		function getMsdSetting( id, fallback ) {
			if ( ! wp.customize( id ) ) {
				return fallback;
			}
			var value = wp.customize( id ).get();
			return ( value === undefined || value === null || value === '' ) ? fallback : value;
		}

		function clampInt( value, min, max, fallback ) {
			var parsed = parseInt( value, 10 );
			if ( isNaN( parsed ) ) {
				return fallback;
			}
			return Math.max( min, Math.min( max, parsed ) );
		}

		function clampFloat( value, min, max, fallback ) {
			var parsed = parseFloat( value );
			if ( isNaN( parsed ) ) {
				return fallback;
			}
			return Math.max( min, Math.min( max, parsed ) );
		}

		function hexToRgba( hex, opacity ) {
			var color = String( hex || '' ).replace( '#', '' );
			if ( 3 === color.length ) {
				color = color[ 0 ] + color[ 0 ] + color[ 1 ] + color[ 1 ] + color[ 2 ] + color[ 2 ];
			}
			if ( 6 !== color.length ) {
				return 'transparent';
			}
			var r = parseInt( color.substr( 0, 2 ), 16 );
			var g = parseInt( color.substr( 2, 2 ), 16 );
			var b = parseInt( color.substr( 4, 2 ), 16 );
			var alpha = clampInt( opacity, 0, 100, 100 ) / 100;
			return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
		}

		function buildMobileSearchDrawerPreviewCss() {
			var dockBg = getMsdSetting( 'hvn_realty_msd_dock_bg', defaults.hvn_realty_msd_dock_bg || '#ffffff' );
			var dockOp = clampInt( getMsdSetting( 'hvn_realty_msd_dock_bg_opacity', defaults.hvn_realty_msd_dock_bg_opacity ), 0, 100, 72 );
			var drawerBg = getMsdSetting( 'hvn_realty_msd_drawer_bg', defaults.hvn_realty_msd_drawer_bg || '#ffffff' );
			var drawerOp = clampInt( getMsdSetting( 'hvn_realty_msd_drawer_bg_opacity', defaults.hvn_realty_msd_drawer_bg_opacity ), 0, 100, 97 );
			var button = getMsdSetting( 'hvn_realty_msd_button_color', defaults.hvn_realty_msd_button_color || '#1f3a3a' );
			var buttonSec = getMsdSetting( 'hvn_realty_msd_button_color_secondary', defaults.hvn_realty_msd_button_color_secondary || '#2a4c4a' );
			var active = getMsdSetting( 'hvn_realty_msd_active_dept_color', defaults.hvn_realty_msd_active_dept_color || '#1f3a3a' );
			var activeText = getMsdSetting( 'hvn_realty_msd_active_dept_text_color', defaults.hvn_realty_msd_active_dept_text_color || '#ffffff' );
			var border = getMsdSetting( 'hvn_realty_msd_border_color', defaults.hvn_realty_msd_border_color || '#e3dccd' );
			var shadowOp = clampInt( getMsdSetting( 'hvn_realty_msd_shadow_opacity', defaults.hvn_realty_msd_shadow_opacity ), 0, 100, 22 ) / 100;
			var overlayOp = clampInt( getMsdSetting( 'hvn_realty_msd_overlay_opacity', defaults.hvn_realty_msd_overlay_opacity ), 0, 100, 40 ) / 100;
			var animMs = clampInt( getMsdSetting( 'hvn_realty_msd_animation_duration', defaults.hvn_realty_msd_animation_duration ), 120, 1200, 460 );
			var bottom = clampInt( getMsdSetting( 'hvn_realty_msd_bottom_spacing', defaults.hvn_realty_msd_bottom_spacing ), 0, 80, 16 );
			var maxHeight = clampInt( getMsdSetting( 'hvn_realty_msd_max_drawer_height', defaults.hvn_realty_msd_max_drawer_height ), 40, 90, 70 );
			var deptSize = clampFloat( getMsdSetting( 'hvn_realty_msd_dept_font_size', defaults.hvn_realty_msd_dept_font_size ), 10, 24, 13.5 );
			var buttonSize = clampFloat( getMsdSetting( 'hvn_realty_msd_button_font_size', defaults.hvn_realty_msd_button_font_size ), 10, 24, 16 );
			var dockRadius = clampInt( getMsdSetting( 'hvn_realty_msd_dock_radius', defaults.hvn_realty_msd_dock_radius ), 0, 60, 30 );
			var drawerRadius = clampInt( getMsdSetting( 'hvn_realty_msd_drawer_radius', defaults.hvn_realty_msd_drawer_radius ), 0, 60, 28 );
			var buttonRadius = clampInt( getMsdSetting( 'hvn_realty_msd_button_radius', defaults.hvn_realty_msd_button_radius ), 0, 40, 18 );
			var dockPad = clampInt( getMsdSetting( 'hvn_realty_msd_dock_padding', defaults.hvn_realty_msd_dock_padding ), 4, 32, 12 );
			var drawerPad = clampInt( getMsdSetting( 'hvn_realty_msd_drawer_padding', defaults.hvn_realty_msd_drawer_padding ), 8, 40, 20 );
			var deptGap = clampInt( getMsdSetting( 'hvn_realty_msd_dept_spacing', defaults.hvn_realty_msd_dept_spacing ), 4, 24, 8 );
			var spring = !! getMsdSetting( 'hvn_realty_msd_spring_animation', defaults.hvn_realty_msd_spring_animation );
			var blur = !! getMsdSetting( 'hvn_realty_msd_backdrop_blur', defaults.hvn_realty_msd_backdrop_blur );
			var edgeFade = !! getMsdSetting( 'hvn_realty_msd_edge_fade', defaults.hvn_realty_msd_edge_fade );
			var enabled = !! getMsdSetting( 'hvn_realty_msd_enabled', defaults.hvn_realty_msd_enabled );
			var easeDock = spring ? 'cubic-bezier(0.22, 1, 0.36, 1)' : 'ease';
			var easeSpring = spring ? 'cubic-bezier(0.22, 1.12, 0.32, 1)' : 'ease';
			var fadeBg = hexToRgba( dockBg, Math.min( 100, dockOp + 20 ) );
			var css = '';

			css += 'body.hvn-theme-home .hvn-theme-home-msd-root{';
			css += '--hvn-theme-home-msd-primary:' + button + ';';
			css += '--hvn-theme-home-msd-primary-light:' + buttonSec + ';';
			css += '--hvn-theme-home-msd-border:' + border + ';';
			css += '--hvn-theme-home-msd-glass-bg:' + hexToRgba( dockBg, dockOp ) + ';';
			css += '--hvn-theme-home-msd-drawer-bg:' + hexToRgba( drawerBg, drawerOp ) + ';';
			css += '--hvn-theme-home-msd-active-bg:' + active + ';';
			css += '--hvn-theme-home-msd-active-text:' + activeText + ';';
			css += '--hvn-theme-home-msd-dock-shadow:0 18px 45px rgba(20,30,25,' + shadowOp + '),0 2px 8px rgba(20,30,25,' + ( shadowOp * 0.36 ) + ');';
			css += '--hvn-theme-home-msd-overlay:rgba(8,10,9,' + overlayOp + ');';
			css += '--hvn-theme-home-msd-anim-duration:' + animMs + 'ms;';
			css += '--hvn-theme-home-msd-bottom-offset:' + bottom + 'px;';
			css += '--hvn-theme-home-msd-max-height:' + maxHeight + 'vh;';
			css += '--hvn-theme-home-msd-dept-font-size:' + deptSize + 'px;';
			css += '--hvn-theme-home-msd-button-font-size:' + buttonSize + 'px;';
			css += '--hvn-theme-home-msd-dock-radius:' + dockRadius + 'px;';
			css += '--hvn-theme-home-msd-drawer-radius:' + drawerRadius + 'px;';
			css += '--hvn-theme-home-msd-button-radius:' + buttonRadius + 'px;';
			css += '--hvn-theme-home-msd-dock-padding:' + dockPad + 'px;';
			css += '--hvn-theme-home-msd-drawer-padding:' + drawerPad + 'px;';
			css += '--hvn-theme-home-msd-dept-gap:' + deptGap + 'px;';
			css += '--hvn-theme-home-msd-ease-dock:' + easeDock + ';';
			css += '--hvn-theme-home-msd-ease-spring:' + easeSpring + ';';
			css += '--hvn-theme-home-msd-fade-bg:' + fadeBg + ';';
			css += '}';

			css += 'body.hvn-theme-home .hvn-theme-home-msd-dock-wrap{bottom:calc(var(--hvn-theme-home-msd-bottom-offset, 16px) + env(safe-area-inset-bottom, 0px));top:auto}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-dock-wrap.hvn-theme-home-msd-drawer-open .hvn-theme-home-msd-drawer{max-height:var(--hvn-theme-home-msd-max-height)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-dock{border-radius:var(--hvn-theme-home-msd-dock-radius);padding:var(--hvn-theme-home-msd-dock-padding);box-shadow:var(--hvn-theme-home-msd-dock-shadow)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-drawer{background:var(--hvn-theme-home-msd-drawer-bg);border-radius:0 0 var(--hvn-theme-home-msd-drawer-radius) var(--hvn-theme-home-msd-drawer-radius)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-pills{gap:var(--hvn-theme-home-msd-dept-gap)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-pill{font-size:var(--hvn-theme-home-msd-dept-font-size)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-pill.hvn-theme-home-msd-pill-active{background:var(--hvn-theme-home-msd-active-bg);border-color:var(--hvn-theme-home-msd-active-bg);color:var(--hvn-theme-home-msd-active-text)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-search-submit{font-size:var(--hvn-theme-home-msd-button-font-size);border-radius:var(--hvn-theme-home-msd-button-radius);background:linear-gradient(135deg,var(--hvn-theme-home-msd-primary),var(--hvn-theme-home-msd-primary-light))}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-drawer-body{padding-left:var(--hvn-theme-home-msd-drawer-padding);padding-right:var(--hvn-theme-home-msd-drawer-padding)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-drawer-footer{padding-left:var(--hvn-theme-home-msd-drawer-padding);padding-right:var(--hvn-theme-home-msd-drawer-padding)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-scrim{background:var(--hvn-theme-home-msd-overlay)}';
			css += 'body.hvn-theme-home .hvn-theme-home-msd-dock-wrap{transition:opacity var(--hvn-theme-home-msd-anim-duration) var(--hvn-theme-home-msd-ease-dock),transform calc(var(--hvn-theme-home-msd-anim-duration) * 1.13) var(--hvn-theme-home-msd-ease-spring),left 0.42s var(--hvn-theme-home-msd-ease-dock),right 0.42s var(--hvn-theme-home-msd-ease-dock),bottom 0.42s var(--hvn-theme-home-msd-ease-dock)}';

			if ( ! blur ) {
				css += 'body.hvn-theme-home .hvn-theme-home-msd-dock,body.hvn-theme-home .hvn-theme-home-msd-drawer,body.hvn-theme-home .hvn-theme-home-msd-scrim{backdrop-filter:none;-webkit-backdrop-filter:none}';
			}

			if ( ! edgeFade ) {
				css += 'body.hvn-theme-home .hvn-theme-home-msd-pills-fade{display:none}';
			}

			if ( ! enabled ) {
				css += 'body.hvn-theme-home .hvn-theme-home-msd-root{display:none!important}';
			}

			return css;
		}

		function refreshMobileSearchDrawerPreview() {
			setStyle( 'hvn-realty-msd-live-preview', buildMobileSearchDrawerPreviewCss() );

			if ( window.hvnRealtyMobileSearchDrawerApi && 'function' === typeof window.hvnRealtyMobileSearchDrawerApi.refreshConfig ) {
				window.hvnRealtyMobileSearchDrawerApi.refreshConfig( {
					autoCenterPills: !! getMsdSetting( 'hvn_realty_msd_auto_center', defaults.hvn_realty_msd_auto_center ),
					edgeFade: !! getMsdSetting( 'hvn_realty_msd_edge_fade', defaults.hvn_realty_msd_edge_fade ),
					dragClose: !! getMsdSetting( 'hvn_realty_msd_drag_close', defaults.hvn_realty_msd_drag_close ),
					swipeGestures: !! getMsdSetting( 'hvn_realty_msd_swipe_gestures', defaults.hvn_realty_msd_swipe_gestures ),
					heroTriggerOffset: clampInt( getMsdSetting( 'hvn_realty_msd_hero_trigger_offset', defaults.hvn_realty_msd_hero_trigger_offset ), 0, 400, 0 ),
				} );
			}

			if ( window.hvnRealtyMobileSearchDrawerApi && 'function' === typeof window.hvnRealtyMobileSearchDrawerApi.updatePillsFade ) {
				window.hvnRealtyMobileSearchDrawerApi.updatePillsFade();
			}
		}

		settingIds.forEach( function ( settingId ) {
			wp.customize( settingId, function ( value ) {
				value.bind( refreshMobileSearchDrawerPreview );
			} );
		} );

		if ( wp.customize.preview ) {
			wp.customize.preview.bind( 'ready', refreshMobileSearchDrawerPreview );
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

	function reinitHomepageBehaviors( placement ) {
		var container = ( placement && placement.container && placement.container[0] ) || document;
		if ( typeof window.hvnRealtyHomeReinit === 'function' ) {
			window.hvnRealtyHomeReinit( container );
		}
	}

	function registerSelectiveRefreshReinit() {
		if ( ! wp.customize || ! wp.customize.selectiveRefresh ) {
			return;
		}
		wp.customize.selectiveRefresh.bind( 'partial-content-rendered', reinitHomepageBehaviors );
	}

	// Deferred setting API — registers when the preview messenger syncs each setting.
	registerPreviewBindings();
	wp.customize.bind( 'preview-ready', registerHomepageSectionScroll );
	registerSelectiveRefreshReinit();

}( jQuery ) );
