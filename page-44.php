<?php if ( isset($_GET['checkemail']) && ($_GET['checkemail'] == 'confirm') ) : ?>
    <p class="login-info text-center">
        <?php _e( 'Please check your email for a confirmation link.', 'member-login' ); ?>
    </p>
<?php endif; ?>

<?php if ( isset($_GET['password']) && ($_GET['password'] == 'changed') ) : ?>
    <p class="login-info text-center">
        <?php _e( 'Your password has been changed. You can now login.', 'member-login' ); ?>
    </p>
<?php endif; ?>



<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/content', 'page'); ?>
<?php endwhile; ?>
