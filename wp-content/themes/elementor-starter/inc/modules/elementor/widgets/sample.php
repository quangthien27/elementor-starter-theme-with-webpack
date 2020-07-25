<?php

defined( 'ABSPATH' ) or exit;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class Custom_El_Sample extends Widget_Base {
  public function get_name() {
    return 'Custom_El_Sample';
  }

  public function get_title() {
    return 'Sample Widget';
  }

  public function get_icon() {
    return 'fa fa-puzzle-piece';
  }

  public function get_categories() {
    return [ 'custom' ];
  }

  protected function _register_controls() {
    $this->start_controls_section(
      'content_section',
      [
        'label' => 'Content',
        'tab'   => Controls_Manager::TAB_CONTENT,
      ]
    );

    // Add controls here

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();

    $uid = uniqid( 'sample-' );
    ?>
    <div id="<?= $uid ?>">Sample Widget</div>
    <?php
  }
}
