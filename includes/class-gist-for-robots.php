<?php
/**
 * Shortcode Gist
 *
 * @link       https://github.com/pedroelsner/gist-for-robots-wordpress
 * @since      1.3.1
 *
 * @package    Gist_For_Robots
 * @subpackage Gist_For_Robots/includes
 * @author     Pedro Elsner
 */

class Gist_For_Robots {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Gist_For_Robots_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'gist-for-robots';
		$this->version = '1.3.1';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_public_hooks();

	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Gist_For_Robots_i18n. Defines internationalization functionality.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gist-for-robots-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-gist-for-robots-i18n.php';

		$this->loader = new Gist_For_Robots_Loader();

	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Gist_For_Robots_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Gist_For_Robots_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

    	public static function gfr_shortcode( $atts, $content=null ) {

        	extract( shortcode_atts ( array(
        	'id' => null,
        	'file' => null,
        	), $atts ) );

        	$pattern = "/gist\.github\.com\/([a-zA-Z0-9-]*)(?:\/?)([a-zA-Z0-9-]*)\.js/";
        	if ( $content != null && $id == null & preg_match( $pattern, $content, $matches ) ) {
                	$has_username = ( ! empty( $matches[2] ) );
                	if ( $has_username ) {
                        	$id = $matches[2];
                	} else {
                        	$id = $matches[1];
                	}
        	}

        	$pattern = "/\?file=(\S+)\">/";

        	if ( $content != null && $file == null & preg_match( $pattern, $content, $matches ) ) {
            	$file = sanitize_file_name( $matches[1] );
        	}

        	// Simplistic ID validation
        	if ( $id == null || preg_match( '~([^a-z0-9]+)~', $id ) ) {
            	return 'Invalid Gist ID';
        	}
        	$gist_url_base = 'http://gist.github.com/' . $id;

        	$html = '<div class="gist-for-robots">';

        	if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'bot' ) !== false ) {

            	$gist_url = $gist_url_base . '.json';
            	$gist_url .= $file != null ? '?file=' . $file : '';

            	$gist_content = wp_remote_retrieve_body( wp_remote_get( $gist_url ) );

            	// If there's an error getting the gist don't bother trying to handle the error, just dumbly return the gist URL as a link.

            	if ( is_wp_error( $gist_content ) ) {
                		return '<a href="' . esc_url( $gist_url ) . '" title="'.$file.'">' . esc_html( $gist_url ) . '</a>';
            	}

            	$json  = json_decode( $gist_content, true );
            	$html .= $json['div'];
            	$html .= '<noscript>'.$json['div'].'</noscript>';

        	} else {
            	$gist_url = $gist_url_base . '.js';
            	if ( $file !== null) {
                    		$gist_url .= '?file=' . $file;
            	}
           	$html .= '<script src="' . esc_url( $gist_url ) . '"></script>';
        		}
        		$html .= '</div>';
        		return $html;
    	}


    	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Gist_For_Robots_Loader( $this->get_plugin_name(), $this->get_version() );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Gist_For_Robots_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
add_shortcode( 'gist', array( 'Gist_For_Robots', 'gfr_shortcode' ) );
