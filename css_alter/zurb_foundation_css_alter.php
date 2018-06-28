<?php

/**
 * Implements hook_css_alter().
 * @param $css
 */
function zurb_foundation_css_alter(&$css) {
  // Remove base theme CSS.
  if(theme_get_setting('zurb_foundation_disable_base_css') == TRUE) {
    $theme_path = drupal_get_path('theme', 'zurb_foundation');

    foreach($css as $path => $values) {
      if(strpos((string) $values['data'], $theme_path) === 0) {
        unset($css[$path]);
      }
    }
  }
}
