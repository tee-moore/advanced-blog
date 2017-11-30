<?php
/*
Plugin Name: Advanced Widget
Plugin URI: https://github.com/tee-moore/advanced-widget
Description: Advanced Widget
Version: 1.0.0
Author: Timur Panchenko
*/

/*  Copyright 2017  Timur Panchenko  (email: 2teemoore@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//settings
$settings['add_series_to_post_types'] = array('post');
$settings['display_on_post_types'] = array('post', 'page');
$settings['add_supports_to_post_types'] = 'page';





//add textdomai
add_action('init', 'aw_locale');
function aw_locale() {
     load_plugin_textdomain( 'advanced-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}


//add scripts
add_action( 'admin_enqueue_scripts', 'aw_admin_enqueue_scripts' );
function aw_admin_enqueue_scripts(){
    wp_enqueue_script( 'select2-js', plugins_url('/js/selectWoo.full.min.js', __FILE__), array(), null, true );
    wp_enqueue_script( 'aw-script', plugins_url('/js/aw-script-admin.js', __FILE__), array(), null, true);
    wp_enqueue_style( 'select2-style', plugins_url('/css/selectWoo.min.css', __FILE__), array(), null, 'all' );
    wp_enqueue_style( 'aw-style', plugins_url('/css/aw-style-admin.css', __FILE__), array(), null, 'all' );
}

add_action( 'wp_enqueue_scripts', 'aw_enqueue_scripts' );
function aw_enqueue_scripts(){
    wp_enqueue_script( 'aw-script', plugins_url('/js/aw-script-front.js', __FILE__), array(), null, true);
    wp_enqueue_style( 'aw-style', plugins_url('/css/aw-style-front.css', __FILE__), array(), null, 'all' );
}


//add taxonomy 'series' & metabox on page (post) edit page
add_action('init', 'aw_create_taxonomy');
function aw_create_taxonomy(){
    global $settings;
    register_taxonomy('series', $settings['add_series_to_post_types'], array(
        'label'                          => __( 'Series', 'advanced-widget' ),
        'labels'                         => array(
            'name'                       => __( 'Series of articles', 'advanced-widget' ),
            'singular_name'              => __( 'Serie', 'advanced-widget' ),
            'search_items'               => __( 'Search Series', 'advanced-widget' ),
            'all_items'                  => __( 'All Series', 'advanced-widget' ),
            'view_item '                 => __( 'View Serie', 'advanced-widget' ),
            'edit_item'                  => __( 'Edit Serie', 'advanced-widget' ),
            'update_item'                => __( 'Update Serie', 'advanced-widget' ),
            'add_new_item'               => __( 'Add New Serie', 'advanced-widget' ),
            'new_item_name'              => __( 'New Serie Name', 'advanced-widget' ),
            'separate_items_with_commas' => __( 'Separate series with commas', 'advanced-widget' ),
            'choose_from_most_used'      => __( 'Choose from the most used series', 'advanced-widget' ),
            'not_found'                  => __( 'No series found.', 'advanced-widget' ),
            'menu_name'                  => __( 'Series', 'advanced-widget' ),
        ),
        'description'           => '',
        'public'                => true,
        'publicly_queryable'    => true,
        'show_in_nav_menus'     => false,
        'show_ui'               => true,
        'show_tagcloud'         => false,
        'hierarchical'          => false,
        'update_count_callback' => '',
        'rewrite'               => true,
        //'query_var'             => $taxonomy,
        'capabilities'          => array('manage_categories', 'edit_posts'),
        'meta_box_cb'           => 'post_tags_meta_box',
        'show_admin_column'     => false,
        '_builtin'              => false,
        'show_in_quick_edit'    => true,
    ) );
}



// add meta box category & tags to pages
function wp_add_categories_tags_for_pages(){
    global $settings;

    //add_meta_box( 'categorydiv', 'Категории1', 'post_categories_meta_box', $post_types_support, 'side', 'normal'); 
    //add_meta_box( 'tagsdiv-page_tag', 'Теги1', 'post_tags_meta_box', $post_types_support, 'side', 'normal' );
    register_taxonomy_for_object_type('category', $settings['add_supports_to_post_types']);
    register_taxonomy_for_object_type('post_tag', $settings['add_supports_to_post_types']);
    register_taxonomy_for_object_type('series', $settings['add_supports_to_post_types']);
}
add_action('admin_init','wp_add_categories_tags_for_pages');

function aw_widget_request_category($q) {
    if (isset($q['category_name'])) 
        $q['post_type'] = $post_types_support;
    return $q;
}
add_filter('request', 'aw_widget_request_category');

function aw_widget_request_post_tags($q) {
    if (isset($q['tag']))
        $q['post_type'] = $post_types_support;
    return $q;
}
 
add_filter('request', 'aw_widget_request_post_tags');


include "widgets/aw_category_widget.php";
include "widgets/aw_series_widget.php";