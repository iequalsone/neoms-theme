<?php
  $user_role = Roots\Sage\Members\check_member_role();
  $user = wp_get_current_user();
  $member_obj = Roots\Sage\Members\get_member_profile_info(get_the_id());
?>
<?php if(($user_role == 'expired_member') && ($user->ID != $member_obj->user_id)) : // display Expired Member info ?>
  <p>Sorry, this profile has been disabled.</p>
<?php else : // display Active Member Profile info ?>
  <?php while (have_posts()) : the_post(); ?>
    <article <?php post_class(); ?>>
      <div class="row">
        <div class="col-sm-8 member-profile-image text-center">
          <img class="profile-image" src="<?= $member_obj->featured_image_src; ?>" alt="<?= $member_obj->title; ?>">
          <?php if(!empty($member_obj->additional_images[0])) : ?>
            <ul class="additional-images">
              <?php
                foreach($member_obj->additional_images as $ai){
                  $img_src = wp_get_attachment_image_src($ai['ID'], 'member-thumbnail');
                  $img_src_large = wp_get_attachment_image_src($ai['ID'], 'member-featured-image');
                  echo "<li><img data-large-image='".$img_src_large[0]."' src='".$img_src[0]."' alt='".$member_obj->title."'></li>";
                  // echo "<pre>".print_r($img_src, true)."</pre>";
                }
              ?>
            </ul>
          <?php endif; ?>
        </div>
        <div class="col-sm-4 member-info-wrap">
          <h2 class="gray-light"><?= the_title(); ?> <?= (($user->ID == $member_obj->user_id) ? "<a href='/profile-editor'>(edit)</a>" : "") ?></h2>
          <p class="genre red text-uppercase"><?= $member_obj->formatted_genre; ?></p>
          <div class="member-content-wrap">
            <?php the_content(); ?>
          </div>

          <div class="row">
            <?php if(!empty($member_obj->facebook) || !empty($member_obj->twitter)) : ?>
              <?php
                $fb = explode("/", $member_obj->facebook);
                $tw = explode("/", $member_obj->twitter);
              ?>
              <div class="col-xs-6 col-sm-12">
                <h4>Website</h4>
                <?php if(!empty($member_obj->website_url)) : ?>
                  <p><a href="<?= $member_obj->website_url; ?>" target="_blank"><i class="fa fa-external-link"></i> <?= $member_obj->website_url; ?></a></p>
                <?php endif; ?>
              </div>
              <div class="col-xs-6 col-sm-12">
                <h4>Social Media</h4>
                <?php if(!empty($member_obj->facebook)) : ?>
                  <p><a href="<?= $member_obj->facebook; ?>" target="_blank"><i class="fa fa-facebook-square"></i> /<?= end($fb); ?></a></p>
                <?php endif; ?>
                <?php if(!empty($member_obj->twitter)) : ?>
                  <p><a href="<?= $member_obj->twitter; ?>" target="_blank"><i class="fa fa-twitter"></i> @<?= end($tw); ?></a></p>
                <?php endif; ?>
                <?php if(!empty($member_obj->youtube)) : ?>
                  <p><a href="<?= $member_obj->youtube; ?>" target="_blank"><i class="fa fa-youtube"></i> <?= 'Youtube Channel'; ?></a></p>
                <?php endif; ?>
                <?php if(!empty($member_obj->instagram)) : ?>
                  <p><a href="<?= $member_obj->instagram; ?>" target="_blank"><i class="fa fa-instagram"></i> <?= $member_obj->instagram; ?></a></p>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <?php if(!empty($member_obj->soundcloud) || !empty($member_obj->spotify)) : ?>
              <?php
                $sc = explode("/", $member_obj->soundcloud);
                $sp = explode("/", $member_obj->spotify);
              ?>
              <div class="col-xs-6 col-sm-12">
                <h4>Listen</h4>
                <?php if(!empty($member_obj->bandcamp)) : ?>
                  <p><a href="<?= $member_obj->bandcamp; ?>" target="_blank"><i class="fa fa-bandcamp icon-bandcamp"></i> <?= $member_obj->bandcamp; ?></a></p>
                <?php endif; ?>
                <?php if(!empty($member_obj->soundcloud)) : ?>
                  <p><a href="<?= $member_obj->soundcloud; ?>" target="_blank"><i class="fa fa-soundcloud"></i> /<?= end($sc); ?></a></p>
                <?php endif; ?>
                <?php if(!empty($member_obj->spotify)) : ?>
                  <p><a href="<?= $member_obj->spotify; ?>" target="_blank"><i class="fa fa-spotify"></i> <?= 'Spotify'; ?></a></p>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if(!empty($member_obj->member_email)) : ?>
            <a class="btn btn-default btn-lg" href="#book-us-modal" data-toggle="modal" data-target="#book-us-modal">BOOK US!</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="row share-wrap">
        <div class="col-sm-8 text-uppercase">
          <hr />
          <div class="row">
            <div class="col-sm-4">
              <p><strong>Share this profile:</strong></p>
            </div>
            <div class="col-sm-8">
              <?= do_shortcode('[rrssb_share]'); ?>
            </div>
          </div>
        </div>
      </div>
    </article>

    <!-- Modal Dialog for book us form -->
    <div id="book-us-modal" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h2 class="modal-title">Book <?= $member_obj->title; ?>!</h2>
          </div>
          <div class="modal-body">
            <?= do_shortcode('[gravityform id="2" title="false" description="false" field_values="member_email='.$member_obj->member_email.'" ajax="true"]'); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
  <?php endwhile; ?>
<?php endif; ?>
