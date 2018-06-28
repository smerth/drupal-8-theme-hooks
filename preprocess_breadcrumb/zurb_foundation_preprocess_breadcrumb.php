<?php

/**
 * Implements hook_preprocess_breadcrumb().
 *
 * Adds the "title" variable so that the current page can be added as a breadcrumb.
 */
function zurb_foundation_preprocess_breadcrumb(&$variables) {
  $request = \Drupal::request();
  $route_match = \Drupal::routeMatch();
  $title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());

  $variables['title'] = $title;
}

