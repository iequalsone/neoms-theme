<?php
  // Show similar News item based on News Tag
  $current_terms = wp_get_post_terms(get_the_id(), 'news_tag');
  $ct_slugs = [];
  if(!empty($current_terms)){
    foreach($current_terms as $term){
      $ct_slugs = $term->slug;
    }
  }

  $query = new WP_Query([
    'tax_query' => array(
      array(
        'taxonomy' => 'news_tag',
        'field' => 'slug',
        'terms' => $ct_slugs
      ),
    ),
    'post_type' => 'news_item',
    'post__not_in' => [get_the_id()],
    'posts_per_page' => 1
  ]);
?>

<div class="col-sm-12 no-padding">
  <h2 class='show-filters'>Similar News</h2>
  <hr />

  <div class="inner-wrap">
    <?php if($query->have_posts()) : ?>
      <?php foreach($query->posts as $post) : ?>
        <?php
          $id = $post->ID;
          $title = $post->post_title;
          $date = get_post_meta($id, 'e_date', 1);
          $time = get_post_meta($id, 'time', 1);
          $location = get_post_meta($id, 'location', 1);
          $content = substr(strip_tags($post->post_content), 0, 500);
          $content .= "...";
          $permalink = get_the_permalink($id);
        ?>
        <sub class="font-montserrat">Event | <?= date("M j, Y", strtotime($date)); ?></sub>
        <h3><?= $title; ?></h3>
        <h4>
          <?= (!empty($date) ? date("l, M jS,", strtotime($date)) : "") . (!empty($time) ? " " . $time : ""); ?>
        </h4>

        <p><?= apply_filters('the_content', $content); ?></p>

        <p class='text-right'><a class='permalink font-montserrat' href="<?= $permalink; ?>">Learn More</a></p>
      <?php endforeach; ?>
    <?php else : ?>
      <p>Sorry, there are no similar events to show at this time.</p>
      <p>Please check back again later.</p>
    <?php endif; ?>
  </div>
</div>

<?php
  wp_reset_query();
?>