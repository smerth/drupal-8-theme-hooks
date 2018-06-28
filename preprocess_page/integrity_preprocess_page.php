<?php




/**
 * Implements hook_preprocess_HOOK() for page templates.
 */
function integrity_preprocess_page(&$variables) {

  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut']) && $variables['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render
    // elements.
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

  $slider_contents = array();
  $resp_contents = array();

  // Slider block contents
  $slider_contents = _integrity_get_home_block_contents('slider');

  $variables['base_path'] = base_path();
  $variables['logo'] = THEME_PATH . '/logo.png';
  if (theme_get_setting('slideshow_display')) {
    $variables['slider_contents'] = $slider_contents;
  }
}


/**
 * Function to get home page contents for the slider and responsive block in front page
 */
function _integrity_get_home_block_contents($sec = '') {

  $slider_content = array();
    for ($i = 1; $i <= SLIDESHOW_COUNT; $i++) {

      $fid = theme_get_setting('slide_image_path'.$i,'integrity');
      $file = file_load($fid[0]);
      if (!empty($file)) {
        $uri = $file->getFileUri();
        $path = file_create_url($uri);
      }
      else {

        $path = base_path() . drupal_get_path('theme', 'integrity') . theme_get_setting('slide_image_path_' . $i, 'integrity');
      }


     $active_class = $i == 1 ? 'active' : 'in-active';
     $slider_content[$i] = '<div class="item ' . $active_class . ' demo-text-goes-top">
      <div class="content-wrapper">
        <div class="slide-body blue-container">
          <h2>'.wordwrap(theme_get_setting('slide_title_' . $i, 'integrity'), 50, "<br>").'.</h2>
          <p>'.wordwrap(theme_get_setting('slide_description_'. $i, 'integrity'), 60, "<br>").'</p>
          <div class="demo-devices">
            <div class="ipad">
              <div class="content">
                <img src=' . $path . ' class="retina" width="181" height="241">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>';
      }


  $image_list = array();
  switch ($sec) {
    case 'slider':
      $image_list = $slider_content;
      break;
  }
  return $image_list;
}

