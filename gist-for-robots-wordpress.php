<?php
/**
* Plugin Name: Gist for Robots Wordpress Plugin
* Plugin URI: https://github.com/pedroelsner/gist-for-robots-wordpress
* Description: Makes embedding github.com gists SEO friendily and super awesomely easy.
* Usage: Drop in the embed code from github between the gist shortcode. [gist]<script src="http://gist.github.com/00000.js?file=file.txt"></script>[/gist] or [gist id=00000 file=file.txt]
* Version: 1.3.1
* Author: Pedro Elsner
* Author URI: http://pedroelsner.com/
**/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-gist-for-robots.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gist_for_robots() {

    $plugin = new Gist_For_Robots();
    $plugin->run();

}
run_gist_for_robots();
