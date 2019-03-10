<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Members;
use Roots\Sage\Setup;

/**
 * Add <body> classes
 */
function body_class($classes)
{
    // Add page slug if it doesn't exist
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    // Add class if sidebar is active
    if (Setup\display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 **/
function excerpt_more()
{return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

// Move Yoast to bottom
add_filter('wpseo_metabox_prio', function () {return 'low';});

// link Custom Post Type to Gravty Form Dropdown
add_filter('gform_pre_render_1', __NAMESPACE__ . '\\populate_posts');
add_filter('gform_pre_validation_1', __NAMESPACE__ . '\\populate_posts');
add_filter('gform_pre_submission_filter_1', __NAMESPACE__ . '\\populate_posts');
add_filter('gform_admin_pre_render_1', __NAMESPACE__ . '\\populate_posts');
function populate_posts($form)
{
    foreach ($form['fields'] as &$field) {
        if ($field->type != 'select' || strpos($field->cssClass, 'populate-posts') === false) {
            continue;
        }

        // you can add additional parameters here to alter the posts that are retrieved
        // more info: [http://codex.wordpress.org/Template_Tags/get_posts](http://codex.wordpress.org/Template_Tags/get_posts)
        $posts = get_posts('post_type=genre&numberposts=-1&post_status=publish&orderby=name&order=ASC');
        $choices = array();

        foreach ($posts as $post) {
            $choices[] = array('text' => $post->post_title, 'value' => $post->post_title);
        }

        // update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = 'Select a Genre';
        $field->choices = $choices;
    }

    return $form;
}

// link Custom Post Type to Gravty Form Dropdown
add_filter('gform_pre_render_1', __NAMESPACE__ . '\\populate_member_categories');
add_filter('gform_pre_validation_1', __NAMESPACE__ . '\\populate_member_categories');
add_filter('gform_pre_submission_filter_1', __NAMESPACE__ . '\\populate_member_categories');
add_filter('gform_admin_pre_render_1', __NAMESPACE__ . '\\populate_member_categories');
function populate_member_categories($form)
{
    foreach ($form['fields'] as &$field) {
        if (strpos($field->cssClass, 'populate-member-categories') === false) {
            continue;
        }

        // you can add additional parameters here to alter the posts that are retrieved
        // more info: [http://codex.wordpress.org/Template_Tags/get_posts](http://codex.wordpress.org/Template_Tags/get_posts)
        $posts = get_posts('post_type=member_category&numberposts=-1&post_status=publish&orderby=name&order=ASC');
        $choices = array();

        foreach ($posts as $post) {
            $choices[] = array('text' => $post->post_title, 'value' => $post->post_title);
        }

        // update 'Select a Post' to whatever you'd like the instructive option to be
        $field->placeholder = 'Select a Category';
        $field->choices = $choices;
    }

    return $form;
}

// update member status to Active after payment has been processed
add_action('gform_paypal_post_ipn', __NAMESPACE__ . '\\update_member', 10, 4);
function update_member($ipn_post, $entry, $feed, $cancel)
{

    // if the IPN was canceled, don't process
    if ($cancel) {
        return;
    }

    $user = get_user_by('login', $entry[1]);

    // check for valid user login, update member status if valid
    if (!empty($user)) {
        if ($entry['form_id'] == '7') { // Renewal form
            $user_data = get_userdata($user->ID);
            $user_name = $user_data->data->display_name;
            $user_email = $user_data->data->user_email;
            update_member_status($ipn_post, $user);
            construct_activation_email($user_name, $user_email);
        } else {
            update_member_status($ipn_post, $user);
        }
    }

    return;
}
function update_member_status($ipn_post, $user)
{
    // set to expired first to fire trigger on wp_usermeta table
    if (!empty($ipn_post['txn_id'])) {
        wp_update_user(array('ID' => $user->ID, 'role' => 'expired_member'));
        wp_update_user(array('ID' => $user->ID, 'role' => 'active_member'));
    }

    return;
}
function construct_activation_email($name, $email)
{
    $permalink = get_the_permalink(44);
    $to = $email;
    $subject = 'Your membership has been renewed!';
    $message = '<h1>Thank you for renewing your membership!</h1><br /><p><a href="' . $permalink . '">Click here to login.</a></p>';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    wp_mail($to, $subject, $message, $headers);
}

add_action('gform_user_registered', __NAMESPACE__ . '\\add_user_profile', 10, 4);
function add_user_profile($user_id, $feed, $entry)
{
    $user = get_user_by('email', $entry[2]);

    // Gather post data.
    $up_post = array(
        'post_type' => 'user_profile',
        'post_title' => $entry[1],
        'post_status' => 'publish',
        'post_author' => 1,
    );

    // Insert a new User Profile post
    $post_id = wp_insert_post($up_post);

    // set up meta data for add_post_meta
    $p1 = get_page_by_title($entry[3], OBJECT, 'genre');
    $p2 = get_page_by_title($entry[4], OBJECT, 'genre');
    $p3 = get_page_by_title($entry[5], OBJECT, 'genre');
    $p4 = get_page_by_title($entry[15], OBJECT, 'member_category');
    $membership_type = explode("|", $entry[10]);
    // $member_cats = $entry[14];

    // Add user ID to post
    add_post_meta($post_id, 'user_id', $user->ID, true);
    if (!empty($p1->ID)) {add_post_meta($post_id, 'genre_1', $p1->ID);}
    if (!empty($p2->ID)) {add_post_meta($post_id, 'genre_2', $p2->ID);}
    if (!empty($p3->ID)) {add_post_meta($post_id, 'genre_3', $p3->ID);}
    if (!empty($p4->ID)) {add_post_meta($post_id, 'member_category', $p4->ID);}
    if (!empty($membership_type[0])) {add_post_meta($post_id, 'membership_type', $membership_type[0]);}
    if (!empty($membership_type[0])) {add_post_meta($post_id, 'membership_type', $membership_type[0]);}

    // testing purposes
    // $file_1 = $_SERVER['DOCUMENT_ROOT'].'/feed_output.txt';
    // $file_2 = $_SERVER['DOCUMENT_ROOT'].'/entry_output.txt';
    // $file_3 = '/app/user_output.txt';

    // Open the file to get existing content
    // $current_1 = "<pre>".print_r($p1, true)."</pre>";
    // $current_2 = "<pre>".print_r($entry, true)."</pre>";
    // $current_3 = "<pre>".print_r($p3, true)."</pre>";

    // Write the contents back to the file
    // file_put_contents($file_1, $current_1);
    // file_put_contents($file_2, $current_2);
    // file_put_contents($file_3, $current_3);

    return;
}

/***************************************
 * Gravity Forms notifications override
 * - send to emails to Member Email
 ****************************************/
add_filter('gform_notification_2', __NAMESPACE__ . '\\route_notification', 10, 3);
function route_notification($notification, $form, $entry)
{

    $notification['to'] = $entry[4];

    // $file_1 = '/var/www/public/feed_output.txt';
    // $current_1 = "<pre>".print_r($entry, true)."</pre>";
    // file_put_contents($file_1, $current_1);

    return $notification;
}

/* Custom pagination */
function wordpress_numeric_post_nav()
{
    // print_r($_POST);
    if (is_singular()) {
        return;
    }

    global $wp_query;
    /* Stop the code if there is only a single page page */
    if ($wp_query->max_num_pages <= 1) {
        return;
    }

    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max = intval($wp_query->max_num_pages);
    /*Add current page into the array */
    if ($paged >= 1) {
        $links[] = $paged;
    }

    /*Add the pages around the current page to the array */
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }
    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }
    echo '<div class="navigation"><ul>' . "\n";
    /*Display Previous Post Link */
    if (get_previous_posts_link()) {
        printf('<li class="prev">%s</li>' . "\n", get_previous_posts_link());
    }

    /*Display Link to first page*/
    if (!in_array(1, $links)) {
        $class = 1 == $paged ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link(1)), '1');
        if (!in_array(2, $links)) {
            echo '<li>…</li>';
        }

    }
    /* Link to current page */
    sort($links);
    foreach ((array) $links as $link) {
        $class = $paged == $link ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($link)), $link);
    }
    /* Link to last page, plus ellipses if necessary */
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links)) {
            echo '<li>…</li>' . "\n";
        }

        $class = $paged == $max ? ' class="active"' : '';
        printf('<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url(get_pagenum_link($max)), $max);
    }
    /** Next Post Link */
    if (get_next_posts_link()) {
        printf('<li class="next">%s</li>' . "\n", get_next_posts_link());
    }

    echo '</ul></div>' . "\n";
}

