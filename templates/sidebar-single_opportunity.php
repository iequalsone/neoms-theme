<?php
  $terms = wp_get_post_terms( get_the_id(), 'opportunity_category' );
  if(!empty($terms[0]->slug)){
    $slug = $terms[0]->slug;
  }else{
    $slug = "";
  }

  $args = [
    'post_type' => 'opportunity',
    'posts_per_page' => 5,
    'tax_query' => [
      [
        'taxonomy' => 'opportunity_category',
        'field'    => 'slug',
        'terms'    => $slug,
      ],
    ],
    'meta_key' => 'publish_date',
    'meta_type' => 'DATE',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
    'post__not_in' => [get_the_id()]
  ];

  $query = new \WP_Query($args);

  if($query->have_posts()){
    echo "<h2>Opportunities<br /><span>At a glance</span></h2><ul class='nav'>";
    foreach($query->posts as $p){
      $id = $p->ID;
      $name = $p->post_title;
      $link = get_permalink($id);

      echo "<li><a href='$link'>$name</a></li>";
    }
    echo "</ul>";
  }

  wp_reset_query();
?>