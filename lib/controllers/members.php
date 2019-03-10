<?php
namespace Roots\Sage\Members;

// build Member object for Member Profile page
function get_member_profile_info($id)
{
    if (empty($id)) {
        return false;
    }

    $query = new \WP_Query([
        'post_type' => 'user_profile',
        'p' => $id,
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;
            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'member-featured-image');
            $featured_image_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'member-profile-thumbnail');

            $user_id = get_post_meta($id, 'user_id', 1);
            $user_info = get_userdata($user_id);

            if (!empty($featured_image[0])) {
                $featured_image_src = $featured_image[0];
            } else {
                $featured_image_src = DEFAULT_MEMBER_IMAGE;
            }

            if (!empty($featured_image_thumb[0])) {
                $featured_image_thumb_src = $featured_image_thumb[0];
            } else {
                $featured_image_thumb_src = DEFAULT_MEMBER_PROFILE_IMAGE_THUMB;
            }

            $membership_type = get_post_meta($id, 'membership_type', 1);
            $membership_expiry_date = get_post_meta($id, 'membership_expiry_date', 1);
            $genre_1 = get_post_meta($id, 'genre_1', 1);
            $genre_2 = get_post_meta($id, 'genre_2', 1);
            $genre_3 = get_post_meta($id, 'genre_3', 1);
            $member_category = get_post_meta($id, 'member_category', 1);
            // $artist_name = get_post_meta($id, 'artist_name', 1);
            $additional_image_1 = get_post_meta($id, 'additional_image_1');
            $additional_image_2 = get_post_meta($id, 'additional_image_2');
            $additional_image_3 = get_post_meta($id, 'additional_image_3');
            $additional_image_4 = get_post_meta($id, 'additional_image_4');
            $facebook = get_post_meta($id, 'facebook', 1);
            $twitter = get_post_meta($id, 'twitter', 1);
            $youtube = get_post_meta($id, 'youtube', 1);
            $instagram = get_post_meta($id, 'instagram', 1);
            $website_url = get_post_meta($id, 'website_url', 1);
            $bandcamp = get_post_meta($id, 'bandcamp', 1);
            $soundcloud = get_post_meta($id, 'soundcloud', 1);
            $spotify = get_post_meta($id, 'spotify', 1);
            $contact_name = get_post_meta($id, 'contact_name', 1);
            $contact_email = get_post_meta($id, 'contact_email', 1);
            $tel_number = get_post_meta($id, 'tel_number', 1);
            // $biography = get_post_meta($id, 'biography', 1);
            $biography = $post->post_content;
            $testimonial = get_post_meta($id, 'testimonial', 1);

            $formatted_genre = "";
            if (!empty($genre_1['post_title'])) {$formatted_genre .= $genre_1['post_title'];}
            if (!empty($genre_2['post_title'])) {$formatted_genre .= " \ " . $genre_2['post_title'];}
            if (!empty($genre_3['post_title'])) {$formatted_genre .= " \ " . $genre_3['post_title'];}

            $obj = new \stdClass();
            $obj->ID = $id;
            $obj->user_id = $user_id;
            $obj->title = $title;
            $obj->membership_type = $membership_type;
            $obj->membership_expiry_date = $membership_expiry_date;
            $obj->featured_image_src = $featured_image_src;
            $obj->featured_image_thumb_src = $featured_image_thumb_src;
            $obj->formatted_genre = $formatted_genre;
            // $obj->artist_name = $artist_name;
            $obj->genre_1 = (isset($genre_1['post_title']) ? $genre_1['post_title'] : "");
            $obj->genre_2 = (isset($genre_2['post_title']) ? $genre_2['post_title'] : "");
            $obj->genre_3 = (isset($genre_3['post_title']) ? $genre_3['post_title'] : "");
            $obj->member_category = $member_category;
            $obj->member_email = (!empty($user_info->user_email) ? $user_info->user_email : "");
            $obj->member_role = (!empty($user_info->roles[0]) ? $user_info->roles[0] : "");
            $obj->additional_image_1 = $additional_image_1;
            $obj->additional_image_2 = $additional_image_2;
            $obj->additional_image_3 = $additional_image_3;
            $obj->additional_image_4 = $additional_image_4;
            $obj->additional_images = array($additional_image_1[0], $additional_image_2[0], $additional_image_3[0], $additional_image_4[0]);
            $obj->facebook = $facebook;
            $obj->twitter = $twitter;
            $obj->youtube = $youtube;
            $obj->instagram = $instagram;
            $obj->website_url = $website_url;
            $obj->bandcamp = $bandcamp;
            $obj->soundcloud = $soundcloud;
            $obj->spotify = $spotify;
            $obj->contact_name = $contact_name;
            $obj->contact_email = $contact_email;
            $obj->tel_number = $tel_number;
            $obj->biography = $biography;
            $obj->testimonial = $testimonial;
        }

        return $obj;
    }

    wp_reset_query();
    return;
}

