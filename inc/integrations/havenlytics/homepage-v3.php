<?php
/**
 * Homepage 3.0 helpers.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the display name for a property's hvnly_prop_status term.
 *
 * Prefers the plugin helper when available; falls back to taxonomy terms.
 *
 * @param int $post_id Property post ID.
 * @return string Status name or empty string.
 */
function hvn_realty_get_property_status_name( $post_id ) {
	$post_id = absint( $post_id );
	if ( $post_id <= 0 ) {
		return '';
	}

	if ( function_exists( 'hvnly_get_property_status' ) ) {
		$term = hvnly_get_property_status( $post_id );
		if ( $term && ! is_wp_error( $term ) && ! empty( $term->name ) ) {
			return (string) $term->name;
		}
	}

	if ( ! taxonomy_exists( 'hvnly_prop_status' ) ) {
		return '';
	}

	$terms = get_the_terms( $post_id, 'hvnly_prop_status' );
	if ( ! $terms || is_wp_error( $terms ) || empty( $terms[0]->name ) ) {
		return '';
	}

	return (string) $terms[0]->name;
}

/**
 * Resolve map coordinates for a property post.
 *
 * @param int $post_id Property post ID.
 * @return array{lat: float, lng: float}|null
 */
function hvn_realty_get_property_map_coordinates( $post_id ) {
	$post_id = absint( $post_id );
	if ( $post_id <= 0 ) {
		return null;
	}

	$lat = null;
	$lng = null;

	// Active-key pointers store the meta key name that currently holds coords.
	$active_lat_key = (string) get_post_meta( $post_id, '_hvnly_active_map_latitude_key', true );
	$active_lng_key = (string) get_post_meta( $post_id, '_hvnly_active_map_longitude_key', true );
	if ( '' !== $active_lat_key && '' !== $active_lng_key ) {
		$active_lat = get_post_meta( $post_id, $active_lat_key, true );
		$active_lng = get_post_meta( $post_id, $active_lng_key, true );
		if ( is_numeric( $active_lat ) && is_numeric( $active_lng ) ) {
			$lat = (float) $active_lat;
			$lng = (float) $active_lng;
		}
	}

	if ( null === $lat || null === $lng ) {
		$legacy_lat = get_post_meta( $post_id, '_hvnly_property_location_Latitude', true );
		$legacy_lng = get_post_meta( $post_id, '_hvnly_property_location_Longitude', true );
		if ( is_numeric( $legacy_lat ) && is_numeric( $legacy_lng ) ) {
			$lat = (float) $legacy_lat;
			$lng = (float) $legacy_lng;
		}
	}

	if ( null === $lat || null === $lng ) {
		$plain_lat = get_post_meta( $post_id, '_hvnly_property_latitude', true );
		$plain_lng = get_post_meta( $post_id, '_hvnly_property_longitude', true );
		if ( is_numeric( $plain_lat ) && is_numeric( $plain_lng ) ) {
			$lat = (float) $plain_lat;
			$lng = (float) $plain_lng;
		}
	}

	if ( null === $lat || null === $lng ) {
		return null;
	}

	if ( $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || ( 0.0 === $lat && 0.0 === $lng ) ) {
		return null;
	}

	return array(
		'lat' => $lat,
		'lng' => $lng,
	);
}

/**
 * Build marker payload for a single property.
 *
 * @param int $post_id Property post ID.
 * @return array<string, mixed>|null
 */
function hvn_realty_build_home_map_marker( $post_id ) {
	$post_id = absint( $post_id );
	if ( $post_id <= 0 ) {
		return null;
	}

	$coords = hvn_realty_get_property_map_coordinates( $post_id );
	if ( null === $coords ) {
		return null;
	}

	$price_raw = get_post_meta( $post_id, '_hvnly_property_price', true );
	$price     = '';
	if ( '' !== $price_raw && null !== $price_raw ) {
		$price = function_exists( 'hvnly_format_price' )
			? wp_strip_all_tags( hvnly_format_price( $price_raw ) )
			: number_format_i18n( (float) $price_raw );
	}

	$beds  = get_post_meta( $post_id, '_hvnly_property_bedrooms', true );
	$baths = get_post_meta( $post_id, '_hvnly_property_bathrooms', true );
	$area  = get_post_meta( $post_id, '_hvnly_property_sqft', true );

	$address = (string) get_post_meta( $post_id, '_hvnly_property_address', true );
	if ( '' === $address ) {
		$loc_terms = get_the_terms( $post_id, 'hvnly_prop_locations' );
		if ( $loc_terms && ! is_wp_error( $loc_terms ) && ! empty( $loc_terms[0]->name ) ) {
			$address = $loc_terms[0]->name;
		}
	}

	$status_name  = hvn_realty_get_property_status_name( $post_id );
	$status_group = 'available';
	if ( false !== stripos( $status_name, 'open' ) ) {
		$status_group = 'open';
	}

	$thumb = get_the_post_thumbnail_url( $post_id, 'medium_large' );

	return array(
		'id'      => $post_id,
		'lat'     => $coords['lat'],
		'lng'     => $coords['lng'],
		'title'   => get_the_title( $post_id ),
		'price'   => $price,
		'url'     => get_permalink( $post_id ),
		'image'   => is_string( $thumb ) ? $thumb : '',
		'address' => $address,
		'beds'    => '' !== $beds ? (string) $beds : '',
		'baths'   => '' !== $baths ? (string) $baths : '',
		'area'    => '' !== $area ? (string) $area : '',
		'status'  => $status_group,
	);
}

