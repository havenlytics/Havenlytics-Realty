<?php
/**
 * Homepage Testimonials section helpers.
 *
 * Phase 1: Theme-owned Customizer repeater.
 * Phase 2: Havenlytics plugin reviews via hvnly_get_reviews() when available.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical testimonial item shape used by templates and carousel JS.
 *
 * @return array<string, mixed>
 */
function hvn_realty_get_testimonial_item_schema() {
	return array(
		'name'       => '',
		'location'   => '',
		'text'       => '',
		'rating'     => 5,
		'avatar_id'  => 0,
		'avatar_url' => '',
		'source'     => 'theme',
		'source_id'  => '',
	);
}

/**
 * Default theme-owned testimonials for new installs.
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_default_home_testimonials() {
	return array(
		array(
			'name'       => __( 'Sarah Mitchell', 'havenlytics-realty' ),
			'position'   => __( 'Home Buyer, Austin TX', 'havenlytics-realty' ),
			'text'       => __( 'The team made finding our dream home effortless. The search tools and map view saved us weeks of browsing.', 'havenlytics-realty' ),
			'rating'     => 5,
			'avatar_id'  => 0,
		),
		array(
			'name'       => __( 'James Carter', 'havenlytics-realty' ),
			'position'   => __( 'Property Investor, Denver CO', 'havenlytics-realty' ),
			'text'       => __( 'Clear listings, accurate details, and responsive agents. Exactly what I needed to close my latest investment.', 'havenlytics-realty' ),
			'rating'     => 5,
			'avatar_id'  => 0,
		),
		array(
			'name'       => __( 'Elena Rodriguez', 'havenlytics-realty' ),
			'position'   => __( 'First-time Renter, Miami FL', 'havenlytics-realty' ),
			'text'       => __( 'I loved how easy it was to filter by location and property type. The whole experience felt modern and trustworthy.', 'havenlytics-realty' ),
			'rating'     => 5,
			'avatar_id'  => 0,
		),
	);
}

/**
 * Whether the Havenlytics plugin review API is available.
 *
 * @return bool
 */
function hvn_realty_plugin_reviews_are_available() {
	return function_exists( 'hvnly_get_reviews' );
}

/**
 * Whether homepage testimonials should prefer plugin reviews.
 *
 * @return bool
 */
function hvn_realty_should_use_plugin_testimonials() {
	if ( ! hvn_realty_plugin_reviews_are_available() ) {
		return false;
	}

	return (bool) apply_filters( 'hvn_realty_use_plugin_testimonials', true );
}

/**
 * Normalize one testimonial/review payload into the canonical card shape.
 *
 * @param mixed  $item   Raw item.
 * @param string $source Data source: theme|plugin.
 * @return array<string, mixed>|null
 */
function hvn_realty_normalize_testimonial_item( $item, $source = 'theme' ) {
	if ( ! is_array( $item ) ) {
		return null;
	}

	$text = $item['text'] ?? $item['content'] ?? $item['review'] ?? $item['comment'] ?? '';
	$name = $item['name'] ?? $item['author'] ?? $item['author_name'] ?? '';

	if ( ! is_string( $text ) || '' === trim( $text ) || ! is_string( $name ) || '' === trim( $name ) ) {
		return null;
	}

	$location = $item['location'] ?? $item['position'] ?? $item['role'] ?? $item['city'] ?? '';
	$rating   = isset( $item['rating'] ) ? absint( $item['rating'] ) : 5;
	$rating   = max( 1, min( 5, $rating ) );

	$avatar_id  = 0;
	$avatar_url = '';

	if ( ! empty( $item['avatar_id'] ) ) {
		$avatar_id = absint( $item['avatar_id'] );
	} elseif ( ! empty( $item['avatar'] ) && is_numeric( $item['avatar'] ) ) {
		$avatar_id = absint( $item['avatar'] );
	}

	foreach ( array( 'avatar_url', 'image_url', 'photo', 'avatar' ) as $url_key ) {
		if ( ! empty( $item[ $url_key ] ) && is_string( $item[ $url_key ] ) && filter_var( $item[ $url_key ], FILTER_VALIDATE_URL ) ) {
			$avatar_url = esc_url_raw( $item[ $url_key ] );
			break;
		}
	}

	if ( $avatar_id > 0 && '' === $avatar_url ) {
		$resolved = wp_get_attachment_image_url( $avatar_id, 'thumbnail' );
		if ( is_string( $resolved ) ) {
			$avatar_url = $resolved;
		}
	}

	$source_id = '';
	if ( isset( $item['id'] ) ) {
		$source_id = sanitize_key( (string) $item['id'] );
	} elseif ( isset( $item['source_id'] ) ) {
		$source_id = sanitize_key( (string) $item['source_id'] );
	}

	$normalized = array(
		'name'       => sanitize_text_field( $name ),
		'location'   => sanitize_text_field( (string) $location ),
		'text'       => sanitize_textarea_field( $text ),
		'rating'     => $rating,
		'avatar_id'  => $avatar_id,
		'avatar_url' => $avatar_url,
		'source'     => 'plugin' === $source ? 'plugin' : 'theme',
		'source_id'  => $source_id,
	);

	/**
	 * Filter a normalized homepage testimonial item.
	 *
	 * @param array<string, mixed> $normalized Canonical item.
	 * @param array<string, mixed> $item       Raw source item.
	 * @param string               $source     theme|plugin.
	 */
	return apply_filters( 'hvn_realty_normalize_testimonial_item', $normalized, $item, $source );
}

