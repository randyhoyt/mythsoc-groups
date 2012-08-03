<?php

/*
Plugin Name: Mythopoeic Society Groups Plugin
Description: Custom plugin that adds discussion groups and meetings to WordPress.
*/

add_action('init','mythsoc_groups_register');
function mythsoc_groups_register() {

  $labels = array(
    'name' => _x('Groups', 'post type general name'),
    'singular_name' => _x('Group', 'post type singular name'),
    'add_new' => _x('Add Group', 'book'),
    'add_new_item' => __('Add Group'),
    'edit_item' => __('Edit Group'),
    'new_item' => __('New Group'),
    'all_items' => __('All Groups'),
    'view_item' => __('View Groups'),
    'search_items' => __('Search Groups'),
    'not_found' =>  __('No groups found'),
    'not_found_in_trash' => __('No groups found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Groups'
  );
  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title','editor' )
  ); 
  register_post_type('mythsoc_group',$args);

  $labels = array(
    'name' => _x('Meetings', 'post type general name'),
    'singular_name' => _x('Meetings', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add Meeting'),
    'edit_item' => __('Edit Meeting'),
    'new_item' => __('New Meeting'),
    'all_items' => __('All Meetings'),
    'view_item' => __('View Meetings'),
    'search_items' => __('Search Meetings'),
    'not_found' =>  __('No meetings found'),
    'not_found_in_trash' => __('No meetings found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Meetings'
  );
  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title','editor','custom-fields' )
  );

  if (function_exists(register_sub_post_type)) {
    register_sub_post_type('mythsoc_meeting',$args,'mythsoc_group');
  } else {
    register_post_type('mythsoc_meeting',$args);
  }
}

add_action( 'admin_head', 'mythsoc_group_icon' );
function mythsoc_group_icon() {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-mythsoc_group .wp-menu-image {
            background-position: -59px -33px !important;
        }
        #menu-posts-mythsoc_group:hover .wp-menu-image, #menu-posts-mythsoc_group.wp-has-current-submenu .wp-menu-image {
            background-position: -59px -1px !important;
        }
        #menu-posts-mythsoc_meeting .wp-menu-image {
            background: url(<?php echo plugin_dir_url(__FILE__); ?>/img/icon-16-mythsoc-meeting.png) no-repeat 6px -17px !important;
        }
        #menu-posts-mythsoc_meeting:hover .wp-menu-image, #menu-posts-mythsoc_meeting.wp-has-current-submenu .wp-menu-image {
            background-position:6px 7px!important;
        }        
        div.icon32-posts-mythsoc_group {
            background-position: -137px -5px !important;
        }
        div.icon32-posts-mythsoc_meeting {
            background: url(<?php echo plugin_dir_url(__FILE__); ?>/img/icon-32-mythsoc-meeting.png) top left no-repeat !important;
        }
    </style>
<?php }

add_filter('subpost_form_fields','mythsoc_meeting_subpost_form_fields',10,2);
function mythsoc_meeting_subpost_form_fields($output,$post) {

    if ($post->post_type == 'mythsoc_meeting') {
        $meeting_date = get_post_meta($post->ID,'meeting_date',true);
        if ($meeting_date != "" && (int) $meeting_date > 0) {
            $meeting_date = date('m/d/Y',$meeting_date);
        } else {
            $meeting_date = "";
        }
        $output = '
            <tr>
                <th valign="top" scope="row" class="label" style="width:130px;">
                    <span class="alignleft"><label for="src">Meeting Date</label></span>
                </th>
                <td class="field"><input type="text" style="width: 140px;" name="meeting_date" value="' . $meeting_date . '" class="datepicker"></td>
                <link rel="stylesheet" href="' . plugin_dir_url(__FILE__) . 'css/smoothness/jquery-ui-with-datepicker.css"></script> 
                <script type="text/javascript" src="' . plugin_dir_url(__FILE__) . 'js/jquery-ui-with-datepicker.min.js"></script> 
                <script>
                    jQuery(function($) {
                        $( ".datepicker" ).datepicker();
                    });
                </script>                       
            </tr>';

    }
    return $output;

}

add_action('subpost_save_form_fields','mythsoc_meeting_subpost_save_form_fields',10,2);
function mythsoc_meeting_subpost_save_form_fields($post) {

    if ($post->post_type == 'mythsoc_meeting')
    {
        $meeting_date = $_POST['meeting_date'];    
        if ($meeting_date != "") {
            $meeting_date = strtotime($meeting_date);
        }
        if ($meeting_date != "" && (int) $meeting_date > 0) {
            update_post_meta($post->ID,'meeting_date',$meeting_date);
        } else {
            delete_post_meta($post->ID,'meeting_date');
        }
    }
}

add_filter('subpost_display_column','mythsoc_meeting_subpost_display_column',10,4);
function mythsoc_meeting_subpost_display_column($output,$post,$sub_post_type) {

    if ($sub_post_type['post_type']=="mythsoc_meeting") {
        $output .= "<td>";
        $meeting_date = get_post_meta($post->ID,"meeting_date",true);
        if ($meeting_date != "" && (int) $meeting_date > 0) {
            $output .= date('m/d/Y',$meeting_date);
        }
        $output .= "</td>";
    }
    return $output;

}