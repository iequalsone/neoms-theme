<?php
  $pt = get_post_type(get_the_id());

  if(is_post_type_archive('event_listing') || is_tax('event_category') || is_tax('event_tag')){ // Events Archive, Events Category archive page, Events Tag archive page
    include 'sidebar-event_listing.php';
  }elseif(is_singular('event_listing')){ // Single Events Listing page
    include 'sidebar-single_event_listing.php';
  }elseif(is_post_type_archive('news_item') || is_tax('news_category') || is_tax('news_tag')){ // News Items Archive page, News Category archive page, News Tags archive page
    include 'sidebar-news_item.php';
  }elseif(is_singular('news_item')){ // Single News Item page
    include 'sidebar-single_news_item.php';
  }elseif(is_post_type_archive('opportunity') || is_tax('opportunity_category') || is_tax('opportunity_tag')){ // Opportunity Items Archive page, Opportunity Category archive page, Opportunity Tags archive page
    include 'sidebar-opportunity.php';
  }elseif(is_singular('opportunity')){ // Single Opportunity page
    include 'sidebar-single_opportunity.php';
  }elseif(is_page_template('template-poll-vote.php')){ // Award Poll Voting Page
    include 'sidebar-poll-vote.php';
  }

  // if($pt == 'event_listing'){
  //   include 'sidebar-event_listing.php';
  // }elseif($pt == ''){

  // }
?>