<?php

/**
 * Implements hook_form_FORM_ID_form_alter().
 * Custom search block form
 *  No 'submit button'
 *  Use javascript to show/hide the 'search this site' prompt inside of the text field
 */
function mayo_form_search_block_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    unset($form['actions']['submit']);
    $form['keys']['#size'] = theme_get_setting('searchbox_size', 'mayo');
    $prompt = t('search this site');
    $form['keys']['#default_value'] = $prompt;
    $form['actions']['submit']['#type'] = 'hidden';
    $form['keys']['#attributes'] = array('onblur' => "if (this.value == '') {this.value = '{$prompt}';}", 'onfocus' => "if (this.value == '{$prompt}') {this.value = '';}" );
  }
}

