<?php

defined( 'ABSPATH' ) or exit;

class App_Helpers {
  // THE
  function the_posted_on_date(): void {
    echo $this->get_posted_on_date_html();
  }

  function the_posted_date_with_author(): void {
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
      esc_html_x( 'Posted on %s', 'post date', 'mit' ),
      '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );

    $byline = sprintf(
    /* translators: %s: post author. */
      esc_html_x( 'by %s', 'post author', 'mit' ),
      '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
  }

  function the_assets_path( $filename ): void {
    echo $this->get_assets_path( $filename );
  }

  function get_posted_on_date_html(): string {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

    return sprintf( $time_string,
      esc_attr( get_the_date( 'c' ) ),
      esc_html( get_the_date( 'd.m.Y' ) )
    );
  }

  // GET
  function get_assets_path( $filename = '' ): string {
    $dist_path = get_template_directory_uri() . '/app/assets/';

    if ( empty( $filename ) ) {
      return $dist_path;
    }

    $directory = dirname( $filename ) . '/';
    $file      = basename( $filename );

    return esc_url( $dist_path . $directory . $file );
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
      return str_replace( [ 'Archives: ' ], [ '' ], get_the_archive_title() );
    } elseif ( is_search() ) {
      return sprintf( __( 'Search Results for %s', 'sage' ), get_search_query() );
    } elseif ( is_404() ) {
      return __( 'Not Found', 'sage' );
    } else {
      return get_the_title();
    }
  }

  function get_post_type_primary_tax( $post_type ): string {
    switch ( $post_type ) {
      case 'cpt-news':
        return 'category_news';

      case 'cpt-download':
        return 'doc_category';

      case 'cpt-video':
        return 'video_category';

      case 'cpt-recipe':
        return 'recipe_category';

      case 'product':
        return 'product_cat';

      default:
        return 'category';
    }
  }

  function get_post_types_options(): array {
    $options = [
      'post' => 'Posts',
    ];

    $post_types = get_post_types( array( '_builtin' => false ), 'objects' );

    foreach ( $post_types as $post_type ) {
      if ( false !== strpos( $post_type->name, 'cpt-' ) ) {
        $options[ $post_type->name ] = $post_type->label;
      }
    }

    return $options;
  }

  function get_taxonomies_options(): array {
    $options = [];

    $args = array(
      'public' => true,
    );

    if ( ! empty( $taxonomies = get_taxonomies( $args, 'objects' ) ) ) {
      foreach ( $taxonomies as $taxonomy => $obj ) {
        $options[ $taxonomy ] = $obj->label;
      }
    }

    return $options;
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

  function get_related_posts( $post_id, $related_count, $post_type = 'post' ): array {
    $primary_tax = $this->get_post_type_primary_tax( $post_type );
    $terms       = get_the_terms( $post_id, $primary_tax );

    if ( empty( $terms ) ) {
      $terms = array();
    }

    $term_list = wp_list_pluck( $terms, 'slug' );

    $related_args = array(
      'post_type'      => $post_type,
      'posts_per_page' => $related_count,
      'post_status'    => 'publish',
      'post__not_in'   => array( $post_id ),
      'orderby'        => 'rand',
      'tax_query'      => array(
        array(
          'taxonomy' => $primary_tax,
          'field'    => 'slug',
          'terms'    => $term_list,
        ),
      ),
    );

    $related_posts = get_posts( $related_args );

    if ( count( $related_posts ) < $related_count ) {
      $other_args = array(
        'post_type'      => $post_type,
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

  function get_maybe_wrapped_link( $url, $inner_html ) {
    ob_start();
    ?>
    <?= ! empty( $url ) ? '<a href="' . esc_url( $url ) . '">' : '' ?>
    <?= $inner_html ?>
    <?= ! empty( $url ) ? '</a>' : '' ?>
    <?php

    return ob_get_clean();
  }

  function get_relative_permalink( $url = '' ) {
    if ( empty( $url ) ) {
      $url = get_permalink();
    }

    return str_replace( home_url(), "", $url );
  }

  function get_video_iframe( $video_url ): string {
    if ( empty( $video_url ) ) {
      return '';
    }

    ob_start();

    if ( false !== strpos( $video_url, 'youtube' ) ) { ?>
      <?
      // For Youtube
      parse_str( parse_url( $video_url, PHP_URL_QUERY ), $link_params );
      ?>
      <div class="ratio ratio-16x9">
        <iframe loading="lazy" src="<?= esc_url( "https://www.youtube.com/embed/{$link_params['v']}?rel=0" ) ?>" frameborder="0" title="YouTube video" allowfullscreen></iframe>
      </div>
    <? } elseif ( false !== strpos( $video_url, 'vimeo' ) ) { ?>
      <?
      // For Vimeo
      $vimeo_id = '';
      $regs     = array();
      if ( preg_match( '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $video_url, $regs ) ) {
        $vimeo_id = $regs[3];
      }
      ?>
      <div class="ratio ratio-16x9">
        <iframe loading="lazy" src="https://player.vimeo.com/video/<?= $vimeo_id ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
      </div>
    <? }

    return ob_get_clean();
  }

  // IS
  function is_elementor_active(): bool {
    return class_exists( 'Elementor\Plugin' );
  }
}
