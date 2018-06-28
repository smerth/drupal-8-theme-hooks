<?php 


/**
 * Preprocess variables for comment templates.
 * @param $variables
 */
function at_core_preprocess_comment(&$variables) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');

  // Initialize new attributes arrays.
  $variables['meta_attributes'] = new Attribute(array('class' => array()));
  $variables['meta_attributes']['class'][] = 'comment__meta';

  $variables['submitted_attributes'] = new Attribute(array('class' => array()));
  $variables['submitted_attributes']['class'][] = 'comment__submitted';

  // Add a class if user picture is printing. Render early.
  if ($user_picture = \Drupal::service('renderer')->render($variables['user_picture'])) {
    // TODO - does this fail if twig debugging is on?
    if (!empty($user_picture)) {
      $variables['meta_attributes']['class'][] = 'comment__meta--has-user-picture';
    }
  }

  // Use permalink URI as the title link.
  $comment = $variables['elements']['#comment'];
  if (!isset($comment->in_preview)) {
    $uri = $comment->permalink();
    $attributes = $uri->getOption('attributes') ?: array();
    $attributes += array('class' => array('permalink'), 'rel' => 'bookmark');
    $uri->setOption('attributes', $attributes);
    $variables['title'] = \Drupal::l($comment->getSubject(), $uri);
  }

  // Hide comment titles.
  $variables['title_visibility'] = TRUE;
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {
      if (isset($config['comments_hide_title']) && $config['comments_hide_title'] === 1) {
        $variables['title_visibility'] = FALSE;
      }
    }
  }
}

