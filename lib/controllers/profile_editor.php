<?php
namespace Roots\Sage\Members;

require_once ABSPATH . "wp-admin" . '/includes/image.php';
require_once ABSPATH . "wp-admin" . '/includes/file.php';
require_once ABSPATH . "wp-admin" . '/includes/media.php';

add_action('wp_ajax_nopriv_profile_editor_update_profile_image', __NAMESPACE__ . '\\profile_editor_update_profile_image');
add_action('wp_ajax_profile_editor_update_profile_image', __NAMESPACE__ . '\\profile_editor_update_profile_image');
function profile_editor_update_profile_image()
{
    if (empty($_POST)) {
        die();
    }

    $html = "";
    $user_profile_id = htmlentities($_POST['user_profile_id']);

    foreach ($_FILES as $file) {
        if (is_array($file)) {
            $file_handler = 'profile_image';
            $attach_id = media_handle_upload($file_handler, $user_profile_id);
            update_post_meta($user_profile_id, '_thumbnail_id', $attach_id);

            $img = wp_get_attachment_image_src($attach_id, 'member-profile-thumbnail');
            echo $img[0];
        }
    }

    // $html .= "<pre>".print_r($_FILES, true)."</pre>";
    // echo $response;
    die();
}

add_action('wp_ajax_nopriv_profile_editor_update_additional_images', __NAMESPACE__ . '\\profile_editor_update_additional_images');
add_action('wp_ajax_profile_editor_update_additional_images', __NAMESPACE__ . '\\profile_editor_update_additional_images');
function profile_editor_update_additional_images()
{
    if (empty($_POST)) {
        die();
    }

    $html = "";
    $user_profile_id = htmlentities($_POST['user_profile_id']);
    $image_handler = htmlentities($_POST['additional_image_handler']);

    foreach ($_FILES as $file) {
        if (is_array($file)) {
            $attach_id = media_handle_upload('additional_image', $user_profile_id);
            update_post_meta($user_profile_id, $image_handler, $attach_id);

            $img = wp_get_attachment_image_src($attach_id, 'member-thumbnail');

            echo json_encode(
                array(
                    'src' => $img[0],
                    'remove_button' => '<a class="additional-image-delete block text-center" data-additional-image-id="' . $attach_id . '" data-slug="' . $image_handler . '" href="#"><i class="fa fa-remove"></i></a>',
                )
            );
        }
    }

    // foreach($attach_ids as $image_id){
    //   $image = wp_get_attachment_image_src($image_id, 'member-thumbnail');
    //   $image_src[] = $image[0];
    // }

    // $html .= "<pre>".print_r($image_src, true)."</pre>";
    // echo json_encode($image_src);
    die();
}

add_action('wp_ajax_nopriv_profile_editor_delete_additional_images', __NAMESPACE__ . '\\profile_editor_delete_additional_images');
add_action('wp_ajax_profile_editor_delete_additional_images', __NAMESPACE__ . '\\profile_editor_delete_additional_images');
function profile_editor_delete_additional_images()
{
    if (empty($_POST)) {
        die();
    }

    $html = "";
    $user_profile_id = htmlentities($_POST['user_profile_id']);
    $additional_image_id = htmlentities($_POST['additional_image_id']);
    $image_handler = htmlentities($_POST['additional_image_handler']);

    $detach_success = wp_delete_attachment($additional_image_id);
    $meta_update_success = update_post_meta($user_profile_id, $image_handler, null);

    if ($detach_success && $meta_update_success) {
        echo get_template_directory_uri() . '/dist/images/profile-editor-default-member-image.png';
    } else {
        echo 'Error something happened!';
    }

    die();
}

// Simple Form update function (non-image data)
add_action('wp_ajax_nopriv_profile_editor_simple_form_update', __NAMESPACE__ . '\\profile_editor_simple_form_update');
add_action('wp_ajax_profile_editor_simple_form_update', __NAMESPACE__ . '\\profile_editor_simple_form_update');
function profile_editor_simple_form_update()
{
    if (empty($_POST)) {
        die();
    }

    $html = "";
    $user_profile_id = htmlentities($_POST['user_profile_id']);
    if (!empty($_POST['form_data'])) {
        foreach ($_POST['form_data'] as $data) {
            if ($data['name'] === 'post_title') {
                //update post title and slug
                $flag = wp_update_post(array(
                    'ID' => $user_profile_id,
                    'post_title' => $data['value'],
                    'post_name' => sanitize_title($data['value']),
                ));
            }
            //update post content/bio
            elseif ($data['name'] === 'biography') {
                $bio_content = strip_tags($data['value']);
                $flag = wp_update_post(array(
                    'ID' => $user_profile_id,
                    'post_content' => esc_textarea($bio_content),
                ));
            } else { //else
                $meta_key = htmlentities($data['name']);
                $meta_value = htmlentities($data['value']);

                $flag = update_post_meta($user_profile_id, $meta_key, $meta_value);
            }
        }
    }

    $html .= $flag;

    // $html .= print_r($_POST, true);
    echo $html;
    die();
}
