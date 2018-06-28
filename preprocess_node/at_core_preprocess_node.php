<?php

/**
 * Preprocess variables for node templates.
 * @param $variables
 */
function at_core_preprocess_node(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');
  $node = $variables['node'];

  // Extension settings
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
    if (isset($config['enable_shortcodes']) && $config['enable_shortcodes'] === 1) {
      if (!empty($config['nodetype_classes_' . $node->getType()])) {
        $shortcodes = Tags::explode($config['nodetype_classes_' . $node->getType()]);
        foreach ($shortcodes as $class) {
          $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
        }
      }
    }
  }

  // Header and Footer attributes.
  $variables['header_attributes'] = new Attribute(array('class' => array()));
  $variables['footer_attributes'] = new Attribute(array('class' => array()));

  // SEE https://drupal.org/node/2004252 or a follow up issue.
  if ($variables['display_submitted']) {

    // Add a class to the header if submitted is active, so we can theme dynamically.
    $variables['header_attributes']['class'][] = 'node__header--has-meta';

    // Initialize new attributes arrays.
    $variables['meta_attributes'] = new Attribute(array('class' => array()));
    $variables['meta_attributes']['class'][] = 'node__meta';

    $variables['submitted_attributes'] = new Attribute(array('class' => array()));
    $variables['submitted_attributes']['class'][] = 'node__submitted';

    // Add a class if author picture is printing.
    // TODO - does this break the entity render cache?
    if ($author_picture = \Drupal::service('renderer')->render($variables['author_picture'])) {
      // TODO - does this fail if twig debugging is on?
      if (!empty($author_picture)) {
        $variables['meta_attributes']['class'][] = 'node__meta--has-author-picture';
      }
    }
  }
}

