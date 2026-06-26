/**
 * Havenlytics Realty — homepage map and carousels.
 */
( function ( $ ) {
	'use strict';

	var carouselConfig = window.hvnRealtyHomeCarousel || {};

	function getGap() {
		var gap = parseInt( carouselConfig.gap, 10 );
		return isNaN( gap ) ? 16 : gap;
	}

	function slidesForBreakpoint( desktop, tablet, mobile ) {
		if ( ! window.matchMedia ) {
			return desktop;
		}
		if ( window.matchMedia( '(min-width: 1024px)' ).matches ) {
			return desktop;
		}
		if ( window.matchMedia( '(min-width: 768px)' ).matches ) {
			return tablet;
		}
		return mobile;
	}

	function visibleFeaturedSlides() {
		return slidesForBreakpoint(
			parseInt( carouselConfig.featuredDesktop, 10 ) || 3,
			parseInt( carouselConfig.featuredTablet, 10 ) || 2,
			parseInt( carouselConfig.featuredMobile, 10 ) || 1
		);
	}

	function visibleCardSlides() {
		return slidesForBreakpoint(
			parseInt( carouselConfig.cardsDesktop, 10 ) || 4,
			parseInt( carouselConfig.cardsTablet, 10 ) || 2,
			parseInt( carouselConfig.cardsMobile, 10 ) || 1
		);
	}

	function TrackCarousel( options ) {
		this.$track = options.$track;
		this.$slides = options.$slides;
		this.$prev = options.$prev;
		this.$next = options.$next;
		this.$dots = options.$dots || $();
		this.getVisibleSlides = options.getVisibleSlides;
		this.currentIndex = 0;
		this.isAnimating = false;
		this.autoPlayInterval = null;
		this.autoPlayDelay = options.autoPlayDelay || 4000;
		this.isHovering = false;
		this.userInteracted = false;
		this.enableAutoPlay = !! options.enableAutoPlay;

		if ( ! this.$track.length || ! this.$slides.length ) {
			return;
		}

		this.init();
	}

	TrackCarousel.prototype.init = function () {
		this.totalSlides = this.$slides.length;
		this.visibleSlides = this.getVisibleSlides();
		this.slideWidth = this.calculateSlideWidth();
		this.applySlideWidths();
		this.createDots();
		this.updateCarousel();

		var self = this;

		if ( this.$prev.length ) {
			this.$prev.on( 'click', function () {
				self.userInteracted = true;
				self.prev();
			} );
		}

		if ( this.$next.length ) {
			this.$next.on( 'click', function () {
				self.userInteracted = true;
				self.next();
			} );
		}

		this.$track.closest( '[data-hvn-realty-similar-carousel], .hvn-realty-card-carousel, [data-hvn-realty-testimonials-carousel]' ).on( 'mouseenter', function () {
			self.isHovering = true;
			self.stopAutoPlay();
		} ).on( 'mouseleave', function () {
			self.isHovering = false;
			self.startAutoPlay();
		} );

		this.resizeHandler = function () {
			window.clearTimeout( self.resizeTimeout );
			self.resizeTimeout = window.setTimeout( function () {
				var nextVisible = self.getVisibleSlides();
				if ( nextVisible !== self.visibleSlides ) {
					self.visibleSlides = nextVisible;
				}
				self.slideWidth = self.calculateSlideWidth();
				self.applySlideWidths();
				self.createDots();
				self.updateCarousel();
			}, 200 );
		};

		$( window ).on( 'resize', this.resizeHandler );
		this.startAutoPlay();
	};

	TrackCarousel.prototype.calculateSlideWidth = function () {
		var container = this.$track.parent()[0];
		if ( ! container || ! this.$slides.length ) {
			return 0;
		}

		var containerWidth = container.clientWidth;
		var gap = getGap();
		var totalGap = gap * Math.max( 0, this.visibleSlides - 1 );

		return Math.max( 0, ( containerWidth - totalGap ) / this.visibleSlides );
	};

	TrackCarousel.prototype.applySlideWidths = function () {
		var width = this.slideWidth;
		this.$slides.css( {
			width: width + 'px',
			flexBasis: width + 'px',
			maxWidth: width + 'px',
			paddingRight: 0,
		} );
		this.$track.css( 'gap', getGap() + 'px' );
	};

	TrackCarousel.prototype.getStepWidth = function () {
		return this.slideWidth + getGap();
	};

	TrackCarousel.prototype.createDots = function () {
		if ( ! this.$dots.length ) {
			return;
		}

		this.$dots.empty();
		var totalPages = Math.max( 1, this.totalSlides - this.visibleSlides + 1 );
		var self = this;

		for ( var i = 0; i < totalPages; i += 1 ) {
			var $dot = $( '<button type="button" class="hvnly-property-single__carousel-dot" />' );
			if ( i === 0 ) {
				$dot.addClass( 'hvnly-property-single__carousel-dot--active' );
			}
			$dot.attr( 'aria-label', 'Go to slide group ' + ( i + 1 ) + ' of ' + totalPages );
			$dot.on( 'click', function ( index ) {
				return function () {
					self.userInteracted = true;
					self.goToSlide( index );
				};
			}( i ) );
			this.$dots.append( $dot );
		}
	};

	TrackCarousel.prototype.updateCarousel = function () {
		if ( this.isAnimating || ! this.$slides.length ) {
			return;
		}

		this.isAnimating = true;
		var maxIndex = Math.max( 0, this.totalSlides - this.visibleSlides );
		this.currentIndex = Math.min( this.currentIndex, maxIndex );
		var offset = -( this.currentIndex * this.getStepWidth() );

		this.$track.css( {
			transform: 'translateX(' + offset + 'px)',
			transition: 'transform 0.3s ease',
		} );

		this.updateButtons();
		this.updateDots();

		var self = this;
		window.setTimeout( function () {
			self.isAnimating = false;
		}, 300 );
	};

	TrackCarousel.prototype.updateButtons = function () {
		var maxIndex = Math.max( 0, this.totalSlides - this.visibleSlides );
		if ( this.$prev.length ) {
			this.$prev.prop( 'disabled', this.currentIndex === 0 );
		}
		if ( this.$next.length ) {
			this.$next.prop( 'disabled', this.currentIndex >= maxIndex );
		}
	};

	TrackCarousel.prototype.updateDots = function () {
		if ( ! this.$dots.length ) {
			return;
		}

		var totalPages = Math.max( 1, this.totalSlides - this.visibleSlides + 1 );
		var currentPage = Math.min( this.currentIndex, totalPages - 1 );

		this.$dots.children().each( function ( index ) {
			$( this ).toggleClass( 'hvnly-property-single__carousel-dot--active', index === currentPage );
		} );
	};

	TrackCarousel.prototype.prev = function () {
		if ( this.currentIndex > 0 ) {
			this.currentIndex -= 1;
			this.updateCarousel();
		}
	};

	TrackCarousel.prototype.next = function () {
		var maxIndex = Math.max( 0, this.totalSlides - this.visibleSlides );
		if ( this.currentIndex < maxIndex ) {
			this.currentIndex += 1;
			this.updateCarousel();
		} else if ( this.totalSlides > this.visibleSlides ) {
			this.currentIndex = 0;
			this.updateCarousel();
		}
	};

	TrackCarousel.prototype.goToSlide = function ( index ) {
		var maxIndex = Math.max( 0, this.totalSlides - this.visibleSlides );
		index = Math.min( index, maxIndex );
		if ( index !== this.currentIndex ) {
			this.currentIndex = index;
			this.updateCarousel();
		}
	};

	TrackCarousel.prototype.startAutoPlay = function () {
		if ( ! this.enableAutoPlay ) {
			return;
		}

		this.stopAutoPlay();
		if ( this.totalSlides <= this.visibleSlides ) {
			return;
		}

		var self = this;
		this.autoPlayInterval = window.setInterval( function () {
			if ( ! self.isHovering && ! self.userInteracted ) {
				self.next();
			}
		}, this.autoPlayDelay );
	};

	TrackCarousel.prototype.stopAutoPlay = function () {
		if ( this.autoPlayInterval ) {
			window.clearInterval( this.autoPlayInterval );
			this.autoPlayInterval = null;
		}
	};

	function visibleBlogSlides() {
		return slidesForBreakpoint(
			parseInt( carouselConfig.blogDesktop, 10 ) || 3,
			parseInt( carouselConfig.blogTablet, 10 ) || 2,
			parseInt( carouselConfig.blogMobile, 10 ) || 1
		);
	}

	function getHomeMapDepartmentSlugs() {
		var $shell = $( '#hvn-realty-home-map-shell' );
		if ( ! $shell.length ) {
			return [];
		}

		var raw = $shell.attr( 'data-map-departments' );
		if ( raw ) {
			try {
				var parsed = JSON.parse( raw );
				if ( Array.isArray( parsed ) && parsed.length ) {
					return parsed.map( String );
				}
			} catch ( error ) {
				// Fall through to DOM lookup.
			}
		}

		var slugs = [];
		$shell.find( '.hvn-realty-home-map-filters input[name="hvnly_prop_depts[]"]:checked' ).each( function () {
			slugs.push( String( $( this ).val() ) );
		} );

		if ( ! slugs.length ) {
			var single = $shell.find( '.hvn-realty-home-map-filters input[name="department"]' ).val();
			if ( single ) {
				slugs.push( String( single ) );
			}
		}

		return slugs;
	}

	/**
	 * Merge Customizer department filters into map AJAX requests.
	 *
	 * The plugin map loader prefers havenlyticsAJAX.getFormData(), which only
	 * reads the property-search form and #hvnly-filter-sidebar — not the theme
	 * hero map filter inputs.
	 */
	function ensureHomeMapDepartmentFilters() {
		var slugs = getHomeMapDepartmentSlugs();
		if ( ! slugs.length ) {
			return;
		}

		if ( window.havenlyticsAJAX && typeof window.havenlyticsAJAX.getFormData === 'function' ) {
			if ( ! window.havenlyticsAJAX._hvnRealtyHomeMapFormDataPatched ) {
				var originalGetFormData = window.havenlyticsAJAX.getFormData.bind( window.havenlyticsAJAX );

				window.havenlyticsAJAX.getFormData = function () {
					var data = originalGetFormData();

					if ( ! $( '#hvn-realty-home-map-shell' ).length ) {
						return data;
					}

					var homeSlugs = window.havenlyticsAJAX._hvnRealtyHomeMapDepartments || getHomeMapDepartmentSlugs();
					if ( ! homeSlugs.length ) {
						return data;
					}

					data.hvnly_prop_depts = homeSlugs.slice();
					if ( homeSlugs.length === 1 ) {
						data.department = homeSlugs[ 0 ];
					} else {
						delete data.department;
					}

					return data;
				};

				window.havenlyticsAJAX._hvnRealtyHomeMapFormDataPatched = true;
			}

			window.havenlyticsAJAX._hvnRealtyHomeMapDepartments = slugs;
		}
	}

	function getMapInstance() {
		return window.HavenlyticsPropertyMap || window.HvnlyPropertyMap || null;
	}

	function invalidateHomeMapSize() {
		var mapInstance = getMapInstance();
		if ( ! mapInstance || ! mapInstance.map ) {
			return;
		}

		if ( typeof mapInstance.map.invalidateSize === 'function' ) {
			mapInstance.map.invalidateSize( true );
		} else if ( typeof mapInstance.updateMapBounds === 'function' ) {
			window.setTimeout( function () {
				mapInstance.updateMapBounds();
			}, 100 );
		}
	}

	function activateHomeMap() {
		var $root = $( '.hvn-realty-home-map-embed' );
		if ( ! $root.length ) {
			return true;
		}

		if ( $root.data( 'hvn-realty-map-ready' ) ) {
			return true;
		}

		var mapInstance = getMapInstance();
		if ( ! mapInstance || typeof mapInstance.loadPropertyMap !== 'function' ) {
			return false;
		}

		var slugs = getHomeMapDepartmentSlugs();
		if ( slugs.length ) {
			ensureHomeMapDepartmentFilters();

			if ( ! window.havenlyticsAJAX || typeof window.havenlyticsAJAX.getFormData !== 'function' ) {
				return false;
			}
		}

		$root.data( 'hvn-realty-map-ready', true );
		mapInstance.loadPropertyMap();

		return true;
	}

	function waitForHomeMap( attempt ) {
		attempt = attempt || 0;

		if ( activateHomeMap() ) {
			return;
		}

		if ( attempt >= 24 ) {
			return;
		}

		window.setTimeout( function () {
			waitForHomeMap( attempt + 1 );
		}, 150 );
	}

	function visibleTestimonialsSlides() {
		return slidesForBreakpoint( 3, 2, 1 );
	}

	function initHomepage() {
		initFeaturedCarousels();
		initCardCarousels();
		initBlogCarousels();
		initTestimonialsCarousel();
		initDepartmentTabs();
	}

	function initBlogCarousels() {
		$( '.hvn-realty-section--blog' ).each( function () {
			var $section = $( this );
			if ( $section.data( 'hvn-realty-blog-ready' ) ) {
				return;
			}

			var $track = $section.find( '[data-blog-carousel-track]' ).first();
			var $slides = $track.children( '.hvn-realty-blog-carousel__slide' );

			if ( ! $slides.length ) {
				return;
			}

			$section.data( 'hvn-realty-blog-ready', true );

			new TrackCarousel( {
				$track: $track,
				$slides: $slides,
				$prev: $section.find( '[data-blog-carousel-prev]' ),
				$next: $section.find( '[data-blog-carousel-next]' ),
				getVisibleSlides: visibleBlogSlides,
				enableAutoPlay: !! carouselConfig.blogAutoplay,
				autoPlayDelay: parseInt( carouselConfig.autoplaySpeed, 10 ) || 5000,
			} );
		} );
	}

	function initFeaturedCarousels() {
		$( '.hvn-realty-section--featured' ).each( function () {
			var $section = $( this );
			if ( $section.data( 'hvn-realty-featured-ready' ) ) {
				return;
			}

			var $carousel = $section.find( '[data-hvn-realty-similar-carousel]' ).first();
			var $track = $carousel.find( '[data-carousel-track]' ).first();
			var $slides = $track.children( '.hvnly-property-single__carousel-slide' );

			if ( ! $slides.length ) {
				return;
			}

			$section.data( 'hvn-realty-featured-ready', true );

			new TrackCarousel( {
				$track: $track,
				$slides: $slides,
				$prev: $section.find( '[data-carousel-prev]' ),
				$next: $section.find( '[data-carousel-next]' ),
				$dots: $carousel.find( '[data-carousel-dots]' ),
				getVisibleSlides: visibleFeaturedSlides,
				enableAutoPlay: !! carouselConfig.autoplay,
				autoPlayDelay: parseInt( carouselConfig.autoplaySpeed, 10 ) || 5000,
			} );
		} );
	}

	function initCardCarousels() {
		$( '[data-hvn-realty-card-carousel]' ).each( function () {
			var $root = $( this );
			if ( $root.data( 'hvn-realty-card-ready' ) ) {
				return;
			}

			var type = $root.data( 'carousel-type' );
			var selector = type === 'agencies' ? '.hvnly-agency-card' : '.hvnly-agent-card';
			var $source = $root.find( '.hvn-realty-card-carousel__source' ).first();
			var $cards = $source.find( selector );
			var $viewport = $root.find( '.hvn-realty-card-carousel__viewport' ).first();

			if ( ! $cards.length ) {
				return;
			}

			var $track = $( '<ul class="hvn-realty-card-carousel__track" role="list" />' );
			$cards.each( function () {
				$track.append(
					$( '<li class="hvn-realty-card-carousel__slide" role="listitem" />' ).append( $( this ) )
				);
			} );

			$viewport.append( $track );
			$root.addClass( 'is-ready' );
			$root.data( 'hvn-realty-card-ready', true );

			new TrackCarousel( {
				$track: $track,
				$slides: $track.children( '.hvn-realty-card-carousel__slide' ),
				$prev: $root.find( '[data-card-carousel-prev]' ),
				$next: $root.find( '[data-card-carousel-next]' ),
				getVisibleSlides: visibleCardSlides,
				enableAutoPlay: !! carouselConfig.cardAutoplay,
				autoPlayDelay: parseInt( carouselConfig.autoplaySpeed, 10 ) || 5000,
			} );
		} );
	}

	function initTestimonialsCarousel() {
		$( '[data-hvn-realty-testimonials-carousel]' ).each( function () {
			var $root = $( this );
			if ( $root.data( 'hvn-realty-testimonials-ready' ) ) {
				return;
			}

			var $track = $root.find( '.hvn-realty-testimonials__track' ).first();
			var $slides = $track.children( '.hvn-realty-testimonials__slide' );

			if ( ! $slides.length ) {
				return;
			}

			$root.data( 'hvn-realty-testimonials-ready', true );

			var autoplayAttr = $root.attr( 'data-autoplay' );
			var enableAutoPlay = autoplayAttr === '1' || autoplayAttr === 'true';
			var speed = parseInt( $root.attr( 'data-speed' ), 10 ) || 5000;

			new TrackCarousel( {
				$track: $track,
				$slides: $slides,
				$prev: $root.find( '[data-testimonials-prev]' ),
				$next: $root.find( '[data-testimonials-next]' ),
				$dots: $root.find( '[data-testimonials-dots]' ),
				getVisibleSlides: visibleTestimonialsSlides,
				enableAutoPlay: enableAutoPlay,
				autoPlayDelay: speed,
			} );
		} );
	}

	function initDepartmentTabs() {
		$( '[data-hvn-realty-dept-tabs]' ).each( function () {
			var $tabs = $( this );
			if ( $tabs.data( 'hvn-realty-dept-ready' ) ) {
				return;
			}
			$tabs.data( 'hvn-realty-dept-ready', true );

			$tabs.on( 'click', '.hvn-realty-dept-tabs__btn', function () {
				var $btn = $( this );
				var target = $btn.data( 'dept-tab' );

				$btn.addClass( 'is-active' ).attr( 'aria-selected', 'true' )
					.siblings( '.hvn-realty-dept-tabs__btn' )
					.removeClass( 'is-active' ).attr( 'aria-selected', 'false' );

				$tabs.find( '.hvn-realty-dept-tabs__panel' ).each( function () {
					var $panel = $( this );
					var isActive = $panel.data( 'dept-panel' ) === target;
					$panel.toggleClass( 'is-active', isActive );
					if ( isActive ) {
						$panel.removeAttr( 'hidden' );
					} else {
						$panel.attr( 'hidden', 'hidden' );
					}
				} );
			} );
		} );
	}

	$( document ).ready( function () {
		initHomepage();
		waitForHomeMap( 0 );
	} );

	$( window ).one( 'load', function () {
		invalidateHomeMapSize();
	} );

	window.hvnRealtyInvalidateHomeMap = invalidateHomeMapSize;
}( jQuery ) );
