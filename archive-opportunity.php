<?php if (!have_posts()): ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage');?>
  </div>
  <?php get_search_form();?>
<?php endif;?>

<h2>Internal Funding</h2>
<hr />
<?=do_shortcode('[get_funding_opportunities post_limit="3"]');?>

<h2>External Funding</h2>
<hr />
<?=do_shortcode('[get_funding_opportunities category_slug="2-external-funding" post_limit="5"]');?>

<h2>Awards and Showcases</h2>
<hr />
<?=do_shortcode('[get_funding_opportunities category_slug="awards-and-showcases" post_limit="5"]');?>

<h2>Other Opportunities</h2>
<hr />
<?php
global $wp_query;
$args = array_merge($wp_query->query_vars, ['meta_key' => 'deadline',
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'tax_query' => [
        [
            'taxonomy' => 'opportunity_category',
            'field' => 'slug',
            'terms' => '3-other-opportunities',
        ],
    ],
]);
query_posts($args);
?>
<?php while (have_posts()): the_post();?>
					  <?php get_template_part('templates/content', get_post_type() != 'post' ? get_post_type() : get_post_format());?>
					<?php endwhile;?>

<?php Roots\Sage\Extras\wordpress_numeric_post_nav();?>
