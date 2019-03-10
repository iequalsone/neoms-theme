<?php

function get_member_login_form($atts)
{
    // $a = shortcode_atts( array(
    //     'category' => '',
    //     'upcoming' => 'no',
    // ), $atts );

    // $html = '';
    // $args = [];

    ob_start();
    the_widget('GFLoginWidget', [
        'title' => __('Login', 'gravityformsuserregistration'),
        'tabindex' => 25,
        'logged_in_title' => 'Welcome {user:display_name}',
        'logged_in_avatar' => '',
        'logged_in_links' => [
            [
                'text' => '',
                'url' => '',
            ],
        ],
        'logged_in_message' => '',
        'logged_out_links' => [
            [
                'text' => esc_html__('Register', 'gravityformsuserregistration'),
                'url' => '{register_url}',
            ],
            [
                'text' => esc_html__('Forgot Password?', 'gravityformsuserregistration'),
                'url' => '{password_url}',
            ],
        ],
        'login_redirect_url' => '/sage/check-login.php',
        'logout_redirect_url' => '/',
    ]);
    $widget = ob_get_clean();

    return $widget;
}

function rrssb_share($atts, $content = "")
{
    ob_start();
    get_template_part('templates/rrssb');
    return ob_get_clean();
}

function get_staff_members($atts)
{
    $a = shortcode_atts(array(
        'show_board_of_directors' => false,
    ), $atts);

    $html = '';

    if ($a['show_board_of_directors']) {
        $args = [
            'post_type' => 'staff_member',
            'posts_per_page' => -1,
            'meta_key' => 'board_of_directors',
            'meta_value' => 1,
            'meta_compare' => '=',
            'order' => 'ASC',
            'orderby' => 'menu_order',
        ];
    } else {
        $args = [
            'post_type' => 'staff_member',
            'posts_per_page' => -1,
            'meta_key' => 'board_of_directors',
            'meta_value' => 0,
            'meta_compare' => '=',
            'order' => 'ASC',
            'orderby' => 'menu_order',
        ];
    }

    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;
            $gender = get_post_meta($id, 'gender', 1);
            $position = get_post_meta($id, 'position', 1);
            $content = apply_filters('the_content', $post->post_content);

            $member_profile = get_post_meta($id, 'member_profile', 1);
            // print_r($member_profile);
            if (!empty($member_profile)) {
                $member_profile_image = wp_get_attachment_image_src(get_post_thumbnail_id($member_profile['ID']), 'member-profile-thumbnail');
                $content = !empty($member_profile['post_content']) ? $member_profile['post_content'] : $content;
                if (!empty($member_profile_image[0])) {
                    $member_profile_image_src = $member_profile_image[0];
                } else {
                    $member_profile_image_src = DEFAULT_MEMBER_PROFILE_IMAGE_THUMB;
                }
            } else {
                $member_profile_image_src = DEFAULT_MEMBER_PROFILE_IMAGE_THUMB;
            }

            $html .= "<div class='row staff-row'>
                    <div class='col-sm-3 text-center staff-profile-image'>
                      <img src='$member_profile_image_src' alt='$title'>
                    </div>
                    <div class='col-sm-9 staff-info'>
                      <h3>$title</h3>
                      <p class='position'>$position</p>
                      " . (!empty($content) ? $content : "") . "
                      " . (!empty($member_profile) ? "<a href='" . get_the_permalink($member_profile['ID']) . "'>View full profile</a>" : "") . "
                    </div>
                  </div>";
        }
    }

    return $html;
}

function sub_nav_holder($atts)
{
    $html = " <h2>Menu</h2>
              <ul class='sub-nav-holder'></ul>";
    return $html;
}

function get_documentation($atts)
{
    // $a = shortcode_atts( array(
    //     'show_board_of_directors' => false
    // ), $atts );

    $html = '';

    $args = [
        'post_type' => 'documentation',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'menu_order',
    ];

    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;
            $description = get_post_meta($id, 'description', 1);
            $file = get_post_meta($id, 'file', 1);
            if (!empty($file['ID'])) {
                $file_src = wp_get_attachment_url($file['ID']);
            }

            if (!empty($file_src)) {
                $html .= "<div class='row documentation-row'>
                      <div class='col-sm-12'>
                        <div class='single-doc'>
                          <h3>$title</h3>
                          <p>$description</p>
                          <a href='$file_src' target='_blank'></a>
                        </div>
                      </div>
                    </div>";
            }
        }
    }

    return $html;
}

// return contact info
function get_social_links($atts)
{
    $html = "";
    if (COMPANY_ADDRESS_FACEBOOK || COMPANY_ADDRESS_TWITTER || COMPANY_ADDRESS_YOUTUBE) {
        $html .= "<ul class='social-media-icons'>";
        if (COMPANY_ADDRESS_FACEBOOK) {
            $html .= "<li><a href='" . COMPANY_ADDRESS_FACEBOOK . "' target='_blank'><i class='fa fa-facebook-square'></i></a></li>";
        }

        if (COMPANY_ADDRESS_TWITTER) {
            $html .= "<li><a href='" . COMPANY_ADDRESS_TWITTER . "' target='_blank'><i class='fa fa-twitter'></i></a></li>";
        }

        if (COMPANY_ADDRESS_YOUTUBE) {
            $html .= "<li><a href='" . COMPANY_ADDRESS_YOUTUBE . "' target='_blank'><i class='fa fa-youtube-square'></i></a></li>";
        }
        $html .= "</ul>";
    }

    return $html;
}

