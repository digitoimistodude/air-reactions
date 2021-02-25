<?php
/**
 * Plugin Name: Air Reactions
 * Description: A developer-friendly WordPress-plugin for adding customizable reactions (reactions, hearts, disreactions, shrugs or whatever you feel reaction) to your content.
 * Version: 0.2
 * Author: Digitoimisto Dude Oy, Niku Hietanen
 * Author URI: https://www.dude.fi
 * Requires at least: 5.0
 * Tested up to: 5.6
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: air-reactions
 * Domain Path: /languages
 *
 * @package air-reactions
 */

namespace Air_Reactions;

if ( ! defined( 'ABSPATH' ) ) {
  exit();
}

const PLUGIN_VERSION = '0.2';
const META_FIELD_KEY = '_air-reactions';
const REST_NAMESPACE = 'air-reactions/v1';
const REST_ROUTE = 'add-reaction/';

/**
 * Plugin setup, script registering etc.
 */
require plugin_dir_path( __FILE__ ) . 'inc/setup.php';
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_scripts' );
add_shortcode( 'air-reactions', __NAMESPACE__ . '\register_shortcode' );

/**
 * API related
 */
require plugin_dir_path( __FILE__ ) . 'inc/api.php';
add_action( 'rest_api_init', __NAMESPACE__ . '\register_reaction_api' );

/**
 * Helper functions
 */
require plugin_dir_path( __FILE__ ) . 'inc/helpers.php';

/**
 * Plugin output
 */
require plugin_dir_path( __FILE__ ) . 'inc/output.php';
add_action( 'air_reactions_display', __NAMESPACE__ . '\the_output', 1, 2 );
