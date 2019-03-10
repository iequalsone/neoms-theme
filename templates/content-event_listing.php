<?php
  $id = get_the_id();
  $sub_title = get_post_meta($id, 'sub_title', 1);
  $event_date = get_post_meta($id, 'e_date', 1);
  $time = get_post_meta($id, 'time', 1);
  $location = get_post_meta($id, 'location', 1);
  $terms = get_the_terms($id, 'event_category');

  // echo "<pre>".print_r($terms, true)."</pre>";
?>

<article <?php post_class('row'); ?>>
  <a href="<?= get_the_permalink(get_the_id()); ?>"></a>
  <div class="col-xs-2 event-date">
    <span class="month"><?= date("F", strtotime($event_date)); ?></span>
    <span class="day"><?= date("j", strtotime($event_date)); ?></span>
  </div>
  <div class="col-xs-10">
    <header>
      <h2>
        <?= (!empty($terms[0]) ? $terms[0]->name . " - " : ""); ?>
        <?= get_the_title(); ?>
        <?= (!empty($location) ? " @ " . $location : ""); ?>
        <?= (!empty($time) ? " - " . $time : ""); ?>
      </h2>
      <?php if(!empty($sub_title)) : ?>
        <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?= $sub_title; ?></a></h3>
      <?php endif; ?>
    </header>
    <div class="entry-summary">
      <a href="<?= get_the_permalink(get_the_id()); ?>">View Event Info</a>
    </div>
  </div>
</article>
