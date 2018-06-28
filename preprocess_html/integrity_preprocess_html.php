<?php

/**
 * Implements hook_preprocess_HOOK() for HTML document templates.
 *
 * Adds body classes if certain regions have content.
 */
function integrity_preprocess_html(&$variables) {
  // Add information about the number of sidebars.
  if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
    $variables['attributes']['class'][] = 'layout-two-sidebars';
  }
  elseif (!empty($variables['page']['sidebar_first'])) {
    $variables['attributes']['class'][] = 'layout-one-sidebar';
    $variables['attributes']['class'][] = 'layout-sidebar-first';
  }
  elseif (!empty($variables['page']['sidebar_second'])) {
    $variables['attributes']['class'][] = 'layout-one-sidebar';
    $variables['attributes']['class'][] = 'layout-sidebar-second';
  }
  else {
    $variables['attributes']['class'][] = 'layout-no-sidebars';
  }

  if (!empty($variables['page']['featured_top'])) {
    $variables['attributes']['class'][] = 'has-featured-top';
  }

}