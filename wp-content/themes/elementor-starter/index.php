<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 */

get_header(); ?>

<?php if ( have_posts() ) : ?>

  <?php
  $is_elementor_theme_exist = function_exists( 'elementor_theme_do_location' );

  if ( is_singular() ) {
    if ( ! $is_elementor_theme_exist || ! elementor_theme_do_location( 'single' ) ) {
      get_template_part( 'template-parts/single' );
    }
  } elseif ( is_archive() || is_home() ) {
    if ( ! $is_elementor_theme_exist || ! elementor_theme_do_location( 'archive' ) ) {
      get_template_part( 'template-parts/archive' );
    }
  } elseif ( is_search() ) {
    if ( ! $is_elementor_theme_exist || ! elementor_theme_do_location( 'archive' ) ) {
      get_template_part( 'template-parts/search' );
    }
  } else {
    if ( ! $is_elementor_theme_exist || ! elementor_theme_do_location( 'single' ) ) {
      get_template_part( 'template-parts/404' );
    }
  }
  ?>

<?php else :

  get_template_part( 'template-parts/content', 'none' );

endif; ?>

<?php get_footer();
