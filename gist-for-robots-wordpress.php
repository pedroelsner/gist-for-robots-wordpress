<?php
/*
Plugin Name: Gist for Boots Wordpress Plugin
Plugin URI: https://github.com/pedroelsner/gist-for-robots-wordpress
Description: Makes embedding github.com gists SEO friendily and super awesomely easy.
Usage: Drop in the embed code from github between the gist shortcode.
[gist]<script src="http://gist.github.com/00000.js?file=file.txt"></script>[/gist]
or
[gist id=00000 file=file.txt]
Version: 1.1
Author: Pedro Elsner
Author URI: http://pedroelsner.com/
*/


/**
 * Shortcode Gist
 *
 * @param array $atts Argumentos
 * @param string $content Conteúdo
 * @return string
 */
function shortcode_gist($atts, $content=null) {

    extract(shortcode_atts(array(
      'id' => null,
      'file' => null,
    ), $atts));

    $pattern = "/gist.github.com\/(\d+).js/";
    if ($content != null && $id == null & preg_match($pattern, $content, $matches)) {
	  $id = $matches[1];
	}
    $pattern = "/\?file=(\S+)\">/";
    if ($content != null && $file == null & preg_match($pattern, $content, $matches)) {
	  $file = $matches[1];
	}

    if ($id == null && $file == null) {
      return 'Gist id: '.$id.' file:'.$file;
    }

	// Simplistic ID validation
	if ( preg_match( '~([^a-z0-9]+)~', $id ) ) {
		return 'Invalid Gist ID';
	}

	$gist_url_base = 'http://gist.github.com/' . $id;

    $html = '<div class="gist-for-robots">';

    if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'bot' ) !== false ) {

        $gist_url = $gist_url_base . '.json';
        $gist_url .= $file != null ? '?file=' . trim($file) : '';

		$gist_content = wp_remote_retrieve_body( wp_remote_get( $gist_url ) );
		// If there's an error getting the gist don't bother trying to handle the error, just dumbly return the gist URL as a link.
		if ( is_wp_error( $gist_content ) ) {
			return '<a href="' . esc_url( $gist_url_base ) . '">' . esc_html( $gist_url_base ) . '</a>';
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
add_shortcode('gist', 'shortcode_gist');

?>