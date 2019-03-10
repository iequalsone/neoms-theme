<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h2 class="entry-title"><?php the_title(); ?></h2>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>

      <?php
        $id = get_the_id();
        $eligibility = apply_filters('the_content', get_post_meta($id, 'eligibility', 1));
        $how_to_apply = apply_filters('the_content', get_post_meta($id, 'how_to_apply', 1));
        $payment_process_and_project_completion = apply_filters('the_content', get_post_meta($id, 'payment_process_and_project_completion', 1));
      ?>

      <?php if(!empty($eligibility)) : ?>
        <h3 class="section-trigger">Eligibility</h3>
        <div class="section-collapsable">
          <?= $eligibility; ?>
        </div>
      <?php endif; ?>

      <?php if(!empty($how_to_apply)) : ?>
        <h3 class="section-trigger">How to Apply</h3>
        <div class="section-collapsable">
          <?= $how_to_apply; ?>
        </div>
      <?php endif; ?>

      <?php if(!empty($payment_process_and_project_completion)) : ?>
        <h3 class="section-trigger">Payment Process and Project Completion</h3>
        <div class="section-collapsable">
          <?= $payment_process_and_project_completion; ?>
        </div>
      <?php endif; ?>
    </div>
    <footer>
      <?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>

<?php
  $news_tags = get_the_terms($id, 'opportunity_tag');
  if(!empty($news_tags[0])){
    echo "<p class='opportunity-tags font-montserrat'>TAGS: ";
    foreach($news_tags as $tag){
      $name = $tag->name;
      $link = get_term_link($tag->term_id);
      echo "<a href='$link'>#$name</a>&nbsp;";
    }
    echo "</p>";
  }
?>

<!--<div class="row share-wrap">
  <div class="share-label col-sm-4 col-lg-3 font-montserrat">
    SPREAD THE WORD:
  </div>
  <div class="col-sm-8 col-lg-9 no-padding">
    <?= ''; #do_shortcode('[rrssb_share]'); ?>
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

  <div class="col-sm-3 col-sm-offset-6 text-right no-padding">
    <?php if(!empty($next_post)) : ?>
      <a href="<?= get_permalink($next_post->ID); ?>">Next Event</a>
    <?php endif; ?>
  </div>
</div>
