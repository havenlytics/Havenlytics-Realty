<?php



/**



 * Havenlytics integration assets.



 *



 * @package Havenlytics_Realty



 */







if ( ! defined( 'ABSPATH' ) ) {



	exit;



}







/**



 * Enqueue modular homepage styles.



 *



 * @return void



 */



function hvn_realty_enqueue_home_styles() {



	if ( ! function_exists( 'hvn_realty_should_show_realty_home' ) || ! hvn_realty_should_show_realty_home() ) {



		return;



	}



	if ( function_exists( 'hvn_realty_skip_legacy_home_styles' ) && hvn_realty_skip_legacy_home_styles() ) {



		return;



	}







	$modules  = array(



		'base',



		'hero',



		'carousel',



		'departments',



		'sections',



		'taxonomies',



		'property-types',



		'testimonials',



		'blog',



	);







	$dependency = array( 'hvn-realty-theme' );







	foreach ( $modules as $module ) {



		$handle = 'hvn-realty-home-' . $module;



		$relative_path = 'assets/css/home/' . $module . '.css';



		if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {



			if ( ! hvn_realty_enqueue_theme_style( $handle, $relative_path, $dependency ) ) {



				continue;



			}



		} else {



			$file = get_template_directory() . '/' . $relative_path;



			if ( ! file_exists( $file ) ) {



				continue;



			}



			wp_enqueue_style(



				$handle,



				HVN_REALTY_TEMPLATE_URL . '/assets/css/home/' . $module . '.css',



				$dependency,



				HVN_REALTY_VERSION



			);



		}



		$dependency = array( $handle );



	}



}







/**



 * Enqueue homepage and plugin compat styles.



 *



 * @return void



 */



function hvn_realty_enqueue_havenlytics_assets() {



	hvn_realty_enqueue_home_styles();







	if (



		function_exists( 'hvn_realty_is_havenlytics_plugin_active' )



		&& hvn_realty_is_havenlytics_plugin_active()



		&& (



			( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() )



			|| ( function_exists( 'hvn_realty_is_havenlytics_view' ) && hvn_realty_is_havenlytics_view() )



		)



	) {



		if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {



			hvn_realty_enqueue_theme_style( 'hvn-realty-havenlytics-compat', 'assets/css/havenlytics-compat.css', array( 'hvn-realty-theme' ) );



		} elseif ( file_exists( get_template_directory() . '/assets/css/havenlytics-compat.css' ) ) {



			wp_enqueue_style(



				'hvn-realty-havenlytics-compat',



				HVN_REALTY_TEMPLATE_URL . '/assets/css/havenlytics-compat.css',



				array( 'hvn-realty-theme' ),



				HVN_REALTY_VERSION



			);



		}



	}



}



add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_havenlytics_assets', 25 );