// return contact info
function get_contact_info($atts)
{
    $html = "";
    $html .= "<div class='contact-info-wrap'>";
    $html .= "<p class='font-montserrat bold font-large'><span class='white'>MUSIC</span><span class='green'>NL</span></p>";
    if (COMPANY_ADDRESS_LINE_1) {$html .= "<p>" . COMPANY_ADDRESS_LINE_1;}
    if (COMPANY_ADDRESS_LINE_2) {$html .= "<br />" . COMPANY_ADDRESS_LINE_2 . "</p>";
    } else { $html .= "</p>";}

    if (COMPANY_ADDRESS_TEL) {$html .= "<p>TEL: <a href='tel:" . COMPANY_ADDRESS_TEL . "'>" . COMPANY_ADDRESS_TEL . "</a></p>";}

    if (COMPANY_ADDRESS_EMAIL) {$html .= "<p>EMAIL: <a href='mailto:" . COMPANY_ADDRESS_EMAIL . "'>" . COMPANY_ADDRESS_EMAIL . "</a></p>";}

    if (COMPANY_ADDRESS_FACEBOOK || COMPANY_ADDRESS_TWITTER || COMPANY_ADDRESS_YOUTUBE) {
        $html .= "<p class='social-wrap'>";
        if (COMPANY_ADDRESS_FACEBOOK) {
            $html .= "<a href='" . COMPANY_ADDRESS_FACEBOOK . "' target='_blank'><i class='fa fa-facebook-square'></i></a>";
        }

        if (COMPANY_ADDRESS_TWITTER) {
            $html .= "<a href='" . COMPANY_ADDRESS_TWITTER . "' target='_blank'><i class='fa fa-twitter'></i></a>";
        }

        if (COMPANY_ADDRESS_YOUTUBE) {
            $html .= "<a href='" . COMPANY_ADDRESS_YOUTUBE . "' target='_blank'><i class='fa fa-youtube-square'></i></a>";
        }
        $html .= "</p>";
    }
    $html .= "</div>";
    return $html;
}

// return Staff Contact Info
function get_staff_contact_info($atts)
{
    $html = "";

    $args = [
        'post_type' => 'staff_member',
        'posts_per_page' => -1,
        'meta_key' => 'board_of_directors',
        'meta_value' => 0,
        'meta_compare' => '=',
        'order' => 'ASC',
        'orderby' => 'menu_order',
    ];

    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;
            $position = get_post_meta($id, 'position', 1);

            $member_profile = get_post_meta($id, 'member_profile', 1);
            if (!empty($member_profile)) {
                $tel = get_post_meta($member_profile['ID'], 'tel_number', 1);
                $email = get_post_meta($member_profile['ID'], 'contact_email', 1);
                $profile_link = get_the_permalink($member_profile['ID']);
            } else {
                $tel = "";
                $email = "";
                $profile_link = "";
            }

            $html .= "<div class='staff-contact row'>
                    <div class='col-sm-12'>
                      <p class='title'>$title" . (!empty($position) ? " - $position" : "") . "</p>
                    </div>
                    <div class='col-sm-12 col-lg-5'>
                      <p>Tel: <a href='tel:$tel'>$tel</a></p>
                    </div>
                    <div class='col-sm-12 col-lg-7'>
                      <p>Email: <a href='mailto:$email'>$email</a></p>
                    </div>
                    <div class='col-sm-12'><a href='$profile_link'><small>View Profile</small></a></div>
                  </div>";
        }
    }

    return $html;
}

function get_latest_news_home_page($atts)
{
    $a = shortcode_atts(array(
        'offset' => '',
    ), $atts);

    if (!empty($a['offset'])) {
        $args = [
            'post_type' => 'news_item',
            'posts_per_page' => 2,
            'offset' => $a['offset'],
            'posts_per_page' => 2,
        ];
    } else {
        $args = [
            'post_type' => 'news_item',
            'posts_per_page' => 2,
            'posts_per_page' => 2,
        ];
    }

    $html = '';
    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $excerpt = substr($p->post_excerpt, 0, 150);
            $sub_title = get_post_meta($id, 'sub_title', 1);
            $pub_date = get_post_meta($id, 'publication_date', 1);
            $terms = get_the_terms($id, 'news_category');

            $html .= "<div class='col-sm-12 home-news-item no-padding'>
                    <article class='news_item " . (!empty($terms[0]) ? $terms[0]->slug : "") . "'>
                      <a href='" . get_the_permalink($id) . "'></a>
                      <header>
                        <sub>" . (!empty($terms[0]) ? $terms[0]->name : "") . (!empty($pub_date) ? date(" | F j, Y", strtotime($pub_date)) : "") . "</sub>
                        <h2 class='entry-title'><a href='" . get_the_permalink($id) . "'>$title</a></h2>
                        " . (!empty($sub_title) ? "<h3>$sub_title</h3>" : "") . "
                      </header>
                      <div class='entry-summary'>
                        <p>$excerpt</p>
                        <a class='font-montserrat' href='" . get_the_permalink($id) . "'>Learn More</a>
                      </div>
                    </article>
                  </div>";
        }
    }

    return $html;
}

function get_pinned_news_home_page($atts)
{
    $a = shortcode_atts(array(
        'offset' => '',
    ), $atts);

    if (!empty($a['offset'])) {
        $args = [
            'post_type' => 'news_item',
            'posts_per_page' => 2,
            'offset' => $a['offset'],
            'posts_per_page' => 2,
            'meta_key' => 'pin_to_front',
            'meta_value' => 1,
            'meta_compare' => '=',
        ];
    } else {
        $args = [
            'post_type' => 'news_item',
            'posts_per_page' => 2,
            'meta_key' => 'pin_to_front',
            'meta_value' => 1,
            'meta_compare' => '=',
        ];
    }

    $html = '';
    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $excerpt = substr($p->post_excerpt, 0, 150);
            $sub_title = get_post_meta($id, 'sub_title', 1);
            $pub_date = get_post_meta($id, 'publication_date', 1);
            $terms = get_the_terms($id, 'news_category');

            $html .= "<div class='col-sm-12 home-news-item no-padding'>
                    <article class='news_item " . (!empty($terms[0]) ? $terms[0]->slug : "") . "'>
                      <a href='" . get_the_permalink($id) . "'></a>
                      <header>
                        <sub>" . (!empty($terms[0]) ? $terms[0]->name : "") . (!empty($pub_date) ? date(" | F j, Y", strtotime($pub_date)) : "") . "</sub>
                        <h2 class='entry-title'><a href='" . get_the_permalink($id) . "'>$title</a></h2>
                        " . (!empty($sub_title) ? "<h3>$sub_title</h3>" : "") . "
                      </header>
                      <div class='entry-summary'>
                        <p>$excerpt</p>
                        <a class='font-montserrat' href='" . get_the_permalink($id) . "'>Learn More</a>
                      </div>
                    </article>
                  </div>";
        }
    }

    return $html;
}