/* Custom pagination for user profiles / member directory */
function wordpress_user_profile_numeric_post_nav()
{
    if (is_singular()) {
        return;
    }

    $html = '';

    global $wp_query;

    /* Stop the code if there is only a single page page */
    if ($wp_query->max_num_pages <= 1) {
        return;
    }

    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max = intval($wp_query->max_num_pages);

    $search_key = '';
    $alpha_sort = '';
    $genre_sort = '';
    $category_sort = '';

    if (isset($_POST['member-search-str'])) {
        $search_key = $_POST['member-search-str'];
    } elseif (isset($_GET['ss'])) {
        $search_key = $_GET['ss'];
    }

    if (isset($_POST['filter-alphabetically'])) {
        $alpha_sort = $_POST['filter-alphabetically'];
    } elseif (isset($_GET['a'])) {
        $alpha_sort = $_GET['a'];
    }

    if (isset($_POST['filter-genre'])) {
        $genre_sort = $_POST['filter-genre'];
    } elseif (isset($_GET['g'])) {
        $genre_sort = $_GET['g'];
    }

    if (isset($_POST['filter-category']) && !empty($_POST['filter-category'])) {
        $genre_sort = $_POST['filter-category'];
    } elseif (isset($_GET['cat']) && !empty($_GET['cat'])) {
        $genre_sort = $_GET['cat'];
    }

    $query_args = '?ss=' . $search_key . '&a=' . $alpha_sort . '&g=' . $genre_sort . '&cat=' . $category_sort;

    // echo '<pre>';
    // print_r($_POST);
    // print_r($query_args);
    // echo '</pre>';

    /*Add current page into the array */
    if ($paged >= 1) {
        $links[] = $paged;
    }

    /*Add the pages around the current page to the array */
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }
    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }

    $html .= '<div class="member-directory-search-navigation navigation"><ul>' . "\n";

    /*Display Previous Post Link */
    if (get_previous_posts_link()) {
        $prev_posts_url = get_string_between(get_previous_posts_link(), 'href="', '"');

        // $current_query_args = get_string_between($prev_posts_url, '/', '');
        $current_args_str_pos = strpos($prev_posts_url, '?ss');

        if ($current_args_str_pos > 0) {
            $prev_posts_url = substr($prev_posts_url, 0, $current_args_str_pos);
        }
        $prev_posts_url .= $query_args;

        $html .= '<li class="prev"><a href="' . $prev_posts_url . '">Previous Page&raquo;</a></li>' . "\n";
    }

    /*Display Link to first page*/
    if (!in_array(1, $links)) {
        $class = 1 == $paged ? ' class="active"' : '';

        $page_link = esc_url(get_pagenum_link(1));

        $current_args_str_pos = strpos($page_link, '?ss');

        if ($current_args_str_pos > 0) {
            $page_link = substr($page_link, 0, $current_args_str_pos);
        }
        $page_link .= $query_args;

        $html .= '<li' . $class . '><a href="' . $page_link . '">1</a></li>' . "\n";

        if (!in_array(2, $links)) {
            $html .= '<li>…</li>';
        }

    }

    /* Link to current page */
    sort($links);
    foreach ((array) $links as $link) {
        $class = $paged == $link ? ' class="active"' : '';

        $page_link = esc_url(get_pagenum_link($link));

        $current_args_str_pos = strpos($page_link, '?ss');

        if ($current_args_str_pos > 0) {
            $page_link = substr($page_link, 0, $current_args_str_pos);
        }
        $page_link .= $query_args;

        $html .= '<li' . $class . '><a href="' . $page_link . '">' . $link . '</a></li>' . "\n";
    }

    /* Link to last page, plus ellipses if necessary */
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links)) {
            $html .= '<li>…</li>' . "\n";
        }

        $class = $paged == $max ? ' class="active"' : '';

        $page_link = esc_url(get_pagenum_link($max));

        $current_args_str_pos = strpos($page_link, '?ss');

        if ($current_args_str_pos > 0) {
            $page_link = substr($page_link, 0, $current_args_str_pos);
        }
        $page_link .= $query_args;

        $html .= '<li' . $class . '><a href="' . $page_link . '">' . $max . '</a></li>' . "\n";
    }

    /* Next Post Link */
    if (get_next_posts_link()) {
        $next_posts_url = get_string_between(get_next_posts_link(), 'href="', '"');

        $current_args_str_pos = strpos($next_posts_url, '?ss');

        if ($current_args_str_pos > 0) {
            $next_posts_url = substr($next_posts_url, 0, $current_args_str_pos);
        }
        $next_posts_url .= $query_args;

        $html .= '<li class="next"><a href="' . $next_posts_url . '">Next Page &raquo;</a></li>' . "\n";
    }

    $html .= '</ul></div>' . "\n";

    return $html;
}

