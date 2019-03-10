<?php
/**
 * Template Name: Profile Editor
 */
?>

<?php if (is_user_logged_in()): ?>
  <?php while (have_posts()): the_post();?>
							    <?php
    $user = wp_get_current_user();
    $member_obj = Roots\Sage\Members\get_member_profile_info_by_user_id($user->ID);
    ?>
							    <div class="col-sm-3">
							      <input id="user-profile-id" type="hidden" name="user-id" value="<?=$member_obj->ID;?>">
							      <div class="profile-image">
							        <?=(!empty($member_obj->featured_image_thumb_src) ? "<img src='" . $member_obj->featured_image_thumb_src . "' alt='" . $member_obj->title . "'>" : "");?>
							        <a id="update-profile-image" class="block text-right" href="#">Upload Image <i class="fa fa-upload"></i></a>
							        <form id="profile-image-upload-form" enctype="multipart/form-data" method="post">
							          <input id="profile-image-upload" class="hidden" type="file" name="profile-image-upload">
							        </form>
							      </div>

							      <div class="profile-links">
							        <a href="<?=get_permalink($member_obj->ID);?>">View Profile <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
							        <a href="/dashboard">Dashboard <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
							        <a href="#">Photos <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
							        <a href="/opportunities" target="_blank">Apply For ... <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
							      </div>

							      <div class="profile-social-media">
							        <h3>Social Media</h3>
							        <form class="simple-form" method="post">
							          <p>
							            <label for="facebook"><i class="fa fa-facebook-square"></i></label>
							            <input type="text" name="facebook" value="<?=(!empty($member_obj->facebook)) ? $member_obj->facebook : "";?>">
							          </p>

							          <p>
							            <label for="twitter"><i class="fa fa-twitter"></i></label>
							            <input type="text" name="twitter" value="<?=(!empty($member_obj->twitter)) ? $member_obj->twitter : "";?>">
							          </p>

							          <p>
							            <label for="youtube"><i class="fa fa-youtube"></i></label>
							            <input type="text" name="youtube" value="<?=(!empty($member_obj->youtube)) ? $member_obj->youtube : "";?>">
							          </p>

							          <p>
							            <label for="instagram"><i class="fa fa-instagram"></i></label>
							            <input type="text" name="instagram" value="<?=(!empty($member_obj->instagram)) ? $member_obj->instagram : "";?>">
							          </p>
							          <p>
							            <label for="website_url" title="Website URL"><i class="fa fa-external-link"></i></label>
							            <input type="text" name="website_url" value="<?=(!empty($member_obj->website_url)) ? $member_obj->website_url : "";?>">
							          </p>

							          <p id="social-media-update-submit" class="submit"><input type="submit" name="submit" value="SAVE"></p>
							        </form>
							      </div>

							      <div class="profile-streaming">
							        <h3>Streaming</h3>
							        <form class="simple-form" method="post">
							          <p>
							            <label for="bandcamp"><i class="fa fa-bandcamp icon-bandcamp"></i></label>
							            <input type="text" name="bandcamp" value="<?=(!empty($member_obj->bandcamp)) ? $member_obj->bandcamp : "";?>">
							          </p>
							          <p>
							            <label for="soundcloud"><i class="fa fa-soundcloud"></i></label>
							            <input type="text" name="soundcloud" value="<?=(!empty($member_obj->soundcloud)) ? $member_obj->soundcloud : "";?>">
							          </p>

							          <p>
							            <label for="spotify"><i class="fa fa-spotify"></i></label>
							            <input type="text" name="spotify" value="<?=(!empty($member_obj->spotify)) ? $member_obj->spotify : "";?>">
							          </p>

							          <p class="submit"><input type="submit" name="submit" value="SAVE"></p>
							        </form>
							      </div>
							    </div>
							    <div class="col-sm-9">
							      <h2>Edit Images</h2>
							      <div class="row collapsible">
							        <?php
    // echo "<pre>".print_r($member_obj, true)."</pre>";

    $default_member_image = "/sage/dist/images/profile-editor-default-member-image.png";

    if (!empty($member_obj->additional_image_1[0])) {
        $additional_image_1_id = $member_obj->additional_image_1[0]['ID'];
        $additional_image_1 = wp_get_attachment_image_src($additional_image_1_id, 'member-thumbnail');
        $additional_image_1_src = $additional_image_1[0];
        $additional_image_1_delete = '<a class="additional-image-delete block text-center" data-additional-image-id="' . $additional_image_1_id . '" data-slug="additional_image_1" href="#"><i class="fa fa-remove"></i></a>';
    } else {
        $additional_image_1_src = $default_member_image;
        $additional_image_1_delete = '';
    }

    if (!empty($member_obj->additional_image_2[0])) {
        $additional_image_2_id = $member_obj->additional_image_2[0]['ID'];
        $additional_image_2 = wp_get_attachment_image_src($additional_image_2_id, 'member-thumbnail');
        $additional_image_2_src = $additional_image_2[0];
        $additional_image_2_delete = '<a class="additional-image-delete block text-center" data-additional-image-id="' . $additional_image_2_id . '" data-slug="additional_image_2" href="#"><i class="fa fa-remove"></i></a>';
    } else {
        $additional_image_2_src = $default_member_image;
        $additional_image_2_delete = '';
    }

    if (!empty($member_obj->additional_image_3[0])) {
        $additional_image_3_id = $member_obj->additional_image_3[0]['ID'];
        $additional_image_3 = wp_get_attachment_image_src($additional_image_3_id, 'member-thumbnail');
        $additional_image_3_src = $additional_image_3[0];
        $additional_image_3_delete = '<a class="additional-image-delete block text-center" data-additional-image-id="' . $additional_image_3_id . '" data-slug="additional_image_3" href="#"><i class="fa fa-remove"></i></a>';
    } else {
        $additional_image_3_src = $default_member_image;
        $additional_image_3_delete = '';
    }

    if (!empty($member_obj->additional_image_4[0])) {
        $additional_image_4_id = $member_obj->additional_image_4[0]['ID'];
        $additional_image_4 = wp_get_attachment_image_src($additional_image_4_id, 'member-thumbnail');
        $additional_image_4_src = $additional_image_4[0];
        $additional_image_4_delete = '<a class="additional-image-delete block text-center" data-additional-image-id="' . $additional_image_4_id . '" data-slug="additional_image_4" href="#"><i class="fa fa-remove"></i></a>';
    } else {
        $additional_image_4_src = $default_member_image;
        $additional_image_4_delete = '';
    }
    ?>
							        <div class="additional-image col-sm-3">
							          <div class="inner-wrap">
							            <div class="image-container">
							              <img src="<?=$additional_image_1_src;?>" alt="<?=$member_obj->title;?>">
							              <?=$additional_image_1_delete;?>
							            </div>

							            <a class="additional-image-upload block text-center" href="#">Upload Image <i class="fa fa-upload"></i></a>

							            <form class="additional-image-upload-form" enctype="multipart/form-data" method="post">
							              <input class="hidden" type="file" name="additional-image-1-upload" data-slug="additional_image_1">
							            </form>
							          </div>
							        </div>

							        <div class="additional-image col-sm-3">
							          <div class="inner-wrap">
							            <div class="image-container">
							              <img src="<?=$additional_image_2_src;?>" alt="<?=$member_obj->title;?>">
							              <?=$additional_image_2_delete;?>
							            </div>

							            <a class="additional-image-upload block text-center" href="#">Upload Image <i class="fa fa-upload"></i></a>

							            <form class="additional-image-upload-form" enctype="multipart/form-data" method="post">
							              <input class="hidden" type="file" name="additional-image-2-upload" data-slug="additional_image_2">
							            </form>
							          </div>
							        </div>

							        <div class="additional-image col-sm-3">
							          <div class="inner-wrap">
							            <div class="image-container">
							              <img src="<?=$additional_image_3_src;?>" alt="<?=$member_obj->title;?>">
							              <?=$additional_image_3_delete;?>
							            </div>

							            <a class="additional-image-upload block text-center" href="#">Upload Image <i class="fa fa-upload"></i></a>

							            <form class="additional-image-upload-form" enctype="multipart/form-data" method="post">
							              <input class="hidden" type="file" name="additional-image-3-upload" data-slug="additional_image_3">
							            </form>
							          </div>
							        </div>

							        <div class="additional-image col-sm-3">
							          <div class="inner-wrap">
							            <div class="image-container">
							              <img src="<?=$additional_image_4_src;?>" alt="<?=$member_obj->title;?>">
							              <?=$additional_image_4_delete;?>
							            </div>

							            <a class="additional-image-upload block text-center" href="#">Upload Image <i class="fa fa-upload"></i></a>

							            <form class="additional-image-upload-form" enctype="multipart/form-data" method="post">
							              <input class="hidden" type="file" name="additional-image-4-upload" data-slug="additional_image_4">
							            </form>
							          </div>
							        </div>
							      </div>

							      <h2>Edit Profile Info</h2>
							      <div class="collapsible">
							        <form class="profile-info simple-form">
			                  <input type="hidden" name="member_id" value="<?=$member_obj->ID;?>">
							          <div class="row">
							            <div class="col-sm-12">
							              <label for="post_title">Profile Name</label>
							               <input type="text" name="post_title" value="<?=$member_obj->title;?>">
							            </div>
						              <div class="col-sm-12 minimal">
						                <label for="">Member Category</label>
							              <select name="member_category">
							                <option value="">Select category</option>
							                <?=Roots\Sage\Members\get_member_cats_formated_dd($member_obj->member_category);?>
							              </select>
							            </div>
							            <div class="col-sm-4 minimal">
							              <label for="">Genre 1</label>
							              <select name="genre_1">
							                <option value="">Select genre 1</option>
							                <?=Roots\Sage\Members\get_genres_formated_dd($member_obj->genre_1);?>
							              </select>
							            </div>
							            <div class="col-sm-4 minimal">
							              <label for="">Genre 2</label>
							              <select name="genre_2">
							                <option value="">Select genre 2</option>
							                <?=Roots\Sage\Members\get_genres_formated_dd($member_obj->genre_2);?>
							              </select>
							            </div>
							            <div class="col-sm-4 minimal">
							              <label for="">Genre 3</label>
							              <select name="genre_3">
							                <option value="">Select genre 3</option>
							                <?=Roots\Sage\Members\get_genres_formated_dd($member_obj->genre_3);?>
							              </select>
							            </div>
							          </div>

							          <div class="row">
							            <div class="col-sm-4">
							              <label for="contact_name">Contact Name</label>
							              <input type="text" name="contact_name" value="<?=$member_obj->contact_name;?>">
							            </div>
							            <div class="col-sm-5">
							              <label for="contact_email">Contact Email</label>
							              <input type="text" name="contact_email" value="<?=$member_obj->contact_email;?>">
							            </div>
							            <div class="col-sm-3">
							              <label for="tel_number">Tel Number</label>
							              <input type="text" name="tel_number" value="<?=$member_obj->tel_number;?>">
							            </div>
							          </div>

							          <div class="row">
							            <div class="col-sm-12">
							              <label for="biography">Biography</label>

							              <?php
    $biography = preg_replace("/<p[^>]*?>/", "", $member_obj->biography);
    $biography = str_replace("</p>", "\n", $biography);
    ?>
							              <textarea name="biography"><?=$biography;?></textarea>
							            </div>
							          </div>

							          <div class="row">
							            <div class="col-sm-12">
							              <label for="testimonial">Testimonial</label>

							              <?php
    $testimonial = preg_replace("/<p[^>]*?>/", "", $member_obj->testimonial);
    $testimonial = str_replace("</p>", "\n", $testimonial);
    ?>
							              <textarea name="testimonial"><?=$testimonial;?></textarea>

							              <p class="submit"><input type="submit" name="submit" value="SAVE"></p>
							            </div>
							          </div>
							        </form>
							      </div>
							    </div>
							  <?php endwhile;?>
<?php endif;?>
