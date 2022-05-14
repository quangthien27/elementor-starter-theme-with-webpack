<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

if ( ! App_Core::instance()->helpers->is_elementor_active() ) {
  return;
}

use Elementor\Elements_Manager;
use Elementor\Plugin;

final class Custom_Elementor {
  const VERSION = '1.0.0';

  private static ?Custom_Elementor $_instance = null;

  public function __construct() {
    add_action( 'after_setup_theme', [ $this, 'init' ] );
  }

  public function init(): void {
    // Check if Elementor installed and activated
    if ( ! did_action( 'elementor/loaded' ) ) {
      add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );

      return;
    }

    // Init elements
    add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'init_dynamic_tags' ] );
    add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
    add_action( 'elementor/elements/categories_registered', [ $this, 'init_categories' ] );
    add_action( 'elementor/element/post/document_settings/before_section_end', [ $this, 'init_page_settings_controls' ] );
  }

  public function init_widgets(): void {
    // Register widgets (class is autoloaded, no need to include)
    Plugin::instance()->widgets_manager->register( new Elem_Header() );
  }

  public function init_dynamic_tags( $dynamic_tags ): void {
    \Elementor\Plugin::$instance->dynamic_tags->register_group( 'app', [
      'title' => 'Custom App',
    ] );

    // Register the tag (class is autoloaded, no need to include)
    $dynamic_tags->register_tag( 'Elem_Tag_Home_Url' );
  }

  public function init_categories( Elements_Manager $categories_manager ): void {
    $categories_manager->add_category(
      'custom',
      [
        'title' => 'Custom App Widgets',
        'icon'  => 'fa fa-plug',
      ]
    );
  }

  public function init_page_settings_controls( Elementor\Core\DocumentTypes\PageBase $page ): void {
    // Sample control on Page

    // $page->add_control(
    //   'show_key_background',
    //   [
    //     'label'        => __( 'Show Key Background', 'app' ),
    //     'type'         => Controls_Manager::SWITCHER,
    //     'label_on'     => __( 'Show', 'app' ),
    //     'label_off'    => __( 'Hide', 'app' ),
    //     'return_value' => 'yes',
    //     'default'      => 'yes',
    //   ]
    // );
  }

  public function admin_notice_missing_main_plugin(): void {
    if ( isset( $_GET['activate'] ) ) {
      unset( $_GET['activate'] );
    }

    $message = sprintf(
    /* translators: 1: Plugin name 2: Elementor */
      esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-test-extension' ),
      '<strong>' . esc_html__( 'Elementor Test Extension', 'elementor-test-extension' ) . '</strong>',
      '<strong>' . esc_html__( 'Elementor', 'elementor-test-extension' ) . '</strong>'
    );

    printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
  }

  public static function instance(): ?Custom_Elementor {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }
}

Custom_Elementor::instance();