/* Disable WordPress Admin Bar for all users but admins. */
show_admin_bar(false);

/* Return random background image for Home Page */
function get_random_background_image()
{
    $html = "";
    $query = new \WP_Query([
        'post_type' => 'static_image',
        'posts_per_page' => 1,
        'orderby' => 'rand',
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $description = get_post_meta($id, 'description', 1);
            $copyright = get_post_meta($id, 'copyright', 1);
            $img = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'hi-res');

            if (!empty($img[0])) {
                $html .= "<div class='static-image-wrap' style='background-image: url($img[0]);'>";
            }

            if (!empty($description) || !empty($copyright)) {
                $html .= "<div class='static-text'>
                    <p class='description'>$description</p>
                    <p class='copyright'>$copyright</p>
                  </div>";
            }
        }
    }

    return $html;
}

// update Member Role after user profile update
add_action('profile_update', __NAMESPACE__ . '\\set_membership_exp_date', 10, 2);
add_action('set_user_role', __NAMESPACE__ . '\\set_membership_exp_date', 10, 3);
function set_membership_exp_date($user_id, $old_user_data)
{
    $user_info = get_userdata($user_id);
    $date = new \DateTime();
    $date->modify('+1 year');
    $new_date = $date->format('Y-m-d');

    $query = new \WP_Query([
        'post_type' => 'user_profile',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => 'user_id',
                'value' => $user_id,
                'compare' => '=',
            ],
        ],
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            if ($user_info->roles[0] == 'active_member') {
                add_post_meta($p->ID, 'membership_expiry_date', $new_date);
            } else {
                add_post_meta($p->ID, 'membership_expiry_date', '');
            }
        }
    }

    wp_reset_query();
}

