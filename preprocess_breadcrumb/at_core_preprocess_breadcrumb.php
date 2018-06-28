<?php 

/**
 * Preprocess variables for breadcrumb templates.
 * @param $variables
 */
function at_core_preprocess_breadcrumb(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');

  // New attributes array for breadcrumb title.
  $variables['title_attributes'] = new Attribute(array('class' => array()));

  // Set attributes.
  $variables['breadcrumb_title_hidden'] = TRUE;

  // If home is the only item, remove it.
  $arr_length = count($variables['breadcrumb']);
  if ($arr_length == 1 && $variables['breadcrumb'][0]['url'] == base_path()) {
    unset($variables['breadcrumb'][0]);
  }

  // Theme settings.
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {

      // Show or hide the label.
      if (isset($config['breadcrumb_label']) && $config['breadcrumb_label'] === 1) {
        $variables['breadcrumb_title_hidden'] = FALSE;
      }

      // Show or hide the Home link.
      if (isset($config['breadcrumb_home']) && $config['breadcrumb_home'] === 1) {
        $first_item = array_values($variables['breadcrumb'])[0];
        if (isset($first_item['url']) && $first_item['url'] == base_path()) {
          array_shift($variables['breadcrumb']);
        }
      }
    }
  }
}

