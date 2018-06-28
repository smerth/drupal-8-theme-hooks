<?php


/**
 * Preprocess variables for user templates.
 * @param $variables
 */
function at_core_preprocess_views_view(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');

  // Theme settings.
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {
      if (isset($config['views_hide_feedicon']) && $config['views_hide_feedicon'] === 1) {
        $variables['feed_icons'] = array();
      }
    }
  }
}