// build Member object for Member Profile page
function get_member_profile_info_by_user_id($id)
{
    if (empty($id)) {
        return false;
    }

    $query = new \WP_Query([
        'post_type' => 'user_profile',
        'meta_query' => array(
            array(
                'key' => 'user_id',
                'value' => $id,
                'compare' => '=',
            ),
        ),
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            return get_member_profile_info($post->ID);
        }
    }

    wp_reset_query();
    return;
}

// check and return Member Role
function check_member_role($id = null)
{
    if (!empty($id)) {
        $user_id = get_post_meta($id, 'user_id', 1);
    } else {
        $user_id = get_post_meta(get_the_id(), 'user_id', 1);
    }

    if (!empty($user_id)) {
        $user_info = get_userdata($user_id);
        if (!empty($user_info->roles[0])) {
            return $user_info->roles[0];
        }
    }

    return;
}

// outputs noindex, nofollow if member is expired
function output_expired_member_meta_tags()
{
    $pt = get_post_type();
    if ($pt == 'user_profile') { // check if page is a Member Profile
        $user_role = check_member_role();

        // if Member role is Expired output meta tags noindex, nofollow <meta name="robots" content="noindex,nofollow">
        if ($user_role == 'expired_member') {
            return '<meta name="robots" content="noindex,nofollow">';
        }
    }

    return;
}

// return array of Expired Members User Profiles IDs
function get_expired_member_user_profile_ids()
{
    // Find all Expired Members
    $user_profiles_query = new \WP_Query([
        'post_type' => 'user_profile',
        'posts_per_page' => -1,
    ]);

    // set up expired members array
    $expired_members_array = [];

    if ($user_profiles_query->have_posts()) {
        foreach ($user_profiles_query->posts as $p) {
            if (check_member_role($p->ID) == 'expired_member') {
                $expired_members_array[] = $p->ID;
            }
        }
        return $expired_members_array;
    } else {
        return;
    }

    wp_reset_query();
}

// return array of Active Members User Profiles IDs
function get_active_member_user_profile_ids()
{
    // Find all Active Members
    $user_profiles_query = new \WP_Query([
        'post_type' => 'user_profile',
        'posts_per_page' => -1,
    ]);

    // set up active members array
    $active_members_array = [];

    if ($user_profiles_query->have_posts()) {
        foreach ($user_profiles_query->posts as $p) {
            if (check_member_role($p->ID) == 'active_member') {
                $active_members_array[] = $p->ID;
            }
        }
        return $active_members_array;
    } else {
        return;
    }

    wp_reset_query();
}

// return Member Directory list item info
function get_member_directory_info()
{
    $title = get_the_title();
    $featured_image_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_id()), 'member-thumbnail');

    if (!empty($featured_image_src[0])) {
        $image_src = $featured_image_src[0];
    } else {
        $image_src = DEFAULT_MEMBER_IMAGE_THUMB;
    }

    $html = "<div class='inner-wrap'><img src='$image_src' alt='$title'>
            <a class='member-link' href='" . get_the_permalink(get_the_id()) . "'>$title</a></div>";
    return $html;
}

// Autocomplete member search
add_action('wp_ajax_nopriv_get_member_search_results', __NAMESPACE__ . '\\get_member_search_results');
add_action('wp_ajax_get_member_search_results', __NAMESPACE__ . '\\get_member_search_results');
function get_member_search_results()
{
    $html = "";

    $search_str = $_POST['search_str'] . "%";

    global $wpdb;
    $pids = $wpdb->get_col($wpdb->prepare("
    SELECT      ID
    FROM        $wpdb->posts
  	WHERE       $wpdb->posts.post_title LIKE %s AND $wpdb->posts.post_status = 'publish'
  	AND 		$wpdb->posts.post_type = 'user_profile'", $search_str));

    if (!empty($pids)) {
        $html .= "<ul class='autocomplete-results'>";
        foreach ($pids as $p) {
            // $user_id = get_post_meta($p, 'user_id', 1);
            $user_role = check_member_role($p);
            if ($user_role == "active_member") {
                $ptitle = get_the_title($p);
                $html .= "<li class='autocomplete-result-item'><a href='#'>$ptitle</a></li>";
            }
        }
        $html .= "</ul>";
    }
    echo $html;
    die();
}

// Return Genre options formatted for Select drop down list
function get_genres_formated_dd($value)
{
    $html = "";
    $query = new \WP_Query([
        'post_type' => 'genre',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;
            $html .= "<option value='$id' " . (($title == $value) ? "selected='selected'" : "") . ">$title</option>";
        }
    }

    wp_reset_query();
    return $html;
}

// Return Member Category options formatted for Select drop down list
function get_member_cats_formated_dd($value)
{
    $html = "";
    $query = new \WP_Query([
        'post_type' => 'member_category',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;
            // $html .= "<pre>" . print_r($value, true) . "</pre>";
            $html .= "<option value='$id' " . (($id == $value['ID']) ? "selected='selected'" : "") . ">$title</option>";
        }
    }

    wp_reset_query();
    return $html;
}