// Set membership to expired when past expiry date
if (!wp_next_scheduled('set_expiration_hook')) {
    wp_schedule_event(time(), 'daily', 'set_expiration_hook');
}

add_action('set_expiration_hook', __NAMESPACE__ . '\\set_expiration_function');
function set_expiration_function()
{
    $query = new \WP_Query([
        'post_type' => 'user_profile',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'membership_expiry_date',
                'value' => date("Y-m-d"),
                'compare' => '=',
                'type' => 'DATETIME,',
            ),
        ),
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $user_id = get_post_meta($p->ID, 'user_id', 1);
            $user_data = get_userdata($user_id);
            $user_name = $user_data->data->display_name;
            $user_email = $user_data->data->user_email;

            wp_update_user(array('ID' => $user_id, 'role' => 'expired_member'));
            construct_expiration_email($user_name, $user_email, 'Your membership expires today');
        }
    }
}

// 1 day member expiration notice
if (!wp_next_scheduled('expiration_notice_1_day_hook')) {
    wp_schedule_event(time(), 'daily', 'expiration_notice_1_day_hook');
}

add_action('expiration_notice_1_day_hook', __NAMESPACE__ . '\\expiration_notice_1_day_function');
function expiration_notice_1_day_function()
{
    $date = new \DateTime();
    $date->modify('+1 day');

    $query = new \WP_Query([
        'post_type' => 'user_profile',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'membership_expiry_date',
                'value' => $date->format('Y-m-d'),
                'compare' => '=',
                'type' => 'DATETIME,',
            ),
        ),
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $user_id = get_post_meta($p->ID, 'user_id', 1);
            $user_data = get_userdata($user_id);
            $user_name = $user_data->data->display_name;
            $user_email = $user_data->data->user_email;

            construct_expiration_email($user_name, $user_email, 'Your membership expires in 1 day');
        }
    }
}

// 30 day member expiration notice
if (!wp_next_scheduled('expiration_notice_30_day_hook')) {
    wp_schedule_event(time(), 'daily', 'expiration_notice_30_day_hook');
}

