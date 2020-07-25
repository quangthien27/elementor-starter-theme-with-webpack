<?php
/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
?>
<?php
while ( have_posts() ) : the_post();
  ?>

  <main <?php post_class( 'site-main' ); ?> role="main">
    <div class="page-content">
      <?php the_content(); ?>
      <div class="post-tags">
        <?php the_tags( '<span class="tag-links">' . __( 'Tagged ', 'hello-elementor' ), null, '</span>' ); ?>
      </div>
      <?php wp_link_pages(); ?>
    </div>

    <?php comments_template(); ?>
  </main>

<?php
endwhile;
