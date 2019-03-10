<?php
/**
 * Template Name: Member Dashboard
 */
?>

<?php if (is_user_logged_in() && !current_user_can('administrator')): ?>
  <?php while (have_posts()): the_post();?>
																																					    <?php
    $user = wp_get_current_user();
    $member_obj = Roots\Sage\Members\get_member_profile_info_by_user_id($user->ID);
    ?>

	    <div class="col-sm-3">
	      <input id="user-profile-id" type="hidden" name="user-id" value="<?=$member_obj->ID;?>">
	      <div class="profile-image">
	        <?=(!empty($member_obj->featured_image_thumb_src) ? "<img src='" . $member_obj->featured_image_thumb_src . "' alt='" . $member_obj->title . "'>" : "");?>
	      </div>

	      <div class="profile-links">
	        <a href="<?=get_permalink($member_obj->ID);?>">View Profile <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
	        <a href="/profile-editor">Edit Profile <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
	        <a href="/profile-editor">Photos <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
	        <a href="/opportunities" target="_blank">Apply For ... <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
	      </div>

	      <div class="member-expiration">
	        <?php
    if ($member_obj->member_role == 'active_member') {
        $date_expired = date("F j, Y", strtotime($member_obj->membership_expiry_date));
    } else {
        $date_expired = "EXPIRED";
    }
    ?>

	        <h3>Membership Expires</h3>
	        <p><strong><?=$date_expired;?></strong></p>
	      </div>

	      <div class="funding-deadlines">
	        <h3>Upcoming Funding &amp; grants Deadlines</h3>
	        <?php if ($member_obj->member_role == 'active_member'): ?>
	          <?php
    $curdate = date("Y-m-d");
    $query = new \WP_Query([
        'post_type' => 'opportunity',
        'posts_per_page' => 5,
        'tax_query' => [
            [
                'taxonomy' => 'opportunity_tag',
                'field' => 'slug',
                'terms' => array('funding', 'grant'),
            ],
        ],
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'deadline',
                'compare' => 'EXISTS',
            ],
            [
                'key' => 'deadline',
                'value' => $curdate,
                'compare' => '>=',
                'type' => 'DATE',
            ],
        ],
        'meta_key' => 'deadline',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    ]);

    if ($query->have_posts()) {
        echo "<ul>";
        foreach ($query->posts as $p) {
            $id = $p->ID;
            $title = $p->post_title;
            $deadline_date = get_post_meta($id, 'deadline', 1);
            $deadline_date = date("F jS, Y", strtotime($deadline_date));
            $permalink = get_the_permalink($id);

            echo "<li><a href='$permalink'><span class='date'>$deadline_date</span>$title</a></li>";
        }
        echo "</ul>";
    }

    wp_reset_query();
    ?>
	        <?php endif;?>
      </div>
    </div>
    <div class="col-sm-9">
      <h2><?=$member_obj->title;?></h2>

      <?php
/*
 * TESTING
 * TESTING
 * TESTING
 * TESTING
 * TESTING
 * TESTING
 * TESTING
 * TESTING
 * TESTING */
?>
      <?php $wpenv = getenv('WP_ENV');?>
      <?php if ($wpenv === "development"): ?>
        <?php date_default_timezone_set("Canada/Newfoundland");?>
        <?php $current_login = strtotime(get_user_meta($user->ID, 'current_login', 1));?>
        <?php $previous_login = strtotime(get_user_meta($user->ID, 'previous_login', 1));?>
        <pre>Current Login: <?=date("Y-m-d H:i:s", $current_login);?></pre>
        <pre>Previous Login: <?=date("Y-m-d H:i:s", $previous_login);?></pre>
      <?php endif;?>
