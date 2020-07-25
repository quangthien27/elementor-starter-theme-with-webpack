<?php

require_once 'inc/setup.php';
require_once 'inc/modules/elementor/elementor.php';

function the_posted_on_date() {
  $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
  if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
  }

  $time_string = sprintf( $time_string,
    esc_attr( get_the_date( 'c' ) ),
    esc_html( get_the_date() ),
    esc_attr( get_the_modified_date( 'c' ) ),
    esc_html( get_the_modified_date() )
  );

  $posted_on = sprintf(
  /* translators: %s: post date. */
    esc_html_x( 'Posted on %s', 'post date', 'getstored' ),
    '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
  );

  $byline = sprintf(
  /* translators: %s: post author. */
    esc_html_x( 'by %s', 'post author', 'getstored' ),
    '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
  );

  echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
}

function get_assets_path( $filename = '' ) {
  $dist_path = get_template_directory_uri() . '/assets/';

  if ( empty( $filename ) ) {
    return $dist_path;
  }

  $directory = dirname( $filename ) . '/';
  $file      = basename( $filename );

  return esc_url( $dist_path . $directory . $file );
}

function the_assets_path( $filename ) {
  echo get_assets_path( $filename );
}

function get_page_id_by_template( $template_file_name ) {
  $pages = get_posts( array(
    'post_type'      => 'page',
    'posts_per_page' => - 1,
    'offset'         => 0,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
    'meta_key'       => '_wp_page_template',
    'meta_value'     => $template_file_name,
  ) );
  if ( isset( $pages[0] ) ) {
    return $pages[0]->ID;
  }

  return false;
}

function get_page_title() {
  if ( is_home() ) {
    if ( get_option( 'page_for_posts', true ) ) {
      return get_the_title( get_option( 'page_for_posts', true ) );
    } else {
      return __( 'Latest Posts', 'sage' );
    }
  } elseif ( is_archive() ) {
    return get_the_archive_title();
  } elseif ( is_search() ) {
    return sprintf( __( 'Search Results for %s', 'sage' ), get_search_query() );
  } elseif ( is_404() ) {
    return __( 'Not Found', 'sage' );
  } else {
    return get_the_title();
  }
}

function get_last_term( $post_id, $taxonomy ) {
  if ( ! $post = get_post( $post_id ) ) {
    return false;
  }

  if ( class_exists( 'WPSEO_Primary_Term' ) ) {
    $term    = new WPSEO_Primary_Term( $taxonomy, $post->ID );
    $term_id = $term->get_primary_term();

    if ( $term_id ) {
      return get_term( $term_id, $taxonomy );
    }
  }

  $terms = get_the_terms( $post->ID, $taxonomy );
  if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
    return array_pop( $terms );
  }

  return null;
}

function get_related_posts( $post_id, $related_count, $args = array() ) {
  $terms = get_the_terms( $post_id, 'category' );

  if ( empty( $terms ) ) {
    $terms = array();
  }

  $term_list = wp_list_pluck( $terms, 'slug' );

  $related_args = array(
    'post_type'      => 'post',
    'posts_per_page' => $related_count,
    'post_status'    => 'publish',
    'post__not_in'   => array( $post_id ),
    'orderby'        => 'rand',
    'tax_query'      => array(
      array(
        'taxonomy' => 'category',
        'field'    => 'slug',
        'terms'    => $term_list,
      ),
    ),
  );

  $related_posts = get_posts( $related_args );

  if ( count( $related_posts ) < $related_count ) {
    $other_args = array(
      'post_type'      => 'post',
      'posts_per_page' => ( $related_count - count( $related_posts ) ),
      'post_status'    => 'publish',
      'post__not_in'   => array_merge( array( $post_id ), wp_list_pluck( $related_posts, 'ID' ) ),
      'orderby'        => 'rand',
    );

    $related_posts = array_merge( $related_posts, get_posts( $other_args ) );
  }

  return $related_posts;
}

function get_sibling( $post_id, $link = 'next' ) {
  if ( ! empty( $current_post = get_post( $post_id ) ) ) {
    $all_posts = get_posts( [
      'posts_per_page' => - 1,
      'post_type'      => $current_post->post_type,
      'post_parent'    => $current_post->post_parent,
    ] );

    if ( 1 == count( $all_posts ) ) {
      return false;
    }

    foreach ( $all_posts as $index => $p ) {
      if ( $p->ID == $current_post->ID ) {
        switch ( $link ) {
          case 'prev':
          case 'before':
            if ( $index == 0 ) {
              return false;
            }

            return $all_posts[ $index - 1 ];
            break;

          default:
            if ( $index < count( $all_posts ) - 1 ) {
              return $all_posts[ $index + 1 ];
            }

            return false;
            break;
        }
      }
    }
  }

  return false;
}

function is_elementor_active() {
  return class_exists( 'Elementor\Plugin' );
}
