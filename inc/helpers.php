<?php
/**
 * Helper functions
 *
 * @package air-reactions
 */

namespace Air_Reactions;

/**
 * Check if user can reaction.
 * Return true if you want to allow non-logged in users to reaction posts
 *
 * @return boolean Is user allowed to reaction
 */
function can_user_react() {
  $require_login = apply_filters( 'air_reactions_require_login', DEFAULT_REQUIRE_LOGIN );

  if ( $require_login ) {
    $capability = apply_filters( 'air_reactions_capability', 'read' );

    return current_user_can( $capability );
  }
  return true;
}

/**
 * Get reactions from user meta if user is logged in
 *
 * @return array Array of reactions
 */
function get_user_reactions( int $user_id ) {
  if ( ! $user_id && 0 === get_current_user_id() ) {
    return [];
  }

  $user_reactions = get_user_meta( $user_id, META_FIELD_KEY, true );
  return is_array( $user_reactions ) ? $user_reactions : [];
}

/**
 * Has user reacted a post?
 *
 * @param int|string $post_id Post id
 * @param int        $user_id User id
 * @return string|bool False if not has reacted, the reaction type if has
 */
function has_user_reacted( $post_id, int $user_id ) {
  $user_reactions = get_user_reactions( $user_id );
  if ( empty( $user_reactions[ $post_id ] ) ) {
    return false;
  }

  return $user_reactions[ $post_id ];
}

/**
 * Get default reaction types
 *
 * @return array filtered set of default types
 */
function get_types() {
  $default_types = [
    'heart' => [
      'icon_path' => plugin_dir_path( __DIR__ ) . 'svg/heart.svg',
      'texts'     => [
        'reaction'   => __( 'Love this post', 'air-reactions' ),
        'amount_pre' => __( 'Loved', 'air-reactions' ),
        'amount_post' => __( 'times', 'air-reactions' ),
      ],
    ],
    'like' => [
      'icon_path' => plugin_dir_path( __DIR__ ) . 'svg/thumbs-up.svg',
      'texts'     => [
        'reaction'   => __( 'Like this post', 'air-reactions' ),
        'amount_pre' => __( 'Liked', 'air-reactions' ),
        'amount_post' => __( 'times', 'air-reactions' ),
      ],
    ],
    'dislike' => [
      'icon_path' => plugin_dir_path( __DIR__ ) . 'svg/thumbs-down.svg',
      'texts'     => [
        'reaction'   => __( 'Dislike this post', 'air-reactions' ),
        'amount_pre' => __( 'Disliked', 'air-reactions' ),
        'amount_post' => __( 'times', 'air-reactions' ),
      ],
    ],
  ];

  return apply_filters( 'air_reactions_types', (array) $default_types );
}

/**
 * Get allowed post types
 *
 * @return array Array of allowed post types
 */
function get_allowed_post_types() {
  return apply_filters( 'air_reactions_post_types', [ 'post', 'page', 'comment' ] );
}

/**
 * Check if post type is allowed
 *
 * @param int|string $post_id The post id to check
 * @return bool
 */
function is_post_type_allowed( $post_id ) {
  if ( is_comment( $post_id ) ) {
    return in_array( 'comment', get_allowed_post_types(), true );
  }
  return in_array( \get_post_type( $post_id ), get_allowed_post_types(), true );
}

/**
 * Add reaction to post
 *
 * @param int|string $post_id Post id
 * @param int|string $user_id User id or hash
 * @param string     $type Reaction type
 */
function save_reaction( $post_id, $user_id, string $type ) {
  $meta = is_comment( $post_id ) ? get_comment_meta( parse_comment_id( $post_id ), META_FIELD_KEY, true ) : get_post_meta( $post_id, META_FIELD_KEY, true );

  $post_reactions = is_array( $meta ) ? $meta : [];

  // Check if user already reacted and is now trying to reverse the reaction
  if ( ! empty( $post_reactions[ $user_id ] ) && $post_reactions[ $user_id ] === $type ) {
    unset( $post_reactions[ $user_id ] );
  } else {
    $post_reactions[ $user_id ] = $type;
  }

  if ( is_comment( $post_id ) ) {
    update_comment_meta( parse_comment_id( $post_id ), META_FIELD_KEY, $post_reactions );
  } else {
    update_post_meta( $post_id, META_FIELD_KEY, $post_reactions );
  }

  // Check if this is an actual user and save to user meta as well
  if ( ! get_user_by( 'id', $user_id ) ) {
    return;
  }

  $user_reactions = is_array( get_user_meta( $user_id, META_FIELD_KEY, true ) ) ? get_user_meta( $user_id, META_FIELD_KEY, true ) : [];

  // Check if user already reacted and is now trying to reverse the reaction
  if ( ! empty( $user_reactions[ $post_id ] ) && $user_reactions[ $post_id ] === $type ) {
    unset( $user_reactions[ $post_id ] );
  } else {
    $user_reactions[ $post_id ] = $type;
  }

  update_user_meta( $user_id, META_FIELD_KEY, $user_reactions );
}

/**
 * Get post reactions
 *
 * @param int $post_id The post id
 * @return array Array of reaction types with reaction count
 */
function count_post_reactions( $post_id ) {
  $meta = is_comment( $post_id ) ? get_comment_meta( parse_comment_id( $post_id ), META_FIELD_KEY, true ) : get_post_meta( $post_id, META_FIELD_KEY, true );

  $post_reactions = is_array( $meta ) ? $meta : [];

  $types = array_keys( get_types() );
  $post_reaction_count = [];

  foreach ( $types as $key ) {
    $post_reaction_count[ $key ] = 0;
  }

  foreach ( $post_reactions as $type ) {
    $post_reaction_count[ $type ] += 1;
  }

  return apply_filters( 'air_reactions_count_post_reactions', (array) $post_reaction_count, (int) $post_id, (string) META_FIELD_KEY );
}

/**
 * Check if the id is comment
 *
 * @param string|int $post_id Post ID
 * @return bool
 */
function is_comment( $post_id ) {
  return false === strpos( $post_id, 'comment' ) ? false : true;
}

/**
 * Parses comment id from comment-34 to 34
 *
 * @param string|int $post_id Post ID
 * @return int The comment id
 */
function parse_comment_id( $post_id ) {
 return intval( str_replace( 'comment-', '', $post_id ), 10 );
}

function enqueue_scripts() {
  $settings = [
    'requireLogin'         => apply_filters( 'air_reactions_require_login', DEFAULT_REQUIRE_LOGIN ),
    'loginRequiredMessage' => apply_filters( 'air_reactions_login_required_message', __( 'Please login to react this', 'air-reactions' ) ),
  ];

  wp_localize_script( 'air-reactions', 'airReactionsSettings', $settings );

  // Enqueue script so it's not enqueued until we are displaying some reactions
  \wp_enqueue_script( 'air-reactions' );

  if ( apply_filters( 'air_reactions_load_default_styles', true ) ) {
    \wp_enqueue_style( 'air-reactions' );
  }
}