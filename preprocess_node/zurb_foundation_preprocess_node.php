<?php

/**
 * Implements template_preprocess_node
 *
 * Add template suggestions and classes
 */
function zurb_foundation_preprocess_node(&$variables) {
  // Add node--node_type--view_mode.html.twig suggestions
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['elements']['#node']->bundle() . '__' . $variables['view_mode'];

  // Add node--view_mode.html.twig suggestions
  $variables['theme_hook_suggestions'][] = 'node__' . $variables['view_mode'];

  // Add a class for the view mode.
  if (!$variables['teaser']) {
    $variables['content_attributes']['class'][] = 'view-mode-' . $variables['view_mode'];
  }

  $variables['title_attributes']['class'][] = 'node-title';
}