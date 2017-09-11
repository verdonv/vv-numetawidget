<?php 
/*
Plugin Name: Verdon's NU Meta Widget
Description: Clone of the standard Meta widget with options specifically for Nipissing U.
Version: 1.0.1
Author: Verdon Vaillancourt
Author URI: http://verdon.ca/
License: GPLv2 or later
Text Domain: vv-numetawidget
*/

/*
Please note: This plugin is a quick and dirty rip-off of the 
'Custom Meta Widget' plugin written by bitacre of 
http://shinraholdings.com/ with a couple specific tweaks for
Nipissing University
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/* setup a few constants */
define( 'VVNUMW_VERSION', '1.0.0' );
define( 'VVNUMW__MINIMUM_WP_VERSION', '4.0' );
define( 'VVNUMW__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VVNUMW__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );



/**
 * CLASS VVNUMetaWidget 
 */
class VVNUMetaWidget extends WP_Widget { // start of class


/**
 * CONSTRUCTOR
 */
function VVNUMetaWidget() {	
	// set widget options
	$widget_ops = array ( 
		'classname' => 'VVNUMetaWidget',
		'description' => __( 'Hide the individual log in/out, admin, feed and Nipissing links', 'VVNUMetaWidget' )
	); 
	
	// extend WP_Widget
	parent::__construct( 'VVNUMetaWidget', 'Verdon\'s Nipissing University Meta Links', $widget_ops );
}


/**
 *  Declare Form Input Options 
 * (not part of WP_Widget class)
 */
function get_options() {
	$keys = array( 'slug', 'type', 'default', 'label', 'before' );
	
	$values = array( 
		'title' => array( 'title', 'text', __( 'Meta Links', 'vv-numetawidget' ), __( 'Title', 'vv-numetawidget' ), '' ),			
		'register' => array( 'register', 'checkbox', 0, __( 'Show "Register/Admin" link?', 'vv-numetawidget' ), '' ),
		'login' => array( 'login', 'checkbox', 1, __( 'Show "Log in/out" link?', 'vv-numetawidget' ), '' ),
		'entryrss' => array( 'entryrss', 'checkbox', 1, __( 'Show "Entries RSS" link?', 'vv-numetawidget' ), '' ),
		'commentrss' => array( 'commentrss', 'checkbox', 1, __( 'Show "Comments RSS" link?', 'vv-numetawidget' ), '' ),
		'nipissingu' => array( 'nipissingu', 'checkbox', 1, __( 'Show "Nipissing University" link?', 'vv-numetawidget' ), '' ),
		'showcustom' => array( 'showcustom', 'checkbox', 0, __( 'Show the custom link?', 'vv-numetawidget' ), 'before' => '' ),
		'customurl' => array( 'customurl', 'text', '', __( 'URL', 'vv-numetawidget' ), ' style="margin-left:20px;"' ),
		'customtext' => array( 'customtext', 'text', '', __( 'Text', 'vv-numetawidget' ), ' style="margin-left:20px;"' )
	);
	
	// build into multi-array
	$options = array();
	foreach( $values as $slug => $sub_values ) {
		$temp = array();
		for( $i=0; $i<5; $i++ )
			$temp[$keys[$i]] = $sub_values[$i];
		$options[$slug] = $temp;
	} 
	return $options;
}


/**
 * Declare Form Input Defaults
 * (not part of WP_Widget Class)
 */
function get_defaults() {
	// create container and loop
	$defaults = array(); 
	foreach( $this->get_options() as $key => $value )
		$defaults[$key] = $value['default'];
	return $defaults;
}


/**
 * Declare Form Input Keys
 * (not part of WP_Widget Class)
 */
function get_keys() {
	// create container and loop
	$keys = array(); 
	foreach( $this->get_options() as $key => $value )
			$keys[] = $key;
	return $keys;
}


/**
 * Draw Widget Options
 */
function form( $instance ) { 
	// parse instance values over defaults
	$instance = wp_parse_args( ( array ) $instance, $this->get_defaults() ); 

	// loop through input option
	foreach( $this->get_options() as $slug => $value ) :
		extract( $value );
		$id = $this->get_field_id( $slug );
		$name = $this->get_field_name( $slug );
		if( $type == 'text' ) {
			$value = $instance[$slug];
			$checked = '';
			$label = $label . ': ';
		} else {
			$checked = checked( $instance[$slug], 1, false );
			$value = 1;
		}
		$label_tag = '<label style="margin:0 3px;" for="' . $id . '">' . $label . '</label>'; 
		?>

        
	<!-- <?php echo $slug; ?> -->
    
	<p<?php echo $before; ?>><?php echo ( $type == 'text' ? $label_tag : '' ); ?><input class="<?php echo ( $type == 'text' ? 'widefat' : 'check' ); ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="<?php echo $type; ?>" value="<?php echo $value; ?>" <?php echo $checked; ?>/><?php echo ( $type == 'checkbox' ? $label_tag : '' ); ?></p>
    
	<?php endforeach; ?>

	

	<?php // check for errors
	if( $instance['showcustom'] ) { // IF 'showcustom' is checked, AND
			
		if( empty( $instance['customtext']) && empty( $instance['customurl'] ) ) // 1. no link and no URL
			$message = 'You have a custom link with no URL or text!';
		
		elseif( empty( $instance['customtext'] ) ) // 2. no link
			$message = 'You have a custom link with no text!';
		
		elseif( empty( $instance['customurl' ] ) ) // 3. no url
			$message = 'You have a custom link with no URL!';
		
	}
	if( isset( $message ) ) // set message (or don't)
		echo '<p style="color:#f00; font-weight:bold;" >' . __( $message, 'vv-numetawidget' ) . '</p>';
}


/**
 * SAVE WIDGET OPTIONS 
 */
function update( $new_instance, $old_instance) {
	$instance = $old_instance; // move over unchanged
	
	foreach( $this->get_keys() as $key ) // parse new values over
		$instance[$key] = $new_instance[$key];

	return $instance;
}

// ACTUAL WIDGET OUTPUT
function widget( $args, $instance ) { 
   	extract( $args, EXTR_SKIP ); // extract arguments
	$title = empty( $instance['title'] ) ? __( 'Meta Links', 'vv-numetawidget' ) : apply_filters( 'widget_title', $instance['title'] ); // if no title, use default ?>
    
	<?php echo $before_widget; // (from theme) ?>
	<?php echo $before_title . esc_attr( $instance['title'] ) . $after_title; ?>
	<ul>
	
	
    <?php // ADD LINKS
	$content = array(
		'register' => wp_register( '<li>', '</li>', false ),
		
		'login' => '<li>' . wp_loginout( NULL, false ) . '</li>',
		
		'entryrss' => sprintf( __( '%1$sSyndicate this site using RSS 2.0%2$sEntries %3$sRSS%4$s feed%5$s', 'vv-numetawidget' ), 
			'<li><a href="' . get_bloginfo( 'rss2_url' ) . '" title="', '">', 
			'<abbr title="' . __( 'Really Simple Syndication', 'vv-numetawidget' ) . '">', '</abbr>', '</a></li>' ),
			
		'commentrss' => sprintf( __( '%1$sSyndicate this site using RSS 2.0%2$sComments %3$sRSS%4$s feed%5$s', 'vv-numetawidget' ),
			'<li><a href="' . get_bloginfo( 'comments_rss2_url' ) . '" title="', '">',
			'<abbr title="' . __( 'Really Simple Syndication', 'vv-numetawidget' ) . '">', '</abbr>', '</a></li>' ),
			
		'nipissingu' => '<li><a href="http://nipissingu.ca/" title="' . 
			__( 'Nipissing University, One Student at a Time.', 'vv-numetawidget' ) . 
			'">Nipissing University</a></li>', 
	
		'showcustom' => ( !empty( $instance['customtext'] ) && !empty( $instance['customurl'] ) ? 
			'<li><a href="' . esc_url( $instance['customurl'] ) . '">' . esc_attr( $instance['customtext'] ) . '</a></li>' :
		'<!--' . __( 'Error: "Show Custom Link" is checked, but either the text or URL for that link are not specified. The link was not displayed because it would be broken. Check the settings for your Custom Meta widget.', 'vv-numetawidget' ) . '-->' ) 
	
	);
	
	if ( isset($instance['register']) || isset($instance['login']) || isset($instance['entryrss']) || isset($instance['commentrss']) || isset($instance['nipissingu']) || isset($instance['showcustom']) ) {
		foreach( $content as $checked => $output )
			if( (int) esc_attr( $instance[$checked] ) === 1 ) echo $output; 
	} else {
		echo sprintf( __( '%1$sThe Nipissing University Meta Links widget is installed, but no links are configured to display. Please visit the Widgets area in your administration and enable at least one link, or remove the widget.%2$s', 'vv-numetawidget'), '<li>', '</li>');
	}
	?>

	</ul>
    
	<?php echo $after_widget;

}

} // end class 


/**
 * Unregister WP_Widget_Meta
 */
function VVNUMetaWidget_swap() {
	unregister_widget( 'WP_Widget_Meta' );
	register_widget( 'VVNUMetaWidget' );
} add_action( 'widgets_init', 'VVNUMetaWidget_swap' ); // hook


/**
 * Load TextDomain
 */
function VVNUMetaWidget_i18n() {
	load_plugin_textdomain( 'vv-numetawidget', NULL, trailingslashit( basename( dirname(__FILE__) ) ) . 'lang' );
} add_action( 'plugins_loaded', 'VVNUMetaWidget_i18n' ); // hook

?>
