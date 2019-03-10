<?php
// get featured image, use inline style for bg image
$img_featured = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_id()), 'page-featured');

// Make sure featured image isn't empty, not on Archive page, and not on Single Member page
if (!empty($img_featured[0]) && !is_archive() && !is_singular('user_profile')) {
    $img_featured_src = $img_featured[0];
} else {
    $img_featured_src = '/sage/dist/images/flag-default.jpg';
}

// echo "<pre>".print_r($img_featured_src, true)."</pre>";

$hide_title = get_post_meta(get_the_id(), 'hide_title', 1);
?>

<header
  class="banner navbar navbar-default navbar-static-top <?=($hide_title ? "hide-title" : "")?>"
  role="banner"
  <?=((!empty($img_featured_src) && !is_front_page()) ? "style='background-image: url(" . $img_featured_src . ");'" : "");?>>

  <?php // echo Roots\Sage\Extras\get_notifications(); ?>

  <div class="navbar-wrap">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only"><?=__('Toggle navigation', 'sage');?></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?=esc_url(home_url('/'));?>"><img src="/sage/assets/images/brand-logo.png" alt="<?php bloginfo('name');?>"></a>
      </div>

      <nav class="collapse navbar-collapse" role="navigation">
        <?php
if (has_nav_menu('primary_navigation')):
    wp_nav_menu(['theme_location' => 'primary_navigation', 'walker' => new wp_bootstrap_navwalker(), 'menu_class' => 'nav navbar-nav']);
endif;
?>

        <div class="utility-nav-wrap">
          <?php
if (has_nav_menu('utility_navigation')):
    wp_nav_menu(['theme_location' => 'utility_navigation', 'walker' => new wp_bootstrap_navwalker(), 'menu_class' => 'nav navbar-nav']);
endif;
?>

          <?=do_shortcode("[get_social_links]");?>
        </div>
      </nav>
    </div>

    <div class="row">
      <div class="container">
        <?php get_template_part('templates/page', 'header');?>
      </div>
    </div>
  </div>

  <?php if (is_front_page()): ?>
    <div class="banner-wrap">
      <?=do_shortcode('[rev_slider alias="home-banner"]');?>
      <?=Roots\Sage\Extras\get_random_background_image();?>
    </div>
  <?php endif;?>
</header>


<?php

// $events = get_posts(
//   [
//     'post_type' => 'event_listing',
//     'posts_per_page'=> -1,
//     'meta_query' => [
//       [
//         'key' => 'start_datetime',
//         'value' => [date('Y-m-d')],
//         'compare' => 'IN',
//         'type' => 'DATE',
//       ]
//     ],
// ]);


// echo '<pre>';
// print_r($events);
// echo '</pre>';
?>