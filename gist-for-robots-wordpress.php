<?php
/*
Plugin Name: Gist for Boots Wordpress Plugin
Plugin URI: https://github.com/pedroelsner/gist-for-robots-wordpress
Description: Makes embedding github.com gists SEO friendily and super awesomely easy.
Usage: Drop in the embed code from github between the gist shortcode.
[gist]<script src="http://gist.github.com/00000.js?file=file.txt"></script>[/gist]
or
[gist id=00000 file=file.txt]
Version: 1.0
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
    
    
    $html = '<div class="gist-for-robots">';
    
    if (eregi('bot',$_SERVER['HTTP_USER_AGENT'])) {
        
        $url = 'http://gist.github.com/' . trim($id) . '.json';
        $url .= $file != null ? '?file=' . trim($file) : '';
        
        $json  = json_decode(file_get_contents($url), true);
        $html .= $json['div'];
        $html .= '<noscript>'.$json['div'].'</noscript>';
    
    } else {
        $html .= '<script src="https://gist.github.com/'.$id.'.js?file='.$file.'"></script>';
    }
    
    $html .= '</div>';
    
    return $html;
}
add_shortcode('gist', 'shortcode_gist');
 
?>