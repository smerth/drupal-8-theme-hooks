<?php

/**
 * Preprocess variables for forums templates.
 * @param $variables
 */
function at_core_preprocess_forums(&$variables) {
  // Add a class to each forum topic table header td.
  if (isset($variables['topics']['#header'])) {
    foreach ($variables['topics']['#header'] as $topic_list_key => $topic_list_value) {
      $variables['topics']['#header'][$topic_list_key]['class'][] = 'forum-header__' . Html::cleanCssIdentifier($variables['topics']['#header'][$topic_list_key]['data']);
    }
  }

  // Add BEM classes to row items to match the forum-list.html.twig BEM classes.
  if (isset($variables['topics']['#rows'])) {
    foreach ($variables['topics']['#rows'] as $row_key => $row_values) {
      foreach ($row_values as $row_values_key => $row_values_value) {
        foreach ($row_values_value['class'] as $class_key => $class_value) {
          $variables['topics']['#rows'][$row_key][$row_values_key]['class'][] = 'forum-list__' . Html::cleanCssIdentifier($class_value);
          unset($variables['topics']['#rows'][$row_key][$row_values_key]['class'][$class_key]);
        }
      }
    }
  }
}