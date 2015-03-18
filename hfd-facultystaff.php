<?php
/*
Plugin Name: Faculty and Staff
Description: Proper display of faculty and staff
Author: Kyle Menigoz
Author URI: http://highflydesigns.com
Version: 1.0
*/


function hfd_facultystaff_create_posttype() {
	$labels = array(
		'name' => _x("Faculty and Staff", "post type general name"),
		'singular_name' => _x("Faculty and Staff", "post type singular name"),
		'menu_name' => 'Faculty and Staff',
		'add_new' => _x("Add New", "facstaff item"),
		'add_new_item' => __("Faculty/Staff Name"),
		'edit_item' => __("Edit Profile"),
		'new_item' => __("New Profile"),
		'view_item' => __("View Profile"),
		'search_items' => __("Search Profiles"),
		'not_found' =>  __("No Profiles Found"),
		'not_found_in_trash' => __("No Profiles Found in Trash"),
		'parent_item_colon' => ''
	);
	register_post_type( 'faculty_staff',
		array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => true,
			'menu_icon' => 'dashicons-groups',
			'rewrite' => array('slug' => 'facultystaff'),
			'supports' => array('title', 'page-attributes', 'excerpt', 'editor')
		)
	);
}
add_action( 'init', 'hfd_facultystaff_create_posttype' );

function hfd_facultystaff_custom_enter_title( $input ) {
    global $post_type;
    if( is_admin() && 'Enter title here' == $input && 'faculty_staff' == $post_type )
        return 'Enter the full name of the individual here';
    return $input;
}
add_filter('gettext','hfd_facultystaff_custom_enter_title');

function hfd_facultystaff_info($post) {
	wp_nonce_field( basename( __FILE__ ), 'hfd_facultystaff_nonce' );
    $faculty_staff_stored_meta = get_post_meta( $post->ID );	
	echo "<p>\n";
		echo "<form name='fsprofile' id='fsprofile' method='post'>\n";
		echo "<table>\n";
		echo "<tr>\n";
		echo "<td><label for='fs_title'>Title:</label></td>\n";
		echo "<td><input type='text' name='fs_title' placeholder='Organizational Title' value='".( isset ( $faculty_staff_stored_meta['fs_title'] ) ? $faculty_staff_stored_meta['fs_title'][0] : "")."' /></td>\n";
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='fs_company'>Company:</label></td>\n";
		echo "<td><input type='text' name='fs_company' placeholder='Individual Company' value='".( isset ( $faculty_staff_stored_meta['fs_company'] ) ? $faculty_staff_stored_meta['fs_company'][0] : "")."' /></td>\n";
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo "<td><label for='fs_email'>Email:</label></td>\n";
		echo "<td><input type='email' name='fs_email' placeholder='Individual Email' value='".( isset ( $faculty_staff_stored_meta['fs_email'] ) ? $faculty_staff_stored_meta['fs_email'][0] : "")."' /></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</form>\n";
	echo "</p>\n";
}

function hfd_facultystaff_add_info_metabox() {
	add_meta_box('hfd_facultystaff_info', 'Faculty/Staff Profile Info', 'hfd_facultystaff_info', 'faculty_staff', 'normal', 'core','post');
}
add_action( 'add_meta_boxes', 'hfd_facultystaff_add_info_metabox' );

function hfd_facultystaff_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'hfd_facultystaff_nonce' ] ) && wp_verify_nonce( $_POST[ 'hfd_facultystaff_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    
    if( isset( $_POST[ 'fs_title' ] ) ) {
     	update_post_meta( $post_id, 'fs_title', sanitize_text_field( $_POST[ 'fs_title' ] ) );
    }
    
    if( isset( $_POST[ 'fs_company' ] ) ) {
     	update_post_meta( $post_id, 'fs_company', sanitize_text_field( $_POST[ 'fs_company' ] ) );
    }
    

    if( isset( $_POST[ 'fs_email' ] ) ) {
     	update_post_meta( $post_id, 'fs_email', sanitize_text_field( $_POST[ 'fs_email' ] ) );
    }   
 
}
add_action( 'save_post', 'hfd_facultystaff_meta_save' );

function hfd_facultystaff_shortcode_page() {
	$facultystaff_posts = get_posts(array(
		'post_type'			=> 'faculty_staff',
		'posts_per_page'	=> -1,
		'orderby'			=>	'menu_order'
	) );
	if ( $facultystaff_posts )
	{
		wp_enqueue_style( 'hfd-facultystaff-jquery-ui-style', plugins_url('styles/jquery-ui.css', __FILE__) );
		wp_enqueue_style( 'hfd-facultystaff-plugin-style', plugins_url('styles/plugin.css', __FILE__) );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-position' );
		wp_enqueue_script( 'whitebox-dialog', plugins_url('/js/functions.js', __FILE__));
		echo "<section class=\"faculty_staff\">\n";
		foreach($facultystaff_posts as $fs_post)
		{
			$the_post = get_post($fs_post->ID);
			echo "<div class=\"faculty_staff_entry\">\n";
			echo "<h3 class=\"faculty_staff_header\">";
			echo $the_post->post_title;
			if ( get_post_meta($fs_post->ID, 'fs_title', true)){
				echo " – ".get_post_meta($fs_post->ID, 'fs_title', true);
			}
			echo "</h3>\n";
			if(get_post_meta($fs_post->ID, 'fs_company', true))
			{
				//display for title set
				echo "<h5>";
				echo get_post_meta($fs_post->ID, 'fs_company', true);
				if(get_post_meta($fs_post->ID, 'fs_email', true))
				{
					//display for email set
					echo "<span class=\"alignright\">";
					echo "<a href=\"mailto:".get_post_meta($fs_post->ID, 'fs_email', true)."\">".get_post_meta($fs_post->ID, 'fs_email', true)."</a>";
					echo "</span>\n";
				}
				echo "</h5>\n";
			}
			echo "<div class=\"faculty_staff_excerpt\">".$the_post->post_excerpt."</div>\n";
			if(!empty($the_post->post_content))
			{
				echo "<a href=\"#\" class=\"whitebox_open alignright\" data-id=\"faculty_staff_content".$fs_post->ID."\">Read More</a>\n";
				echo "<div id=\"faculty_staff_content".$fs_post->ID."\" class=\"faculty_staff_content\" title=\"".$the_post->post_title."\"\>".$the_post->post_content."</div>\n";
			}
			echo "</div>\n";
			echo "<br class=\"clear\" />\n";
		} // end foreach
	} // end if($facultystaff_posts)
} //end hfd_facultystaff_shortcode_page
add_shortcode("hfd-facultystaff", "hfd_facultystaff_shortcode_page");
?>