add_action('expiration_notice_30_day_hook', __NAMESPACE__ . '\\expiration_notice_30_day_function');
function expiration_notice_30_day_function()
{
    $date = new \DateTime();
    $date->modify('+30 days');

    $query = new \WP_Query([
        'post_type' => 'user_profile',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'membership_expiry_date',
                'value' => $date->format('Y-m-d'),
                'compare' => '=',
                'type' => 'DATETIME,',
            ),
        ),
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $user_id = get_post_meta($p->ID, 'user_id', 1);
            $user_data = get_userdata($user_id);
            $user_name = $user_data->data->display_name;
            $user_email = $user_data->data->user_email;

            construct_expiration_email($user_name, $user_email, 'Your membership expires in 30 days');
        }
    }
}

// Cron for push notifications
// if (!wp_next_scheduled('send_notifications_total_push')) {
//   wp_schedule_event(time(), 'weekly', 'send_notifications_total_push_hook');
// }

// add_action('send_notifications_total_push_hook', __NAMESPACE__ . '\\send_notifications_total_push_function');

// TESTING ONLY
// add_shortcode( 'send_notifications_total_push_function' , __NAMESPACE__ . '\\send_notifications_total_push_function' );

function send_notifications_total_push_function()
{
    $users = new \WP_Query([
        "post_type" => "user_profile",
        "posts_per_page" => -1,
        "meta_query" => [
            [
                "key" => "enable_push_notifications",
                "value" => 1,
                "compare" => "=",
            ],
        ],
    ]);

    $user_info = [];

    if ($users->have_posts()) {
        foreach ($users->posts as $p) {
            $user_id = get_post_meta($p->ID, "user_id", 1);
            $token = get_post_meta($p->ID, "expo_push_token", 1);
            $user_info[] = [
                "ID" => $user_id,
                "token" => $token,
            ];
        }
    }

    foreach ($user_info as $ui) {
        $q2 = new \WP_Query([
            "post_type" => "notification",
            "posts_per_page" => -1,
            "meta_query" => [
                [
                    "key" => "user_id",
                    "value" => $ui['ID'],
                    "compare" => "=",
                ],
            ],
        ]);

        if (($q2->found_posts > 0) && !empty($ui['token'])) {
            // Data in JSON format
            $data = array(
                'token' => $ui['token'],
                'message' => 'You have ' . $q2->found_posts . ' unread notifications',
            );

            $payload = json_encode($data);

            echo "<pre>" . print_r($payload, true) . "</pre>";

            // Prepare new cURL resource
            $ch = curl_init('https://google-cloud-function-domain/neoms-send-push-notification');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            // Set HTTP Header for POST request
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
            );

            // Submit the POST request
            $result = curl_exec($ch);

            echo "<pre>" . print_r($result, true) . "</pre>";

            // Close cURL session handle
            curl_close($ch);
        }
    }
}

function construct_expiration_email($name, $email, $subject_text)
{
    $permalink = get_the_permalink(591);
    $to = $email;
    $subject = $name . ' - ' . $subject_text . '!';
    $message = '<h1>Warning: Your membership is about to expire.</h1><br /><p><a href="' . $permalink . '?contact_name=' . $name . '&email_address=' . $email . '">Click here to renew instantly!</a></p>';
    $headers[] = 'Content-Type: text/html; charset=UTF-8';

    wp_mail($to, $subject, $message, $headers);
}

//modify query options to sort results by Published Date meta, DESC
function event_listing_archive_filter($query)
{
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('event_listing')) {
            $query->set('meta_key', 'e_date');
            $query->set('orderby', 'meta_value');
            $query->set('order', 'DESC');
            return;
        }
    }
}
add_action('pre_get_posts', __NAMESPACE__ . '\\event_listing_archive_filter', 1);

