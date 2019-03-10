<?php
  $id = get_the_id();
  $sub_title = get_post_meta($id, 'sub_title', 1);
  $pub_date = get_post_meta($id, 'publication_date', 1);
  $terms = get_the_terms($id, 'news_category');
?>

<article <?php post_class(); ?>>
  <a href="<?= get_the_permalink(get_the_id()); ?>"></a>
  <header>
    <sub><?= (!empty($terms[0]) ? $terms[0]->name  : "") . (!empty($pub_date) ? date(" | F j, Y", strtotime($pub_date)) : ""); ?></sub>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?= (!empty($sub_title) ? "<h3>$sub_title</h3>" : ""); ?>
  </header>
  <div class="entry-summary">
    <?php the_excerpt(); ?>
    <a class="font-montserrat" href="<?= get_the_permalink(get_the_id()); ?>">Learn More</a>
  </div>
</article>
