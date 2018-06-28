<?php

/**
 * @file
 * Preprocess functions for Basic.
 */

use Drupal\Core\Cache\CacheableMetadata;

/**
 * Prepares variables for the html.html.twig template.
 */
function basic_preprocess_html(&$variables) {
  try {
    $variables['is_front'] = \Drupal::service('path.matcher')->isFrontPage();
  }
  catch (Exception $e) {
    // If the database is not yet available, set default values for these
    // variables.
    $variables['is_front'] = FALSE;
  }

  // If we're on the front page.
  if (!$variables['is_front']) {
    // Add unique classes for each page and website section.
    $path = \Drupal::service('path.current')->getPath();
    $alias = \Drupal::service('path.alias_manager')->getAliasByPath($path);
    $alias = trim($alias, '/');
    $alias = str_replace('/', '-', $alias);
    if (!empty($alias)) {
      $variables['attributes']['class'][] = 'page-' . $alias;
      list($section,) = explode('/', $alias, 2);
      if (!empty($section)) {
        $variables['attributes']['class'][] = 'section-' . $section;
      }
    }
  }

  // Add cachability metadata.
  $theme_name = \Drupal::theme()->getActiveTheme()->getName();
  $theme_settings = \Drupal::config($theme_name . '.settings');
  CacheableMetadata::createFromRenderArray($variables)
    ->addCacheableDependency($theme_settings)
    ->applyTo($variables);
  // Union all theme setting variables to the html.html.twig template.
  $variables += $theme_settings->getOriginal();
}
