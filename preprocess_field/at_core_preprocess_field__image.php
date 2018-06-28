<?php


/**
 * Preprocess variables for image field templates.
 * @param $variables
 */
function at_core_preprocess_field__image(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');

  // Extension settings.
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
    if (isset($config['enable_images']) && $config['enable_images'] === 1) {

      $entity_type = $variables['element']['#entity_type'];
      $node_type = $variables['element']['#bundle'];
      $view_mode = $variables['element']['#view_mode'];

      // Reset items array to first item only. This causes only the first image
      // to be shown, e.g. in teaser view mode.
      if (isset($config['image_count_' . $node_type . '_' . $entity_type . '_' . $view_mode]) && $config['image_count_' . $node_type . '_' . $entity_type . '_' . $view_mode] === 1) {
        $item = reset($variables['items']);
        $variables['items'] = array($item);
      }

      // Captions.
      if (isset($config['image_captions_' . $node_type . '_' . $entity_type . '_' . $view_mode]) && $config['image_captions_' . $node_type . '_' . $entity_type . '_' . $view_mode] === 1) {
        foreach ($variables['items'] as $delta => $item) {
          $values = $item['content']['#item']->getValue();
          if (!empty($values['title'])) {
            $variables['items'][$delta]['caption'] = array(
              'show' => TRUE,
              'title' => $values['title'],
            );
          }
          else {
            $variables['items'][$delta]['caption'] = array(
              'show' => FALSE,
            );
          }
        }
      }

      // Image align class, provide a variable for use in field template.
      $variables['image_align'] = 'align-none';
      if (!empty($config['image_alignment_' . $node_type . '_' . $entity_type . '_' . $view_mode])) {
        $variables['image_align'] = 'align-' . $config['image_alignment_' . $node_type . '_' . $entity_type . '_' . $view_mode];
      }

      // This is quite aggressive and it may be better to let the site fail?
      foreach ($variables['items'] as $delta => $item) {
        if (empty($item['content']['#image_style'])) {
          $variables['attributes']['class'][] = 'float-none';
        }
      }
    }
  }
}



