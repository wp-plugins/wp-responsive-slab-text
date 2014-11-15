<?php
/*
Plugin Name: WP Responsive Auto Fit Text
Plugin URI: http://www.vibesdesign.com.au/wp-responsive-auto-fit-text-wordpress-plugin/
Description: WP Responsive Fit Text allows you to create great, big, bold & responsive headlines that resize to the viewport width, using a simple shortcode.
Version: 0.1
Author: Gal Opatovsky
Author URI: http://www.vibesdesign.com.au
License: GPLv2 or later

*/

function slabtext_shortcode( $atts, $content = null ) {

	wp_enqueue_script('jquery-slabtext',plugins_url( '/js/jquery.slabtext.min.js' , __FILE__ ),	array( 'jquery' ));	
	wp_register_style( 'jquery-slabtext-css', plugins_url('/css/wp-responsive-auto-fit-text.css', __FILE__) );
	wp_enqueue_style( 'jquery-slabtext-css');
	
	$array = array (
		'<p>[' => '[', 
		']</p>' => ']', 
		']<br />' => ']'
	);
	
	$rand_id = rand(1000,2000000);
	
	$content = strtr($content, $array);	
	
	$GLOBALS["SLAB_TEXT_LINE"] = "";
	
	$content = do_shortcode($content);
	
	$GLOBALS["SC_SCRIPTS"] .= 'var stS = "<span class=\'slabtext\'>",';
	$GLOBALS["SC_SCRIPTS"] .= 'stE = "</span>",';
	$GLOBALS["SC_SCRIPTS"] .= 'txt = [';
	if(strlen($GLOBALS["SLAB_TEXT_LINE"])>1) $GLOBALS["SC_SCRIPTS"] .= substr($GLOBALS["SLAB_TEXT_LINE"],0,(strlen($GLOBALS["SLAB_TEXT_LINE"])-1));
	$GLOBALS["SC_SCRIPTS"] .= '];';
	$GLOBALS["SC_SCRIPTS"] .= 'jQuery("#slabText'.$rand_id.'").html(stS + txt.join(stE + stS) + stE).slabText( {"viewportBreakpoint":290} );';
	$GLOBALS["SLAB_TEXT_LINE"] = ""; //empty

    return '<div id="slabText'.$rand_id.'" class="slabtext-wrapper"></div>';
}
add_shortcode( 'slabtext', 'slabtext_shortcode' );

function slabtextline_shortcode( $atts, $content = null ) {

	$array = array (
		'<p>[' => '[', 
		']</p>' => ']', 
		']<br />' => ']'
	);
	
	$content = strtr($content, $array);	

	$GLOBALS["SLAB_TEXT_LINE"] .= '"' . $content . '",';
	
	return '';
}
add_shortcode( 'slab', 'slabtextline_shortcode' );

add_action('wp_footer', function(){

	echo '<script type="text/javascript">';
	echo $GLOBALS["SC_SCRIPTS"];
	echo '</script>';

 }, 100);

?>