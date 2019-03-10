<?php
  $html = "";
  $terms = get_terms( array(
    'taxonomy' => 'opportunity_category',
    'hide_empty' => false,
    'orderby' => 'slug'
  ));

  foreach($terms as $t){
    $id = $t->term_id;
    $name = $t->name;
    $link = get_term_link($t, 'opportunity_category');

    $html .= "<li><a href='$link'>$name</a></li>";
  }
?>

<h2>MENU</h2>
<hr />
<div class='inner-wrap'>
  <ul class='nav nav-opportunity'>
    <?= $html; ?>
  </ul>
</div>