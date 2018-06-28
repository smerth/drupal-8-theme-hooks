<?php

/**
 * Implements template_preprocess_html().
 *
 * Adds additional classes
 */
function zurb_foundation_preprocess_html(&$variables) {
  /* @var Drupal\Core\Language\LanguageInterface */
  $language = \Drupal::languageManager()->getCurrentLanguage();
  $site_language = $language->getId();
  $site_language_direction = $language->getDirection();
  $request = \Drupal::request();

  // Clean up the lang attributes
  $variables['html_attributes'] = 'lang="' . $site_language . '" dir="' . $site_language_direction . '"';

  // Add language body class.
  $variables['attributes']['class'][] = 'lang-' . $site_language;

  // Classes for body element. Allows advanced theming based on context
  $is_front_page = \Drupal::service('path.matcher')->isFrontPage();
  if (!$is_front_page) {
    $path = trim($request->getRequestUri(), '/');
    // Add unique class for each website section.
    $arg = explode('/', $path);
    $section = $arg[0];
    if ($arg[0] == 'node' && isset($arg[1])) {
      if ($arg[1] == 'add') {
        $section = 'node-add';
      }
      elseif (isset($arg[2]) && is_numeric($arg[1]) && ($arg[2] == 'edit' || $arg[2] == 'delete')) {
        $section = 'node-' . $arg[2];
      }
    }
    $variables['attributes']['class'][] = \Drupal\Component\Utility\Html::getClass('section-' . $section);
  }

  // Store the menu item since it has some useful information.
  if ($request->attributes->get('view_id')) {
    $variables['attributes']['class'][] = 'views-page';
  }
  elseif ($request->attributes->get('panel')) {
    $variables['attributes']['class'][] = 'panels-page';
  }
}