<?php // END TESTING ?>

      <h3 class="genre"><?=$member_obj->formatted_genre;?></h3>
      <h4 class="pageviews"><i class="fa fa-eye red" aria-hidden="true"></i>PROFILE VIEWS <span class="total float-right">TOTAL: <span class="red"><?=wp_statistics_pages('total', get_page_uri($member_obj->ID), $member_obj->ID);?></span></span></h4>
      <hr />

      <?php if ($member_obj->member_role != 'active_member'): ?>
        <div class="expired-membership-wrap">
          <h2>Your Membership<br />Has Expired</h2>
          <p class='text-uppercase font-montserrat'>all member features have been locked and public visibility of your profile has been disabled.  To reactivate these features and visibility please renew your membership.</p>
          <a id="renew-membership-btn" class="btn btn-default" href="#">Renew Your Membership</a>

          <!-- Modal Dialog for Renew Membership form -->
          <div id="renew-membership-modal" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h2 class="modal-title">Membership Renewal</h2>
                </div>
                <div class="modal-body">
                  <?=do_shortcode('[gravityform id="7" title="false" description="false" field_values="contact_name=' . $member_obj->title . '&email_address=' . $member_obj->member_email . '"]');?>
                </div>
              </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
          </div><!-- /.modal -->
        </div>
      <?php endif;?>

      <?php if ($member_obj->member_role == 'active_member'): ?>
        <div class="upcoming-events-wrap">
          <?php
$query = new \WP_Query([
    'post_type' => 'event_listing',
    'author' => $user->ID,
]);

if ($query->have_posts()) {
    echo "<h2><span>Your</span> Upcoming Events</h2>
                    <hr />";
    foreach ($query->posts as $p) {
        $id = $p->ID;
        $title = $p->post_title;
        $content = $p->post_content;
        $sub_title = get_post_meta($id, 'sub_title', 1);
        $event_date = get_post_meta($id, 'e_date', 1);
        $time = get_post_meta($id, 'time', 1);
        $location = get_post_meta($id, 'location', 1);
        $terms = get_the_terms($id, 'event_category');
        ?>
                <article <?php post_class('row event_listing');?>>
                  <a href="<?=get_the_permalink($id);?>"></a>
                  <div class="col-xs-2 event-date">
                    <span class="month"><?=date("F", strtotime($event_date));?></span>
                    <span class="day"><?=date("j", strtotime($event_date));?></span>
                  </div>
                  <div class="col-xs-10">
                    <header>
                      <h2>
                        <?=(!empty($terms[0]) ? $terms[0]->name . " - " : "");?>
                        <?=$title;?>
                        <?=(!empty($location) ? " @ " . $location : "");?>
                        <?=(!empty($time) ? " | " . $time : "");?>
                      </h2>
                      <?php if (!empty($sub_title)): ?>
                        <h3 class="entry-title"><a href="<?php get_the_permalink($id);?>"><?=$sub_title;?></a></h3>
                      <?php endif;?>
                    </header>
                    <div class="entry-summary">
                      <p><?=substr($content, 0, 150);?>...</p>
                      <a href="<?=get_the_permalink($id);?>">View Event Info</a>
                    </div>
                  </div>
                </article>
                <?php
}
}

