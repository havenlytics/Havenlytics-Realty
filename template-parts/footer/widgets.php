<?php
/**
 * Footer widgets template part.
 *
 * @package Havenlytics_Realty
 */

$max_columns     = hvn_realty_get_footer_columns();
$footer_sidebars = array();

for ( $i = 1; $i <= $max_columns; $i++ ) {
	$sidebar_id = 'footer-' . $i;
	if ( is_active_sidebar( $sidebar_id ) ) {
		$footer_sidebars[] = $sidebar_id;
	}
}

if ( empty( $footer_sidebars ) ) {
	return;
}

foreach ( $footer_sidebars as $sidebar_id ) :
	?>
	<div class="hvn-theme-footer-col hvn-theme-footer-widget-area">
		<?php dynamic_sidebar( $sidebar_id ); ?>
	</div>
	<?php
endforeach;