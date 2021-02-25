
# Air Reactions

A developer-friendly WordPress-plugin for adding customizable reactions (likes, hearts, dislikes, shrugs or whatever you feel like) to your content.

## Usage

### Basic usage

Call hook `air_reactions_display` on your template file

```
do_action( 'air_reactions_display', [] );
```

It will echo the reaction container with default buttons where called. You can specify the reaction types using ['air_reactions_types' filter.](#set-default-reaction-types)

### Advanced usage

Available parameters are:
```
  [
    'echo'    => (bool),
    'types'   => (array),
    'post_id' => (int),
  ]
```
#### Example:

This will return the markup for reacting the post id '5' with only hearts
```
do_action(
  'air_reactions_display',
  [
    'echo' => false,
    'post_id' => 5,
    'types' => [ 'heart' ],
  ]
)
```

## Hooks

  ### Load default styles

  Set false to not load reaction styles from plugin, if you are going to write them in your theme

  Default: `true`

  ```
  add_filter( 'air_reactions_load_default_styles', '__return_false' );
  ```

  ### Set allowed post types

  Liking is allowed only on these post types.

  Default: `[ 'post', 'page' ]`

  ```
  add_filter( 'air_reactions_post_types', function( (array) $post_types ) {
    return [ 'post', 'page' ];
  } );
  ```

  ### Allow only registered users to react

  By default, reactions will be saved to user meta to allow users to react once per post.

  TODO: If this is set to `false`, use localstorage or [FingerprintJS](https://github.com/fingerprintjs/fingerprintjs) to set browser fingerprints to reaction events and save the reactions to the posts only.


  Default: `true`

  ```
  add_filter( 'air_reactions_only_registered_users', function( (bool) $allow_only_logged_in_users ) {
    return true;
  } );
  ```

  ### Set capability for reacting posts

  You can use any capability recognized by `current_user_can()` function

  Default: `read`

  ```
  add_filter( 'air_reactions_capability', function( (string) $capability ) {
    return 'read';
  } );
  ```

  ### Filter post reactions after they are loaded from meta and counted

  ```
  add_filter( 'air_reactions_count_post_reactions', function( (array) $post_reactions, (int) $post_id, (string) $meta_key ) {
    // Do something
    return $post_reactions;
  } )
  ```

  ### Set default reaction types

  #### Default:
  ```
  [
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
  ]
  ```
  #### Example:
  ```
  add_filter( 'air_reactions_default_types', function( (array) $default_types ) {
    return [
      'heart' => [
        'icon_path' => get_theme_file_path( 'svg/heart.svg' ),
        'texts'     => [
          'reaction'   => 'Love this post',
          'amount_pre' => 'Loved',
          'amount_post' => 'times',
        ],
      ],
    ];
  });
  ```

  ### Modify reaction item markup

  You can use this to build your own reaction item, just remember to add data-attributes like in the original, the javascript depends entirely on those. Feel free to modify the structure and classes etc.
  ```
  add_filter( 'air_reactions_reaction_item', function( (string) $output, (string) $key, (array) $item ) {
    // Do something
    return $output;
  });
  ```

  ### Modify container start and end markup

  You can change the markup or add wrapper etc. Just remember to add data-attributes like in the original, the javascript depends entirely on those.

  ```
  add_filter( 'air_reactions_container_start', function( (string) $output, (array) $args, (bool), (int) $current_user_id ) {
    // Do something
    return $output;
  });
  ```

  ```
  add_filter( 'air_reactions_container_start', function( (string) $output, (array) $args, (bool), (int) $current_user_id ) {
    // Do something
    return $output;
  });
  ```
