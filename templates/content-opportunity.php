<article <?php post_class('row'); ?>>
  <?php
    $id = get_the_id();
    $title = get_the_title();
    $deadline = get_post_meta($id, 'deadline', 1);
    $date_html = "";
    if(!empty($deadline)){
      $month = date("F", strtotime($deadline));
      $day = date("j", strtotime($deadline));
      $date_html = "<div class='date-wrap'>
                      <span class='month'>$month</span>
                      <span class='day'>$day</span>
                    </div>";
    }
    $excerpt = get_the_excerpt();
    $permalink = get_the_permalink($id);
    $logoId = get_post_meta($id, 'logo', 1);
    $external_url = get_post_meta($id, 'external_url', 1);
    $external_url_text = get_post_meta($id, 'external_url_text', 1);

    if(!empty($logoId)){
      $logo = wp_get_attachment_image_src($logoId['ID'], 'opportunity-logo');
      $logoSrc = $logo[0];
    }else{
      $logoSrc = "";
    }
  ?>

  <div class='col-xs-3 col-sm-2 date-wrap-col'>
    <?php if(!empty($date_html)) : ?>
      <?= $date_html; ?>
    <?php endif; ?>
  </div>
  <div class='col-sm-8'>
    <h3><?= $title; ?></h3>
    <p><?= $excerpt; ?></p>
    <p class='link'><a href='<?= $permalink; ?>'>READ MORE</a></p>
  </div>
  <div class='col-sm-2 no-padding text-center logo'>
    <?= (!empty($logoSrc) ? "<img src='$logoSrc' alt='$title'>" : ""); ?>
    <?= ((!empty($external_url) && !empty($external_url_text)) ? "<a class='block' href='$external_url' target='_blank'>$external_url_text</a>" : ""); ?>
  </div>
</article>