function get_events_home_page($atts)
{
    // $a = shortcode_atts( array(
    //     'offset' => ''
    // ), $atts );

    $html = '';
    $query = new \WP_Query([
        'post_type' => 'event_listing',
        'posts_per_page' => 4,
        // 'meta_type' => 'DATE',
        'meta_query' => [
            [
                'key' => 'start_datetime',
                'type' => 'DATETIME',
                'value' => date('Y-m-d'),
                'compare' => '>=',
            ],
        ],
        'meta_key' => 'e_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $sub_title = get_post_meta($id, 'sub_title', 1);
            // $event_date = get_post_meta($id, 'e_date', 1);
            $event_date = get_post_meta($id, 'start_datetime', 1);
            $time = get_post_meta($id, 'time', 1);
            $location = get_post_meta($id, 'location', 1);
            $terms = get_the_terms($id, 'event_category');

            $html .= "<div class='col-sm-12 home-event-listing no-padding'>
                    <article class='event_listing'>
                      <a href='" . get_the_permalink($id) . "'></a>
                      <div class='col-xs-3 event-date'>
                        <span class='month'>" . date("F", strtotime($event_date)) . "</span>
                        <span class='day'>" . date("j", strtotime($event_date)) . "</span>
                      </div>
                      <div class='col-xs-9'>
                        <header>
                          <h2>
                            " . (!empty($terms[0]) ? $terms[0]->name . " - " : "") . "
                            " . $title . "
                            " . (!empty($location) ? " @ " . $location : "") . "
                            " . (!empty($time) ? " - " . $time : "") . "
                          </h2>
                          " . (!empty($sub_title) ? "<h3 class='entry-title'><a href='" . get_the_permalink($id) . "'>" . substr($sub_title, 0, 75) . "</a></h3>" : "") . "
                        </header>
                        <div class='entry-summary'>
                          <a href='" . get_the_permalink($id) . "'>View Event Info</a>
                        </div>
                      </div>
                    </article>
                  </div>";
        }
    }

    return $html;
}

function get_pinned_events_home_page($atts)
{
    // $a = shortcode_atts( array(
    //     'offset' => ''
    // ), $atts );

    $html = '';
    $query = new \WP_Query([
        'post_type' => 'event_listing',
        'posts_per_page' => 4,
        'meta_query' => [
            [
                'key' => 'pin_to_front',
                'value' => 1,
                'compare' => '=',
            ],
        ],
        'meta_key' => 'e_date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
    ]);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $sub_title = get_post_meta($id, 'sub_title', 1);
            $event_date = get_post_meta($id, 'e_date', 1);
            $time = get_post_meta($id, 'time', 1);
            $location = get_post_meta($id, 'location', 1);
            $terms = get_the_terms($id, 'event_category');

            $html .= "<div class='col-sm-12 home-event-listing no-padding'>
                    <article class='event_listing'>
                      <a href='" . get_the_permalink($id) . "'></a>
                      <div class='col-xs-3 event-date'>
                        <span class='month'>" . date("F", strtotime($event_date)) . "</span>
                        <span class='day'>" . date("j", strtotime($event_date)) . "</span>
                      </div>
                      <div class='col-xs-9'>
                        <header>
                          <h2>
                            " . (!empty($terms[0]) ? $terms[0]->name . " - " : "") . "
                            " . $title . "
                            " . (!empty($location) ? " @ " . $location : "") . "
                            " . (!empty($time) ? " - " . $time : "") . "
                          </h2>
                          " . (!empty($sub_title) ? "<h3 class='entry-title'><a href='" . get_the_permalink($id) . "'>" . substr($sub_title, 0, 75) . "</a></h3>" : "") . "
                        </header>
                        <div class='entry-summary'>
                          <a href='" . get_the_permalink($id) . "'>View Event Info</a>
                        </div>
                      </div>
                    </article>
                  </div>";
        }
    }

    return $html;
}

function get_members_home_page($atts)
{
    // $a = shortcode_atts( array(
    //     'offset' => ''
    // ), $atts );

    $html = '';

    $active_members = Roots\Sage\Members\get_active_member_user_profile_ids();
    $query = new \WP_Query([
        'post_type' => 'user_profile',
        'posts_per_page' => 12,
        'orderby' => 'rand',
        'post__in' => $active_members,
        'meta_query' => [
            [
                'key' => 'staff_profile',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ]);

    if ($query->have_posts()) {
        $html .= "<div class='row'><div class='col-sm-1 hidden-xs'><a class='desktop-prev' href='#'><i class='fa fa-angle-double-left' aria-hidden='true'></i></a></div>";
        $html .= "<div class='col-xs-12 col-sm-10'><div class='member-spotlight-wrap'>";
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $genre_1 = get_post_meta($id, 'genre_1', 1);
            $genre_2 = get_post_meta($id, 'genre_2', 1);
            $genre_3 = get_post_meta($id, 'genre_3', 1);
            $formatted_genre = "";
            if (!empty($genre_1['post_title'])) {$formatted_genre .= $genre_1['post_title'];}
            if (!empty($genre_2['post_title'])) {$formatted_genre .= " \ " . $genre_2['post_title'];}
            if (!empty($genre_3['post_title'])) {$formatted_genre .= " \ " . $genre_3['post_title'];}
            $facebook = get_post_meta($id, 'facebook', 1);
            $twitter = get_post_meta($id, 'twitter', 1);
            $youtube = get_post_meta($id, 'youtube', 1);
            $content = $p->post_content;
            $content = strip_tags(substr($content, 0, 150));
            $permalink = get_permalink($id);
            $img = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'member-profile-thumbnail');
            if (!empty($img[0])) {
                $imgSrc = $img[0];
            } else {
                $imgSrc = '/sage/dist/images/member-default-image.jpg';
            }

            $html .= "<div class='row member-spotlight'>
                    <div class='col-sm-3 image-wrap clearfix'>
                      <img src='$imgSrc' alt='$title'>
                      <div class='mobile-nav visible-xs col-xs-12 clearfix'>
                        <a class='mobile-prev' href='#'><i class='fa fa-angle-double-left' aria-hidden='true'></i></a>
                        <a class='mobile-next' href='#'><i class='fa fa-angle-double-right' aria-hidden='true'></i></a>
                      </div>
                    </div>
                    <div class='col-sm-9 text-wrap'>
                      <h3>$title</h3>
                      <h4>$formatted_genre</h4>
                      $content
                      <p class='profile-link'><a href='$permalink'>View Profile</a></p>
                      <p class='social-wrap'>
                        <a href='$facebook' target='_blank'><i class='fa fa-facebook-square'></i></a>
                        <a href='$twitter' target='_blank'><i class='fa fa-twitter'></i></a>
                        <a href='$youtube' target='_blank'><i class='fa fa-youtube-square'></i></a>
                      </p>
                    </div>
                  </div>";
        }
        $html .= "</div></div><div class='col-sm-1 hidden-xs'><a class='desktop-next' href='#'><i class='fa fa-angle-double-right' aria-hidden='true'></i></a></div></div>";
    }

    $html .= "<div class='row'>
                <div class='col-sm-12'><a class='red' href='" . get_post_type_archive_link('user_profile') . "'>VIEW ALL</a></div>
              </div>";

    return $html;
}

