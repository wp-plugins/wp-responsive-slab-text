<?php
/*
Plugin Name: WP Responsive Auto Fit Text
Plugin URI: http://www.vibesdesign.com.au/wp-responsive-auto-fit-text-wordpress-plugin/
Description: WP Responsive Fit Text allows you to create great, big, bold & responsive headlines that resize to the viewport width, using a simple shortcode.
Version: 0.2
Author: Gal Opatovsky
Author URI: http://www.vibesdesign.com.au
License: GPLv2 or later

*/
add_action('admin_notices', 'cfs_wdc_admin_notice');

function cfs_wdc_admin_notice() {
	global $current_user ;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
	if ( ! get_user_meta($user_id, 'cfs_wdc_ignore_notice') ) {
        echo '<div class="updated"><p style="float:left;">'; 
        printf(__('If you like "WP Responsive Auto Fit Text" plugin, please consider making a small donation. Thanks! :) <br> <br> <a href="%1$s">Hide Notice</a>'), '?cfs_wdc_nag_ignore=0');
        echo "</p>";
		
		echo '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="float:right;">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="NDPJSSZE6KEB6">
<table>
<tr><td><input type="hidden" name="on0" value="Select donation amount">Select donation amount</td></tr><tr><td><select name="os0">
	<option value="Buy me a coffee">Buy me a coffee $5.00 AUD</option>
	<option value="Buy me a beer">Buy me a beer $10.00 AUD</option>
	<option value="Motivate me to keep developing Plugins">Motivate me to keep developing Plugins $20.00 AUD</option>
	<option value="Too generous! Thank you!">Too generous! Thank you! $50.00 AUD</option>
</select> </td></tr>
</table>
<input type="hidden" name="currency_code" value="AUD">
<input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal — The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/en_AU/i/scr/pixel.gif" width="1" height="1">
</form>';
		
		echo "<div style='clear:both'></div>";
		echo "</div>";
	}
}

add_action('admin_init', 'cfs_wdc_nag_ignore');

function cfs_wdc_nag_ignore() {
	global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['cfs_wdc_nag_ignore']) && '0' == $_GET['cfs_wdc_nag_ignore'] ) {
             add_user_meta($user_id, 'cfs_wdc_ignore_notice', 'true', true);
	}
}

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

	$array = shortcode_atts( array (
		'<p>[' => '[',
		']</p>' => ']',
		']<br />' => ']',
		'font' => '',
		'transform'  => '',
		'color' => ''

	), $atts );
	

	$content = strtr($content, $array);	

	$GLOBALS["SLAB_TEXT_LINE"] .= '"<span style=\'color:' . $array['color'] .';text-transform:'. $array['transform'] .';font-family:'. $array['font'] .'\'>' . $content . '</span>",';

	return '';
}
add_shortcode( 'slab', 'slabtextline_shortcode' );

add_action('wp_footer', function(){

	echo '<script type="text/javascript">';
	echo $GLOBALS["SC_SCRIPTS"];
	echo '</script>';

 }, 100);

?>