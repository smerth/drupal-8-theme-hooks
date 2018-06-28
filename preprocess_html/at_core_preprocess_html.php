<?php


/**
 * Preprocess variables for html templates.
 * @param $variables
 */
function at_core_preprocess_html(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');

  // Add theme variables, we use these to set a class and provide a very easy
  // way for themers to link to files in the theme, such as background images
  // or other files.
  $variables['theme']['name'] = Html::escape($theme);
  $variables['theme']['path'] = base_path() . $variables['directory'];

  // Set the skip navigation target ID
  $variables['skip_link_target'] = '#block-' . $theme . '-content';

  // Defaults for Appearance setting variables.
  $variables['touch_icons'] = FALSE;
  // BC, deprecated.
  $variables['googlefonts_url'] = '';
  $variables['typekit_id'] = '';

  // We use this to set body classes based in the URI.
  $request = \Drupal::request();

  // Set a class for query pages, e,g, pager page 1, page 2 etc.
  $request_uri = parse_url($request->getRequestUri());
  if (isset($request_uri['query'])) {
    $query = isset($request_uri['query']) ? Html::cleanCssIdentifier(ltrim($request_uri['query'], '/')) : NULL;
    $variables['path_info']['query'] = (strlen($query) > 25) ? substr($query, 0, 25) : $query;
  }
  else {
    $variables['path_info']['query'] = NULL;
  }

  // We use this to replicate Drupal 7's path-[root_path]-[id] type classes.
  $variables['path_info']['args'] = FALSE;
  $path = $request->getPathInfo();
  $path_args = explode('/', $path);
  if (count($path_args) >= 3) {
    $variables['path_info']['args'] = Html::cleanCssIdentifier(ltrim($path, '/'));
  }

  // Extensions
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {

    // Skip navigation target setting.
    if (isset($config['skip_link_target'])) {
      $variables['skip_link_target'] = '#' . Html::cleanCssIdentifier($config['skip_link_target']);
    }

    // Apple touch icons - low, medium and high (see the Apple docs).
    if (isset($config['enable_touch_icons']) && $config['enable_touch_icons'] === 1) {
      $variables['touch_icons'] = TRUE;
      $variables['touch_rel'] = 'apple-touch-icon';

      if (isset($config['apple_touch_icon_precomposed']) && $config['apple_touch_icon_precomposed'] === 1) {
        $variables['touch_rel'] = 'apple-touch-icon-precomposed';
      }

      // Apple default icon and Nokia shortcut icon.
      if (isset($config['icon_path_default']) && !empty($config['icon_path_default'])) {
        $default_icon = file_create_url($variables['directory'] . '/' . Html::escape($config['icon_path_default']));
        $variables['touch_icon_nokia'] = $default_icon;
        $variables['touch_icon_default'] = $default_icon;
      }

      // iPad (standard display).
      if (isset($config['apple_touch_icon_path_ipad']) && !empty($config['apple_touch_icon_path_ipad'])) {
        $variables['touch_icon_ipad'] = file_create_url($variables['directory'] . '/' . $config['apple_touch_icon_path_ipad']);
      }

      // iPhone retina.
      if (isset($config['apple_touch_icon_path_iphone_retina']) && !empty($config['apple_touch_icon_path_iphone_retina'])) {
        $variables['touch_icon_iphone_retina'] = file_create_url($variables['directory'] . '/' . $config['apple_touch_icon_path_iphone_retina']);
      }

      // iPad retina.
      if (isset($config['apple_touch_icon_path_ipad_retina']) && !empty($config['apple_touch_icon_path_ipad_retina'])) {
        $variables['touch_icon_ipad_retina'] = file_create_url($variables['directory'] . '/' . $config['apple_touch_icon_path_ipad_retina']);
      }
    }

    // Shortcodes.
    if (isset($config['enable_shortcodes']) && $config['enable_shortcodes'] === 1) {
      if (!empty($config['page_classes_body'])) {
        $shortcodes = Tags::explode($config['page_classes_body']);
        foreach ($shortcodes as $class) {
          $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
        }
      }
    }
  }

  // Add all breakpoints to drupalSettings (key:media query).
  $breakpoints_module = \Drupal::moduleHandler()->moduleExists('breakpoint');
  if ($breakpoints_module == TRUE) {
    $breakpoints_array = array();
    if (isset($config['breakpoint_group_layout'])) {
      $breakpoints = \Drupal::service('breakpoint.manager')->getBreakpointsByGroup($config['breakpoint_group_layout']);

      foreach ($breakpoints as $breakpoint_key => $breakpoint_values) {
        $breakpoint_label =  strtolower($breakpoint_values->getLabel()->getUntranslatedString());
        $breakpoints_array[$breakpoint_label]['breakpoint'] = $breakpoint_key;
        $breakpoints_array[$breakpoint_label]['mediaquery'] = $breakpoint_values->getMediaQuery();
      }

      $variables['#attached']['drupalSettings'][$theme]['at_breakpoints'] = $breakpoints_array;
    }
  }
}

