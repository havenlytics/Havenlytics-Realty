<?php
/**
 * Homepage hero search — Havenlytics query parameter helpers.
 *
 * Maps the hero search form to the plugin's PropertyQueryArgsBuilder so every
 * field submits a supported, correctly named parameter.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the Havenlytics property search engine is available.
 *
 * @return bool
 */
function hvn_realty_home_search_is_available() {
	return post_type_exists( 'hvnly_property' ) && function_exists( 'hvn_realty_get_property_search_url' );
}

/**
 * Load taxonomy terms for hero search selects (empty when taxonomy is missing).
 *
 * @param string $taxonomy Taxonomy slug.
 * @param int    $limit    Max terms (0 = no limit).
 * @return WP_Term[]
 */
function hvn_realty_home_search_get_terms( $taxonomy, $limit = 0 ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	$args = array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);

	if ( $limit > 0 ) {
		$args['number'] = $limit;
	}

	$terms = get_terms( $args );

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Resolve a department slug from preferred candidates against live terms.
 *
 * @param string[] $candidates Preferred slugs, highest priority first.
 * @return string Department slug or empty string when none match.
 */
function hvn_realty_home_search_resolve_department_slug( array $candidates ) {
	$terms = hvn_realty_home_search_get_terms( 'hvnly_prop_depts' );
	if ( empty( $terms ) ) {
		return '';
	}

	$slugs = wp_list_pluck( $terms, 'slug' );

	foreach ( $candidates as $candidate ) {
		if ( in_array( $candidate, $slugs, true ) ) {
			return $candidate;
		}
	}

	return '';
}

/**
 * Buy / Rent / Sell tab configuration mapped to Havenlytics department slugs.
 *
 * @return array<string, array{label:string, department:string, is_default:bool}>
 */
function hvn_realty_get_home_search_department_tabs() {
	$tabs = array(
		'buy'  => array(
			'label'      => __( 'Buy', 'havenlytics-realty' ),
			'department' => hvn_realty_home_search_resolve_department_slug( array( 'sale', 'buy', 'for-sale' ) ),
			'is_default' => true,
		),
		'rent' => array(
			'label'      => __( 'Rent', 'havenlytics-realty' ),
			'department' => hvn_realty_home_search_resolve_department_slug( array( 'rent', 'let' ) ),
			'is_default' => false,
		),
		'sell' => array(
			'label'      => __( 'Sell', 'havenlytics-realty' ),
			'department' => hvn_realty_home_search_resolve_department_slug( array( 'commercial', 'sell' ) ),
			'is_default' => false,
		),
	);

	/**
	 * Filter hero search department tab mapping.
	 *
	 * @param array<string, array{label:string, department:string, is_default:bool}> $tabs Tab config.
	 */
	return apply_filters( 'hvn_realty_home_search_department_tabs', $tabs );
}

/**
 * Bedroom / bathroom / reception count options (plugin sidebar helpers when present).
 *
 * @param string $field bedrooms|bathrooms|reception_rooms.
 * @return array<int|string, string> Value => label.
 */
function hvn_realty_home_search_get_count_options( $field ) {
	$options = array( '' => __( 'Any', 'havenlytics-realty' ) );

	if ( 'reception_rooms' === $field && function_exists( 'hvnly_filter_sidebar_get_reception_rooms_options' ) ) {
		foreach ( hvnly_filter_sidebar_get_reception_rooms_options() as $value ) {
			$options[ (string) $value ] = sprintf(
				/* translators: %s: minimum count. */
				__( '%s+', 'havenlytics-realty' ),
				$value
			);
		}
		return $options;
	}

	if ( function_exists( 'hvn_realty_get_search_count_select_options' ) ) {
		$base = hvn_realty_get_search_count_select_options();
		foreach ( $base as $value => $label ) {
			if ( '' === $value ) {
				continue;
			}
			$options[ $value ] = ( is_numeric( $label ) ? $label . '+' : $label );
		}
		return $options;
	}

	for ( $i = 1; $i <= 4; $i++ ) {
		$options[ (string) $i ] = $i . '+';
	}

	return $options;
}

/**
 * Master registry of Havenlytics-supported hero search fields.
 *
 * New fields added via filter automatically appear in the Search Builder.
 *
 * @return array<string, array<string, mixed>>
 */