/**
 * Query properties with map coordinates for the homepage map.
 *
 * @param int $limit Max markers.
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_home_map_properties( $limit = 40 ) {
	$limit = max( 1, min( 100, absint( $limit ) ) );

	if ( ! post_type_exists( 'hvnly_property' ) ) {
		return array();
	}

	$query_args = array(
		'post_type'              => 'hvnly_property',
		'post_status'            => 'publish',
		'posts_per_page'         => $limit * 3,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
		'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			array(
				'key'     => '_hvnly_active_map_latitude_key',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => '_hvnly_property_location_Latitude',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => '_hvnly_property_latitude',
				'compare' => 'EXISTS',
			),
		),
	);

	$query   = new WP_Query( $query_args );
	$markers = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$marker = hvn_realty_build_home_map_marker( get_the_ID() );
			if ( null !== $marker ) {
				$markers[] = $marker;
			}
			if ( count( $markers ) >= $limit ) {
				break;
			}
		}
		wp_reset_postdata();
	}

	return $markers;
}

/**
 * Sanitized map markers for wp_localize_script.
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_home_map_markers_payload() {
	$limit = absint( get_theme_mod( 'hvn_realty_home_map_limit', 40 ) );
	if ( $limit <= 0 ) {
		$limit = 40;
	}

	$markers = hvn_realty_get_home_map_properties( $limit );
	$payload = array();

	foreach ( $markers as $marker ) {
		if ( ! is_array( $marker ) ) {
			continue;
		}

		$payload[] = array(
			'id'      => absint( $marker['id'] ?? 0 ),
			'lat'     => isset( $marker['lat'] ) ? (float) $marker['lat'] : 0.0,
			'lng'     => isset( $marker['lng'] ) ? (float) $marker['lng'] : 0.0,
			'title'   => sanitize_text_field( (string) ( $marker['title'] ?? '' ) ),
			'price'   => sanitize_text_field( (string) ( $marker['price'] ?? '' ) ),
			'url'     => esc_url_raw( (string) ( $marker['url'] ?? '' ) ),
			'image'   => esc_url_raw( (string) ( $marker['image'] ?? '' ) ),
			'address' => sanitize_text_field( (string) ( $marker['address'] ?? '' ) ),
			'beds'    => sanitize_text_field( (string) ( $marker['beds'] ?? '' ) ),
			'baths'   => sanitize_text_field( (string) ( $marker['baths'] ?? '' ) ),
			'area'    => sanitize_text_field( (string) ( $marker['area'] ?? '' ) ),
			'status'  => in_array( (string) ( $marker['status'] ?? '' ), array( 'available', 'open' ), true )
				? (string) $marker['status']
				: 'available',
		);
	}

	/**
	 * Filter homepage map marker payload passed to JavaScript.
	 *
	 * @param array<int, array<string, mixed>> $payload Marker list.
	 */
	return apply_filters( 'hvn_realty_home_map_markers_payload', $payload );
}

/**
 * Collect unique hero carousel slides from newest published properties.
 *
 * Prefers property gallery images when available, otherwise featured image.
 * Falls back to the static hero image, then any property thumbnail.
 *
 * @param int $limit Max slides (clamped 3–8).
 * @return array<int, array{url: string, alt: string, id: int}>
 */