function get_newsletter_signup()
{
    $html = "<div id='mc_embed_signup'>
              <form action='' method='post' id='mc-embedded-subscribe-form' name='mc-embedded-subscribe-form' class='validate' target='_blank'>
                <div class='row'>
                  <div class='col-xs-8'>
                    <input type='email' value='' name='EMAIL' class='required email font-montserrat' id='mce-EMAIL' placeholder='Enter email address here' required='required'>
                  </div>
                  <div class='col-xs-4'>
                    <input type='submit' value='Subscribe' name='subscribe' id='mc-embedded-subscribe' class='button font-montserrat'>
                  </div>
                </div>
                <div id='mce-responses' class='clear'>
                  <div class='response' id='mce-error-response' style='display:none'></div>
                  <div class='response' id='mce-success-response' style='display:none'></div>
                </div>
                <div style='position: absolute; left: -5000px;' aria-hidden='true'><input type='text' name='b_8b1a29192e67a8ea3c2d4d7b9_509ec5f61c' tabindex='-1' value=''></div>
              </form>
            </div>";
    return $html;
}

function get_industry_partners($atts)
{
    // $a = shortcode_atts( array(
    //     'offset' => ''
    // ), $atts );

    $html = '';
    $query = new \WP_Query([
        'post_type' => 'industry_partner',
        'posts_per_page' => -1,
    ]);

    if ($query->have_posts()) {
        $html .= "<div class='row industry-partner-wrap'>
                  <div class='col-sm-3 leading-text'>
                    <h2>Industry<br />Partners</h2>
                    <a class='font-montserrat text-uppercase' href='" . get_post_type_archive_link('industry_partner') . "'>View all industry partners</a>
                  </div>";
        $html .= "<div class='col-sm-9'><div class='industry-partner-slider'>";
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $img = get_post_meta($id, 'partner_logo', 1);
            $link = get_post_meta($id, 'link', 1);
            $imgSrc = $id === 388 ? wp_get_attachment_image_src($img['ID'], 'full') : wp_get_attachment_image_src($img['ID'], 'partner-logo');
            if (!empty($link)) {
                $html .= "<div class='slide'><a href='$link' target='_blank'><img src='$imgSrc[0]' alt='$title'></a></div>";
            } else {
                $html .= "<div class='slide'><img src='$imgSrc[0]' alt='$title'></div>";
            }
        }
        $html .= "</div></div></div>";
    }

    return $html;
}

