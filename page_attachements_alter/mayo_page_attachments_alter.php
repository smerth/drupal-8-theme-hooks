<?php

/**
 * Implements hook_page_attachments_alter().
 */
function mayo_page_attachments_alter(&$page) {
  global $theme_name;

  if (theme_get_setting('menubar_style', 'mayo') == 2) {
    $page['#attached']['library'][] = 'mayo/black-menu';
  }
  // Get the path to the directory where our responsive.layout.css file is saved.
  $path = \Drupal::configFactory()->getEditable('mayo.settings')->get('theme_' . $theme_name . '_files_directory');
  // Load the responsive layout
  $filepath = $path . '/' . $theme_name . '.responsive.layout.css';
  //$media_query = 'only screen'; // keyword "only" hide this from unsupporting user agents
  if (file_exists($filepath)) {
    $page['#attached']['library'][] = $theme_name . '/responsive-layout';
  }
}