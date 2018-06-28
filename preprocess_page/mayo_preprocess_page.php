<?php

/**
 * Process variables for page.html.twig.
 *
 * @see page.html.twig
 */
function mayo_preprocess_page(&$variables) {

  // Add optional stylesheets
  if (theme_get_setting('menubar_style') == 2) {
    $black_menu['#attached']['library'][] = 'mayo/black-menu';
    drupal_render($black_menu);
  }

  $base_vmargin = theme_get_setting('base_vmargin', 'mayo');
    if (\Drupal::service('router.admin_context')->isAdminRoute(\Drupal::routeMatch()->getRouteObject())) $base_vmargin = '0px'; // admin page
    if (empty($base_vmargin)) $base_vmargin = '0px';
  $variables['page_wrapper_style'] = ' margin-top: ' . $base_vmargin . '; margin-bottom: ' . $base_vmargin . ';';

  $layout_style = theme_get_setting('layout_style', 'mayo');
  $page_margin = theme_get_setting('page_margin', 'mayo');
  if (empty($page_margin)) $page_margin = '0px';
  if (\Drupal::service('router.admin_context')->isAdminRoute(\Drupal::routeMatch()->getRouteObject())) $page_margin = '20px'; // admin page
  if ($layout_style == 1) {
    $variables['page_style'] = 'padding: ' . $page_margin . ';';
  }
  else {
    $variables['main_style'] = 'padding: 0px ' . $page_margin . '; box-sizing: border-box;';
  }

  $variables['header_style'] = '';

  $header_bg_file = theme_get_setting('header_bg_file', 'mayo');
  if ($header_bg_file) {
    $variables['header_style'] .= 'filter:;background: url(' . $header_bg_file . ') repeat ';
    $variables['header_style'] .= theme_get_setting('header_bg_alignment', 'mayo') . ';';
  }
  if ($layout_style == 2 || $header_bg_file) {
    // no header margin, so skip header borders to make it nicer
    $variables['header_style'] .= 'border: none;';
  }
  else {
    $header_border_width = theme_get_setting('header_border_width', 'mayo');
    $variables['header_style'] .= ' border-width: ' . $header_border_width . ';';
  }

  $header_watermark = theme_get_setting('header_watermark', 'mayo');
  if($header_watermark) {
    $variables['header_watermark_style']   = 'background-image: url(' . Url::fromUri('base:' . drupal_get_path('theme', 'mayo') . '/images/pat-' . $header_watermark . '.png')->toString() . ');';
  }

  if (theme_get_setting('header_searchbox', 'mayo') && \Drupal::moduleHandler()->moduleExists('search')) {
    $variables['header_searchbox'] = theme_get_setting('header_searchbox', 'mayo');
    $variables['output_form'] = \Drupal::formBuilder()->getForm('Drupal\search\Form\SearchBlockForm');
  }

  $variables['menubar_background'] = theme_get_setting('menubar_background', 'mayo');
  if ($variables['menubar_background'] == 1) {
    $variables['menubar_bg_value'] = 'background-color:' . theme_get_setting('menubar_bg_value', 'mayo');
  }

  $variables['header_fontsizer'] = theme_get_setting('header_fontsizer', 'mayo');
  $variables['sb_first_width'] = theme_get_setting('sidebar_first_width', 'mayo');
    if (empty($variables['sb_first_width'])) $variables['sb_first_width'] = '25%';
  $variables['sb_first_style'] = 'width: ' . $variables['sb_first_width'] . ';';
  $variables['sb_second_width'] = theme_get_setting('sidebar_second_width', 'mayo');
    if (empty($variables['sb_second_width'])) $variables['sb_second_width'] = '25%';
  $variables['sb_second_style'] = 'width: ' . $variables['sb_second_width'] . ';';

  $content_width = 100;
  if ($variables['page']['sidebar_first']) {
    $content_width -= intval(preg_replace('/%/', '', $variables['sb_first_width']));
  }
  if ($variables['page']['sidebar_second']) {
    $content_width -= intval(preg_replace('/%/', '', $variables['sb_second_width']));
  }
  $variables['content_style'] = 'width: ' . $content_width . '%;';

  if (theme_get_setting('header_fontsizer', 'mayo')) {
    $font_resize['#attached']['library'][] = 'mayo/fontsizer';
    drupal_render($font_resize);
  }

  $page = $variables['page'];
  // Attach javascript for equal height columns.
  if ($page['top_column_first'] ||
      $page['top_column_second'] ||
      $page['top_column_third'] ||
      $page['top_column_fourth'] ||
      $page['bottom_column_first'] ||
      $page['bottom_column_second'] ||
      $page['bottom_column_third'] ||
      $page['bottom_column_fourth'] ||
      $page['footer_column_first'] ||
      $page['footer_column_second'] ||
      $page['footer_column_third'] ||
      $page['footer_column_fourth']) {
  $libraries['#attached']['library'][] = 'mayo/mayo-columns';
  drupal_render($libraries);
  }

  $variables['top_columns_width'] =  mayo_build_columns_width( array(
            $page['top_column_first'],
            $page['top_column_second'],
            $page['top_column_third'],
            $page['top_column_fourth'],
          ));
  $variables['bottom_columns_width'] =  mayo_build_columns_width( array(
            $page['bottom_column_first'],
            $page['bottom_column_second'],
            $page['bottom_column_third'],
            $page['bottom_column_fourth'],
          ));
  $variables['footer_columns_width'] =  mayo_build_columns_width( array(
            $page['footer_column_first'],
            $page['footer_column_second'],
            $page['footer_column_third'],
            $page['footer_column_fourth'],
          ));

  // Pass the main menu and secondary menu to the template.
  if (!empty($variables['main_menu'])) {
    $variables['main_menu']['#attributes']['id'] = 'main-menu';
    $variables['main_menu']['#attributes']['class'] = array('links', 'inline', 'clearfix');
  }
  if (!empty($variables['secondary_menu'])) {
    $variables['secondary_menu']['#attributes']['id'] = 'secondary-menu';
    $variables['secondary_menu']['#attributes']['class'] = array('links', 'inline', 'clearfix');
  }

  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut']) && $variables['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render elements.
    $variables['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $variables['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $variables['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;
  }
}