function hvn_realty_get_home_hero_carousel_slides( $limit ) {
	$limit = max( 3, min( 8, absint( $limit ) ) );
	$slides    = array();
	$seen_ids  = array();
	$seen_urls = array();

	/**
	 * Append a unique attachment slide when capacity remains.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return bool True when more slides may still be added.
	 */
	$add_slide = static function ( $attachment_id ) use ( &$slides, &$seen_ids, &$seen_urls, $limit ) {
		if ( count( $slides ) >= $limit ) {
			return false;
		}

		$attachment_id = absint( $attachment_id );
		if ( $attachment_id <= 0 || isset( $seen_ids[ $attachment_id ] ) ) {
			return true;
		}

		$url = wp_get_attachment_image_url( $attachment_id, 'large' );
		if ( ! is_string( $url ) || '' === $url || isset( $seen_urls[ $url ] ) ) {
			return true;
		}

		$alt = (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		if ( '' === $alt ) {
			$alt = (string) get_the_title( $attachment_id );
		}

		$seen_ids[ $attachment_id ] = true;
		$seen_urls[ $url ]          = true;
		$slides[]                   = array(
			'url' => $url,
			'alt' => sanitize_text_field( $alt ),
			'id'  => $attachment_id,
		);

		return count( $slides ) < $limit;
	};

	/**
	 * Resolve gallery attachment IDs for a property post.
	 *
	 * @param int $post_id Property ID.
	 * @return int[]
	 */
	$get_gallery_ids = static function ( $post_id ) {
		$post_id = absint( $post_id );
		$ids     = array();

		if ( function_exists( 'hvnly_get_property_gallery_ids' ) ) {
			$plugin_ids = hvnly_get_property_gallery_ids( $post_id );
			if ( is_array( $plugin_ids ) ) {
				$ids = array_map( 'absint', $plugin_ids );
			}
		}

		if ( empty( $ids ) ) {
			foreach ( array( '_hvnly_property_gallery', '_hvnly_property_gallery_images' ) as $meta_key ) {
				$raw = get_post_meta( $post_id, $meta_key, true );
				if ( empty( $raw ) ) {
					continue;
				}
				if ( is_array( $raw ) ) {
					$ids = array_map( 'absint', $raw );
				} else {
					$ids = array_map( 'absint', preg_split( '/\s*,\s*/', (string) $raw ) );
				}
				$ids = array_filter( $ids );
				if ( ! empty( $ids ) ) {
					break;
				}
			}
		}

		if ( empty( $ids ) ) {
			$children = get_children(
				array(
					'post_parent'    => $post_id,
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'numberposts'    => 8,
					'orderby'        => 'menu_order ID',
					'order'          => 'ASC',
					'fields'         => 'ids',
				)
			);
			if ( ! empty( $children ) && is_array( $children ) ) {
				$ids = array_map( 'absint', $children );
			}
		}

		return array_values( array_filter( array_unique( $ids ) ) );
	};

	if ( post_type_exists( 'hvnly_property' ) ) {
		$query = new WP_Query(
			array(
				'post_type'              => 'hvnly_property',
				'post_status'            => 'publish',
				'posts_per_page'         => max( $limit * 3, 12 ),
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$property_id = get_the_ID();
				$gallery_ids = $get_gallery_ids( $property_id );
				$image_id    = ! empty( $gallery_ids[0] ) ? (int) $gallery_ids[0] : (int) get_post_thumbnail_id( $property_id );

				if ( $image_id > 0 && ! $add_slide( $image_id ) ) {
					break;
				}
			}
			wp_reset_postdata();
		}
	}

	if ( count( $slides ) < $limit ) {
		$hero_image_a = absint( get_theme_mod( 'hvn_realty_home_hero_image_a', 0 ) );
		if ( $hero_image_a > 0 ) {
			$add_slide( $hero_image_a );
		}
	}

	if ( count( $slides ) < $limit && post_type_exists( 'hvnly_property' ) ) {
		$fallback_props = get_posts(
			array(
				'post_type'           => 'hvnly_property',
				'posts_per_page'      => $limit * 2,
				'post_status'         => 'publish',
				'meta_key'            => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'fields'              => 'ids',
			)
		);
		foreach ( $fallback_props as $fallback_id ) {
			$thumb_id = get_post_thumbnail_id( $fallback_id );
			if ( $thumb_id && ! $add_slide( $thumb_id ) ) {
				break;
			}
		}
	}

	/**
	 * Filter homepage hero carousel slides.
	 *
	 * @param array<int, array{url: string, alt: string, id: int}> $slides Slides.
	 * @param int                                                    $limit  Requested limit.
	 */
	return apply_filters( 'hvn_realty_home_hero_carousel_slides', $slides, $limit );
}

/**
 * Render pin-style section eyebrow markup.
 *
 * @param string $text Eyebrow label.
 * @return void
 */
function hvn_realty_render_home_eyebrow( $text ) {
	$text = trim( (string) $text );
	if ( '' === $text ) {
		return;
	}
	?>
	<div class="hvn-realty-eyebrow">
		<svg class="hvn-realty-eyebrow-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
		<span class="hvn-realty-eyebrow-rule" aria-hidden="true"></span><span class="hvn-realty-eyebrow-text"><?php echo esc_html( $text ); ?></span>
	</div>
	<?php
}
