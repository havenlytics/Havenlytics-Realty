<?php
/**
 * Global page heading — single H1 below breadcrumbs.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = get_query_var( 'hvn_realty_page_heading_title', '' );
if ( ! is_string( $title ) || '' === trim( $title ) ) {
	return;
}
?>
<header class="hvn-theme-page-header hvn-theme-page-header--global">
	<h1 class="hvn-theme-page-title"><?php echo esc_html( $title ); ?></h1>
</header>
