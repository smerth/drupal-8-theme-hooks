<?php

/**
 * Implements template_preprocess_field().
 */
function zurb_foundation_preprocess_field(&$variables) {
  if (!isset($variables['title_attributes']['class'])) {
    $variables['title_attributes']['class'] = array();
  }
  if (!isset($variables['content_attributes']['class'])) {
    $variables['content_attributes']['class'] = array();
  }
  $variables['title_attributes']['class'][] = 'field-label';

  // Convenience variables
  $mode = $variables['element']['#view_mode'];
  $classes = &$variables['attributes']['class'];
  $content_classes = &$variables['content_attributes']['class'];
  $item_classes = array();

  // Global field classes
  $classes[] = 'field-wrapper';
  $content_classes[] = 'field-items';
  $item_classes[] = 'field-item';

  // Add specific classes to targeted fields
  if(isset($field)) {
    switch ($mode) {
      // All teasers
      case 'teaser':
        switch ($field) {
          // Teaser read more links
          case 'node_link':
            $item_classes[] = 'more-link';
            break;
          // Teaser descriptions
          case 'body':
          case 'field_description':
            $item_classes[] = 'description';
            break;
        }
      break;
    }
  }

  // Apply odd or even classes along with our custom classes to each item
  foreach ($variables['items'] as $delta => $item) {
    $item_classes[] = $delta % 2 ? 'odd' : 'even';
    $variables['item_attributes'][$delta]['class'] = $item_classes;
  }

  // Add class to a specific fields across content types.
  switch ($variables['element']['#field_name']) {
    case 'body':
      $classes = array('body');
      break;

    case 'field_summary':
      $classes[] = 'text-teaser';
      break;

    case 'field_link':
    case 'field_date':
      // Replace classes entirely, instead of adding extra.
    $classes = array('text-content');
      break;

    case 'field_image':
      // Replace classes entirely, instead of adding extra.
      $classes = array('image');
      break;

    default:
      break;
  }

  // Add classes to body based on content type and view mode.
  if ($variables['element']['#field_name'] == 'body') {
    // Add classes to other content types with view mode 'teaser';
    if ($variables['element']['#view_mode'] == 'teaser') {
      $classes[] = 'text-secondary';
    }
    // The rest is text-content.
    else {
      $classes[] = 'field';
    }
  }
}

