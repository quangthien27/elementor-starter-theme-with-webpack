<?php

defined( 'ABSPATH' ) or exit;

class App_Admin {
  public function __construct() {
    add_filter( 'acf/settings/show_admin', ( defined( 'SHOW_ACF' ) && SHOW_ACF ? '__return_true' : '__return_false' ) );
    add_filter( 'relevanssi_search_ok', [ $this, 'fix_relevanssi_search_elementor_library' ], 10, 2 );
    add_action( 'admin_head', [ $this, 'admin_head' ] );
    add_action( 'acf/init', [ $this, 'site_settings_page' ] );
    add_filter( 'map_meta_cap', [ $this, 'unfiltered_html_capability' ], 1, 3 );
  }

  function admin_head(): void {
    ?>
    <style>
        .alm-err-notice {
            display: none;
        }

        #adminmenu li.wp-menu-separator + .wp-menu-separator {
            display: none !important;
        }

        #adminmenu li.wp-menu-separator {
            border-bottom: 1px solid #5f5f5f;
        }

        .plugins tr[data-plugin='admin-columns-pro/admin-columns-pro.php'] th,
        .plugins tr[data-plugin='admin-columns-pro/admin-columns-pro.php'] td {
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.1) !important;
        }

        .plugins .plugin-update-tr[data-slug="admin-columns-pro.php"],
        .plugins [data-slug^="ajax-load-more-"] + .plugin-update-tr {
            display: none;
        }
    </style>
    <?php
  }

  function fix_relevanssi_search_elementor_library( $ok, $query ) {
    if ( 'elementor_library' === $query->query_vars['post_type'] ) {
      $ok = false;
    }

    return $ok;
  }

  function site_settings_page(): void {
    if ( ( current_user_can( 'manage_options' ) || current_user_can( 'editor' ) ) && function_exists( 'acf_add_options_page' ) ) {
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

  function unfiltered_html_capability( $caps, $cap, $user_id ) {
    if ( 'unfiltered_html' === $cap && user_can( $user_id, 'edit_posts' ) ) {
      $caps = [ 'unfiltered_html' ];
    }

    return $caps;
  }
}