/**
 * Normalize a collection of testimonials/reviews.
 *
 * @param mixed  $items  Raw collection.
 * @param string $source Data source: theme|plugin.
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_normalize_testimonial_collection( $items, $source = 'theme' ) {
	if ( ! is_array( $items ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $items as $item ) {
		$card = hvn_realty_normalize_testimonial_item( $item, $source );
		if ( null !== $card ) {
			$normalized[] = $card;
		}
	}

	return array_values( $normalized );
}

/**
 * Back-compat alias for plugin review normalization.
 *
 * @param mixed $reviews Plugin reviews.
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_normalize_plugin_reviews( $reviews ) {
	return hvn_realty_normalize_testimonial_collection( $reviews, 'plugin' );
}

/**
 * Sanitize testimonials JSON for theme_mod storage.
 *
 * @param mixed $input Raw value.
 * @return string JSON string.
 */
function hvn_realty_sanitize_home_testimonials( $input ) {
	$items = array();

	if ( is_string( $input ) ) {
		$decoded = json_decode( wp_unslash( $input ), true );
		if ( is_array( $decoded ) ) {
			$items = $decoded;
		}
	} elseif ( is_array( $input ) ) {
		$items = $input;
	}

	$sanitized = array();

	foreach ( $items as $index => $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		// Customizer repeater still stores "position"; normalize to location in storage.
		if ( ! isset( $item['location'] ) && isset( $item['position'] ) ) {
			$item['location'] = $item['position'];
		}

		$item['source_id'] = 'theme-' . (int) $index;
		$card              = hvn_realty_normalize_testimonial_item( $item, 'theme' );

		if ( null === $card ) {
			continue;
		}

		$sanitized[] = array(
			'name'       => $card['name'],
			'position'   => $card['location'],
			'text'       => $card['text'],
			'rating'     => $card['rating'],
			'avatar_id'  => $card['avatar_id'],
		);
	}

	return wp_json_encode( array_values( $sanitized ) );
}

/**
 * Plugin-owned testimonials for the homepage.
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_plugin_home_testimonials() {
	if ( ! hvn_realty_should_use_plugin_testimonials() ) {
		return array();
	}

	$reviews = hvnly_get_reviews();

	/**
	 * Filter raw plugin review payloads before normalization.
	 *
	 * @param mixed $reviews Raw plugin reviews.
	 */
	$reviews = apply_filters( 'hvn_realty_plugin_reviews_raw', $reviews );

	return hvn_realty_normalize_testimonial_collection( $reviews, 'plugin' );
}

/**
 * Theme-owned testimonials from Customizer JSON.
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_theme_home_testimonials() {
	$raw = get_theme_mod( 'hvn_realty_home_testimonials', '' );

	if ( is_string( $raw ) && '' !== $raw ) {
		$decoded = json_decode( $raw, true );
		if ( is_array( $decoded ) && ! empty( $decoded ) ) {
			$items = array();

			foreach ( $decoded as $index => $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}

				if ( ! isset( $item['location'] ) && isset( $item['position'] ) ) {
					$item['location'] = $item['position'];
				}

				$item['source_id'] = 'theme-' . (int) $index;
				$items[]           = $item;
			}

			$normalized = hvn_realty_normalize_testimonial_collection( $items, 'theme' );
			if ( ! empty( $normalized ) ) {
				return $normalized;
			}
		}
	}

	return hvn_realty_normalize_testimonial_collection( hvn_realty_get_default_home_testimonials(), 'theme' );
}

/**
 * Resolve homepage testimonials and active data source.
 *
 * @return array{source: string, items: array<int, array<string, mixed>>}
 */
