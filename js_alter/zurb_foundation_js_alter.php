<?php

/**
 * Implements hook_js_alter().
 * @param $js
 */
function zurb_foundation_js_alter(&$js) {
  // Remove base theme JS.
  $theme_path = drupal_get_path('theme', 'zurb_foundation');
  if(theme_get_setting('zurb_foundation_disable_base_js') == TRUE) {

    foreach($js as $path => $values) {
      if(strpos((string) $values['data'], $theme_path) === 0) {
        unset($js[$path]);
      }
    }
  }

  // Increase weight of the JS include that sets proper Active classes for the
  // Topbar.
  if (isset($js[$theme_path . '/js/top_bar_active.js'])) {
    $js[$theme_path . '/js/top_bar_active.js']['weight'] = 1;
  }
}