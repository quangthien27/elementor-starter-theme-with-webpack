<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Elem_Header extends Widget_Base {
  public function get_title(): string {
    return 'Header';
  }

  public function get_script_depends(): array {
    return [ // Just add the registered handle
    ];
  }

  public function get_style_depends(): array {
    return [ // Just add the registered handle
      $this->_get_asset_handle(),
    ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'content_section',
      [
        'label' => 'Content',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    // Add controls from here

    $this->add_control(
      'message',
      [
        'label'     => __( 'Global header', 'plugin-name' ),
        'type'      => \Elementor\Controls_Manager::HEADING,
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $class = 'elem-header';
    $uid   = uniqid( "$class-" );
    ?>
    <div id="<?= $uid ?>" class="<?= $class ?>">
      <?/* Add HTML/PHP code here */ ?>

      <?/* Add JS below, make sure to use defer for performance. Always select child elements starting from "element" parent variable */ ?>
      <script defer>
        jQuery(function($) {
          const element = $('<?="#{$uid}"?>');
        });
      </script>
    </div>
    <?php
  }

  // DO NOT CHANGE/UPDATE BELOW FUNCTIONS IF NOT NECESSARY

  public function __construct( $data = [], $args = null ) {
    parent::__construct( $data, $args );
    $this->_register_assets();
  }

  public function get_name(): string {
    return __CLASS__;
  }

  public function get_categories(): array {
    return [ 'custom' ];
  }

  private function _register_assets() {
    $name = str_replace( '_', '-', strtolower( substr( $this->get_name(), 5 ) ) );

    wp_register_style( $this->_get_asset_handle(), get_theme_file_uri( "dist/css/$name.min.css" ), [], ASSETS_VERSION );
  }

  private function _get_asset_handle(): string {
    return "theme-{$this->get_name()}";
  }
}
