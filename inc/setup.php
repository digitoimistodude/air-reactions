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
