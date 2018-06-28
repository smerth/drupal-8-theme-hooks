<?php

/**
 * Implements hook_preprocess_html().
 */
function mayo_preprocess_html(&$variables) {

  // Add information about the number of sidebars.
  if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
    $variables['attributes']['class'][] = 'two-sidebars';
  }
  elseif (!empty($variables['page']['sidebar_first'])) {
    $variables['attributes']['class'][] = 'one-sidebar';
    $variables['attributes']['class'][] = 'sidebar-first';
  }
  elseif (!empty($variables['page']['sidebar_second'])) {
    $variables['attributes']['class'][] = 'one-sidebar';
    $variables['attributes']['class'][] = 'sidebar-second';
  }
  else {
    $variables['attributes']['class'][] = 'no-sidebars';
  }

  $theme_name = \Drupal::theme()->getActiveTheme()->getName();
  // Adds classes to <body class="">
  // See https://drupal.org/node/1727592
  $settings_array = array(
    'round_corners',
    'dark_messages',
  );
  foreach ($settings_array as $setting) {
    if (theme_get_setting($setting) !== 0) {
      $variables['attributes']['class'][] = theme_get_setting($setting);
    }
  }
  // Add inline body styles to head for font family and size.
  $font_family = array(
  // Added Japanese font support
    0 => "font-family: Georgia, 'Palatino Linotype', 'Book Antiqua', 'URW Palladio L',
      Baskerville, Meiryo, 'Hiragino Mincho Pro', 'MS PMincho', serif; ",
    1 => "font-family: Verdana, Geneva, Arial, 'Bitstream Vera Sans', 'DejaVu Sans',
      Meiryo, 'Hiragino Kaku Gothic Pro', 'MS PGothic', Osaka, sans-serif; ",
  );
  // Add font related inline styles
  $base_font_size = theme_get_setting('base_font_size');
  $style = 'font-size: ' . $base_font_size . '; ';
  $base_font_family = theme_get_setting('base_font_family');
  if ($base_font_family == 2) { // Custom
    $style .= 'font-family: ' . Xss::filterAdmin(theme_get_setting('base_custom_font_family')) . ';';
  }
  else {
    $style .= $font_family[$base_font_family];
  }

  $heading_font_family = theme_get_setting('heading_font_family');
  $style_b = '';
  if ($heading_font_family == 2) {  //Custom
    $style_b .= 'font-family: ' . Xss::filterAdmin(theme_get_setting('heading_custom_font_family')) . ';';
  }
  else {
    $style_b = $font_family[$heading_font_family];
  }
  $variables['base_font'] = "\r<style media=\"all\">\r/* <![CDATA[ */\r\nbody {" . $style . "}\r\nh1,h2,h3,h4,h5 {" . $style_b . "}\r\n/* ]]> */\r\n</style>";

  if ($heading_font_family == 1) {
    $variables['base_font'] = "\r<style media=\"all\">\r/* <![CDATA[ */\r\nbody {" . $style . "}\r\nh1,h2,h3,h4,h5 {" . $style_b . "}\r\n.sidebar h2 { font-size: 1.2em; }\r\n#content .node h2 { font-size: 1.4em; }\r\n/* ]]> */\r\n</style>";
  }
}

