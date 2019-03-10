<?php
  // grab Monthly Archives list
  $args = array(
    'type'            => 'monthly',
    'limit'           => '',
    'format'          => 'html', 
    'before'          => '',
    'after'           => '',
    'show_post_count' => false,
    'echo'            => 0,
    'order'           => 'DESC',
    'post_type'     => 'event_listing'
  );
  $monthly_archives = wp_get_archives( $args );

  // grab all Parent Categories
  $parent_categories = get_terms( array(
    'taxonomy' => 'event_category',
    'hide_empty' => false,
    'parent' => 0
  ) );
?>

<div class="col-sm-12 no-padding">
  <h2 class='show-filters'>Filters</h2>
  <hr />
  <ul class="filter-list">
    <?php if(!empty($monthly_archives)) : ?>
    <li class='toggle active'><span>Month</span>
      <ul class='filter-children'><?= $monthly_archives; ?></ul>
    </li>
    <?php endif; ?>

    <?php 
      foreach($parent_categories as $pc){
        $id = $pc->term_id;
        $parent_name = $pc->name;

        $children = get_terms( array(
          'taxonomy' => 'event_category',
          'hide_empty' => true,
          'parent' => $id,
          'orderby' => 'name',
          'order' => 'ASC'
        ) );

        echo "<li class='toggle'><span>$parent_name</span>";
        if(!empty($children)){
          echo "<ul class='filter-children'>";
          foreach($children as $child){
            $term_name = $child->name;
            $term_link = get_term_link($child, 'event_category');
            echo "<li><a href='$term_link'>$term_name</a></li>";
          }
          echo "</ul>";
        }
        echo "</li>";
      }
    ?>
  </ul>
</div>
