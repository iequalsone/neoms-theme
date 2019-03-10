<?php
  /*************************************************
   * Member login redirects here
   * Gets User ID from Current User and
   * uses it to redirect to Member Profile page
   *************************************************/
   
  // include WP functions so we can query
  include $_SERVER['DOCUMENT_ROOT']."/wp/wp-load.php";

  // $current_user = wp_get_current_user();

  // $query = new \WP_Query([
  //   'post_type' => 'user_profile',
  //   'meta_query' => array(
  //     array(
  //       'key'     => 'user_id',
  //       'value'   => $current_user->ID,
  //       'compare' => '=',
  //     ),
  //   ),
  // ]);

  // if($query->have_posts()){
  //   foreach($query->posts as $post){
  //     $permalink = get_the_permalink($post->ID);
  //     wp_safe_redirect( $permalink );
  //     exit;
  //   }
  // }

  // wp_reset_query;

  wp_safe_redirect( '/dashboard' );
?>
