<?php

$url      = $args['url'] ?? '';
$label    = $args['label'] ?? '';
$modifier = $args['modifier'] ?? 'primary';

if (
  ! empty( $url ) &&
  ! empty( $label )
) : ?>
  <a href="<?= esc_url( $url ) ?>" class="comp-button comp-button--<?= esc_attr( $modifier ) ?>"><?= sanitize_text_field( $label ) ?></a>
<?php endif;
