<?php
/**
 * The API functionality
 *
 * @package air-reactions
 */

namespace Air_Reactions;

/**
 * Register the REST API route for reactions
 */
function register_reaction_api() {
  register_rest_route(
    REST_NAMESPACE,
    REST_ROUTE,
    [
      'methods' => 'post',
      'callback' => __NAMESPACE__ . '\save_reaction_callback',
      'permission_callback' => __NAMESPACE__ . '\can_user_reaction',
    ]
  );
}

/**
 * Save reaction callback function
 *
 * @param object $request The REST API request
 */
function save_reaction_callback( $request ) {
  if ( empty( $request->get_param( 'id' ) ) || empty( $request->get_param( 'type' ) ) ) {
    return;
  }

  $id = sanitize_key( $request->get_param( 'id' ) );
  $type = sanitize_key( $request->get_param( 'type' ) );
  $current_user = get_current_user_id();

  save_reaction( $id, $current_user, $type );

  return [
    'items' => count_post_reactions( $id ),
  ];
}
