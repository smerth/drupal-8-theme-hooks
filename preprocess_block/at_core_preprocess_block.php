<?php

/**
 * Preprocess variables for block templates.
 * @param $variables
 */
function at_core_preprocess_block(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');

  /* Use PNG logo in branding block
    switch ($variables['base_plugin_id']) {
      case 'system_branding_block':
        $variables['site_logo'] = '';
        if ($variables['content']['site_logo']['#access'] && $variables['content']['site_logo']['#uri']) {
          $variables['site_logo'] = str_replace('.svg', '.png', $variables['content']['site_logo']['#uri']);
        }
        break;
    }
  */

  // Extension settings.
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
    // Markup overrides
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {
      // Remove login block links.
      if ($variables['base_plugin_id'] == 'user_login_block') {
        if ((isset($config['login_block_remove_links']) && $config['login_block_remove_links'] === 1) || (isset($config['horizontal_login_block']) && $config['horizontal_login_block'] === 1)) {
          unset($variables['content']['user_links']);

          // Add class for horizontal login.
          if (isset($config['horizontal_login_block']) && $config['horizontal_login_block'] === 1) {
            $variables['attributes']['class'][] = 'is-horizontal';
          }
        }
      }
    }

    // Shortcodes classes.
    if (isset($config['enable_shortcodes']) && $config['enable_shortcodes'] === 1) {
      if (!empty($config['block_classes_' . $variables['elements']['#id']])) {
        $shortcodes = Tags::explode($config['block_classes_' . $variables['elements']['#id']]);
        foreach ($shortcodes as $class) {
          $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
        }
      }
    }
  }
}






