<?php

/**
 * Implements hook_preprocess_block()
 */
function zurb_foundation_preprocess_block(&$variables) {
  // Convenience variable for block headers.
  $title_class = &$variables['title_attributes']['class'];

  // Generic block header class.
  $title_class[] = 'block-title';

  $region = isset($variables['configuration']['region']) ? $variables['configuration']['region'] : '';

  if ($region == 'header') {
    $title_class[] = 'visually-hidden';
  }

  // Add a unique class for each block for styling.
  if (isset($variables['attributes']['id'])) {
    $variables['attributes']['class'][] = $variables['attributes']['id'];
  }

  switch ($region) {
    // Add a striping class
    case 'sidebar_first':
    case 'sidebar_second':
      #$variables['attributes']['class'][] = 'block-' . $variables['zebra'];
    break;

    case 'header':
      $variables['attributes']['class'][] = 'header';
    break;

    default;
  }
}