function get_member_search_form()
{

    $filter_alphabetically_val = '';
    $filter_genre_val = '';
    $filter_category_val = '';
    $search_string_val = '';

    if (!empty($_POST['filter-alphabetically'])) {
        $filter_alphabetically_val = $_POST['filter-alphabetically'];
    } elseif (!empty($_GET['a'])) {
        $filter_alphabetically_val = $_GET['a'];
    }

    if (!empty($_POST['filter-genre'])) {
        $filter_genre_val = $_POST['filter-genre'];
    } elseif (!empty($_GET['g'])) {
        $filter_genre_val = $_GET['g'];
    }

    if (!empty($_POST['filter-category'])) {
        $filter_category_val = $_POST['filter-category'];
    } elseif (!empty($_GET['cat'])) {
        $filter_category_val = $_GET['cat'];
    }

    if (!empty($_POST['member-search-str'])) {
        $search_string_val = $_POST['member-search-str'];
    } elseif (!empty($_GET['ss'])) {
        $search_string_val = $_GET['ss'];
    }

    $genre_query = new \WP_Query([
        'post_type' => 'genre',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $category_query = new \WP_Query([
        'post_type' => 'member_category',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $html = "";

    $archive_page_form_action = is_post_type_archive('user_profile') ? '/member-directory' : '';

    $html .= "<div class='member-search-wrap'>
              <form class='member-search-form' action='" . $archive_page_form_action . "' method='POST'>
                <input id='member-search-str' class='member-search-str' data-submit='#member-search-btn' data-results='#autocomplete-results-wrap' type='text' name='member-search-str' placeholder='Type here to search our members' value='" . $search_string_val . "'>
                <button id='member-search-btn' class='member-search-btn' type='submit' name='Submit'>
                  <i class='fa fa-search'></i>
                </button>
              </form>
              <div id='autocomplete-results-wrap' class='autocomplete-results-wrap'></div>

              <form id='member-filter-form' class='member-filter-form' action='" . $archive_page_form_action . "' method='POST'>
                <ul class='text-uppercase'>
                  <li class='first'>Filter by:</li>
                  <li class='minimal filter-alphabetically-wrap'>
                    <select id='filter-alphabetically' data-form='#member-filter-form' name='filter-alphabetically'>
                    <option value='ASC' selected=''>Order</option>
                      <option value='ASC' " . ((!empty($filter_alphabetically_val) && ($filter_alphabetically_val == "ASC")) ? "selected='selected'" : "") . ">A-Z</option>
                      <option value='DESC' " . ((!empty($filter_alphabetically_val) && ($filter_alphabetically_val == "DESC")) ? "selected='selected'" : "") . ">Z-A</option>
                    </select>
                  <li class='minimal'>
                    <select id='filter-genre' data-form='#member-filter-form' name='filter-genre'>
                      <option value=''>Genre</option>";

    if ($genre_query->have_posts()) {
        foreach ($genre_query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;

            $html .= "<option value='$id' " . ((!empty($filter_genre_val) && ($filter_genre_val == $id)) ? "selected='selected'" : "") . ">$title</option>";
        }
    }

    wp_reset_query();

    $html .= "</select>
                  </li>
                  <li class='minimal'>
                    <select id='filter-category' data-form='#member-filter-form' name='filter-category'>
                      <option value=''>Category</option>";

    if ($category_query->have_posts()) {
        foreach ($category_query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;

            $html .= "<option value='$id' " . ((!empty($filter_category_val) && ($filter_category_val == $id)) ? "selected='selected'" : "") . ">$title</option>";
        }
    }

    wp_reset_query();

    $html .= "</select>
                  </li>
                </ul>
              </form>
            </div>";

    return $html;
}

function get_member_search_results()
{
    // initialize variables for Member Search
    $active_members = Roots\Sage\Members\get_active_member_user_profile_ids();
    global $wp_query;
    global $wpdb;
    $specific_search = 0;
    $html = "";

    // placeholder for message incase search term has no results
    $results_message = "";

    // check if user is searching
    if (isset($_POST) && !empty($_POST['member-search-str'])) {
        $post__in = [];
        $search_str = "%" . $_POST['member-search-str'] . "%";

        // return ID's using LIKE comparison
        $pids = $wpdb->get_col($wpdb->prepare("
        SELECT      ID
        FROM        $wpdb->posts
        WHERE       $wpdb->posts.post_title LIKE %s AND $wpdb->posts.post_status = 'publish'
        AND 		$wpdb->posts.post_type = 'user_profile'", $search_str));

        // check if search string returns valid user profiles
        if (!empty($pids)) {
            foreach ($pids as $p) {
                $user_role = Roots\Sage\Members\check_member_role($p);

                // need to check if user is an active member
                if ($user_role == "active_member") {
                    $post__in[] = $p;
                }
            }
        }

        // use array of valid user profile post ids for querying
        if (!empty($post__in)) {
            $args = array('post_type' => 'user_profile', 'post__in' => $post__in);
            $specific_search = 1;
        } else {
            // need to reset the Args array, otherwise it'll show all users (including expired)
            $results_message = "Sorry, we couldn't find any matching profiles. Try searching again, or look through the profiles below.";
            $args = array('post_type' => 'user_profile', 'post__in' => $active_members);
        }
    } elseif (isset($_POST) && (!empty($_POST['filter-alphabetically']) && !empty($_POST['filter-genre']) && !empty($_POST['filter-category']))) {
        $args = array('post_type' => 'user_profile',
            'orderby' => 'title',
            'order' => $_POST['filter-alphabetically'],
            'meta_query' => [
                'relation' => 'AND',
                [
                    'relation' => 'OR',
                    [
                        'key' => 'genre_1',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_2',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_3',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                ],
                [
                    'key' => 'member_category',
                    'value' => $_POST['filter-category'],
                    'compare' => '=',
                ],
                [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ]);

        // $args = array_merge( $wp_query->query_vars, array( 'orderby' => 'title', 'order' => $_POST['filter-alphabetically'], 'post__not_in' => $expired_members ) );
    } elseif (isset($_POST) && (!empty($_POST['filter-alphabetically']) && !empty($_POST['filter-genre']))) {
        $args = array('post_type' => 'user_profile',
            'orderby' => 'title',
            'order' => $_POST['filter-alphabetically'],
            'meta_query' => [
                'relation' => 'AND',
                [
                    'relation' => 'OR',
                    [
                        'key' => 'genre_1',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_2',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_3',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                ],
                [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ]);

        // $args = array_merge( $wp_query->query_vars, array( 'orderby' => 'title', 'order' => $_POST['filter-alphabetically'], 'post__not_in' => $expired_members ) );
    } elseif (isset($_POST) && (!empty($_POST['filter-alphabetically']) && !empty($_POST['filter-category']))) {
        $args = array('post_type' => 'user_profile',
            'orderby' => 'title',
            'order' => $_POST['filter-alphabetically'],
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'member_category',
                    'value' => $_POST['filter-category'],
                    'compare' => '=',
                ],
                [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ]);

        // $args = array_merge( $wp_query->query_vars, array( 'orderby' => 'title', 'order' => $_POST['filter-alphabetically'], 'post__not_in' => $expired_members ) );
    } elseif (isset($_POST) && (!empty($_POST['filter-alphabetically']))) {
        if (!empty($_POST['filter-alphabetically'])) {
            $args = array('post_type' => 'user_profile',
                'orderby' => 'title',
                'order' => $_POST['filter-alphabetically']);
        }
    } elseif (isset($_POST) && (!empty($_POST['filter-genre']))) {
        $args = array('post_type' => 'user_profile',
            'meta_query' => [
                'relation' => 'AND', [
                    'relation' => 'OR',
                    [
                        'key' => 'genre_1',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_2',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_3',
                        'value' => $_POST['filter-genre'],
                        'compare' => '=',
                    ],
                ], [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ]);
    } elseif (isset($_POST) && (!empty($_POST['filter-category']))) {
        $args = array('post_type' => 'user_profile',
            'meta_query' => [
                'relation' => 'AND', [
                    [
                        'key' => 'member_category',
                        'value' => $_POST['filter-category'],
                        'compare' => '=',
                    ],
                ],
                [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        );
    } else {
        // if no search term is being used, search all active members
        $args = array('post_type' => 'user_profile', 'orderby' => 'rand');
    }

    $args = array_merge($args, ['posts_per_page' => 12]);

    if (!$specific_search) {
        $args = array_merge($args, ['post__in' => $active_members]);
    }

    if (empty($_POST['filter-alphabetically']) && empty($_POST['filter-genre']) && empty($_POST['filter-category'])) {
        $args = array_merge($args, ['meta_query' => [
            [
                'key' => 'staff_profile',
                'compare' => 'NOT EXISTS',
            ],
        ],
        ]);
    }

    $html .= "<div class='row'>";

    if (!empty($results_message)) {
        $html .= "<p class='error col-sm-12'>$results_message</p>";
    }

    $query = new \WP_Query($args);

    if ($query->have_posts() && !empty($active_members)) {
        foreach ($query->posts as $post) {
            $id = $post->ID;
            $title = $post->post_title;
            $featured_image_src = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'member-thumbnail');
            if (!empty($featured_image_src[0])) {
                $image_src = $featured_image_src[0];
            } else {
                $image_src = DEFAULT_MEMBER_IMAGE_THUMB;
            }

            $html .= "<article class='col-xs-6 col-sm-3 col-md-2 member-directory-item'>";
            $html .= "<div class='inner-wrap'><img src='$image_src' alt='$title'>
                    <a class='member-link' href='" . get_the_permalink($id) . "'>$title</a></div>";
            $html .= "</article>";
        }
    } else {
        if (!empty($active_members)) {
            $html .= "<p class='col-sm-12'>Sorry, no members with that information can be found. Please try a different search.</p>";
        }
    }

    $html .= "</div>
              <div class='row'>
                <div class='col-sm-12'>
                  <p class='text-uppercase font-montserrat'><a class='red' href='" . get_post_type_archive_link('user_profile') . "'>View Members Directory</a></p>
                </div>
              </div>";

    wp_reset_query();

    return $html;
}

function get_member_search_results_archive_page()
{
    // initialize variables for Member Search
    $active_members = Roots\Sage\Members\get_active_member_user_profile_ids();
    global $wp_query;
    global $wpdb;
    $html = "";
    $specific_search = 0;

    // placeholder for message incase search term has no results
    $results_message = "";

    // check if user is searching
    if ((isset($_POST) || isset($_GET)) && (!empty($_POST['member-search-str']) || !empty($_GET['ss']))) {
        $post__in = [];
        $query_str = !empty($_POST['member-search-str']) ? $_POST['member-search-str'] : $_GET['ss'];
        $search_str = "%" . $query_str . "%";

        // return ID's using LIKE comparison
        $pids = $wpdb->get_col($wpdb->prepare("
        SELECT      ID
        FROM        $wpdb->posts
        WHERE       $wpdb->posts.post_title LIKE %s AND $wpdb->posts.post_status = 'publish'
        AND 		$wpdb->posts.post_type = 'user_profile'", $search_str));

        // check if search string returns valid user profiles
        if (!empty($pids)) {
            foreach ($pids as $p) {
                $user_role = Roots\Sage\Members\check_member_role($p);

                // need to check if user is an active member
                if ($user_role == "active_member") {
                    $post__in[] = $p;
                }
            }
        }

        // use array of valid user profile post ids for querying
        if (!empty($post__in)) {
            $args = array_merge($wp_query->query_vars, array('post__in' => $post__in));
            $specific_search = 1;
            // $html .= "<pre>".print_r($args, true)."</pre>";
            // $html .= "Here!!!";
        } else {
            // need to reset the Args array, otherwise it'll show all users (including expired)
            $results_message = "Sorry, we couldn't find any matching profiles. Try searching again, or look through the profiles below.";
            $args = array_merge($wp_query->query_vars, array('post__in' => $active_members));
        }
    } elseif ((isset($_POST) || isset($_GET)) && (!empty($_POST['filter-alphabetically']) || !empty($_GET['a'])) && (!empty($_POST['filter-genre']) || !empty($_GET['g'])) && (!empty($_POST['filter-category']) || !empty($_GET['cat']))) {
        $query_genre = !empty($_POST['filter-genre']) ? $_POST['filter-genre'] : $_GET['g'];
        $query_category = !empty($_POST['filter-category']) ? $_POST['filter-category'] : $_GET['cat'];
        $query_alpha = !empty($_POST['filter-alphabetically']) ? $_POST['filter-alphabetically'] : $_GET['a'];

        $args = array_merge($wp_query->query_vars, array('orderby' => 'title',
            'order' => $query_alpha,
            'meta_query' => [
                'relation' => 'AND', [
                    'relation' => 'OR',
                    [
                        'key' => 'genre_1',
                        'value' => $query_genre,
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_2',
                        'value' => $query_genre,
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_3',
                        'value' => $query_genre,
                        'compare' => '=',
                    ],
                ], [
                    'key' => 'member_category',
                    'value' => $query_category,
                    'compare' => '=',
                ], [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ]));

        // $args = array_merge( $wp_query->query_vars, array( 'orderby' => 'title', 'order' => $_POST['filter-alphabetically'], 'post__not_in' => $expired_members ) );
    } elseif ((isset($_POST) || isset($_GET)) && (!empty($_POST['filter-alphabetically']) || !empty($_GET['a'])) && (!empty($_POST['filter-genre']) || !empty($_GET['g']))) {
        $query_genre = !empty($_POST['filter-genre']) ? $_POST['filter-genre'] : $_GET['g'];
        $query_alpha = !empty($_POST['filter-alphabetically']) ? $_POST['filter-alphabetically'] : $_GET['a'];

        $args = array_merge($wp_query->query_vars, array('orderby' => 'title',
            'order' => $query_alpha,
            'meta_query' => [
                'relation' => 'AND', [
                    'relation' => 'OR',
                    [
                        'key' => 'genre_1',
                        'value' => $query_genre,
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_2',
                        'value' => $query_genre,
                        'compare' => '=',
                    ],
                    [
                        'key' => 'genre_3',
                        'value' => $query_genre,
                        'compare' => '=',
                    ],
                ], [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ]));

        // $args = array_merge( $wp_query->query_vars, array( 'orderby' => 'title', 'order' => $_POST['filter-alphabetically'], 'post__not_in' => $expired_members ) );
    } elseif ((isset($_POST) || isset($_GET)) && (!empty($_POST['filter-alphabetically']) || !empty($_GET['a'])) && (!empty($_POST['filter-category']) || !empty($_GET['cat']))) {
        $query_category = !empty($_POST['filter-category']) ? $_POST['filter-category'] : $_GET['cat'];
        $query_alpha = !empty($_POST['filter-alphabetically']) ? $_POST['filter-alphabetically'] : $_GET['a'];

        $args = array_merge($wp_query->query_vars, array('orderby' => 'title',
            'order' => $query_alpha,
            'meta_query' => [
                'relation' => 'AND', [
                    'key' => 'member_category',
                    'value' => $query_category,
                    'compare' => '=',
                ], [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ]));

        // $args = array_merge( $wp_query->query_vars, array( 'orderby' => 'title', 'order' => $_POST['filter-alphabetically'], 'post__not_in' => $expired_members ) );
    } elseif ((isset($_POST) || isset($_GET)) && (!empty($_POST['filter-alphabetically']) || !empty($_GET['a']))) {
        $order_string = !empty($_POST['filter-alphabetically']) ? $_POST['filter-alphabetically'] : $_GET['a'];
        $args = array_merge($wp_query->query_vars, array('orderby' => 'title',
            'order' => $order_string));
    } elseif ((isset($_POST) || isset($_GET)) && (!empty($_POST['filter-genre']) || !empty($_GET['g']))) {
        $genre_string = !empty($_POST['filter-genre']) ? $_POST['filter-genre'] : $_GET['g'];
        $args = array_merge($wp_query->query_vars, array('meta_query' => [
            'relation' => 'AND', [
                'relation' => 'OR',
                [
                    'key' => 'genre_1',
                    'value' => $genre_string,
                    'compare' => '=',
                ],
                [
                    'key' => 'genre_2',
                    'value' => $genre_string,
                    'compare' => '=',
                ],
                [
                    'key' => 'genre_3',
                    'value' => $genre_string,
                    'compare' => '=',
                ],
            ], [
                'key' => 'staff_profile',
                'compare' => 'NOT EXISTS',
            ],
        ]));
    } elseif ((isset($_POST) || isset($_GET)) && (!empty($_POST['filter-category']) || !empty($_GET['cat']))) {
        $category_string = !empty($_POST['filter-category']) ? $_POST['filter-category'] : $_GET['cat'];
        $args = array_merge($wp_query->query_vars,
            array(
                'meta_query' => [
                    'relation' => 'AND',
                    [
                        'key' => 'member_category',
                        'value' => $category_string,
                        'compare' => '=',
                    ],
                    [
                        'key' => 'staff_profile',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
            )
        );
    } else {
        // if no search term is being used, search all active members
        $args = array_merge($wp_query->query_vars, array('post_type' => 'user_profile', 'orderby' => 'rand'));
    }

    // check if searching for specific user
    // -- DO NOT include active_members if true
    if (!$specific_search) {
        $args = array_merge($args, ['post__in' => $active_members]);
    }

    if (empty($_POST['filter-alphabetically']) && empty($_POST['filter-genre']) && empty($_POST['filter-category']) && empty($_GET['a']) && empty($_GET['g']) && empty($_GET['cat'])) {
        $args = array_merge($args, ['meta_query' => [
            [
                'key' => 'staff_profile',
                'compare' => 'NOT EXISTS',
            ],
        ],
        ]);
    }

    // set number of posts to return
    $args = array_merge($args, ['posts_per_page' => 24]);

    // used for altering main Query, seeing we're using the User Profile archive page
    // query_posts( $args );
    $profiles = new \WP_Query($args);

    $html .= "<div class='row'>";

    if (!empty($results_message)) {
        $html .= "<p class='error col-sm-12'>$results_message</p>";
    }

    if ($profiles->have_posts()) {
        while ($profiles->have_posts()): $profiles->the_post();

            $html .= get_template_part('templates/content', get_post_type() != 'post' ? get_post_type() : get_post_format());
        endwhile;
    } else {
        $html .= "<p class='col-sm-12'>Sorry, no members with that information can be found. Please try a different search.</p>";
    }

    // echo 'Number of posts: ' . $profiles->post_count;

    $html .= Roots\Sage\Extras\wordpress_user_profile_numeric_post_nav();

    wp_reset_query();

    return $html;
}

function get_member_news()
{
    // $a = shortcode_atts( array(
    //     'number_of_posts' => ''
    // ), $atts );

    $args = [
        'post_type' => 'news_item',
        'posts_per_page' => 3,
        'tax_query' => [
            [
                'taxonomy' => 'news_category',
                'field' => 'slug',
                'terms' => 'member-news',
            ],
        ],
    ];

    $html = '';
    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        $html .= "<div class='row member-news-wrap'>";
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $excerpt = substr($p->post_excerpt, 0, 150);
            $sub_title = get_post_meta($id, 'sub_title', 1);
            $pub_date = get_post_meta($id, 'publication_date', 1);
            $terms = get_the_terms($id, 'news_category');

            $html .= "<div class='col-sm-4 member-news-item'>
                    <article class='news_item " . (!empty($terms[0]) ? $terms[0]->slug : "") . "'>
                      <a href='" . get_the_permalink($id) . "'></a>
                      <header>
                        <sub>" . (!empty($terms[0]) ? $terms[0]->name : "") . (!empty($pub_date) ? date(" | F j, Y", strtotime($pub_date)) : "") . "</sub>
                        <h2 class='entry-title'><a href='" . get_the_permalink($id) . "'>$title</a></h2>
                        " . (!empty($sub_title) ? "<h3>$sub_title</h3>" : "") . "
                      </header>
                      <div class='entry-summary'>
                        <p>$excerpt</p>
                        <a class='font-montserrat' href='" . get_the_permalink($id) . "'>Learn More</a>
                      </div>
                    </article>
                  </div>";
        }
        $html .= "</div>";
        $html .= "<div class='row'><a class='btn' href='" . get_post_type_archive_link('news_item') . "'>View All</a></div>";
    }

    wp_reset_query();

    return $html;
}

function get_member_testimonials($atts)
{
    $a = shortcode_atts(array(
        'show_all' => false,
    ), $atts);

    $active_members = Roots\Sage\Members\get_active_member_user_profile_ids();

    if ($a['show_all']) {
        $args = [
            'post_type' => 'user_profile',
            'posts_per_page' => -1,
            'post__in' => $active_members,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'testimonial',
                    'value' => [''],
                    'compare' => 'NOT IN',
                ],
                [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ];
    } else {
        $args = [
            'post_type' => 'user_profile',
            'posts_per_page' => 2,
            'post__in' => $active_members,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'testimonial',
                    'value' => [''],
                    'compare' => 'NOT IN',
                ],
                [
                    'key' => 'staff_profile',
                    'compare' => 'NOT EXISTS',
                ],
            ],
        ];
    }

    $html = '';
    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        $html .= "<div class='row testimonial-wrap'>";
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $testimonial = get_post_meta($id, 'testimonial', 1);
            $img = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'member-thumbnail');

            if (!empty($img[0])) {
                $imgSrc = $img[0];
            } else {
                $imgSrc = DEFAULT_MEMBER_IMAGE_THUMB;
            }

            $html .= "<div class='col-xs-12 col-md-6 single-testimonial-wrap'>
                    <div class='col-xs-3 no-padding'>
                      <img src='$imgSrc' alt='$title'>
                    </div>
                    <div class='col-xs-9 testimonial-text font-montserrat'>
                      $testimonial
                      <p class='sig'>- $title</p>
                    </div>
                  </div>";
        }
        $html .= "</div>";

        if (!$a['show_all']) {
            $html .= "<div class='row'><a href='/testimonials' class='btn left'>View All</a></div>";
        }
    }

    wp_reset_query();

    return $html;
}

function get_funding_opportunities($atts)
{
    $a = shortcode_atts(array(
        'category_slug' => '1-internal-funding',
        'post_limit' => -1,
        'show_sticky' => 0,
    ), $atts);

    if ($a['show_sticky'] == 'yes') {
        $a['show_sticky'] = 1;
    } elseif ($a['show_sticky'] == 'no') {
        $a['show_sticky'] = 0;
    }

    $args = [
        'post_type' => 'opportunity',
        'posts_per_page' => $a['post_limit'],
        'meta_key' => 'deadline',
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'tax_query' => [
            [
                'taxonomy' => 'opportunity_category',
                'field' => 'slug',
                'terms' => $a['category_slug'],
            ],
        ],
    ];

    $meta_query = ['meta_query' => [
        [
            'key' => 'make_sticky',
            'value' => '1',
            'compare' => '=',
        ],
    ]];

    if ($a['show_sticky']) {
        $args = array_merge($args, $meta_query);
    }

    $html = '';
    $query = new \WP_Query($args);

    if ($query->have_posts()) {
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $deadline = get_post_meta($id, 'deadline', 1);
            $date_html = "";

            if (!empty($deadline)) {
                $month = date("F", strtotime($deadline));
                $day = date("j", strtotime($deadline));
                $date_html = "<div class='date-wrap'>
                          <span class='month'>$month</span>
                          <span class='day'>$day</span>
                        </div>";
            }
            $excerpt = $p->post_excerpt;
            $permalink = get_the_permalink($id);
            $logoId = get_post_meta($id, 'logo', 1);
            $external_url = get_post_meta($id, 'external_url', 1);
            $external_url_text = get_post_meta($id, 'external_url_text', 1);

            if (!empty($logoId)) {
                $logo = wp_get_attachment_image_src($logoId['ID'], 'opportunity-logo');
                $logoSrc = $logo[0];
            } else {
                $logoSrc = "";
            }

            $html .= "<article class='row opportunity-item'><div class='col-xs-3 col-sm-2 date-wrap-col'>
                    " . (!empty($date_html) ? $date_html : "") . "
                  </div>
                  <div class='col-sm-8'>
                    <h3>$title</h3>
                    <p>$excerpt</p>
                    <p class='link'><a href='$permalink'>READ MORE</a></p>
                  </div>
                  <div class='col-sm-2 no-padding text-center logo'>
                    " . (!empty($logoSrc) ? "<img src='$logoSrc' alt='$title'>" : "") . ((!empty($external_url) && !empty($external_url_text)) ? "<a class='block' href='$external_url' target='_blank'>$external_url_text</a>" : "") . "
                  </div></article>";
        }
    }

    wp_reset_query();

    return $html;
}

function get_opportunities_menu()
{
    ob_start();
    include $_SERVER['DOCUMENT_ROOT'] . "/sage/templates/sidebar-opportunities.php";
    $output = ob_get_clean();
    return $output;
}

function expiration_notice_1_day_function_output()
{
    $html = "";
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
            $html .= "<pre>" . print_r($p, true) . "</pre>";
        }
    }

    return $html;
}

function expiration_notice_30_day_function_output()
{
    $html = "";
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
            $html .= "<pre>" . print_r($p, true) . "</pre>";
        }
    }

    return $html;
}

// register shortcodes
function register_shortcodes()
{
    add_shortcode('get_member_login_form', __NAMESPACE__ . '\\get_member_login_form');
    add_shortcode('rrssb_share', __NAMESPACE__ . '\\rrssb_share');
    add_shortcode('get_staff_members', __NAMESPACE__ . '\\get_staff_members');
    add_shortcode('sub_nav_holder', __NAMESPACE__ . '\\sub_nav_holder');
    add_shortcode('get_documentation', __NAMESPACE__ . '\\get_documentation');
    add_shortcode('get_social_links', __NAMESPACE__ . '\\get_social_links');
    add_shortcode('get_contact_info', __NAMESPACE__ . '\\get_contact_info');
    add_shortcode('get_staff_contact_info', __NAMESPACE__ . '\\get_staff_contact_info');
    add_shortcode('get_latest_news_home_page', __NAMESPACE__ . '\\get_latest_news_home_page');
    add_shortcode('get_pinned_news_home_page', __NAMESPACE__ . '\\get_pinned_news_home_page');
    add_shortcode('get_events_home_page', __NAMESPACE__ . '\\get_events_home_page');
    add_shortcode('get_pinned_events_home_page', __NAMESPACE__ . '\\get_pinned_events_home_page');
    add_shortcode('get_members_home_page', __NAMESPACE__ . '\\get_members_home_page');
    add_shortcode('get_newsletter_signup', __NAMESPACE__ . '\\get_newsletter_signup');
    add_shortcode('get_industry_partners', __NAMESPACE__ . '\\get_industry_partners');
    add_shortcode('get_member_search_form', __NAMESPACE__ . '\\get_member_search_form');
    add_shortcode('get_member_search_results', __NAMESPACE__ . '\\get_member_search_results');
    add_shortcode('get_member_search_results_archive_page', __NAMESPACE__ . '\\get_member_search_results_archive_page');
    add_shortcode('get_member_news', __NAMESPACE__ . '\\get_member_news');
    add_shortcode('get_member_testimonials', __NAMESPACE__ . '\\get_member_testimonials');
    add_shortcode('get_internal_funding_opportunities', __NAMESPACE__ . '\\get_internal_funding_opportunities');
    add_shortcode('get_external_funding_opportunities', __NAMESPACE__ . '\\get_external_funding_opportunities');
    add_shortcode('get_funding_opportunities', __NAMESPACE__ . '\\get_funding_opportunities');
    add_shortcode('get_opportunities_menu', __NAMESPACE__ . '\\get_opportunities_menu');
    add_shortcode('expiration_notice_1_day_function_output', __NAMESPACE__ . '\\expiration_notice_1_day_function_output');
    add_shortcode('expiration_notice_30_day_function_output', __NAMESPACE__ . '\\expiration_notice_30_day_function_output');
}
add_action('init', __NAMESPACE__ . '\\register_shortcodes');
