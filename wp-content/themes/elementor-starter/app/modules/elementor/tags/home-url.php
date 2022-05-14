<?php

class Elem_Tag_Home_Url extends \Elementor\Core\DynamicTags\Tag {
  public function get_name(): string {
    return 'Elem_Tag_Home_Url';
  }

  public function get_title() {
    return __( 'Dynamic Home Url', 'elementor-pro' );
  }

  public function get_group(): string {
    return 'app';
  }

  public function get_categories(): array {
    return [
      \Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
    ];
  }

  protected function register_controls() {
  }

  public function render() {
    echo home_url();
  }
}
