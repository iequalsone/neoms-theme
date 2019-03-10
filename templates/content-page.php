<?php
  $user = wp_get_current_user();
  $user_role = Roots\Sage\Members\check_member_role($user->ID);
  
  $member_obj = Roots\Sage\Members\get_member_profile_info(get_the_id());
?>

<?php if( is_page(487) ||
          is_page(491) ||
          is_page(493) ) : ?>
    <?php if(!empty($user->roles[0]) && ($user->roles[0] == 'active_member')) : ?>
      <?php the_content(); ?>
    <?php else: ?> 
      <p>Sorry, you must be an active user to view this page.</p>
      <p><a href="<?= get_the_permalink(38); ?>">Click here to become a member!</a></p>
    <?php endif; ?>
<?php else: ?>
  <?php the_content(); ?>
<?php endif; ?>

<?php wp_link_pages(['before' => '<nav class="page-nav"><p>' . __('Pages:', 'sage'), 'after' => '</p></nav>']); ?>