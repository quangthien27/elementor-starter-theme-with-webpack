<?php

defined( 'ABSPATH' ) or exit;

// CONSTANTS
defined( 'ASSETS_VERSION' ) or define( 'ASSETS_VERSION', md5( filemtime( is_readable( get_theme_file_path( 'dist/main.min.css' ) ) ? get_theme_file_path( 'dist/main.min.css' ) : '' ) . filemtime( is_readable( get_theme_file_path( 'dist/main.min.js' ) ) ? get_theme_file_path( 'dist/main.min.js' ) : '' ) ) );
defined( 'APP_ALM_NO_RESULTS_TEXT' ) or define( 'APP_ALM_NO_RESULTS_TEXT', 'Sorry, nothing found in this search' );

// AUTOLOAD CORE CLASSES
try {
  spl_autoload_register( function ( $class ) {
    if ( false === strpos( $class, 'App_' ) ) {
      return;
    }

    $filename = dirname( __FILE__ ) . '/core/' . str_replace( '_', '-', strtolower( substr( $class, 4 ) ) ) . '.php';

    if ( is_readable( $filename ) ) {
      include_once( $filename );
    }
  } );
} catch ( Exception $e ) {
}

// AUTOLOAD ELEMENTOR CLASSES
try {
  spl_autoload_register( function ( $class ) {
    if ( false === strpos( $class, 'Elem_' ) ) {
      return;
    }

    if ( false !== strpos( $class, 'Elem_Tag_' ) ) {
      $tag_name = str_replace( '_', '-', strtolower( substr( $class, 9 ) ) );
      $filename = dirname( __FILE__ ) . "/modules/elementor/tags/{$tag_name}.php";
    } else {
      $widget_name = str_replace( '_', '-', strtolower( substr( $class, 5 ) ) );
      $filename    = dirname( __FILE__ ) . "/modules/elementor/widgets/{$widget_name}/{$widget_name}.php";
    }

    if ( is_readable( $filename ) ) {
      include_once( $filename );
    }
  } );
} catch ( Exception $e ) {
}

// EXTRA MODULES
require_once 'modules/elementor/elementor.php';

// CORE CLASS
final class App_Core {
  private static ?App_Core $_instance = null;

  public App_Helpers $helpers;
  public App_Setup $setup;
  public App_Admin $admin;

  public function __construct() {
    $this->helpers = new App_Helpers();
    $this->setup   = new App_Setup();
    $this->admin   = new App_Admin();
  }

  public static function instance(): ?App_Core {
    return ( is_null( self::$_instance ) ? self::$_instance = new App_Core() : self::$_instance );
  }
}

