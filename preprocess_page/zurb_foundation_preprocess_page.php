<?php

/**
 * Implements template_preprocess_page
 *
 * Add convenience variables and template suggestions
 */
function zurb_foundation_preprocess_page(&$variables) {
  $site_name = isset($variables['site_name']) ? $variables['site_name'] : \Drupal::config('system.site')->get('name');

  // Add page--node_type.html.twig suggestions
  if (!empty($variables['node'])) {
    $variables['theme_hook_suggestions'][] = 'page__' . $variables['node']->bundle();
  }

  $variables['logo_img'] = '';

  $logo = theme_get_setting('logo.url');

  if (!empty($logo)) {
    $elements = array(
      '#theme' => 'image',
      '#uri' => $logo,
      '#attributes' => array(
        'alt' => strip_tags($site_name) . ' ' . t('logo'),
        'title' => strip_tags($site_name) . ' ' . t('Home'),
        'class' => array('logo'),
      )
    );

    $variables['logo_img'] = $elements;
  }

  $title = strip_tags($site_name) . ' ' . t('Home');
  $url = \Drupal\Core\Url::fromRoute('<front>');
  $url->setOption('attributes', array('title' => $title));

  if (theme_get_setting('zurb_foundation_page_site_logo')) {
    $variables['linked_logo'] = '';
    if (!empty($variables['logo_img'])) {
      $variables['linked_logo'] = \Drupal::l($variables['logo_img'], $url);
    }
  }

  if (theme_get_setting('zurb_foundation_page_site_name')) {
    $variables['linked_site_name'] = '';
    if (!empty($site_name)) {
      $variables['linked_site_name'] = \Drupal::l($site_name, $url);
    }
  }

  // Convenience variables
  $left = $variables['page']['sidebar_first'];
  $right = $variables['page']['sidebar_second'];

  // Dynamic sidebars
  if (!empty($left) && !empty($right)) {
    $variables['main_grid'] = 'large-6 large-push-3';
    $variables['sidebar_first_grid'] = 'large-3 large-pull-6';
    $variables['sidebar_sec_grid'] = 'large-3';
  }
  elseif (empty($left) && !empty($right)) {
    $variables['main_grid'] = 'large-9';
    $variables['sidebar_first_grid'] = '';
    $variables['sidebar_sec_grid'] = 'large-3';
  }
  elseif (!empty($left) && empty($right)) {
    $variables['main_grid'] = 'large-9 large-push-3';
    $variables['sidebar_first_grid'] = 'large-3 large-pull-9';
    $variables['sidebar_sec_grid'] = '';
  }
  else {
    $variables['main_grid'] = 'large-12';
    $variables['sidebar_first_grid'] = '';
    $variables['sidebar_sec_grid'] = '';
  }

  // Add classes to highlighted region.
  if (!empty($variables['page']['highlighted'])) {
    $variables['page']['highlighted']['#attributes']['class'][] = 'region-highlighted';
    $variables['page']['highlighted']['#attributes']['class'][] = 'panel';
    $variables['page']['highlighted']['#attributes']['class'][] = 'callout';
  }

  // Check to see if the Meta Header should be in the Grid.
  $variables['meta_header_grid'] = theme_get_setting('zurb_foundation_meta_header_grid');

  // Make sure site_name is always set, in case there's only a default.
  $variables['site_name'] = $site_name;

  // Variable to disable hard-coded login elements.
  $variables['show_account_info'] = theme_get_setting('zurb_foundation_page_account_info');
}



