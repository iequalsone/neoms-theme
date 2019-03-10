<?php

namespace Roots\Sage\Titles;

/**
 * Page titles
 */

function title() {
  if (is_home()) {
    if (get_option('page_for_posts', true)) {
      return get_the_title(get_option('page_for_posts', true));
    } else {
      return __('Latest Posts', 'sage');
    }
  }elseif (is_front_page()) {
    return;
  } elseif (is_archive()) {
    if(get_post_type(get_the_ID()) == 'user_profile'){ // Member Archive
      return "<span>Member</span> Directory";
    }elseif(get_post_type(get_the_ID()) == 'event_listing'){ // Event Archive
      return "Event Posting";
    }elseif(get_post_type(get_the_ID()) == 'news_item'){ // News Archive
      return "The Latest";
    }elseif(get_post_type(get_the_ID()) == 'opportunity'){ // Opportunity Archive
      // $on_cat_page = get_query_var('cat');
      if(is_category()|| is_tag() || is_tax()){
        $cats = wp_get_post_terms(get_the_ID(), 'opportunity_category');
        return isset($cats[0]->name) ? $cats[0]->name : "Funding Opportunities";
      }else{
        return "Funding Opportunities";
      }
    }elseif(get_post_type(get_the_ID()) == 'industry_partner'){ // Industry Partner Archive
      return "Industry Partners";
    }elseif (is_tax('opportunity_category')) { // Opportunity Category Taxonomy
      return single_term_title();
    }else{
      return get_the_archive_title();
    }
  } elseif (is_singular('user_profile')) { // Single Member Profile
    $user_role = \Roots\Sage\Members\check_member_role();

    if($user_role == 'expired_member'){
      return "Expired Member";
    }else{
      return "Member Profile";
    }
  } elseif (is_singular('event_listing')) { // Single Event Listing
    return "Event Posting";
  } elseif (is_singular('news_item')) { // Single News Item
    return "News Posting";
  } elseif (is_singular('opportunity')) { // Opportunity Listing
    $terms = wp_get_post_terms( get_the_id(), 'opportunity_category' );
    if(!empty($terms[0]->name)){
      return $terms[0]->name;
    }else{
      return "Funding Opportunity";
    }
  } elseif (is_search()) {
    return sprintf(__('Search Results for %s', 'sage'), get_search_query());
  } elseif (is_404()) {
    return __('Not Found', 'sage');
  } else {
    return get_the_title();
  }
}
