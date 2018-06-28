<?php

/**
 * Preprocess variables for region templates.
 * @param $variables
 */
function at_core_preprocess_region(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');
  $layout_loaded = new LayoutLoad($theme, $active_regions = NULL);

  // Set source order data attribute, used to set the layout classes.
  if (isset($config['layout_method']) && $config['layout_method'] === 1) {
    $region_source_order = $layout_loaded->regionSourceOrder($variables['region']);
    $variables['attributes']['data-at-region'] = $region_source_order[$variables['region']];
  }
  // BC
  else {
    $variables['attributes']['data-at-region'] = 'region-' . Html::cleanCssIdentifier($variables['region']);
  }

  // Set variable for the row this region belongs to.
  $region_row = $layout_loaded->regionAttributes($variables['region']);
  if (!empty($region_row)) {
    $variables['region_row'] = $region_row;
  }

  // Set wrapper element. Required for BC. Deprecated.
  $variables['html_element'] = 'div';

  // Extension settings
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
    if (isset($config['enable_shortcodes']) && $config['enable_shortcodes'] === 1) {
      if (!empty($config['page_classes_region_' . $variables['region']])) {
        $shortcodes = Tags::explode($config['page_classes_region_' . $variables['region']]);
        foreach ($shortcodes as $class) {
          $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
        }
      }
    }
  }
}

