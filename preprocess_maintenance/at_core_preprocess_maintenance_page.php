<?php

/**
 * Implements hook_preprocess_HOOK() for maintenance-page.html.twig.
 * @param $variables
 */
function at_core_preprocess_maintenance_page(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  // Load the maintenance library CSS.
  $variables['#attached']['library'][] = $theme . '/maintenance_page';
}