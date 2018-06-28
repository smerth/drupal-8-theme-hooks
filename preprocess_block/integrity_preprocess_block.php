<?php

/**
 * Implements hook_preprocess_HOOK() for block.html.twig.
 */
function integrity_preprocess_block(&$variables) {
  // Add a clearfix class to system branding blocks.
  if ($variables['plugin_id'] == 'system_branding_block') {
    $variables['attributes']['class'][] = 'clearfix';
  }
}