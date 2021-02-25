<?php
/**
 * Plugin setup
 *
 * @package air-reactions
 */

namespace Air_Reactions;

/**
 * Register plugin scripts
 */
function register_scripts() {
  // $script_version = 'development' === wp_get_environment_type() ? filemtime( untrailingslashit( plugin_dir_path( __DIR__ ) ) . 'dist/app.js' ) : PLUGIN_VERSION;

  $script_version = filemtime( plugin_dir_path( __DIR__ ) . 'dist/app.js' );

  wp_register_script(
    'air-reactions',
    plugin_dir_url( __DIR__ ) . 'dist/app.js',
    [],
    $script_version,
    true
  );
  wp_localize_script(
    'air-reactions',
    'airReactionsApi',
    [
      'url' => esc_url_raw( rest_url( REST_NAMESPACE ) ),
      'nonce' => wp_create_nonce( 'wp_rest' ),
    ]
  );

  wp_register_style(
    'air-reactions',
    plugin_dir_url( __DIR__ ) . 'css/style.css',
    [],
    filemtime( plugin_dir_path( __DIR__ ) . 'css/style.css' )
  );
}

/**
 * Register shortcode
 *
 * @param string|array $atts Shortcode atts
 */
function register_shortcode( $atts ) {
  $args = [];

  if ( is_array( $atts ) ) {
    foreach ( $atts as $key => $value ) {
      $parsed_value = null;
      if ( 'types' === $key ) {
        $value_array = explode( ',', $value );
        $parsed_value = array_map( function ( $value ) {
          return trim( $value );
        }, $value_array );
      } else if ( 'post_id' === $key ) {
        $parsed_value = intval( $value );
      } else {
        $parsed_value = $value;
      }
      $args[ $key ] = $parsed_value;
    }
  }

  $args['echo'] = false;
	return the_output( (array) $args );
}
