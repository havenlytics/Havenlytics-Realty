<?php
/**
 * Shared property search form (header overlay or hero panel).
 *
 * @package Havenlytics_Realty
 *
 * @var array $args {
 *     @type string $context Context slug: header|hero.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$context = isset( $args['context'] ) ? sanitize_key( $args['context'] ) : 'header';

if ( ! in_array( $context, array( 'header', 'hero' ), true ) ) {
	$context = 'header';
}

$is_hero   = ( 'hero' === $context );
$id_prefix = $is_hero ? 'hvn-hero' : 'hvn-header';

$form_class   = $is_hero ? 'hvn-realty-hero-search__form' : 'hvn-theme-property-search-panel__form';
$field_class  = $is_hero ? 'hvn-realty-hero-search__field' : 'hvn-theme-property-search-panel__field';
$row_class    = $is_hero ? 'hvn-realty-hero-search__row' : 'hvn-theme-property-search-panel__row';
$actions_class = $is_hero ? 'hvn-realty-hero-search__actions' : 'hvn-theme-property-search-panel__actions';
$submit_class = $is_hero ? 'hvn-realty-hero-search__submit' : 'hvn-theme-property-search-panel__submit';

$search_url     = function_exists( 'hvn_realty_get_property_search_url' ) ? hvn_realty_get_property_search_url() : home_url( '/' );
$property_types = function_exists( 'hvn_realty_get_header_search_terms' ) ? hvn_realty_get_header_search_terms( 'hvnly_prop_types' ) : array();
$locations      = function_exists( 'hvn_realty_get_header_search_terms' ) ? hvn_realty_get_header_search_terms( 'hvnly_prop_locations' ) : array();
$count_options  = function_exists( 'hvn_realty_get_search_count_select_options' ) ? hvn_realty_get_search_count_select_options() : array();
$button_text    = isset( $args['button_text'] ) && is_string( $args['button_text'] ) && '' !== $args['button_text']
	? $args['button_text']
	: __( 'Search properties', 'havenlytics-realty' );
?>
<form class="<?php echo esc_attr( $form_class ); ?>" method="get" action="<?php echo esc_url( $search_url ); ?>">
	<div class="<?php echo esc_attr( $field_class ); ?>">
		<label for="<?php echo esc_attr( $id_prefix ); ?>-address-keyword"><?php esc_html_e( 'Keyword or address', 'havenlytics-realty' ); ?></label>
		<input
			type="search"
			class="hvn-theme-form-control"
			id="<?php echo esc_attr( $id_prefix ); ?>-address-keyword"
			name="address_keyword"
			autocomplete="off"
			placeholder="<?php esc_attr_e( 'City, neighborhood, or address', 'havenlytics-realty' ); ?>"
		/>
	</div>

	<div class="<?php echo esc_attr( $row_class ); ?>">
		<div class="<?php echo esc_attr( $field_class ); ?>">
			<label for="<?php echo esc_attr( $id_prefix ); ?>-property-type"><?php esc_html_e( 'Property type', 'havenlytics-realty' ); ?></label>
			<select class="hvn-theme-form-control" id="<?php echo esc_attr( $id_prefix ); ?>-property-type" name="property_type">
				<option value=""><?php esc_html_e( 'Any type', 'havenlytics-realty' ); ?></option>
				<?php foreach ( $property_types as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="<?php echo esc_attr( $field_class ); ?>">
			<label for="<?php echo esc_attr( $id_prefix ); ?>-location"><?php esc_html_e( 'Location', 'havenlytics-realty' ); ?></label>
			<select class="hvn-theme-form-control" id="<?php echo esc_attr( $id_prefix ); ?>-location" name="location">
				<option value=""><?php esc_html_e( 'Any location', 'havenlytics-realty' ); ?></option>
				<?php foreach ( $locations as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="<?php echo esc_attr( $row_class ); ?>">
		<div class="<?php echo esc_attr( $field_class ); ?>">
			<label for="<?php echo esc_attr( $id_prefix ); ?>-bedrooms"><?php esc_html_e( 'Bedrooms', 'havenlytics-realty' ); ?></label>
			<select class="hvn-theme-form-control" id="<?php echo esc_attr( $id_prefix ); ?>-bedrooms" name="bedrooms">
				<?php foreach ( $count_options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="<?php echo esc_attr( $field_class ); ?>">
			<label for="<?php echo esc_attr( $id_prefix ); ?>-bathrooms"><?php esc_html_e( 'Bathrooms', 'havenlytics-realty' ); ?></label>
			<select class="hvn-theme-form-control" id="<?php echo esc_attr( $id_prefix ); ?>-bathrooms" name="bathrooms">
				<?php foreach ( $count_options as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="<?php echo esc_attr( $row_class ); ?>">
		<div class="<?php echo esc_attr( $field_class ); ?>">
			<label for="<?php echo esc_attr( $id_prefix ); ?>-min-price"><?php esc_html_e( 'Min price', 'havenlytics-realty' ); ?></label>
			<input
				type="number"
				class="hvn-theme-form-control"
				id="<?php echo esc_attr( $id_prefix ); ?>-min-price"
				name="min_price"
				min="0"
				step="1"
				inputmode="numeric"
				placeholder="<?php esc_attr_e( 'No min', 'havenlytics-realty' ); ?>"
			/>
		</div>

		<div class="<?php echo esc_attr( $field_class ); ?>">
			<label for="<?php echo esc_attr( $id_prefix ); ?>-max-price"><?php esc_html_e( 'Max price', 'havenlytics-realty' ); ?></label>
			<input
				type="number"
				class="hvn-theme-form-control"
				id="<?php echo esc_attr( $id_prefix ); ?>-max-price"
				name="max_price"
				min="0"
				step="1"
				inputmode="numeric"
				placeholder="<?php esc_attr_e( 'No max', 'havenlytics-realty' ); ?>"
			/>
		</div>
	</div>

	<input type="hidden" name="view_type" value="grid" />
	<input type="hidden" name="orderby" value="date" />
	<input type="hidden" name="paged" value="1" />
	<?php if ( $is_hero ) : ?>
		<input type="hidden" name="department" id="hvn-hero-department" value="" />
	<?php endif; ?>

	<div class="<?php echo esc_attr( $actions_class ); ?>">
		<button type="submit" class="hvn-theme-btn hvn-theme-btn-primary <?php echo esc_attr( $submit_class ); ?>">
			<?php echo esc_html( $button_text ); ?>
		</button>
	</div>
</form>