wp_reset_query();
?>
        </div>
      <?php endif;?>

      <?php if ($member_obj->member_role == 'active_member'): ?>
        <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // prevent duplicate entries after refreshing page
    if (!in_array($_POST['nonce'], $_SESSION['posts'])) {
        // It is the first time. We add it to the list of "seen" nonces.
        $_SESSION['posts'][] = $_POST['nonce'];
        $event_tags = explode(',', $_POST['event-tags']);

        $up_post = array(
            'post_type' => 'event_listing',
            'post_title' => $_POST['title'],
            'post_status' => 'pending',
            'post_author' => $user->ID,
            'post_content' => $_POST['event-content'],
        );

        $start_datetime = $_POST['start-date'] . " " . $_POST['start-time'];
        $end_datetime = $_POST['end-date'] . " " . $_POST['end-time'];

        // Insert a new User Created Event
        $post_id = wp_insert_post($up_post);
        add_post_meta($post_id, 'sub_title', $_POST['sub-title']);
        add_post_meta($post_id, 'start_datetime', date("Y-m-d H:i", strtotime($start_datetime)));
        add_post_meta($post_id, 'end_datetime', date("Y-m-d H:i", strtotime($end_datetime)));
        add_post_meta($post_id, 'time', $_POST['event-time']);
        add_post_meta($post_id, 'location', $_POST['location']);
        add_post_meta($post_id, 'tickets_cost', $_POST['tickets-cost']);
        add_post_meta($post_id, 'website', $_POST['website']);
        add_post_meta($post_id, 'floating_tag', $_POST['floating-tag']);
        wp_set_object_terms($post_id, [$_POST['event-category']], 'event_category', false);
        wp_set_object_terms($post_id, [$_POST['event-region']], 'event_region', false);
        wp_set_object_terms($post_id, $event_tags, 'event_tag', false);

        $upload = wp_upload_bits($_FILES["e-image"]["name"], null, file_get_contents($_FILES["e-image"]["tmp_name"]));
        $filename = $upload['file'];
        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit',
        );
        $attach_id = wp_insert_attachment($attachment, $filename, $post_id);
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
        wp_update_attachment_metadata($attach_id, $attach_data);
        set_post_thumbnail($post_id, $attach_id);
    }
}
?>

        <?php if (!empty($post_id)): ?>
          <div class="alert alert-success">
            <strong>Success!</strong> Your event has been submitted for review.
          </div>
        <?php endif;?>

        <p><a class="add-event" href="#"><i class="fa fa-plus-square" aria-hidden="true"></i> Add a new event</a></p>
        <div class="event-collapsible">
          <form class="" action="" method="POST" enctype="multipart/form-data">
            <div class="row">
              <div class="col-sm-12">
                <label for="title">Event Title</label>
                <input type="text" name="title" value="" required>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <label for="sub-title">Sub-title (shown on listing page)</label>
                <input type="text" name="sub-title" value="">
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                  <label for="file">Event Image</label>
                  <input type="file" name="e-image">
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <label for="sub-title">Event Content</label>
                <textarea name="event-content" required></textarea>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <label for="start-date">Start Date</label>
                <input autocomplete="off" class="datepicker" type="text" name="start-date" value="" required>

                <label for="start-time">Start Time</label>
                <input autocomplete="off" class="timepicker" type="text" name="start-time" value="" required>
              </div>

              <div class="col-sm-6">
                <label for="end-date">End Date</label>
                <input autocomplete="off" class="datepicker" type="text" name="end-date" value="">

                <label for="end-time">End Time</label>
                <input autocomplete="off" class="timepicker" type="text" name="end-time" value="">
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12 minimal">
                <label for="location">Region</label>
                <select name="event-region" required>
                  <option value="">Choose a Region</option>
                  <?php
$regions = get_terms([
    'taxonomy' => 'event_region',
    'hide_empty' => false,
]);

foreach ($regions as $r) {
    $id = $r->term_id;
    $name = $r->name;
    $slug = $r->slug;

    echo "<option value='$slug'>$name</option>";
}
?>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <label for="location">Location</label>
                <input type="text" name="location" value="">
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <label for="sub-title">Tickets / Cost</label>
                <textarea name="tickets-cost"></textarea>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-6">
                <label for="sub-title">Website</label>
                <input type="text" name="website" value="">
              </div>

              <div class="col-sm-6">
                <label for="sub-title">Floating Tag</label>
                <input type="text" name="floating-tag" value="">

                <p><small>Top right corner of thumbnail listing</small></p>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12 minimal">
                <label for="event-category">Event Category</label>
                <select name="event-category">
                  <option value="">Choose a Category</option>
                  <?php
$terms = get_terms(array(
    'taxonomy' => 'event_category',
    'hide_empty' => false,
));

foreach ($terms as $t) {
    if ($t->parent != 0) {
        $id = $t->term_id;
        $name = $t->name;
        $slug = $t->slug;

        echo "<option value='$slug'>$name</option>";
    }
}
?>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <label for="event-tags">Event Tags (separate using commas) </label>
                <input type="text" name="event-tags" value="">
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <input type="hidden" name="nonce" value="<?=uniqid();?>" />
                <p class="submit"><input class="btn btn-default" type="submit" name="submit" value="Submit for review"></p>
              </div>
            </div>
          </form>
        </div>
      <?php else: ?>
        <p><a class="add-event" href="#"><i class="fa fa-plus-square" aria-hidden="true"></i> Renew membership to post events</a></p>
      <?php endif;?>
    </div>
  <?php endwhile;?>
<?php else: ?>
<?php endif;?>