function hvn_realty_resolve_home_testimonials() {
	static $resolved = null;

	if ( null !== $resolved ) {
		return $resolved;
	}

	$plugin_items = hvn_realty_get_plugin_home_testimonials();

	if ( ! empty( $plugin_items ) ) {
		$resolved = array(
			'source' => 'plugin',
			'items'  => $plugin_items,
		);
	} else {
		$resolved = array(
			'source' => 'theme',
			'items'  => hvn_realty_get_theme_home_testimonials(),
		);
	}

	if ( empty( $resolved['items'] ) ) {
		$resolved['source'] = 'none';
	}

	/**
	 * Filter resolved homepage testimonials and source metadata.
	 *
	 * @param array{source: string, items: array<int, array<string, mixed>>} $resolved Resolved payload.
	 */
	$resolved = apply_filters( 'hvn_realty_resolve_home_testimonials', $resolved );

	$resolved['items'] = hvn_realty_normalize_testimonial_collection( $resolved['items'], $resolved['source'] === 'plugin' ? 'plugin' : 'theme' );

	if ( empty( $resolved['items'] ) ) {
		$resolved['source'] = 'none';
	}

	return $resolved;
}

/**
 * Active homepage testimonial data source.
 *
 * @return string plugin|theme|none
 */
function hvn_realty_get_home_testimonials_source() {
	$resolved = hvn_realty_resolve_home_testimonials();

	return (string) $resolved['source'];
}

/**
 * Testimonials for the homepage section (plugin-first when available).
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_home_testimonials() {
	$resolved = hvn_realty_resolve_home_testimonials();

	/**
	 * Filter homepage testimonials returned to templates.
	 *
	 * @param array<int, array<string, mixed>> $items  Canonical testimonial cards.
	 * @param string                             $source plugin|theme|none.
	 */
	return apply_filters( 'hvn_realty_home_testimonials', $resolved['items'], $resolved['source'] );
}

/**
 * Resolve avatar URL for a testimonial card.
 *
 * @param array<string, mixed> $item Canonical testimonial item.
 * @return string
 */
function hvn_realty_get_testimonial_avatar_url( $item ) {
	if ( ! is_array( $item ) ) {
		return '';
	}

	if ( ! empty( $item['avatar_url'] ) && is_string( $item['avatar_url'] ) ) {
		return esc_url( $item['avatar_url'] );
	}

	$avatar_id = ! empty( $item['avatar_id'] ) ? absint( $item['avatar_id'] ) : 0;
	if ( $avatar_id <= 0 ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $avatar_id, 'thumbnail' );

	return is_string( $url ) ? esc_url( $url ) : '';
}

/**
 * First-letter avatar fallback for a testimonial name.
 *
 * @param string $name Person name.
 * @return string
 */
function hvn_realty_get_testimonial_avatar_initial( $name ) {
	$name = trim( (string) $name );

	if ( '' === $name ) {
		return '';
	}

	$initial = function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 );

	return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $initial ) : strtoupper( $initial );
}

/**
 * Render star rating markup for a testimonial card.
 *
 * @param int  $rating Rating value.
 * @param bool $show   Whether stars are visible.
 * @return void
 */
function hvn_realty_render_home_testimonial_stars( $rating, $show = true ) {
	if ( ! $show ) {
		return;
	}

	$rating = max( 1, min( 5, absint( $rating ) ) );
	?>
	<div class="hvn-realty-testimonials__stars" aria-label="<?php echo esc_attr( sprintf( __( '%d out of 5 stars', 'havenlytics-realty' ), $rating ) ); ?>">
		<?php for ( $star = 1; $star <= 5; $star++ ) : ?>
			<span class="hvn-realty-testimonials__star<?php echo $star <= $rating ? ' is-filled' : ''; ?>" aria-hidden="true">&#9733;</span>
		<?php endfor; ?>
	</div>
	<?php
}

/**
 * Whether testimonial cards show star ratings.
 *
 * @return bool
 */
function hvn_realty_show_home_testimonial_stars() {
	return (bool) get_theme_mod( 'hvn_realty_home_show_testimonial_stars', true );
}

/**
 * Whether the testimonials carousel autoplays.
 *
 * @return bool
 */
function hvn_realty_home_testimonials_autoplay() {
	return (bool) get_theme_mod( 'hvn_realty_home_testimonials_autoplay', true );
}

/**
 * Testimonials carousel autoplay interval in milliseconds.
 *
 * @return int
 */
function hvn_realty_get_home_testimonials_speed() {
	$speed = absint( get_theme_mod( 'hvn_realty_home_testimonials_speed', 5000 ) );

	return max( 2000, min( 15000, $speed ) );
}

/**
 * Whether the Customizer repeater is the active testimonials source.
 *
 * @return bool
 */
function hvn_realty_home_testimonials_use_theme_repeater() {
	return 'theme' === hvn_realty_get_home_testimonials_source();
}
