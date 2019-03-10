<?php

use Roots\Sage\Setup;
use Roots\Sage\Wrapper;

$cookie_name = "neoms_popup";
$cookie_value = 1;

if (!isset($_COOKIE[$cookie_name])):
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
endif;

$cookie_name_enews = "enews_popup";
$cookie_value_enews = 1;
if (!isset($_COOKIE[$cookie_name_enews])):
    setcookie($cookie_name_enews, $cookie_value_enews, time() + (86400 * 30), "/"); // 86400 = 1 day
endif;
?>
<!doctype html>
<html <?php language_attributes();?>>

  <?php get_template_part('templates/head');?>
  <?php
$news_category = "";
if (is_singular('news_item')) {
    $terms = get_the_terms(get_the_id(), 'news_category');
    $news_category = $terms[0]->taxonomy . "-" . $terms[0]->slug;
}
?>
  <body <?php body_class($news_category);?>>

    <?php $wpenv = getenv('WP_ENV');?>
    <?php if ($wpenv === "production"): ?>
      <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-5496526-61', 'auto');
        ga('send', 'pageview');

      </script>
    <?php endif;?>

    <!--[if IE]>
      <div class="alert alert-warning">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'sage');?>
      </div>
    <![endif]-->

    <?php
do_action('get_header');
get_template_part('templates/header');
?>
    <div class="wrap container" role="document">
      <div class="content row">
        <?php if (Setup\display_sidebar() && (
    is_post_type_archive('event_listing') ||
    is_tax('event_category') ||
    is_tax('event_tag') ||
    is_post_type_archive('news_item') ||
    is_tax('news_category') ||
    is_tax('news_tag') ||
    is_post_type_archive('opportunity') ||
    is_tax('opportunity_category') ||
    is_tax('opportunity_tag') ||
    is_page_template('template-poll-vote.php')
)): ?>
          <aside class="sidebar <?=(is_post_type_archive('opportunity') || is_tax('opportunity_category') || is_tax('opportunity_tag') || is_page_template('template-poll-vote.php') ? "visible-xs" : "");?>">
            <?php include Wrapper\sidebar_path();?>
          </aside><!-- /.sidebar -->
        <?php endif;?>
        <main class="main">
          <?php include Wrapper\template_path();?>
        </main><!-- /.main -->
        <?php if (Setup\display_sidebar() && (
    is_singular('event_listing') ||
    is_singular('news_item') ||
    is_singular('opportunity') ||
    is_post_type_archive('opportunity') ||
    is_tax('opportunity_category') ||
    is_tax('opportunity_tag') ||
    is_page_template('template-poll-vote.php')
)): ?>
          <aside class="sidebar <?=(is_post_type_archive('opportunity') || is_tax('opportunity_category') || is_tax('opportunity_tag') || is_page_template('template-poll-vote.php') ? "hidden-xs" : "");?>">
            <?php include Wrapper\sidebar_path();?>
          </aside><!-- /.sidebar -->
        <?php endif;?>
      </div><!-- /.content -->
    </div><!-- /.wrap -->



    <?php
do_action('get_footer');
get_template_part('templates/footer');
wp_footer();
?>
  </body>
</html>