function hvn_realty_get_home_search_field_registry() {
	$registry = array(
		'keyword'         => array(
			'label'                => __( 'Keyword Search', 'havenlytics-realty' ),
			'placeholder'          => __( 'Address, keyword, feature…', 'havenlytics-realty' ),
			'param'                => 'address_keyword',
			'input'                => 'search',
			'supports_default'     => true,
			'supports_placeholder' => true,
		),
		'property_type'   => array(
			'label'                => __( 'Property Type', 'havenlytics-realty' ),
			'placeholder'          => '',
			'empty_label'          => __( 'Any Type', 'havenlytics-realty' ),
			'param'                => 'property_type',
			'input'                => 'select_type',
			'supports_default'     => true,
			'supports_placeholder' => false,
		),
		'status'          => array(
			'label'                => __( 'Property Status', 'havenlytics-realty' ),
			'placeholder'          => '',
			'empty_label'          => __( 'Any Status', 'havenlytics-realty' ),
			'param'                => 'in_status',
			'input'                => 'select_status',
			'taxonomy'             => 'hvnly_prop_status',
			'supports_default'     => true,
			'supports_placeholder' => false,
		),
		'location'        => array(
			'label'                => __( 'Location', 'havenlytics-realty' ),
			'placeholder'          => __( 'City, neighborhood, ZIP', 'havenlytics-realty' ),
			'empty_label'          => __( 'Any location', 'havenlytics-realty' ),
			'param'                => 'location',
			'input'                => 'select_location',
			'taxonomy'             => 'hvnly_prop_locations',
			'supports_default'     => true,
			'supports_placeholder' => true,
		),
		'bedrooms'        => array(
			'label'                => __( 'Bedrooms', 'havenlytics-realty' ),
			'placeholder'          => '',
			'param'                => 'bedrooms',
			'input'                => 'select_count',
			'count_field'          => 'bedrooms',
			'supports_default'     => true,
			'supports_placeholder' => false,
		),
		'bathrooms'       => array(
			'label'                => __( 'Bathrooms', 'havenlytics-realty' ),
			'placeholder'          => '',
			'param'                => 'bathrooms',
			'input'                => 'select_count',
			'count_field'          => 'bathrooms',
			'supports_default'     => true,
			'supports_placeholder' => false,
		),
		'reception_rooms' => array(
			'label'                => __( 'Reception Rooms', 'havenlytics-realty' ),
			'placeholder'          => '',
			'param'                => 'reception_rooms',
			'input'                => 'select_count',
			'count_field'          => 'reception_rooms',
			'supports_default'     => true,
			'supports_placeholder' => false,
		),
		'garages'         => array(
			'label'                => __( 'Garages (min sq ft)', 'havenlytics-realty' ),
			'placeholder'          => __( 'e.g. 200', 'havenlytics-realty' ),
			'param'                => 'garages',
			'input'                => 'number',
			'supports_default'     => true,
			'supports_placeholder' => true,
		),
		'min_price'       => array(
			'label'                => __( 'Min Price', 'havenlytics-realty' ),
			'placeholder'          => __( 'e.g. 250000', 'havenlytics-realty' ),
			'param'                => 'min_price',
			'input'                => 'number',
			'supports_default'     => true,
			'supports_placeholder' => true,
		),
		'max_price'       => array(
			'label'                => __( 'Max Price', 'havenlytics-realty' ),
			'placeholder'          => __( 'e.g. 750000', 'havenlytics-realty' ),
			'param'                => 'max_price',
			'input'                => 'number',
			'supports_default'     => true,
			'supports_placeholder' => true,
		),
		'features'        => array(
			'label'                => __( 'Property Features', 'havenlytics-realty' ),
			'placeholder'          => '',
			'empty_label'          => __( 'Any Feature', 'havenlytics-realty' ),
			'param'                => 'feature',
			'input'                => 'select_taxonomy',
			'taxonomy'             => 'hvnly_prop_features',
			'supports_default'     => true,
			'supports_placeholder' => false,
		),
		'badges'          => array(
			'label'                => __( 'Property Badges', 'havenlytics-realty' ),
			'placeholder'          => '',
			'empty_label'          => __( 'Any Badge', 'havenlytics-realty' ),
			'param'                => 'badge',
			'input'                => 'select_taxonomy',
			'taxonomy'             => 'hvnly_prop_badges',
			'supports_default'     => true,
			'supports_placeholder' => false,
		),
		'property_id'     => array(
			'label'                => __( 'Property ID', 'havenlytics-realty' ),
			'placeholder'          => __( 'e.g. PR-2026-00000001', 'havenlytics-realty' ),
			'param'                => 'property_ids[]',
			'input'                => 'text',
			'supports_default'     => true,
			'supports_placeholder' => true,
		),
	);

	/**
	 * Filter hero search field registry (add new engine-backed fields here).
	 *
	 * @param array<string, array<string, mixed>> $registry Field definitions.
	 */
	return apply_filters( 'hvn_realty_home_search_field_registry', $registry );
}

