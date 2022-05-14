<?

global $post;

switch ( $post->post_type ) {
  case 'cpt-sample':
    ?>
    <div class="col mb-3 mb-lg-4 mb-xxl-5">
      <?
      // Add template part here
      ?>
    </div>
    <?php
    break;

  default:
    ?>
    <div class="col mb-3 mb-lg-4 mb-xxl-5">
      <?
      // Add template part here
      ?>
    </div>
  <?php
}