//modify query options to sort results by passed values
function user_profile_archive_filter($query)
{
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('user_profile')) {
            $active_members = \Roots\Sage\Members\get_active_member_user_profile_ids();
            // global $wp_query;
            global $wpdb;
            global $wp_query;
            $html = "";
            $specific_search = 0;

            // check if user is searching
            if ((isset($_POST) && !empty($_POST['member-search-str'])) || (isset($_GET) && !empty($_GET['ss']))) {
                $search_string = !empty($_POST['member-search-str']) ? $_POST['member-search-str'] : $_GET['ss'];
                $post__in = [];
                $search_str = "%" . $search_string . "%";

                // return ID's using LIKE comparison
                $pids = $wpdb->get_col($wpdb->prepare("
                    SELECT      ID
                    FROM        $wpdb->posts
                    WHERE       $wpdb->posts.post_title LIKE %s AND $wpdb->posts.post_status = 'publish'
                    AND 		$wpdb->posts.post_type = 'user_profile'", $search_str));

                // check if search string returns valid user profiles
                if (!empty($pids)) {
                    foreach ($pids as $p) {
                        $user_role = \Roots\Sage\Members\check_member_role($p);

                        // need to check if user is an active member
                        if ($user_role == "active_member") {
                            $post__in[] = $p;
                        }
                    }
                }

                // use array of valid user profile post ids for querying
                if (!empty($post__in)) {
                    $args = array_merge($wp_query->query_vars, array('post__in' => $post__in));
                    $query->set('post__in', $post__in);
                    $specific_search = 1;
                } else {
                    // need to reset the Args array, otherwise it'll show all users (including expired)
                    $query->set('post__in', $active_members);
                }
            } elseif (
                (isset($_POST) && !empty($_POST['filter-alphabetically']) && !empty($_POST['filter-genre']) && !empty($_POST['filter-category'])) ||
                (isset($_GET) && !empty($_GET['a']) && !empty($_GET['g']) && !empty($_GET['cat']))) {
                $alpha_value = !empty($_POST['filter-alphabetically']) ? $_POST['filter-alphabetically'] : $_GET['a'];
                $genre_value = !empty($_POST['filter-genre']) ? $_POST['filter-genre'] : $_GET['g'];
                $category_value = !empty($_POST['filter-category']) ? $_POST['filter-category'] : $_GET['cat'];

                $query->set('orderby', 'title');
                $query->set('order', $alpha_value);
                $query->set('meta_query', [
                    'relation' => 'AND', [
                        'relation' => 'OR',
                        [
                            'key' => 'genre_1',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                        [
                            'key' => 'genre_2',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                        [
                            'key' => 'genre_3',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                    ], [
                        'key' => 'staff_profile',
                        'compare' => 'NOT EXISTS',
                    ], [
                        'key' => 'member_category',
                        'value' => $category_value,
                        'compare' => '=',
                    ],
                ]);
            } elseif ((isset($_POST) && !empty($_POST['filter-alphabetically']) && !empty($_POST['filter-genre'])) || (isset($_GET) && !empty($_GET['a']) && !empty($_GET['g']))) {
                $alpha_value = !empty($_POST['filter-alphabetically']) ? $_POST['filter-alphabetically'] : $_GET['a'];
                $genre_value = !empty($_POST['filter-genre']) ? $_POST['filter-genre'] : $_GET['g'];

                $query->set('orderby', 'title');
                $query->set('order', $alpha_value);
                $query->set('meta_query', [
                    'relation' => 'AND', [
                        'relation' => 'OR',
                        [
                            'key' => 'genre_1',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                        [
                            'key' => 'genre_2',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                        [
                            'key' => 'genre_3',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                    ], [
                        'key' => 'staff_profile',
                        'compare' => 'NOT EXISTS',
                    ],
                ]);
            } elseif ((isset($_POST) && !empty($_POST['filter-alphabetically']) && !empty($_POST['filter-category'])) || (isset($_GET) && !empty($_GET['a']) && !empty($_GET['cat']))) {
                $alpha_value = !empty($_POST['filter-alphabetically']) ? $_POST['filter-alphabetically'] : $_GET['a'];
                $category_value = !empty($_POST['filter-category']) ? $_POST['filter-category'] : $_GET['cat'];

                $query->set('orderby', 'title');
                $query->set('order', $alpha_value);
                $query->set('meta_query', [
                    'relation' => 'AND',
                    [
                        'key' => 'staff_profile',
                        'compare' => 'NOT EXISTS',
                    ], [
                        'key' => 'member_category',
                        'value' => $category_value,
                        'compare' => '=',
                    ],
                ]);
            } elseif ((isset($_POST) && !empty($_POST['filter-alphabetically'])) || (isset($_GET) && !empty($_GET['a']))) {
                $alpha_value = !empty($_POST['filter-alphabetically']) ? $_POST['filter-alphabetically'] : $_GET['a'];

                if (!empty($alpha_value)) {
                    $query->set('orderby', 'title');
                    $query->set('order', $alpha_value);
                }
            } elseif ((isset($_POST) && !empty($_POST['filter-genre'])) || (isset($_GET) && !empty($_GET['g']))) {
                $genre_value = !empty($_POST['filter-genre']) ? $_POST['filter-genre'] : $_GET['g'];

                $query->set('meta_query', [
                    'relation' => 'AND', [
                        'relation' => 'OR',
                        [
                            'key' => 'genre_1',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                        [
                            'key' => 'genre_2',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                        [
                            'key' => 'genre_3',
                            'value' => $genre_value,
                            'compare' => '=',
                        ],
                    ], [
                        'key' => 'staff_profile',
                        'compare' => 'NOT EXISTS',
                    ],
                ]);
            } elseif ((isset($_POST) && !empty($_POST['filter-category'])) || (isset($_GET) && !empty($_GET['cat']))) {
                $category_value = !empty($_POST['filter-category']) ? $_POST['filter-category'] : $_GET['cat'];

                $query->set('meta_query', [
                    'relation' => 'AND',
                    [
                        'key' => 'member_category',
                        'value' => $category_value,
                        'compare' => '=',
                    ], [
                        'key' => 'staff_profile',
                        'compare' => 'NOT EXISTS',
                    ],
                ]);
            } else {
                $query->set('post_type', 'user_profile');
                $query->set('orderby', 'rand');
            }

            // check if searching for specific user
            // -- DO NOT include active_members if true
            if (!$specific_search) {
                $query->set('post__in', $active_members);
            }

            if ((empty($_POST['filter-alphabetically']) && empty($_POST['filter-genre']) && empty($_POST['filter-category'])) && (empty($_GET['a']) && empty($_GET['g']) && empty($_GET['cat']))) {
                $query->set('meta_query', [
                    [
                        'key' => 'staff_profile',
                        'compare' => 'NOT EXISTS',
                    ],
                ]
                );
            }

            // set number of posts to return
            $query->set('posts_per_page', 24);

            // echo '<pre>';
            // print_r($query);
            // echo '</pre>';

            return;
        }
    }
}
add_action('pre_get_posts', __NAMESPACE__ . '\\user_profile_archive_filter', 1);

//Return Substring between 2 different strings
function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) {
        return '';
    }

    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

// Member Directory Archive Pagination
// add_action( 'generate_rewrite_rules', __NAMESPACE__ . '\\member_directory_archive_pagination_url_rewrite' );
// function member_directory_archive_pagination_url_rewrite( $wp_rewrite ){
//   add_rewrite_rule('^member-directory/page/([0-9]+)/$/$/?', 'member_directory/page/([0-9]+)/?a=$matches[1]&g=$matches[2]', 'top');
// }

// add_action( 'template_redirect', __NAMESPACE__ . '\\redirect_to_specific_page' );
// function redirect_to_specific_page() {
//     if ( is_page_template( 'poll-vote' ) && ( !is_user_logged_in() || !get_option('company_info_award_poll_voting_open') ) ) {
//         wp_redirect( '/', 301 );
//         exit;
//     }
// }

remove_filter('the_content', __NAMESPACE__ . '\\wpautop');
remove_filter('the_excerpt', __NAMESPACE__ . '\\wpautop');

/**
 * Capture user login and add it as timestamp in user meta data
 *
 */

// function user_last_login($user_login, $user)
// {
//     // update_user_meta($user->ID, 'previous_login', $last_login);

//     $current_login = get_user_meta($user->ID, 'current_login', 1);
//     if (!empty($current_login)) {update_user_meta($user->ID, 'previous_login', $current_login);}
//     update_user_meta($user->ID, 'current_login', time());

//     $previous_login = get_user_meta($user->ID, 'previous_login', 1);
//     if (!empty($previous_login)) {
//         get_notifications($previous_login);
//     } else {
//         get_notifications(strtotime('-7 days', $previous_login));
//     }
// }
// add_action('wp_login', __NAMESPACE__ . '\\user_last_login', 10, 2);

/**
 * Display last login time
 *
 */

// function wpb_lastlogin()
// {
//     $last_login = date("Y m d", get_the_author_meta('last_login'));
//     $the_login_date = human_time_diff($last_login);
//     return $last_login;
// }

/**
 * Add Shortcode lastlogin
 *
 */

// add_shortcode('lastlogin', __NAMESPACE__ . '\\wpb_lastlogin');

function set_user_current_login()
{
    if (is_user_logged_in() && !current_user_can('administrator')) {
        // Set timezone for local Newfoundland
        date_default_timezone_set("Canada/Newfoundland");
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        update_user_meta($user_id, 'current_login', date("Y-m-d H:i:s"));

        // testing purposes
        // $file_1 = $_SERVER['DOCUMENT_ROOT'] . '/login_output.txt';
        // $current_1 = "<pre>" . print_r($user_id, true) . "</pre>\n";
        // $current_1 .= "<pre>" . print_r(date("Y-m-d H:i:s"), true) . "</pre>\n";
        // file_put_contents($file_1, $current_1);
    }
}
add_action('wp', __NAMESPACE__ . '\\set_user_current_login');

function set_user_previous_login()
{
    // Set timezone for local Newfoundland
    date_default_timezone_set("Canada/Newfoundland");
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    update_user_meta($user_id, 'current_login', "");
    update_user_meta($user_id, 'previous_login', date("Y-m-d H:i:s"));

    // testing purposes
    // $file_1 = $_SERVER['DOCUMENT_ROOT'] . '/logout_output.txt';
    // $current_1 = "<pre>" . print_r($user_id, true) . "</pre>\n";
    // $current_1 .= "<pre>" . print_r(date("Y-m-d H:i:s"), true) . "</pre>\n";
    // file_put_contents($file_1, $current_1);
}
add_action('wp_logout', __NAMESPACE__ . '\\set_user_previous_login');

function update_notifications()
{
    if (is_user_logged_in() && !current_user_can('administrator')) {
        date_default_timezone_set("Canada/Newfoundland");
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $user_name = $current_user->user_login;

        $current_login = strtotime(get_user_meta($user_id, 'current_login', 1));
        $previous_login = strtotime(get_user_meta($user_id, 'previous_login', 1));

        if (!empty($current_login) && !empty($previous_login)) {
            $n_query = new \WP_Query([
                'post_type' => 'notification',
                'posts_per_page' => -1,
                'meta_query' => [
                    [
                        'key' => 'user_id',
                        'value' => $user_id,
                        'compare' => '=',
                    ],
                ],
            ]);

            $e_ids = [];

            if ($n_query->have_posts()) {
                foreach ($n_query->posts as $np) {
                    $nid = $np->ID;
                    $e_ids[] = get_post_meta($nid, 'event_id', 1);
                }
            }

            wp_reset_query();

            $query = new \WP_Query([
                'post_type' => 'event_listing',
                'posts_per_page' => -1,
                'post__not_in' => $e_ids,
                'meta_key' => 'e_date',
                'orderby' => 'meta_value',
                'order' => 'ASC',
                'meta_query' => [
                    'relation' => 'AND',
                    [
                        'key' => 'e_date',
                        'value' => date("Y-m-d", $previous_login),
                        'type' => 'DATETIME',
                        'compare' => '>',
                    ],
                    [
                        'key' => 'e_date',
                        'value' => date("Y-m-d", $current_login),
                        'type' => 'DATETIME',
                        'compare' => '<',
                    ],
                ],
            ]);

            if ($query->have_posts()) {
                foreach ($query->posts as $p) {
                    $eid = $p->ID;
                    $title = $p->post_title;
                    $excerpt = $p->post_excerpt;
                    $e_date = get_post_meta($p->ID, 'e_date', 1);

                    $up_post = [
                        'post_type' => 'notification',
                        'post_title' => $title,
                        'post_status' => 'publish',
                        'post_excerpt' => $excerpt,
                    ];

                    // Insert a new User Created Event
                    $post_id = wp_insert_post($up_post);
                    add_post_meta($post_id, 'user_id', $user_id);
                    add_post_meta($post_id, 'user_name', $user_name);
                    add_post_meta($post_id, 'event_id', $eid);
                }
            }

            wp_reset_query();
        }
    }

    // return "<pre>".print_r($query->posts, true)."</pre>";
}
add_action('wp', __NAMESPACE__ . '\\update_notifications', 99);