/**
 * Whether a registry field is supported by the Havenlytics search engine.
 *
 * @param string $field_id Field slug.
 * @return bool
 */
function hvn_realty_home_search_field_is_supported( $field_id ) {
	$registry = hvn_realty_get_home_search_field_registry();
	if ( ! isset( $registry[ $field_id ] ) ) {
		return false;
	}

	switch ( $field_id ) {
		case 'property_id':
			return function_exists( 'hvnly_get_unique_property_ids' ) || function_exists( 'hvnly_filter_sidebar_get_unique_property_ids' );
		case 'status':
		case 'features':
		case 'badges':
			$taxonomy = isset( $registry[ $field_id ]['taxonomy'] ) ? (string) $registry[ $field_id ]['taxonomy'] : '';
			return '' !== $taxonomy && taxonomy_exists( $taxonomy );
		case 'location':
			return true;
		default:
			return true;
	}
}

/**
 * Default Search Builder configuration (matches pre-builder layout).
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_default_home_search_fields_config() {
	$defaults = array(
		array(
			'id'       => 'location',
			'enabled'  => true,
			'zone'     => 'primary',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'property_type',
			'enabled'  => true,
			'zone'     => 'primary',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'max_price',
			'enabled'  => true,
			'zone'     => 'primary',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'bedrooms',
			'enabled'  => true,
			'zone'     => 'primary',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'keyword',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'property_id',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'status',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'features',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'badges',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'bathrooms',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'reception_rooms',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'garages',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
		array(
			'id'       => 'min_price',
			'enabled'  => true,
			'zone'     => 'advanced',
			'label'    => '',
			'placeholder' => '',
			'default'  => '',
			'required' => false,
			'width'    => '1',
		),
	);

	/**
	 * Filter default hero search builder field configuration.
	 *
	 * @param array<int, array<string, mixed>> $defaults Default field rows.
	 */
	return apply_filters( 'hvn_realty_default_home_search_fields_config', $defaults );
}

/**
 * Merge saved Search Builder config with registry (request-cached).
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_home_search_fields_config() {
	static $cached = null;

	if ( null !== $cached ) {
		return $cached;
	}

	$registry = hvn_realty_get_home_search_field_registry();
	$saved    = get_theme_mod( 'hvn_realty_home_search_fields', '' );
	$decoded  = is_string( $saved ) && '' !== $saved ? json_decode( $saved, true ) : null;

	if ( ! is_array( $decoded ) || empty( $decoded ) ) {
		$cached = hvn_realty_get_default_home_search_fields_config();
		return $cached;
	}

	$merged  = array();
	$seen    = array();

	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$id = isset( $row['id'] ) ? sanitize_key( (string) $row['id'] ) : '';
		if ( '' === $id || ! isset( $registry[ $id ] ) || isset( $seen[ $id ] ) ) {
			continue;
		}

		if ( ! hvn_realty_home_search_field_is_supported( $id ) ) {
			continue;
		}

		$seen[ $id ]     = true;
		$merged[]        = hvn_realty_normalize_home_search_field_row( $row, $id );
	}

	foreach ( array_keys( $registry ) as $id ) {
		if ( isset( $seen[ $id ] ) || ! hvn_realty_home_search_field_is_supported( $id ) ) {
			continue;
		}

		$merged[] = hvn_realty_normalize_home_search_field_row(
			array(
				'id'      => $id,
				'enabled' => false,
				'zone'    => 'advanced',
			),
			$id
		);
	}

	$cached = $merged;
	return $cached;
}

/**
 * Normalize one Search Builder field row.
 *
 * @param array<string, mixed> $row Field row.
 * @param string               $id  Field slug.
 * @return array<string, mixed>
 */
