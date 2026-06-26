<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Havenlytics_Realty
 */

if ( ! is_active_sidebar( 'sidebar-1' ) && ! is_customize_preview() ) {
	return;
}

$position = function_exists( 'hvn_realty_get_sidebar_position' ) ? hvn_realty_get_sidebar_position() : 'right';
?>

<aside
	id="secondary"
	class="hvn-theme-sidebar-area hvn-theme-sidebar-<?php echo esc_attr( $position ); ?>"
	role="complementary"
	aria-label="<?php esc_attr_e( 'Sidebar', 'havenlytics-realty' ); ?>">
	<div class="hvn-theme-sidebar">
		<?php
		if ( is_active_sidebar( 'sidebar-1' ) ) {
			dynamic_sidebar( 'sidebar-1' );
		}
		?>
	</div>
</aside>
