<?php

namespace Roots\Sage\Setup;

use Roots\Sage\Assets;

/**
 * Theme setup
 */
function setup()
{
    // Enable features from Soil when plugin is activated
    // https://roots.io/plugins/soil/
    add_theme_support('soil-clean-up');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-relative-urls');

    define('COMPANY_ADDRESS_LINE_1', get_option("company_info_address_line_1"));
    define('COMPANY_ADDRESS_LINE_2', get_option("company_info_address_line_2"));
    define('COMPANY_ADDRESS_TEL', get_option("company_info_tel"));
    define('COMPANY_ADDRESS_EMAIL', get_option("company_info_email"));
    define('COMPANY_ADDRESS_FACEBOOK', get_option("company_info_facebook"));
    define('COMPANY_ADDRESS_TWITTER', get_option("company_info_twitter"));
    define('COMPANY_ADDRESS_YOUTUBE', get_option("company_info_youtube"));
    define('DEFAULT_MEMBER_IMAGE', '/sage/dist/images/member-default-image.jpg');
    define('DEFAULT_MEMBER_IMAGE_THUMB', '/sage/dist/images/member-default-image-thumb.jpg');
    define('DEFAULT_MEMBER_PROFILE_IMAGE_THUMB', '/sage/dist/images/member-default-profile-image-thumb.jpg');

    // Make theme available for translation
    // Community translations can be found at https://github.com/roots/sage-translations
    load_theme_textdomain('sage', get_template_directory() . '/lang');

    // Enable plugins to manage the document title
    // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
    add_theme_support('title-tag');

    // Register wp_nav_menu() menus
    // http://codex.wordpress.org/Function_Reference/register_nav_menus
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
        'utility_navigation' => __('Utility Navigation', 'sage'),
        'footer_navigation' => __('Footer Navigation', 'sage'),
    ]);

    // Enable post thumbnails
    // http://codex.wordpress.org/Post_Thumbnails
    // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
    // http://codex.wordpress.org/Function_Reference/add_image_size
    add_theme_support('post-thumbnails');
    add_image_size('page-featured', 1600, 329, true);
    add_image_size('member-featured-image', 794, 529, true);
    add_image_size('member-thumbnail', 170, 170, true);
    add_image_size('member-profile-thumbnail', 260, 220, true);
    add_image_size('hi-res', 1920, 1920, true);
    add_image_size('partner-logo', 150, 80, false);
    add_image_size('opportunity-logo', 164, 45, false);
    add_image_size('event-thumb', 330, 220, 1);

    // Enable post formats
    // http://codex.wordpress.org/Post_Formats
    add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio']);

    // Enable HTML5 markup support
    // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    // Use main stylesheet for visual editor
    // To add custom styles edit /assets/styles/layouts/_tinymce.scss
    add_editor_style(Assets\asset_path('styles/main.css'));
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

/**
 * Register sidebars
 */
function widgets_init()
{
    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ]);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ]);
}
add_action('widgets_init', __NAMESPACE__ . '\\widgets_init');

/**
 * Determine which pages should NOT display the sidebar
 */
function display_sidebar()
{
    static $display;

    isset($display) || $display = !in_array(true, [
        // The sidebar will NOT be displayed if ANY of the following return true.
        // @link https://codex.wordpress.org/Conditional_Tags
        is_404(),
        is_front_page(),
        (is_page() && !is_page_template('template-poll-vote.php')),
        is_page_template('template-custom.php'),
        is_post_type_archive('user_profile'),
        is_post_type_archive('industry_partner'),
        is_singular('user_profile'),
    ]);

    return apply_filters('sage/display_sidebar', $display);
}

/**
 * Theme assets
 */
function assets()
{
    wp_enqueue_style('sage/css', Assets\asset_path('styles/main.css'), false, null);

    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    wp_enqueue_script('sage/js', Assets\asset_path('scripts/main.js'), ['jquery'], null, true);
    wp_localize_script('sage/js', 'ajaxURL', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        //'postCommentNonce'    => wp_create_nonce('my-ajax-comment-nonce')
    )
    );
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);
