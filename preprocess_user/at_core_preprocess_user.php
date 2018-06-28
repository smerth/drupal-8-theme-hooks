<?php

/**
 * Preprocess variables for user templates.
 * @param $variables
 */
function at_core_preprocess_user(&$variables) {
  // Add a proper label for user profiles.
  $user = $variables['elements']['#user'];
  $variables['label'] = $user->getDisplayName();
}