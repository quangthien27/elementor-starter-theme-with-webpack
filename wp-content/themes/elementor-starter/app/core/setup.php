<?php

defined( 'ABSPATH' ) or exit;

class App_Setup {
  public function __construct() {
    add_action( 'wp_head', [ $this, 'pingback_header' ] );
    add_action( 'wp_head', [ $this, 'wp_head' ] );
    add_action( 'wp_footer', [ $this, 'wp_footer' ], 99 );
    add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
    add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 99 );
    add_filter( 'body_class', [ $this, 'body_classes' ] );
    add_filter( 'excerpt_more', [ $this, 'excerpt_more' ] );
    add_filter( 'excerpt_length', [ $this, 'excerpt_length' ] );

    // Register custom post types
    // add_action( 'init', [ $this, 'cpt_register_download' ] );

    // Breadcrumbs
    add_filter( 'rank_math/frontend/breadcrumb/items', [ $this, 'custom_breadcrumbs' ], 10 );
    add_filter( 'woocommerce_get_breadcrumb', [ $this, 'custom_breadcrumbs' ], 10 );

    // Plugins
    add_filter( 'alm_filters_public_taxonomies', '__return_false' );
    add_filter( 'alm_filters_edit', '__return_false' );
  }

  function custom_breadcrumbs( $crumbs ): array {
    $helpers = App_Core::instance()->helpers;

    // Change crumbs here

    return array_values( $crumbs );
  }

  function cpt_register_download(): void {
    $slug      = 'download';
    $post_type = 'cpt-download';
    $public    = false;

    $labels = array(
      'name'               => _x( 'Downloads', 'post type general name' ),
      'singular_name'      => _x( 'Download', 'post type singular name' ),
      'add_new'            => _x( 'Add Download', 'rep' ),
      'add_new_item'       => __( 'Add New Download' ),
      'edit_item'          => __( 'Edit Download' ),
      'new_item'           => __( 'New Download' ),
      'view_item'          => __( 'View Download' ),
      'search_items'       => __( 'Search Download' ),
      'not_found'          => __( 'Nothing found' ),
      'not_found_in_trash' => __( 'Nothing found in Trash' ),
      'parent_item_colon'  => '',
    );

    $args = array(
      'labels'             => $labels,
      'public'             => $public,
      'publicly_queryable' => $public,
      'show_ui'            => true,
      'query_var'          => true,
      'rewrite'            => array( 'slug' => $slug ),
      'capability_type'    => 'post',
      'hierarchical'       => false,
      'menu_position'      => null,
      'menu_icon'          => 'dashicons-category',
      'supports'           => array( 'title', 'excerpt' ),
      'has_archive'        => false,
      'taxonomies'         => [ 'post_tag' ],
    );

    register_post_type( $post_type, $args );

    register_taxonomy(
      'doc_category',
      array( $post_type ),
      array(
        'query_var'          => true,
        'hierarchical'       => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'publicly_queryable' => false,
        'meta_box_cb'        => false,
        'rewrite'            => array( 'slug' => 'doc-category' ),
        'label'              => __( 'Doc Categories' ),
      )
    );
  }

  function body_classes( $classes ) {
    if ( ! is_singular() ) {
      $classes[] = 'hfeed';
    }

    return $classes;
  }

  function pingback_header(): void {
    if ( is_singular() && pings_open() ) {
      echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
    }
  }

  function setup_theme(): void {
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', ) );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'customize-selective-refresh-widgets' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );

    // add_image_size( 'thumbnails_600_516', 600, 516, true );

    add_post_type_support( 'post', 'excerpt' );
    add_post_type_support( 'page', 'excerpt' );

    register_nav_menus( array(
      'primary_menu' => __( 'Primary Menu' ),
      'footer_menu'  => __( 'Footer Menu' ),
    ) );
  }

  function enqueue_scripts(): void {
    $suffix = SCRIPT_DEBUG ? '' : '.min';

    // Only registered assets. Purpose: for enqueuing in components/widgets when needed only
    wp_register_style( 'theme-swiper-css', App_Core::instance()->helpers->get_assets_path( "lib/swiper/swiper-bundle{$suffix}.css" ), [] );
    wp_register_script( 'theme-swiper-js', App_Core::instance()->helpers->get_assets_path( "lib/swiper/swiper-bundle{$suffix}.js" ), [], false, true );

    // Global enqueued assets
    $assets_css = [
      // 'css-font-awesome' => 'lib/font-awesome-4.7.0/css/font-awesome.min.css',
    ];
    $assets_js  = [
      // 'js-css-browser' => 'lib/css_browser_selector/css_browser_selector.js',
      'js-wow' => 'lib/wow/dist/wow.min.js',
    ];

    /* Enqueuing */
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'underscore' );

    foreach ( $assets_css as $handle => $path ) {
      wp_enqueue_style( $handle, App_Core::instance()->helpers->get_assets_path( $path ), [], ASSETS_VERSION );
    }
    foreach ( $assets_js as $handle => $path ) {
      wp_enqueue_script( $handle, App_Core::instance()->helpers->get_assets_path( $path ), [], ASSETS_VERSION, true );
    }

    /* Main assets */
    wp_enqueue_style( 'theme-dashicons', includes_url( "css/dashicons$suffix.css" ), [], ASSETS_VERSION );
    wp_enqueue_style( 'theme-style', get_theme_file_uri( 'dist/main.min.css' ), [], ASSETS_VERSION );
    wp_enqueue_script( 'theme-js', get_theme_file_uri( 'dist/main.min.js' ), [], ASSETS_VERSION, true );

    wp_localize_script( 'theme-js',
      'App',
      [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
      ]
    );
  }

  function excerpt_more( $more ): string {
    return ' ...';
  }

  function excerpt_length( $length ): string {
    return 30;
  }

  function wp_head(): void {
    ?>
    <script type="text/javascript">const $ = jQuery.noConflict();</script>
    <?php
  }

  function wp_footer(): void {
  }
}