function hvn_realty_normalize_home_search_field_row( array $row, $id ) {
	$zone = isset( $row['zone'] ) ? sanitize_key( (string) $row['zone'] ) : 'primary';
	if ( ! in_array( $zone, array( 'primary', 'advanced' ), true ) ) {
		$zone = 'primary';
	}

	$width = isset( $row['width'] ) ? sanitize_key( (string) $row['width'] ) : '1';
	if ( ! in_array( $width, array( '1', '2', 'full' ), true ) ) {
		$width = '1';
	}

	return array(
		'id'          => sanitize_key( $id ),
		'enabled'     => ! empty( $row['enabled'] ),
		'zone'        => $zone,
		'label'       => isset( $row['label'] ) ? sanitize_text_field( (string) $row['label'] ) : '',
		'placeholder' => isset( $row['placeholder'] ) ? sanitize_text_field( (string) $row['placeholder'] ) : '',
		'default'     => isset( $row['default'] ) ? sanitize_text_field( (string) $row['default'] ) : '',
		'required'    => ! empty( $row['required'] ),
		'width'       => $width,
	);
}

/**
 * Sanitize Search Builder JSON for theme_mod storage.
 *
 * @param mixed $input Raw value.
 * @return string JSON.
 */
function hvn_realty_sanitize_home_search_fields( $input ) {
	$registry = hvn_realty_get_home_search_field_registry();
	$decoded  = is_string( $input ) ? json_decode( $input, true ) : $input;

	if ( ! is_array( $decoded ) ) {
		return wp_json_encode( hvn_realty_get_default_home_search_fields_config() );
	}

	$clean = array();
	$seen  = array();

	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$id = isset( $row['id'] ) ? sanitize_key( (string) $row['id'] ) : '';
		if ( '' === $id || ! isset( $registry[ $id ] ) || isset( $seen[ $id ] ) ) {
			continue;
		}

		$seen[ $id ] = true;
		$clean[]     = hvn_realty_normalize_home_search_field_row( $row, $id );
	}

	foreach ( array_keys( $registry ) as $id ) {
		if ( isset( $seen[ $id ] ) ) {
			continue;
		}

		$clean[] = hvn_realty_normalize_home_search_field_row(
			array(
				'id'      => $id,
				'enabled' => false,
				'zone'    => 'advanced',
			),
			$id
		);
	}

	return wp_json_encode( $clean );
}

/**
 * Resolved label for a search field (custom or registry default).
 *
 * @param array<string, mixed> $field  Builder row.
 * @param array<string, mixed> $registry_meta Registry entry.
 * @return string
 */
function hvn_realty_home_search_field_label( array $field, array $registry_meta ) {
	if ( ! empty( $field['label'] ) ) {
		return (string) $field['label'];
	}

	return isset( $registry_meta['label'] ) ? (string) $registry_meta['label'] : '';
}

/**
 * Resolved placeholder for a search field.
 *
 * @param array<string, mixed> $field  Builder row.
 * @param array<string, mixed> $registry_meta Registry entry.
 * @return string
 */
function hvn_realty_home_search_field_placeholder( array $field, array $registry_meta ) {
	if ( ! empty( $field['placeholder'] ) ) {
		return (string) $field['placeholder'];
	}

	return isset( $registry_meta['placeholder'] ) ? (string) $registry_meta['placeholder'] : '';
}

/**
 * CSS width class for a search field.
 *
 * @param string $width Width slug.
 * @return string
 */
function hvn_realty_home_search_field_width_class( $width ) {
	switch ( sanitize_key( (string) $width ) ) {
		case '2':
			return ' hvn-theme-home-search__field--span-2';
		case 'full':
			return ' hvn-theme-home-search__field--span-full';
		default:
			return '';
	}
}

/**
 * Stable element ID map for hero search inputs.
 *
 * @param string $field_id Field slug.
 * @return string
 */
function hvn_realty_home_search_field_element_id( $field_id ) {
	$map = array(
		'keyword'         => 'hvn-theme-home-search-keywords',
		'location'        => 'hvn-theme-home-search-location',
		'property_type'   => 'hvn-theme-home-search-type',
		'max_price'       => 'hvn-theme-home-search-max-price',
		'min_price'       => 'hvn-theme-home-search-min-price',
		'bedrooms'        => 'hvn-theme-home-search-beds',
		'bathrooms'       => 'hvn-theme-home-search-baths',
		'reception_rooms' => 'hvn-theme-home-search-reception',
		'garages'         => 'hvn-theme-home-search-garages',
		'status'          => 'hvn-theme-home-search-status-term',
		'features'        => 'hvn-theme-home-search-features',
		'badges'          => 'hvn-theme-home-search-badges',
		'property_id'     => 'hvn-theme-home-search-property-id',
	);

	$field_id = sanitize_key( $field_id );

	return isset( $map[ $field_id ] ) ? $map[ $field_id ] : 'hvn-theme-home-search-' . $field_id;
}

