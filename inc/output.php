<?php
/**
 * Output functions
 *
 * @package air-reactions
 */

namespace Air_Reactions;

use WP_Error;

/**
 * Outputs the reaction button
 *
 * @param array $args Reaction button arguments
 */
function the_output( array $args ) {
  enqueue_scripts();

  $default_args = [
    'types'      => (array) [ 'heart', 'like', 'dislike' ],
    'post_id'    => (int) \get_the_ID(),
    'echo'       => (bool) true,
  ];

  $args = \wp_parse_args( $args, $default_args );

  if ( ! is_post_type_allowed( $args['post_id'] ) ) {
    return new WP_Error( 'wrong post type', 'Reactions not allowed for post type of post id' . $args['post_id'] );
  }

  $types            = get_types();
  $post_reactions   = count_post_reactions( $args['post_id'] );
  $current_user_id  = \get_current_user_id();
  $has_user_reacted = has_user_reacted( $args['post_id'], $current_user_id );

  $output = container_start( $args, $current_user_id );

  foreach ( $args['types'] as $key ) {
    if ( empty( $types[ $key ] ) ) {
      return new WP_Error( 'reaction type not found', 'Reaction type ' . $key . ' not defined' );
    }
    $item = $types[ $key ];
    $item['reactions'] = ! empty( $post_reactions[ $key ] ) ? $post_reactions[ $key ] : 0;
    $item['user_has_reacted'] = $has_user_reacted === $key;
    $output .= reaction_item( $key, $item, $post_reactions );
  }

  $output .= container_end( $args, $current_user_id );

  if ( $args['echo'] ) {
    // This output has already been escaped while building it
    echo $output; // phpcs:ignore
  } else {
    return $output;
  }
}

/**
 * Output single reaction item
 *
 * @param string $key Item key/slug
 * @param array  $item Array of item properties
 */
function reaction_item( string $key, array $item ) {
  $classes = [
    'air-reactions__item',
    'air-reactions__item--' . esc_attr( $key ),
  ];

  if ( $item['user_has_reacted'] ) {
    $classes[] = 'air-reactions__item--reacted';
  }

  ob_start();
  ?>

  <div class="<?php echo esc_attr( join( ' ', $classes ) ); ?>"
    data-air-reaction-item="<?php echo esc_attr( $key ); ?>">

    <button type="button" class="air-reaction__button">

    <span class="screen-reader-text">
      <?php echo esc_html( $item['texts']['reaction'] ); ?>
    </span>
    <?php include $item['icon_path']; ?>

    </button>

    <div class="air-reaction__item-count">

    <span class="screen-reader-text">
      <?php echo esc_html( $item['texts']['amount_pre'] ); ?>
    </span>

    <span class="air-reaction__item-amount"
    data-air-reaction-count="<?php echo esc_attr( $item['reactions'] ); ?>">
      <?php echo esc_html( $item['reactions'] ); ?>
    </span>

    <span class="screen-reader-text">
      <?php echo esc_html( $item['texts']['amount_post'] ); ?>
    </span>

    </div><!-- air-reaction__item-count -->

  </div><!-- .air-reactions__item -->

  <?php

  $output = ob_get_clean();

  return apply_filters(
    'air_reactions_reaction_item',
    (string) $output,
    (string) $key,
    (array) $item
  );
}

function container_start( array $args, int $current_user_id ) {
  ob_start();
  ?>
  <div class="air-reactions"
    data-air-reaction-id="<?php echo esc_attr( $args['post_id'] ); ?>"
    data-air-reaction-user="<?php echo esc_attr( $current_user_id ); ?>"
    data-air-reaction-user-reaction="<?php echo esc_attr( has_user_reacted( $args['post_id'], $current_user_id ) ); ?>">
  <?php
  $output = ob_get_clean();

  return apply_filters(
    'air_reactions_container_start',
    (string) $output,
    (array) $args,
    (int) $current_user_id
  );
}

function container_end( array $args, int $current_user_id ) {
  ob_start();
  ?>
  </div><!-- .air-reactions -->
  <?php
  $output = ob_get_clean();

  return apply_filters(
    'air_reactions_container_end',
    (string) $output,
    (array) $args,
    (int) $current_user_id
  );
}