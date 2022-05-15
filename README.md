## Common instruction

1. Navigate to theme folder
2. Run `yarn install` (required global `yarn` installed first)
3. Run `yarn run build` to init the build
4. Run `yarn watch` when developing
5. Full folder structure and explanation below (there may be some changes or differences)

```
|-- comments.php    : required WP file (not using)
|-- footer.php      : required WP file (not using, if built with Elementor)
|-- functions.php   : required WP file (not using; contains entry point of the app)
|-- header.php      : required WP file (not using, if built with Elementor)
|-- index.php       : required WP file (not using, if built with Elementor)
|-- screenshot.png  : theme screenshot
|-- sidebar.php     : required WP file (not using)
|-- style.css       : required WP file (not using)
|-- acf-json        : json folder for saved ACF fields
|-- alm_templates   : default folder of Ajax Load More template, do not change folder name
|   |-- default.php : default template of Ajax Load More
|-- app
|   |-- app.php     : main app entry point, contains core classes auto load
|   |-- assets
|   |   |-- fonts   : theme fontfaces
|   |   |-- images  : theme images/icons
|   |   |-- js
|   |   |   |-- main.js     : main js entry point
|   |   |   |-- utils.js    : utilitils js functions
|   |   |-- lib                         : extra libraries
|   |   |   |-- Readmore                : read more toggle 
|   |   |   |-- bootstrap-5.1.3         : Bootstrap
|   |   |   |-- css_browser_selector    : inject browser name and OS to body class
|   |   |   |-- imagesloaded            : provide signals for images loaded
|   |   |   |-- isotope                 : fancy grid
|   |   |   |-- jquery-hoverIntent      : hover intent/delay
|   |   |   |-- lity-2.4.1              : provide quick popups functionality
|   |   |   |-- overlayscrollbars       : custom scrollbars
|   |   |   |-- parallax.js             : paralax effects
|   |   |   |-- swiper                  : for sliders
|   |   |   |-- wow                     : for animating
|   |   |-- scss                : SCSS files, avoid adding extra files if not really needed
|   |       |-- _animate.scss   : extra libraries
|   |       |-- _bootstrap.scss : customize importing Bootstrap 
|   |       |-- _elementor.scss : global styles for Elementor components
|   |       |-- _form.scss      : global styles for form and form elements, such as form, button, inputs
|   |       |-- _general.scss   : global geleral styles, such as heading, paragraph, link, etc.
|   |       |-- _mixins.scss    : SCSS mixins
|   |       |-- _variables.scss : common SCSS variables
|   |       |-- main.scss       : SCSS main entry point
|   |-- components : For components. Use WP "get_template_part(<component>, null, $args[])" function to include
|   |   |-- button
|   |       |-- button.php
|   |       |-- button.scss
|   |-- core
|   |   |-- admin.php   : all hooks related to and for admin screens only
|   |   |-- helpers.php : helper functions
|   |   |-- setup.php   : all hooks related to front end
|   |-- modules
|       |-- elementor
|           |-- elementor.php
|           |-- tags : custom dynamic tag. Check "init_dynamic_tags" function in above "elementor.php" file for how to include
|           |   |-- home-url.php
|           |-- widgets : custom widgets. Check "init_widgets" function in above "elementor.php" file for how to include
|               |-- header
|                   |-- header.php  : contain PHP and JS scripts
|                   |-- header.scss : contain SCSS
|-- template-parts
|   |-- 404.php
|   |-- archive.php
|   |-- search.php
|   |-- single.php
|-- templates
|   |--template-blank.php
```

## How to add an Elementor widget

1. Clone a sample widget, whole folder. Eg: can use `header` widget as an example
2. Rename the widget folder and its PHP + SCSS files. They all must have the same name. If the widget name has more than 1 word, make sure to use dash `-` to separate words. Eg: `featured-heading`
3. Open the PHP file and rename the widget class name, make sure to include `Elem_` as prefix (for autoload), capitalize words and use underscore `_` to separate words, . Eg: `Elem_Featured_Heading`
4. In widget PHP file:
    1. Update `get_title()` function with new widget name, Eg: `Featured Heading`
    2. If the widget requires extra libraries, add to `get_style_depends()` and `get_script_depends()`. Make sure to register the asset first, can register either in the widget `_register_assets()` or in `app/core/setup.php >> enqueue_scripts()` (if the libraries are used across different widgets)
    3. Add the widget controls to `register_controls()`. For more controls, check [this Elementor document](https://developers.elementor.com/docs/controls/data-controls/).
    4. Add widget HTML and JS to `render()`, make sure to update `$uid` and the wrapper class
5. Go to `app/modules/elementor/elementor.php` and register the widget in `init_widgets()`

## TIPS

- Access app helpers functions using `App_Core::instance()->helpers`, eg:

```
App_Core::instance()->helpers->get_page_title();
```

- SCSS media query using `@include media-breakpoint-up()` and `@include media-breakpoint-down()` eg:

```
@include media-breakpoint-up(lg) {
    --swiper-navigation-size: 35px;
}
```

- If we use LocalWP for local development, we can enable "[Instant Reload](https://localwp.com/help-docs/local-features/instant-reload/)" module for faster development
