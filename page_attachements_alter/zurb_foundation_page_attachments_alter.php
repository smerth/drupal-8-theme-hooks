<?php 

/**
 * Implements hook_page_attachments_alter
 *
 * Add custom meta tags to the header.
 */
function zurb_foundation_page_attachments_alter(&$page) {
  // Optimize mobile viewport.
  $page['#attached']['html_head'][] = array(array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
        'name' => 'viewport',
        'content' => 'width=device-width',
      ),
  ), 'mobile_viewport');

  // Force IE to use Chrome Frame if installed.
  $page['#attached']['html_head'][] = array(array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'content' => 'ie=edge, chrome=1',
      'http-equiv' => 'x-ua-compatible',
    ),
  ), 'chrome_frame');

  // Remove image toolbar in IE.
  $page['#attached']['html_head'][] = array(array(
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => array(
      'http-equiv' => 'ImageToolbar',
      'content' => 'false',
    ),
  ), 'ie_image_toolbar');
}



