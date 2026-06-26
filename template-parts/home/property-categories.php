<?php
/**
 * Homepage: Property categories (deprecated alias for property taxonomies).
 *
 * @package Havenlytics_Realty
 */

if ( function_exists( 'hvn_realty_safe_get_template_part' ) ) {
	hvn_realty_safe_get_template_part( 'template-parts/home/property-taxonomies' );
	return;
}

get_template_part( 'template-parts/home/property-taxonomies' );
