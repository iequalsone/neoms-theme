<?php
  $id = get_the_id();
  $sub_title = get_post_meta($id, 'sub_title', 1);
  $event_date = get_post_meta($id, 'e_date', 1);
  $time = get_post_meta($id, 'time', 1);
  $location = get_post_meta($id, 'location', 1);
  $event_category = get_the_terms($id, 'event_category');
?>

<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h2 class="entry-title"><?= (!empty($event_category[0]) ? $event_category[0]->name . ": " : "") ?><?php the_title(); ?></h2>
      <?php if(!empty($event_date) || !empty($time)) : ?>
        <p class='event-date font-montserrat'>
          <?= (!empty($event_date) ? date("l, F j", strtotime($event_date)) : ""); ?><?= (!empty($time) ? ", " . $time : ""); ?>
        </p>
      <?php endif; ?>
      <?php if(!empty($location)): ?>
        <p class='event-location font-montserrat'><?= $location; ?></p>
      <?php endif; ?>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
    <footer>
      <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>

<?php
  $event_tags = get_the_terms($id, 'event_tag');
  if(!empty($event_tags[0])){
    echo "<p class='event-tags font-montserrat'>TAGS: ";
    foreach($event_tags as $tag){
      $name = $tag->name;
      $link = get_term_link($tag->term_id);
      echo "<a href='$link'>#$name</a>&nbsp;";
    }
    echo "</p>";
  }
?>

<!--<div class="row share-wrap">
  <div class="share-label col-sm-4 col-lg-3 font-montserrat no-padding">
    SPREAD THE WORD:
  </div>
  <div class="col-sm-8 col-lg-9 no-padding">
    <?=  ''; #do_shortcode('[rrssb_share]'); ?>
  </div>
</div>-->

<div class="row post-navigation-wrap">
  <?php
    $prev_post = get_adjacent_post(false, '', true);
    $next_post = get_adjacent_post(false, '', false);
  ?>

  
  <div class="col-sm-3">
    <?php if(!empty($prev_post)) : ?>
      <a href="<?= get_permalink($prev_post->ID); ?>">Previous Event</a>
    <?php endif; ?>
  </div>

  <div class="col-sm-3 col-sm-offset-6 text-right">
    <?php if(!empty($next_post)) : ?>
      <a href="<?= get_permalink($next_post->ID); ?>">Next Event</a>
    <?php endif; ?>
  </div>
</div>
