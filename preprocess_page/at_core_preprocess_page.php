<?php

/**
 * Preprocess variables for page templates.
 * @param $variables
 */
function at_core_preprocess_page(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');

  // Page classes
  $variables['attributes']['class'][] = 'page';

  // Set attribution.
  $attribution_url = Url::fromUri('http://adaptivethemes.com',
    $options = array(
      'attributes' => array(
        'class' => array('attribution__link'),
        'target' => '_blank',
      ),
      'absolute' => TRUE,
    ));
  $variables['attribution'] = array(
    '#type' => 'markup',
    '#prefix' => '<div class="l-pr attribution"><div class="l-rw">',
    '#markup' => \Drupal::l(t('Design by Adaptivethemes.com'), $attribution_url),
    '#suffix' => '</div></div>',
  );

  // Disallow access if attribution link is toggled off.
  if (isset($config['attribution_toggle']) && $config['attribution_toggle'] === 0) {
    $variables['attribution']['#access'] = FALSE;
  }

  // Process extension settings.
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
    if (isset($config['enable_shortcodes']) && $config['enable_shortcodes'] === 1) {
      if (!empty($config['page_classes_page'])) {
        $shortcodes = Tags::explode($config['page_classes_page']);
        foreach ($shortcodes as $class) {
          $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
        }
      }
    }
  }

  // Layout Attributes
  // Add dynamic classes to each region wrapper (.regions).
  // This adds two classes to every wrapper:
  //  - "arc--[n]" active region count in this row, e.g. "arc--2".
  //  - "hr--[n-n]" has regions, by source order, e.g. "hr--1-3".
  if (isset($config['layout_method']) && $config['layout_method'] === 1) {
    $variables['attributes']['class'][] = 'js-layout';
    $layout_load = new LayoutLoad($theme, $active_regions = NULL);
    $variables = $variables + $layout_load->rowAttributesJS();
  }
  else {
    $regions = system_region_list($theme, REGIONS_VISIBLE);
    $active_regions = array();
    // Render early because themes cannot check render arrays to determine
    // visibility. This is a critical issue in D8 (and D7 also):
    // https://www.drupal.org/node/953034. Early rendering is the only way I have
    // found to reliably set the layout.
    foreach ($regions as $region_name => $region_label) {
      if (!empty($variables['page'][$region_name])) {
        if ($region = \Drupal::service('renderer')->render($variables['page'][$region_name])) {
          $active_regions[] = $region_name;
        }
      }
    }
    if (!empty($active_regions)) {
      $layout_load = new LayoutLoad($theme, $active_regions);
      $variables = $variables + $layout_load->rowAttributes();
    }
  }
}

