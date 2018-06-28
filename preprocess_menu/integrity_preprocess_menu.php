<?php


/**
 * Implements hook_preprocess_HOOK() for menu.html.twig.
 */
function integrity_preprocess_menu(&$variables) {
  $variables['attributes']['class'][] = 'clearfix';
}