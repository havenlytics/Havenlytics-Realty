<?php
/**
 * Professional Breadcrumb Navigation System
 * 
 * @package Havenlytics_Realty
 * @since 1.0.1
 */

/**
 * Generate professional breadcrumb navigation with schema markup
 * 
 * @param array $args Optional arguments to customize output
 * @return void
 */
function hvn_realty_breadcrumbs( $args = array() ) {
    
    // Default arguments
    $defaults = array(
        'separator'       => '<span class="hvn-breadcrumb-separator">›</span>',
        'home_text'       => __( 'Home', 'havenlytics-realty' ),
        'home_icon'       => true,
        'show_current'    => true,
        'before_current'  => '<span class="hvn-breadcrumb-current">',
        'after_current'   => '</span>',
        'echo'            => true,
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    // Don't show on front page
    if ( is_front_page() ) {
        return;
    }
    
    // Get global post object
    global $post;
    
    // Start output buffer
    $output = '';
    
    // Open breadcrumb container with schema markup
    $output .= '<nav class="hvn-theme-breadcrumbs hvn-breadcrumb-wrapper" aria-label="' . esc_attr__( 'Breadcrumb', 'havenlytics-realty' ) . '" itemscope itemtype="https://schema.org/BreadcrumbList">';
    $output .= '<div class="hvn-breadcrumb-container">';
    $output .= '<div class="hvn-breadcrumb-list">';
    
    // Position counter for schema
    $position = 1;
    $schema_items = array();

    $home_url = home_url( '/' );
    $schema_items[] = array(
        '@type'    => 'ListItem',
        'position' => $position,
        'name'     => $args['home_text'],
        'item'     => $home_url,
    );
    
    // Home link
    $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    $output .= '<a href="' . esc_url( home_url( '/' ) ) . '" class="hvn-breadcrumb-link" itemprop="item">';
    $output .= '<span itemprop="name">';
    
    if ( $args['home_icon'] ) {
        $output .= '<span class="hvn-breadcrumb-home-icon">';
        $output .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
        $output .= '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-8H9v8H4a2 2 0 0 1-2-2z"></path>';
        $output .= '</svg>';
        $output .= '<span class="hvn-breadcrumb-home-text">' . esc_html( $args['home_text'] ) . '</span>';
        $output .= '</span>';
    } else {
        $output .= esc_html( $args['home_text'] );
    }
    
    $output .= '</span>';
    $output .= '</a>';
    $output .= '<meta itemprop="position" content="' . $position . '" />';
    $output .= '</span>';
    $output .= $args['separator'];
    $position++;

    $plugin_trail     = null;
    $plugin_handled   = false;

    if ( function_exists( 'hvn_realty_breadcrumbs_plugin_integration' ) ) {
        $plugin_trail = hvn_realty_breadcrumbs_plugin_integration( $args, $position, $schema_items );
        if ( null !== $plugin_trail ) {
            $output        .= $plugin_trail;
            $plugin_handled = true;
        }
    }

    // Single posts
    if ( ! $plugin_handled && is_single() && ! is_attachment() ) {

        $blog_page_id = (int) get_option( 'page_for_posts' );
        if ( $blog_page_id && 'post' === get_post_type() ) {
            $blog_url = get_permalink( $blog_page_id );
            $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            $output .= '<a href="' . esc_url( $blog_url ) . '" class="hvn-breadcrumb-link" itemprop="item">';
            $output .= '<span itemprop="name">' . esc_html( get_the_title( $blog_page_id ) ) . '</span>';
            $output .= '</a>';
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</span>';
            $output .= $args['separator'];
            $schema_items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => get_the_title( $blog_page_id ),
                'item'     => $blog_url,
            );
            $position++;
        }
        
        // Get post type
        $post_type = get_post_type_object( get_post_type() );
        
        // Check if post type has archive
        if ( $post_type && $post_type->has_archive ) {
            $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            $output .= '<a href="' . esc_url( get_post_type_archive_link( get_post_type() ) ) . '" class="hvn-breadcrumb-link" itemprop="item">';
            $output .= '<span itemprop="name">' . esc_html( $post_type->labels->name ) . '</span>';
            $output .= '</a>';
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</span>';
            $output .= $args['separator'];
            $position++;
        }
        
        // Get categories
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            $category = $categories[0];
            $cat_url  = get_category_link( $category->term_id );
            $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            $output .= '<a href="' . esc_url( $cat_url ) . '" class="hvn-breadcrumb-link" itemprop="item">';
            $output .= '<span itemprop="name">' . esc_html( $category->name ) . '</span>';
            $output .= '</a>';
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</span>';
            $output .= $args['separator'];
            $schema_items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => $category->name,
                'item'     => $cat_url,
            );
            $position++;
        }
        
        // Current post
        if ( $args['show_current'] ) {
            $output .= '<span class="hvn-breadcrumb-item hvn-breadcrumb-current" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            $output .= $args['before_current'];
            $output .= '<span itemprop="name">' . esc_html( get_the_title() ) . '</span>';
            $output .= $args['after_current'];
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</span>';
            $schema_items[] = array(
                '@type'    => 'ListItem',
                'position' => $position,
                'name'     => get_the_title(),
            );
        }
    }
    // Pages
    elseif ( ! $plugin_handled && is_page() && ! is_front_page() ) {
        
        // Get parent pages
        if ( $post->post_parent ) {
            $parent_id   = $post->post_parent;
            $breadcrumbs = array();
            
            while ( $parent_id ) {
                $page = get_post( $parent_id );
                $breadcrumbs[] = array(
                    'id'    => $page->ID,
                    'title' => get_the_title( $page->ID ),
                    'url'   => get_permalink( $page->ID ),
                );
                $parent_id = $page->post_parent;
            }
            
            $breadcrumbs = array_reverse( $breadcrumbs );
            foreach ( $breadcrumbs as $crumb ) {
                $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
                $output .= '<a href="' . esc_url( $crumb['url'] ) . '" class="hvn-breadcrumb-link" itemprop="item">';
                $output .= '<span itemprop="name">' . esc_html( $crumb['title'] ) . '</span>';
                $output .= '</a>';
                $output .= '<meta itemprop="position" content="' . $position . '" />';
                $output .= '</span>';
                $output .= $args['separator'];
                $position++;
            }
        }
        
        // Current page
        if ( $args['show_current'] ) {
            $output .= $args['before_current'];
            $output .= '<span itemprop="name">' . esc_html( get_the_title() ) . '</span>';
            $output .= $args['after_current'];
        }
    }
    // Category archive
    elseif ( ! $plugin_handled && is_category() ) {
        $current_category = single_cat_title( '', false );
        $output .= $args['before_current'];
        $output .= '<span itemprop="name">' . esc_html__( 'Category: ', 'havenlytics-realty' ) . esc_html( $current_category ) . '</span>';
        $output .= $args['after_current'];
    }
    // Tag archive
    elseif ( ! $plugin_handled && is_tag() ) {
        $current_tag = single_tag_title( '', false );
        $output .= $args['before_current'];
        $output .= '<span itemprop="name">' . esc_html__( 'Tag: ', 'havenlytics-realty' ) . esc_html( $current_tag ) . '</span>';
        $output .= $args['after_current'];
    }
    // Author archive
    elseif ( ! $plugin_handled && is_author() ) {
        $author = get_the_author();
        $output .= $args['before_current'];
        $output .= '<span itemprop="name">' . esc_html__( 'Author: ', 'havenlytics-realty' ) . esc_html( $author ) . '</span>';
        $output .= $args['after_current'];
    }
    // Search results
    elseif ( ! $plugin_handled && is_search() ) {
        $search_query = get_search_query();
        $output .= $args['before_current'];
        $output .= '<span itemprop="name">' . sprintf( esc_html__( 'Search Results for: %s', 'havenlytics-realty' ), '"' . esc_html( $search_query ) . '"' ) . '</span>';
        $output .= $args['after_current'];
    }
    // Date archive
    elseif ( ! $plugin_handled && is_date() ) {
        if ( is_year() ) {
            $year = get_the_time( 'Y' );
            $output .= $args['before_current'];
            $output .= '<span itemprop="name">' . esc_html( $year ) . '</span>';
            $output .= $args['after_current'];
        } elseif ( is_month() ) {
            $year = get_the_time( 'Y' );
            $month = get_the_time( 'F' );
            $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            $output .= '<a href="' . esc_url( get_year_link( $year ) ) . '" class="hvn-breadcrumb-link" itemprop="item">';
            $output .= '<span itemprop="name">' . esc_html( $year ) . '</span>';
            $output .= '</a>';
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</span>';
            $output .= $args['separator'];
            $position++;
            $output .= $args['before_current'];
            $output .= '<span itemprop="name">' . esc_html( $month ) . '</span>';
            $output .= $args['after_current'];
        } elseif ( is_day() ) {
            $year = get_the_time( 'Y' );
            $month = get_the_time( 'F' );
            $day = get_the_time( 'j' );
            $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            $output .= '<a href="' . esc_url( get_year_link( $year ) ) . '" class="hvn-breadcrumb-link" itemprop="item">';
            $output .= '<span itemprop="name">' . esc_html( $year ) . '</span>';
            $output .= '</a>';
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</span>';
            $output .= $args['separator'];
            $position++;
            $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            $output .= '<a href="' . esc_url( get_month_link( $year, get_the_time( 'm' ) ) ) . '" class="hvn-breadcrumb-link" itemprop="item">';
            $output .= '<span itemprop="name">' . esc_html( $month ) . '</span>';
            $output .= '</a>';
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</span>';
            $output .= $args['separator'];
            $position++;
            $output .= $args['before_current'];
            $output .= '<span itemprop="name">' . esc_html( $day ) . '</span>';
            $output .= $args['after_current'];
        }
    }
    // 404 page
    elseif ( ! $plugin_handled && is_404() ) {
        $output .= $args['before_current'];
        $output .= '<span itemprop="name">' . esc_html__( '404 - Page Not Found', 'havenlytics-realty' ) . '</span>';
        $output .= $args['after_current'];
    }
    // Post type archive
    elseif ( ! $plugin_handled && is_post_type_archive() ) {
        $post_type = get_post_type_object( get_post_type() );
        if ( $post_type ) {
            $output .= $args['before_current'];
            $output .= '<span itemprop="name">' . esc_html( $post_type->labels->name ) . '</span>';
            $output .= $args['after_current'];
        }
    }
    // Taxonomy archive
    elseif ( ! $plugin_handled && is_tax() ) {
        $term = get_queried_object();
        if ( $term ) {
            $output .= $args['before_current'];
            $output .= '<span itemprop="name">' . esc_html( $term->name ) . '</span>';
            $output .= $args['after_current'];
        }
    }
    // Attachment page
    elseif ( ! $plugin_handled && is_attachment() ) {
        $parent = get_post( $post->post_parent );
        if ( $parent ) {
            $output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            $output .= '<a href="' . esc_url( get_permalink( $parent ) ) . '" class="hvn-breadcrumb-link" itemprop="item">';
            $output .= '<span itemprop="name">' . esc_html( get_the_title( $parent ) ) . '</span>';
            $output .= '</a>';
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</span>';
            $output .= $args['separator'];
            $position++;
        }
        $output .= $args['before_current'];
        $output .= '<span itemprop="name">' . esc_html( get_the_title() ) . '</span>';
        $output .= $args['after_current'];
    }
    // Blog page
    elseif ( ! $plugin_handled && is_home() && ! is_front_page() ) {
        $blog_page_id = get_option( 'page_for_posts' );
        if ( $blog_page_id ) {
            $output .= $args['before_current'];
            $output .= '<span itemprop="name">' . esc_html( get_the_title( $blog_page_id ) ) . '</span>';
            $output .= $args['after_current'];
        }
    }
    // Default fallback
    elseif ( ! $plugin_handled ) {
        $output .= $args['before_current'];
        $output .= '<span itemprop="name">' . esc_html( get_the_title() ) . '</span>';
        $output .= $args['after_current'];
    }
    
    // Close breadcrumb container
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</nav>';

    if ( ! empty( $schema_items ) ) {
        $schema_script = '<script type="application/ld+json">' . wp_json_encode(
            array(
                '@context'        => 'https://schema.org',
                '@type'           => 'BreadcrumbList',
                'itemListElement' => $schema_items,
            ),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) . '</script>';
    } else {
        $schema_script = '';
    }
    
    // Output or return
    if ( $args['echo'] ) {
        echo wp_kses_post( $output );
        if ( $schema_script ) {
            echo $schema_script; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    } else {
        return $output . $schema_script;
    }
}