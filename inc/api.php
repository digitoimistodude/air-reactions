<?php
/**
 * The API functionality
 *
 * @package air-reactions
 */

namespace Air_Reactions;

use WP_Error;

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
      'permission_callback' => '__return_true',
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

  if ( ! can_user_react() ) {
    $response['message'] = apply_filters( 'air_reactions_not_allowed_message', __( 'Please login to react this', 'air-reactions' ) );

    return $response;
  }

  $id = sanitize_key( $request->get_param( 'id' ) );
  $type = sanitize_key( $request->get_param( 'type' ) );
  $current_user = get_current_user_id();

  save_reaction( $id, $current_user, $type );

  $response = [
    'items' => count_post_reactions( $id ),
  ];

  return $response;
}

/**
 * Permission callback
 */
function permission_callback() {
  if ( can_user_react() ) {
    return true;
  }
  $message = apply_filters( 'air_reactions_not_allowed_message', __( 'Please login to react this', 'air-reactions' ) );

  return new WP_Error( 'insufficient permissions', $message );
}