<?php
/**
 * @file
 * Functions to support theming in Adaptivetheme sub-themes.
 */

use Drupal\Core\Url;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Html;
use Symfony\Component\Yaml\Parser;
use Drupal\at_core\Layout\LayoutLoad;

/**
 * Alter attachments (typically assets) to a page before it is rendered.
 *
 * Use this hook when you want to remove or alter attachments on the page, or
 * add attachments to the page that depend on another module's attachments (this
 * hook runs after hook_page_attachments().
 *
 * @param array &$page
 *   An empty renderable array representing the page.
 *
 * @see hook_page_attachments_alter()
 */
function at_core_page_attachments_alter(array &$page) {
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $config = \Drupal::config($theme . '.settings')->get('settings');

  if ($theme === 'seven') {
    return;
  }

  // Common paths.
  $path_to_theme = \Drupal::theme()->getActiveTheme()->getPath();
  $generated_files_path = NULL;

  if (!empty($config['generated_files_path'])) {
    $generated_files_path = $config['generated_files_path'];
  }
  else {
    drupal_set_message(t('The path to generated CSS files is not saved in configuration, try saving your themes Appearance settings.'), 'error');
  }

  // Attach at.settings, we need the ajaxPageState theme name.
  $page['#attached']['library'][] = 'at_core/at.settings';

  // Attach the JS layout script if required.
  if (isset($config['layout_method']) && $config['layout_method'] === 1) {
    $page['#attached']['library'][] = 'at_core/at.layout';
  }

  // Attach module dependant libraries.
  // These libraries are declared in your themeName.libraries.yml and we only
  // load if the module is installed. Unlike core these files will load on every
  // page, so when CSS aggregation is on there will only be one CSS aggregate
  // for the theme.
  $module_libraries = array(
    'aggregator',
    'book',
    'comment',
    'contact',
    'forum',
    'search',
    'taxonomy',
  );
  foreach ($module_libraries as $module_library) {
    if (\Drupal::moduleHandler()->moduleExists($module_library) == TRUE) {
      $page['#attached']['library'][] = "$theme/$module_library";
    }
  }

  // Process extension settings.
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {

    // Fonts.
    if (isset($config['enable_fonts']) && $config['enable_fonts'] === 1) {
      // Fonts generated CSS.
      if (file_exists($generated_files_path . '/fonts.css')) {
        $page['#attached']['library'][] = "$theme/fonts";
      }
      // Google font URL.
      if (isset($config['font_use_google_fonts']) && $config['font_use_google_fonts'] == 1) {
        $page['#attached']['library'][] = "$theme/google_fonts";
      }
      // Typekit ID and JS.
      if (isset($config['font_use_typekit']) && $config['font_use_typekit'] == 1) {
        $page['#attached']['library'][] = "$theme/typekit_id";
        $page['#attached']['library'][] = 'at_core/at.typekit';
      }
    }

    // Titles.
    if (isset($config['enable_titles']) && $config['enable_titles'] === 1) {
      if (file_exists($generated_files_path . '/title-styles.css')) {
        $page['#attached']['library'][] = "$theme/title_styles";
      }
    }

    // Mobile blocks
    if (isset($config['enable_mobile_blocks']) && $config['enable_mobile_blocks'] === 1) {
      if (file_exists($generated_files_path . '/mobile-blocks.css')) {
        $page['#attached']['library'][] = "$theme/mobile_blocks";
      }
    }

    // Custom CSS.
    if (isset($config['enable_custom_css']) && $config['enable_custom_css'] === 1) {
      if (file_exists($generated_files_path . '/custom-css.css')) {
        $page['#attached']['library'][] = "$theme/custom_css";
      }
    }

    // Markup Overrides
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {

      // Responsive tables
      if (isset($config['responsive_tables']) && $config['responsive_tables'] === 1) {
        $page['#attached']['library'][] = "$theme/responsive_tables";
      }

      // Breadcrumbs.
      if (!empty($config['breadcrumb_separator'])) {
        if (file_exists($generated_files_path . '/breadcrumb.css')) {
          $page['#attached']['library'][] = "$theme/breadcrumb";
        }
      }

      // Login block.
      // Just load the login block CSS without the currentUser check.
      if (isset($config['horizontal_login_block']) && $config['horizontal_login_block'] === 1) {
        if (file_exists($generated_files_path . '/login-block.css')) {
          $page['#attached']['library'][] = "$theme/login_block";
        }
      }
    }

    // Devel assets.
    if (isset($config['enable_devel']) && $config['enable_devel'] === 1) {

      // Attach Windowsize library
      if (isset($config['show_window_size']) && $config['show_window_size'] === 1) {
        $page['#attached']['library'][] = 'at_core/at.windowsize';
      }

      // Attach devel_layout CSS file.
      if (isset($config['devel_layout']) && $config['devel_layout'] === 1) {
         $page['#attached']['library'][] = 'at_core/at.devel_debug_layout';
      }

      // Attach devel_colorize-regions CSS file.
      if ((isset($config['devel_color_regions']) && $config['devel_color_regions'] === 1) && (isset($config['devel_layout']) && $config['devel_layout'] === 0)) {
        $page['#attached']['library'][] = 'at_core/at.devel_colorize_regions';
      }

      // Attach show_grid.
      if (isset($config['show_grid']) && $config['show_grid'] === 1) {
        $page['#attached']['library'][] = "$theme/show_grid";
      }

      // Attach nuke_toolbar CSS file.
      if (isset($config['nuke_toolbar']) && $config['nuke_toolbar'] === 1) {
        $page['#attached']['library'][] = 'at_core/at.devel_nuke_toolbar';
      }
    }

    // Shortcodes
    if (isset($config['enable_shortcodes']) && $config['enable_shortcodes'] === 1) {
      $shortcodes_yml = $path_to_theme . '/' . $theme . '.shortcodes.yml';
      if (file_exists($shortcodes_yml)) {
        $shortcodes_parser = new Parser();
        $shortcodes = $shortcodes_parser->parse(file_get_contents($shortcodes_yml));
        unset($shortcodes['animate']);
        foreach ($shortcodes as $class_type => $class_values) {
          if (isset($config['shortcodes_' . $class_type . '_enable']) && $config['shortcodes_' . $class_type . '_enable'] === 1) {
            $page['#attached']['library'][] = "$theme/shortcodes_$class_type";
          }
        }
      }

      // Animate has its own naming convention, being a vendor library.
      if (isset($config['shortcodes_animate_enable']) && $config['shortcodes_animate_enable'] === 1) {
        $page['#attached']['library'][] = "$theme/animate";
      }
    }

    // Add the responsive menu styles settings.
    if (isset($config['enable_responsive_menus']) && $config['enable_responsive_menus'] === 1) {

      // Load Responsive menu dependencies.
      $page['#attached']['library'][] = "$theme/responsive_menus";

      $responsivemenu_settings = array();

      // Breakpoint
      if (isset($config['responsive_menu_breakpoint'])) {
        $responsivemenu_settings['bp'] = $config['responsive_menu_breakpoint'];
      }

      // Loop the config settings to find selected menu styles.
      foreach (array('default', 'responsive') as $style) {
        if (isset($config['responsive_menu_' . $style . '_style'])) {

          // Load the library for each selected menu style.
          $page['#attached']['library'][] = "$theme/responsive_menus_" . $config['responsive_menu_' . $style . '_style'];

          // Set drupalSettings
          $responsivemenu_settings[$style] = 'ms-' . $config['responsive_menu_' . $style . '_style'];
        }
      }

      // Attach JS settings.
      $page['#attached']['drupalSettings'][$theme]['at_responsivemenus'] = $responsivemenu_settings;
    }

    // Attach poly-fills to support IE8.
    if (isset($config['enable_legacy_browsers']) && $config['enable_legacy_browsers'] === 1) {
      if (isset($config['legacy_browser_polyfills']) && $config['legacy_browser_polyfills'] === 1) {
        $page['#attached']['library'][] = 'at_core/at.respond';
        $page['#attached']['library'][] = 'at_core/at.selectivizr';
      }
    }

    // Load slideshow files
    if (isset($config['enable_slideshows']) && $config['enable_slideshows'] === 1) {

      // Get config settings and jam them into drupalSettings.
      if (isset($config['slideshow_count']) && $config['slideshow_count'] >= 1) {

        $basic_slider_settings = array(
          'animation',
          'direction',
          'smoothheight',
          'slideshowspeed',
          'animationspeed',
          'controlnav',
          'directionnav',
        );

        $carousel_settings = array(
          'as_carousel',
          'itemwidth',
          'itemmargin',
          'minitems',
          'maxitems',
          'move',
        );

        $advanced_slider_settings = array(
          'pauseonaction',
          'pauseonhover',
          'animationloop',
          'reverse',
          'randomize',
          'autostart', // Flexslider calls this "slideshow"
          'initdelay',
          'easing',
          'usecss',
          'touch',
          'video',
          'prevtext',
          'nexttext',
          'slideshow_class',
          'selector',
        );

        $slider_settings = array();
        for ($i = 0; $i < $config['slideshow_count']; $i++) {

          // Set a key
          $ss_key = Html::cleanCssIdentifier($theme . '-slideshow-' . $i);

          if (isset($config['slideshow_' . $i . '_enable']) && $config['slideshow_' . $i . '_enable'] == 1) {

            // Basic settings
            foreach ($basic_slider_settings as $basic_slider_setting) {
              if (isset($config['slideshow_' . $i . '_' . $basic_slider_setting])) {
                $slider_settings[$ss_key][$basic_slider_setting] = $config['slideshow_' . $i . '_' . $basic_slider_setting];
              }
            }

            // As Carousel
            if (isset($config['slideshow_' . $i . '_as_carousel']) && $config['slideshow_' . $i . '_as_carousel'] == 1) {
              foreach ($carousel_settings as $carousel_setting) {
                if (isset($config['slideshow_' . $i . '_' . $carousel_setting])) {
                  $slider_settings[$ss_key][$carousel_setting] = $config['slideshow_' . $i . '_' . $carousel_setting];
                }
              }
              // Reset options for carousels, fade won't work and vertical causes issues with Flexslider.
              $slider_settings[$ss_key]['animation'] = 'slide';
              $slider_settings[$ss_key]['direction'] = 'horizonal';
            }

            // Advanced options
            foreach ($advanced_slider_settings as $advanced_slider_setting) {
              if (isset($config['slideshow_' . $i . '_' . $advanced_slider_setting])) {
                $slider_settings[$ss_key][$advanced_slider_setting] = $config['slideshow_' . $i . '_' . $advanced_slider_setting];
              }
            }
          }
        }

        // Attach JS settings.
        if (!empty($slider_settings)) {
          $page['#attached']['drupalSettings'][$theme]['at_slideshows'] = $slider_settings;
          $page['#attached']['library'][] = 'at_core/at.slideshow';
          $page['#attached']['library'][] = "$theme/slideshow_styles";
        }
      }
    }
  }
}