<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 */

defined( 'WP_ENV' ) or define( 'WP_ENV', 'staging' );
defined( 'ASSETS_VERSION' ) or define( 'ASSETS_VERSION', md5( filemtime( get_theme_file_path( 'dist/main.min.css' ) ) . filemtime( get_theme_file_path( 'dist/main.min.js' ) ) ) );
// add_filter( 'acf/settings/show_admin', ( defined( 'SHOW_ACF' ) && SHOW_ACF ? '__return_true' : '__return_false' ) );

add_filter( 'body_class', 'body_classes' );
function body_classes( $classes ) {
  if ( ! is_singular() ) {
    $classes[] = 'hfeed';
  }

  return $classes;
}

add_action( 'wp_head', 'pingback_header' );
function pingback_header() {
  if ( is_singular() && pings_open() ) {
    echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
  }
}

add_action( 'after_setup_theme', 'setup_theme' );
function setup_theme() {
  add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', ) );
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'customize-selective-refresh-widgets' );

  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );

  // add_image_size( 'thumbnails_600_516', 600, 516, true );

  add_post_type_support( 'post', 'excerpt' );

  register_nav_menus( array(
    'primary_menu' => __( 'Primary Menu' ),
    // 'footer_menu'  => __( 'Footer Menu' ),
  ) );
}

add_action( 'acf/init', 'add_site_settings_page' );
function add_site_settings_page() {
  if (
    function_exists( 'acf_add_options_page' ) &&
    current_user_can( 'edit_others_posts' )
  ) {
    acf_add_options_page( array(
      'page_title'    => 'Site Settings',
      'menu_title'    => 'Site Settings',
      'menu_slug'     => 'site-settings',
      'capability'    => 'edit_posts',
      'icon_url'      => 'dashicons-art',
      'update_button' => 'Save Settings',
      'redirect'      => false,
    ) );
  }
}

add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );
function enqueue_scripts() {
  $suffix = SCRIPT_DEBUG ? '' : '.min';

  /* Common assets */
  $assets_css = [
    'css-bootstrap-reboot' => 'libraries/bootstrap-4.5.0/css/bootstrap-reboot.min.css',
    'css-bootstrap-grid'   => 'libraries/bootstrap-4.5.0/css/bootstrap-grid.min.css',
    'css-bootstrap'        => 'libraries/bootstrap-4.5.0/css/bootstrap.min.css',
  ];
  $assets_js  = [
    'js-bootstrap'   => 'libraries/bootstrap-4.5.0/js/bootstrap.bundle.min.js',
    'js-css-browser' => 'libraries/css_browser_selector/css_browser_selector.js',
  ];

  /* Specific assets */
  $assets_css['css-slick']       = 'libraries/slick/slick.min.css';
  $assets_css['css-slick-theme'] = 'libraries/slick/slick-theme.min.css';
  $assets_js['js-slick']         = 'libraries/slick/slick.min.js';

  /* Enqueue */
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'underscore' );
  foreach ( $assets_css as $handle => $path ) {
    wp_enqueue_style( $handle, get_assets_path( $path ), [], ASSETS_VERSION );
  }
  foreach ( $assets_js as $handle => $path ) {
    wp_enqueue_script( $handle, get_assets_path( $path ), [], ASSETS_VERSION, true );
  }

  /* Conditions */
  wp_script_add_data( 'theme-html5', 'conditional', 'lt IE 9' );
  wp_script_add_data( 'theme-respond', 'conditional', 'lt IE 9' );

  /* Main assets */
  wp_enqueue_style( 'theme-dashicons', includes_url( "css/dashicons$suffix.css" ), [], ASSETS_VERSION );
  wp_enqueue_style( 'theme-style', get_theme_file_uri( 'dist/main.min.css' ), [], ASSETS_VERSION );
  wp_enqueue_style( 'theme-style-custom', get_theme_file_uri( 'assets/css/custom.css' ), [], ASSETS_VERSION );
  wp_enqueue_script( 'theme-js', get_theme_file_uri( 'dist/main.min.js' ), [], ASSETS_VERSION, true );

  wp_localize_script( 'theme-js',
    'ElementorStarter',
    [
      'ajaxUrl' => admin_url( 'admin-ajax.php' ),
    ]
  );
}

add_filter( 'excerpt_more', 'custom_excerpt_more' );
function custom_excerpt_more( $more ) {
  return ' ...';
}

add_filter( 'map_meta_cap', 'custom_fix_elementor_not_saved_for_non_admins', 1, 3 );
function custom_fix_elementor_not_saved_for_non_admins( $caps, $cap, $user_id ) {
  if ( ! is_multisite() ) {
    return $caps;
  }

  if ( 'unfiltered_html' === $cap && user_can( $user_id, 'edit_posts' ) ) {
    $caps = [ 'unfiltered_html' ];
  }

  return $caps;
}

add_action( 'wp_head', 'hook_head' );
function hook_head() {
  ?>
  <script type="text/javascript">var $ = jQuery.noConflict();</script>
  <?php
}

add_action( 'wp_footer', 'custom_wp_footer', 99 );
function custom_wp_footer() {
}
