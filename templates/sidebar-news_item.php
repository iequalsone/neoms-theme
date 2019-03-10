<?php
  // grab all Parent Categories
  $news_categories = get_terms( array(
    'taxonomy' => 'news_category',
    'hide_empty' => false,
    'parent' => 0
  ) );
?>

<div class="col-sm-12 no-padding">
  <div class="filter-wrap">
    <h2 class='show-filters'>Filters</h2>
    <hr />
    <ul class="filter-list">
      <?php 
        foreach($news_categories as $nc){
          $id = $nc->term_id;
          $name = $nc->name;
          $link = get_term_link($id, 'news_category');

          echo "<li><a href='$link'>$name</a></li>";
        }
      ?>
    </ul>
  </div>

  <div class="filter-wrap">
    <h2 class='show-filters'>TAGS</h2>
    <hr />
    <ul class="filter-list tags">
      <?php 
        $terms = get_terms( [
          'taxonomy' => 'news_tag',
          'hide_empty' => false,
        ]);
        foreach($terms as $t){
          $id = $t->term_id;
          $name = $t->name;
          $link = get_term_link($t, 'news_tag');

          echo "<li><a href='$link'>$name</a></li>";
        }
      ?>
    </ul>
  </div>
</div>
