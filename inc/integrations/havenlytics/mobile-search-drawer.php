<?php
/**
 * Homepage mobile search drawer — floating dock + filter drawer (mobile only).
 *
 * Isolated from the desktop Hero Search. Reuses hero search field registry,
 * builder config, and query parameters without duplicating search logic.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the mobile search drawer uses Hero Search intersection visibility.
 *
 * True only on the realty homepage when the Hero Search section is present.
 *
 * @return bool
 */
function hvn_realty_mobile_search_drawer_uses_hero_visibility() {
	if ( ! function_exists( 'hvn_realty_is_home_design' ) || ! hvn_realty_is_home_design() ) {
		return false;
	}

	if ( ! function_exists( 'hvn_realty_home_section_visible' ) || ! hvn_realty_home_section_visible( 'search' ) ) {
		return false;
	}

	return true;
}

/**
 * Whether the current view is the Havenlytics Property Search Results page.
 *
 * Uses the plugin page ID when available, with shortcode fallback for custom slugs.
 *
 * @return bool
 */
function hvn_realty_is_property_search_results_page() {
	if ( is_admin() ) {
		return false;
	}

	$post_id = get_queried_object_id();
	if ( $post_id <= 0 ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_get_plugin_page_id' ) ) {
		$search_page_id = (int) hvn_realty_get_plugin_page_id( 'property_search' );
		if ( $search_page_id > 0 && $post_id === $search_page_id ) {
			return true;
		}
	}

	if ( is_page( $post_id ) ) {
		$post = get_post( $post_id );
		if ( $post && has_shortcode( $post->post_content, 'hvnly_property_search' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether the mobile search drawer should load on this request.
 *
 * @return bool
 */
function hvn_realty_should_render_mobile_search_drawer() {
	if ( is_admin() ) {
		return false;
	}

	/*
	 * 2.1.1 — Do not render the theme floating bottom dock on Single Property
	 * pages. Agent Call / WhatsApp / contact chrome is now provided by the
	 * Havenlytics plugin mobile contact dock (unified mobile experience).
	 * Homepage Mobile Search Drawer markup, assets, and behaviour are unchanged.
	 */
	if ( post_type_exists( 'hvnly_property' ) && is_singular( 'hvnly_property' ) ) {
		return false;
	}

	if ( ! function_exists( 'hvn_realty_home_search_is_available' ) || ! hvn_realty_home_search_is_available() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_mobile_search_drawer_is_enabled' ) && ! hvn_realty_mobile_search_drawer_is_enabled() && ! is_customize_preview() ) {
		return false;
	}

	if ( (bool) hvn_realty_get_mobile_search_drawer_mod( 'homepage_only' ) ) {
		if ( ! function_exists( 'hvn_realty_is_home_design' ) || ! hvn_realty_is_home_design() ) {
			return false;
		}
	}

	if ( hvn_realty_is_property_search_results_page() && ! (bool) hvn_realty_get_mobile_search_drawer_mod( 'show_on_search_results' ) ) {
		return false;
	}

	return true;
}

/**
 * Ensure MSD stylesheet selectors apply on non-homepage views.
 *
 * @param array $classes Body classes.
 * @return array
 */
function hvn_realty_mobile_search_drawer_body_classes( $classes ) {
	if ( ! hvn_realty_should_render_mobile_search_drawer() ) {
		return $classes;
	}

	if ( ! in_array( 'hvn-theme-home', $classes, true ) ) {
		$classes[] = 'hvn-theme-home';
	}

	return $classes;
}
add_filter( 'hvn_realty_body_classes', 'hvn_realty_mobile_search_drawer_body_classes', 30 );

/**
 * Shared render context for mobile drawer fields (same sources as hero search).
 *
 * @return array<string, mixed>
 */
function hvn_realty_get_mobile_search_drawer_context() {
	return array(
		'type_terms'     => function_exists( 'hvn_realty_get_home_property_type_terms' )
			? hvn_realty_get_home_property_type_terms( 12 )
			: array(),
		'location_terms' => function_exists( 'hvn_realty_home_search_get_terms' )
			? hvn_realty_home_search_get_terms( 'hvnly_prop_locations', 100 )
			: array(),
		'status_terms'   => function_exists( 'hvn_realty_home_search_get_terms' )
			? hvn_realty_home_search_get_terms( 'hvnly_prop_status', 20 )
			: array(),
		'feature_terms'  => function_exists( 'hvn_realty_home_search_get_terms' )
			? hvn_realty_home_search_get_terms( 'hvnly_prop_features', 50 )
			: array(),
		'badge_terms'    => function_exists( 'hvn_realty_home_search_get_terms' )
			? hvn_realty_home_search_get_terms( 'hvnly_prop_badges', 50 )
			: array(),
		'count_options'  => array(
			'bedrooms'        => function_exists( 'hvn_realty_home_search_get_count_options' ) ? hvn_realty_home_search_get_count_options( 'bedrooms' ) : array(),
			'bathrooms'       => function_exists( 'hvn_realty_home_search_get_count_options' ) ? hvn_realty_home_search_get_count_options( 'bathrooms' ) : array(),
			'reception_rooms' => function_exists( 'hvn_realty_home_search_get_count_options' ) ? hvn_realty_home_search_get_count_options( 'reception_rooms' ) : array(),
		),
	);
}

/**
 * Element ID for a mobile drawer field control.
 *
 * @param string $field_id Field slug.
 * @return string
 */
function hvn_realty_mobile_search_drawer_field_element_id( $field_id ) {
	return 'hvn-theme-home-msd-' . sanitize_key( (string) $field_id );
}

/**
 * Enabled builder fields for the mobile drawer (primary + advanced zones).
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_mobile_search_drawer_fields() {
	if ( ! function_exists( 'hvn_realty_get_home_search_fields_config' ) ) {
		return array();
	}

	$fields = array();
	foreach ( hvn_realty_get_home_search_fields_config() as $row ) {
		if ( empty( $row['enabled'] ) ) {
			continue;
		}
		$id = isset( $row['id'] ) ? sanitize_key( (string) $row['id'] ) : '';
		if ( '' === $id || ! function_exists( 'hvn_realty_home_search_field_is_supported' ) || ! hvn_realty_home_search_field_is_supported( $id ) ) {
			continue;
		}
		$fields[] = $row;
	}

	return $fields;
}

/**
 * Render one mobile drawer field using hero search registry metadata.
 *
 * @param array<string, mixed> $field   Builder row.
 * @param array<string, mixed> $context Shared context.
 * @return void
 */
function hvn_realty_render_mobile_search_drawer_field( array $field, array $context ) {
	if ( ! function_exists( 'hvn_realty_get_home_search_field_registry' ) ) {
		return;
	}

	$id = isset( $field['id'] ) ? sanitize_key( (string) $field['id'] ) : '';
	if ( '' === $id ) {
		return;
	}

	$registry = hvn_realty_get_home_search_field_registry();
	if ( ! isset( $registry[ $id ] ) ) {
		return;
	}

	$meta        = $registry[ $id ];
	$label       = function_exists( 'hvn_realty_home_search_field_label' ) ? hvn_realty_home_search_field_label( $field, $meta ) : (string) $meta['label'];
	$placeholder = function_exists( 'hvn_realty_home_search_field_placeholder' ) ? hvn_realty_home_search_field_placeholder( $field, $meta ) : '';
	$default     = isset( $field['default'] ) ? (string) $field['default'] : '';
	$required    = ! empty( $field['required'] );
	$element_id  = hvn_realty_mobile_search_drawer_field_element_id( $id );
	$param       = isset( $meta['param'] ) ? (string) $meta['param'] : '';
	$input_type  = isset( $meta['input'] ) ? (string) $meta['input'] : 'text';
	$req_attr    = $required ? ' required' : '';

	if ( 'select_count' === $input_type ) {
		$count_field = isset( $meta['count_field'] ) ? (string) $meta['count_field'] : $id;
		$options     = isset( $context['count_options'][ $count_field ] )
			? $context['count_options'][ $count_field ]
			: ( function_exists( 'hvn_realty_home_search_get_count_options' ) ? hvn_realty_home_search_get_count_options( $count_field ) : array() );

		$any_label = isset( $options[''] ) ? (string) $options[''] : __( 'Any', 'havenlytics-realty' );
		?>
		<div class="hvn-theme-home-msd-field">
			<span class="hvn-theme-home-msd-field__label" id="<?php echo esc_attr( $element_id ); ?>-label"><?php echo esc_html( $label ); ?></span>
			<div
				class="hvn-theme-home-msd-stepper"
				data-hvn-msd-stepper
				data-hvn-msd-stepper-for="<?php echo esc_attr( $element_id ); ?>"
				role="group"
				aria-labelledby="<?php echo esc_attr( $element_id ); ?>-label"
			>
				<button type="button" class="hvn-theme-home-msd-stepper-btn" data-hvn-msd-action="dec" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: field label. */ __( 'Decrease %s', 'havenlytics-realty' ), $label ) ); ?>">−</button>
				<span class="hvn-theme-home-msd-stepper-val" data-hvn-msd-value aria-live="polite"><?php echo esc_html( $any_label ); ?></span>
				<button type="button" class="hvn-theme-home-msd-stepper-btn" data-hvn-msd-action="inc" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: field label. */ __( 'Increase %s', 'havenlytics-realty' ), $label ) ); ?>">+</button>
			</div>
			<select id="<?php echo esc_attr( $element_id ); ?>" name="<?php echo esc_attr( $param ); ?>" class="hvn-theme-home-msd-visually-hidden" tabindex="-1" aria-hidden="true"<?php echo $required ? ' required' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php foreach ( $options as $value => $option_label ) : ?>
					<option value="<?php echo esc_attr( (string) $value ); ?>" <?php selected( $default, (string) $value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
		return;
	}

	if ( 'select_taxonomy' === $input_type ) {
		$taxonomy = isset( $meta['taxonomy'] ) ? (string) $meta['taxonomy'] : '';
		$key      = 'feature' === $id ? 'feature_terms' : ( 'badges' === $id ? 'badge_terms' : $taxonomy . '_terms' );
		$terms    = isset( $context[ $key ] ) ? $context[ $key ] : ( function_exists( 'hvn_realty_home_search_get_terms' ) ? hvn_realty_home_search_get_terms( $taxonomy, 50 ) : array() );
		if ( empty( $terms ) ) {
			return;
		}
		?>
		<div class="hvn-theme-home-msd-field">
			<span class="hvn-theme-home-msd-field__label" id="<?php echo esc_attr( $element_id ); ?>-label"><?php echo esc_html( $label ); ?></span>
			<input type="hidden" id="<?php echo esc_attr( $element_id ); ?>" name="<?php echo esc_attr( $param ); ?>" value="<?php echo esc_attr( $default ); ?>">
			<div class="hvn-theme-home-msd-chip-group" role="group" aria-labelledby="<?php echo esc_attr( $element_id ); ?>-label" data-hvn-msd-chip-target="<?php echo esc_attr( $element_id ); ?>">
				<?php foreach ( $terms as $term ) : ?>
					<?php if ( $term instanceof WP_Term ) : ?>
						<button
							type="button"
							class="hvn-theme-home-msd-chip<?php echo $default === $term->slug ? ' hvn-theme-home-msd-chip-active' : ''; ?>"
							data-hvn-msd-chip-value="<?php echo esc_attr( $term->slug ); ?>"
						><?php echo esc_html( $term->name ); ?></button>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return;
	}

	echo '<div class="hvn-theme-home-msd-field">';
	printf(
		'<label class="hvn-theme-home-msd-field__label" for="%1$s">%2$s</label>',
		esc_attr( $element_id ),
		esc_html( $label )
	);
	echo '<div class="hvn-theme-home-msd-input-wrap">';

	switch ( $input_type ) {
		case 'search':
			printf(
				'<input type="search" id="%1$s" name="%2$s" autocomplete="off" placeholder="%3$s" value="%4$s"%5$s>',
				esc_attr( $element_id ),
				esc_attr( $param ),
				esc_attr( $placeholder ),
				esc_attr( $default ),
				$req_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			break;

		case 'text':
			printf(
				'<input type="text" id="%1$s" name="%2$s" autocomplete="off" placeholder="%3$s" value="%4$s"%5$s>',
				esc_attr( $element_id ),
				esc_attr( $param ),
				esc_attr( $placeholder ),
				esc_attr( $default ),
				$req_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			break;

		case 'number':
			printf(
				'<input type="number" id="%1$s" name="%2$s" min="0" step="1" inputmode="numeric" placeholder="%3$s" value="%4$s"%5$s>',
				esc_attr( $element_id ),
				esc_attr( $param ),
				esc_attr( $placeholder ),
				esc_attr( $default ),
				$req_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			break;

		case 'select_type':
			$terms = isset( $context['type_terms'] ) ? $context['type_terms'] : array();
			$empty = isset( $meta['empty_label'] ) ? (string) $meta['empty_label'] : __( 'Any Type', 'havenlytics-realty' );
			echo '<select id="' . esc_attr( $element_id ) . '" name="' . esc_attr( $param ) . '"' . ( $required ? ' required' : '' ) . '>';
			printf( '<option value="">%s</option>', esc_html( $empty ) );
			foreach ( $terms as $term ) {
				if ( $term instanceof WP_Term ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $term->slug ),
						selected( $default, $term->slug, false ),
						esc_html( $term->name )
					);
				}
			}
			echo '</select>';
			break;

		case 'select_location':
			$terms = isset( $context['location_terms'] ) ? $context['location_terms'] : array();
			$empty = isset( $meta['empty_label'] ) ? (string) $meta['empty_label'] : __( 'Any location', 'havenlytics-realty' );
			if ( ! empty( $terms ) ) {
				echo '<select id="' . esc_attr( $element_id ) . '" name="' . esc_attr( $param ) . '"' . ( $required ? ' required' : '' ) . '>';
				printf( '<option value="">%s</option>', esc_html( $empty ) );
				foreach ( $terms as $term ) {
					if ( $term instanceof WP_Term ) {
						printf(
							'<option value="%1$s" %2$s>%3$s</option>',
							esc_attr( $term->slug ),
							selected( $default, $term->slug, false ),
							esc_html( $term->name )
						);
					}
				}
				echo '</select>';
			} else {
				printf(
					'<input type="text" id="%1$s" name="%2$s" placeholder="%3$s" value="%4$s" autocomplete="off"%5$s>',
					esc_attr( $element_id ),
					esc_attr( $param ),
					esc_attr( $placeholder ),
					esc_attr( $default ),
					$req_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
			break;

		case 'select_status':
			$terms = isset( $context['status_terms'] ) ? $context['status_terms'] : array();
			if ( empty( $terms ) ) {
				echo '</div></div>';
				return;
			}
			$empty = isset( $meta['empty_label'] ) ? (string) $meta['empty_label'] : __( 'Any Status', 'havenlytics-realty' );
			echo '<select id="' . esc_attr( $element_id ) . '" name="' . esc_attr( $param ) . '"' . ( $required ? ' required' : '' ) . '>';
			printf( '<option value="">%s</option>', esc_html( $empty ) );
			foreach ( $terms as $term ) {
				if ( $term instanceof WP_Term ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( (string) $term->term_id ),
						selected( $default, (string) $term->term_id, false ),
						esc_html( $term->name )
					);
				}
			}
			echo '</select>';
			break;
	}

	echo '</div></div>';
}

/**
 * Render paired fields in a two-column row when both are enabled.
 *
 * @param array<int, array<string, mixed>> $fields  All drawer fields.
 * @param array<string, mixed>           $context Shared context.
 * @param int                              $index   Current index.
 * @param string                           $left_id Left field slug.
 * @param string                           $right_id Right field slug.
 * @return int Next index to process.
 */
function hvn_realty_render_mobile_search_drawer_pair( array $fields, array $context, $index, $left_id, $right_id ) {
	$left  = isset( $fields[ $index ] ) && $left_id === $fields[ $index ]['id'] ? $fields[ $index ] : null;
	$right = isset( $fields[ $index + 1 ] ) && $right_id === $fields[ $index + 1 ]['id'] ? $fields[ $index + 1 ] : null;

	if ( $left && $right ) {
		echo '<div class="hvn-theme-home-msd-row2">';
		hvn_realty_render_mobile_search_drawer_field( $left, $context );
		hvn_realty_render_mobile_search_drawer_field( $right, $context );
		echo '</div>';
		return $index + 2;
	}

	if ( $left ) {
		hvn_realty_render_mobile_search_drawer_field( $left, $context );
	}

	return $index + 1;
}

/**
 * Output all enabled drawer fields in builder order.
 *
 * @return void
 */
function hvn_realty_render_mobile_search_drawer_fields() {
	$fields  = hvn_realty_get_mobile_search_drawer_fields();
	$context = hvn_realty_get_mobile_search_drawer_context();
	$total   = count( $fields );
	$pairs   = array(
		array( 'property_type', 'status' ),
		array( 'min_price', 'max_price' ),
	);

	for ( $i = 0; $i < $total; ) {
		$current_id = isset( $fields[ $i ]['id'] ) ? sanitize_key( (string) $fields[ $i ]['id'] ) : '';
		$paired     = false;

		foreach ( $pairs as $pair ) {
			if ( $current_id === $pair[0] && isset( $fields[ $i + 1 ]['id'] ) && $pair[1] === sanitize_key( (string) $fields[ $i + 1 ]['id'] ) ) {
				$i = hvn_realty_render_mobile_search_drawer_pair( $fields, $context, $i, $pair[0], $pair[1] );
				$paired = true;
				break;
			}
		}

		if ( ! $paired ) {
			hvn_realty_render_mobile_search_drawer_field( $fields[ $i ], $context );
			++$i;
		}
	}
}

/**
 * Output the mobile search drawer markup in the footer.
 *
 * @return void
 */
function hvn_realty_output_mobile_search_drawer() {
	if ( ! hvn_realty_should_render_mobile_search_drawer() ) {
		return;
	}

	get_template_part( 'template-parts/home/mobile-search-drawer' );
}
add_action( 'wp_footer', 'hvn_realty_output_mobile_search_drawer', 12 );

/**
 * Enqueue mobile drawer assets (homepage, mobile stylesheet media query).
 *
 * @return void
 */
function hvn_realty_enqueue_mobile_search_drawer_assets() {
	if ( ! hvn_realty_should_render_mobile_search_drawer() ) {
		return;
	}

	$style_deps = array( 'hvn-realty-theme' );

	hvn_realty_enqueue_theme_style(
		'hvn-realty-home-mobile-search-drawer',
		'assets/css/home/mobile-search-drawer.css',
		$style_deps,
		false,
		'(max-width: 991px)'
	);

	if ( hvn_realty_enqueue_theme_script( 'hvn-realty-home-mobile-search-drawer', 'assets/js/home/mobile-search-drawer.js' ) ) {
		wp_localize_script(
			'hvn-realty-home-mobile-search-drawer',
			'hvnRealtyMobileSearchDrawer',
			function_exists( 'hvn_realty_get_mobile_search_drawer_js_config' )
				? hvn_realty_get_mobile_search_drawer_js_config()
				: array()
		);
	}
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_mobile_search_drawer_assets', 36 );
