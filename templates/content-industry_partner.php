<article <?php post_class('col-sm-4 text-center'); ?>>
  <?php
    $id = get_the_id();
    $title = get_the_title();
    $link = get_post_meta($id, 'link', 1);
    $logo = get_post_meta($id, 'partner_logo', 1);
    if(!empty($logo['ID'])){
      $logo_src = wp_get_attachment_image_src($logo['ID'], 'partner-logo');

      if(!empty($link)){
        echo "<a href='$link' target='_blank'><img src='$logo_src[0]' alt='$title'></a>";
      }else{
        echo "<img src='$logo_src[0]' alt='$title'>";
      }
    }
  ?>
</article>
