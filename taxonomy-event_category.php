<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'sage'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php
  global $wp_query;
  $args = array_merge( $wp_query->query_vars, [ 
                                                'meta_key' => 'e_date',
                                                'orderby' => 'e_date',
                                                'order' => 'DESC'
                                              ] );
  query_posts( $args );
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/content', get_post_type() != 'post' ? get_post_type() : get_post_format()); ?>
<?php endwhile; ?>

<?php the_posts_navigation(); ?>