/**
 * Render a single hero search field from builder config.
 *
 * @param array<string, mixed> $field         Builder row.
 * @param array<string, mixed> $context       Shared render context (terms, options).
 * @return void
 */
function hvn_realty_render_home_search_field( array $field, array $context = array() ) {
	$id = isset( $field['id'] ) ? sanitize_key( (string) $field['id'] ) : '';
	if ( '' === $id || empty( $field['enabled'] ) ) {
		return;
	}

	$registry = hvn_realty_get_home_search_field_registry();
	if ( ! isset( $registry[ $id ] ) || ! hvn_realty_home_search_field_is_supported( $id ) ) {
		return;
	}

	$meta        = $registry[ $id ];
	$label       = hvn_realty_home_search_field_label( $field, $meta );
	$placeholder = hvn_realty_home_search_field_placeholder( $field, $meta );
	$default     = isset( $field['default'] ) ? (string) $field['default'] : '';
	$required    = ! empty( $field['required'] );
	$element_id  = hvn_realty_home_search_field_element_id( $id );
	$width_class = hvn_realty_home_search_field_width_class( isset( $field['width'] ) ? (string) $field['width'] : '1' );
	$param       = isset( $meta['param'] ) ? (string) $meta['param'] : '';
	$input_type  = isset( $meta['input'] ) ? (string) $meta['input'] : 'text';

	$req_attr = $required ? ' required' : '';

	echo '<div class="hvn-theme-home-search__field' . esc_attr( $width_class ) . '">';
	echo '<label for="' . esc_attr( $element_id ) . '">' . esc_html( $label ) . '</label>';

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

		case 'select_count':
			$count_field = isset( $meta['count_field'] ) ? (string) $meta['count_field'] : $id;
			$options     = isset( $context['count_options'][ $count_field ] )
				? $context['count_options'][ $count_field ]
				: hvn_realty_home_search_get_count_options( $count_field );
			echo '<select id="' . esc_attr( $element_id ) . '" name="' . esc_attr( $param ) . '"' . ( $required ? ' required' : '' ) . '>';
			foreach ( $options as $value => $option_label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( (string) $value ),
					selected( $default, (string) $value, false ),
					esc_html( $option_label )
				);
			}
			echo '</select>';
			break;

		case 'select_type':
			$terms = isset( $context['type_terms'] ) ? $context['type_terms'] : array();
			$empty = isset( $meta['empty_label'] ) ? (string) $meta['empty_label'] : __( 'Any', 'havenlytics-realty' );
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
				echo '</div>';
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

		case 'select_taxonomy':
			$taxonomy = isset( $meta['taxonomy'] ) ? (string) $meta['taxonomy'] : '';
			$key      = 'feature' === $id ? 'feature_terms' : ( 'badges' === $id ? 'badge_terms' : $taxonomy . '_terms' );
			$terms    = isset( $context[ $key ] ) ? $context[ $key ] : hvn_realty_home_search_get_terms( $taxonomy, 50 );
			if ( empty( $terms ) ) {
				echo '</div>';
				return;
			}
			$empty = isset( $meta['empty_label'] ) ? (string) $meta['empty_label'] : __( 'Any', 'havenlytics-realty' );
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
	}

	echo '</div>';
}

/**
 * Supported expanded-panel field registry (only plugin-backed filters).
 *
 * @return array<string, bool>
 */
function hvn_realty_get_home_search_expanded_fields() {
	$fields = array();

	foreach ( hvn_realty_get_home_search_fields_config() as $row ) {
		if ( empty( $row['enabled'] ) || 'advanced' !== $row['zone'] ) {
			continue;
		}
		$fields[ $row['id'] ] = true;
	}

	return $fields;
}

/**
 * Whether a hero search expanded field should render.
 *
 * @param string $field Field slug.
 * @return bool
 */
function hvn_realty_home_search_has_expanded_field( $field ) {
	$fields = hvn_realty_get_home_search_expanded_fields();
	return ! empty( $fields[ $field ] );
}

/**
 * Whether any advanced-zone fields are enabled.
 *
 * @return bool
 */
function hvn_realty_home_search_has_advanced_fields() {
	foreach ( hvn_realty_get_home_search_fields_config() as $row ) {
		if ( ! empty( $row['enabled'] ) && 'advanced' === $row['zone'] ) {
			return true;
		}
	}
	return false;
}
