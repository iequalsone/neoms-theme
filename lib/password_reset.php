<?php
// custom forgot password functionality
add_action( 'login_form_lostpassword', __NAMESPACE__ . '\\redirect_to_custom_lostpassword', 10, 3 );
/**
 * Redirects the user to the custom "Forgot your password?" page instead of
 * wp-login.php?action=lostpassword.
 */
function redirect_to_custom_lostpassword() {
  if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
    if ( is_user_logged_in() ) {
      exit;
    }

    wp_redirect( home_url( 'forgotten-password' ) );
    exit;
  }
}

add_shortcode( 'custom-password-lost-form', __NAMESPACE__ . '\\render_password_lost_form' );
/**
 * A shortcode for rendering the form used to initiate the password reset.
 *
 * @param  array   $attributes  Shortcode attributes.
 * @param  string  $content     The text content for shortcode. Not used.
 *
 * @return string  The shortcode output
 */
function render_password_lost_form( $attributes, $content = null ) {
  $html = "";

  if(isset($_GET['errors'])){
    switch($_GET['errors']){
      case 'invalid_email':
        $message = "<p class='error text-center'>The email address given is invalid.</p>";
        break;

      case 'empty_username':
        $message = "<p class='error text-center'>Please enter a valid email address.</p>";
        break;

      case 'invalidcombo':
        $message = "<p class='error text-center'>Please enter a valid email address.</p>";
        break;

      defaut:
        break;
    }
  }

  if(!empty($message)){
    $html .= $message;
  }
  $html .= "<div class='password-reset-form row'>
              <form class='col-sm-4 col-sm-offset-4' id='lostpasswordform' action='".wp_lostpassword_url()."' method='post'>
                <p class='text-center'>Enter your email address and we'll send you a link you can use to pick a new password.</p>
                <div class='gform_body'>
                  <ul class='gform_fields top_label'>
                    <li class='gfield text-center'>
                      <label class='gfield_label' for='user_login'>Email</label>
                      <input type='text' name='user_login' id='user_login'>
                    </li>
                  </ul>
                </div>
                <div class='gform_footer top_label text-center'>
                  <input type='submit' name='submit' class='lostpassword-button' value='Reset Password'/>
                </div>
              </form>
            </div>";

  if ( is_user_logged_in() ) {
    return __( 'You are already signed in.', 'forgotten-password' );
  } else {
    return $html;
  }
}

add_action( 'login_form_lostpassword', __NAMESPACE__ . '\\do_password_lost' );
/**
 * Initiates password reset.
 */
function do_password_lost() {
  if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
    $errors = retrieve_password();
    if ( is_wp_error( $errors ) ) {
      // Errors found
      $redirect_url = home_url( 'forgotten-password' );
      $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
    } else {
      // Email sent
      $redirect_url = home_url( 'member-login' );
      $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
    }

    wp_redirect( $redirect_url );
    exit;
  }
}

add_filter( 'retrieve_password_message', __NAMESPACE__ . '\\replace_retrieve_password_message', 10, 4 );
/**
 * Returns the message body for the password reset mail.
 * Called through the retrieve_password_message filter.
 *
 * @param string  $message    Default mail message.
 * @param string  $key        The activation key.
 * @param string  $user_login The username for the user.
 * @param WP_User $user_data  WP_User object.
 *
 * @return string   The mail message to send.
 */
function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
    // Create new message
    $msg  = __( 'Hello!', 'forgotten-password' ) . "\r\n\r\n";
    $msg .= sprintf( __( 'You asked us to reset your password for your account using the login %s.', 'forgotten-password' ), $user_login ) . "\r\n\r\n";
    $msg .= __( "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'forgotten-password' ) . "\r\n\r\n";
    $msg .= __( 'To reset your password, visit the following address:', 'forgotten-password' ) . "\r\n\r\n";
    $msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
    $msg .= __( 'Thanks!', 'forgotten-password' ) . "\r\n";

    return $msg;
}

add_action( 'login_form_rp', __NAMESPACE__ . '\\redirect_to_custom_password_reset' );
add_action( 'login_form_resetpass', __NAMESPACE__ . '\\redirect_to_custom_password_reset' );
/**
 * Redirects to the custom password reset page, or the login page
 * if there are errors.
 */
function redirect_to_custom_password_reset() {
    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
        // Verify key / login combo
        $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
        if ( ! $user || is_wp_error( $user ) ) {
            if ( $user && $user->get_error_code() === 'expired_key' ) {
                wp_redirect( home_url( 'member-login?login=expiredkey' ) );
            } else {
                wp_redirect( home_url( 'member-login?login=invalidkey' ) );
            }
            exit;
        }

        $redirect_url = home_url( 'member-password-reset' );
        $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
        $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

        wp_redirect( $redirect_url );
        exit;
    }
}

add_shortcode( 'custom-password-reset-form', __NAMESPACE__ . '\\render_password_reset_form' );
/**
 * A shortcode for rendering the form used to reset a user's password.
 *
 * @param  array   $attributes  Shortcode attributes.
 * @param  string  $content     The text content for shortcode. Not used.
 *
 * @return string  The shortcode output
 */
function render_password_reset_form( $attributes, $content = null ) {
  // Parse shortcode attributes
  $default_attributes = array( 'show_title' => false );
  $attributes = shortcode_atts( $default_attributes, $attributes );

  if ( is_user_logged_in() ) {
    return __( 'You are already signed in.', 'forgotten-password' );
  } else {
    if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
      $attributes['login'] = $_REQUEST['login'];
      $attributes['key'] = $_REQUEST['key'];

      // Error messages
      $errors = array();
      if ( isset( $_REQUEST['error'] ) ) {
          $error_codes = explode( ',', $_REQUEST['error'] );

          foreach ( $error_codes as $code ) {
              $errors []= $this->get_error_message( $code );
          }
      }
      $attributes['errors'] = $errors;

      $html = "";
      $html .= '<div class="password-reset-form row text-center">
                  <h3>Pick a New Password</h3>

                  <form class="col-sm-4 col-sm-offset-4" name="resetpassform" id="resetpassform" action="/wp/wp-login.php?action=resetpass" method="post" autocomplete="off">
                  <div class="gform_body">

                      <input type="hidden" id="user_login" name="rp_login" value="'.esc_attr( $attributes['login'] ).'" autocomplete="off" />
                      <input type="hidden" name="rp_key" value="'.esc_attr( $attributes['key'] ).'" />';

                      if ( count( $attributes['errors'] ) > 0 ) :
                          foreach ( $attributes['errors'] as $error ) :
                              echo "<p>$error</p>";
                          endforeach;
                      endif;

      $html .=        '<ul class="gform_fields top_label">
                        <li class="gfield text-center">
                          <label class="gfield_label" for="pass1">New password</label>
                          <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
                        </li>
                        <li class="gfield text-center">
                          <label class="gfield_label" for="pass2">Repeat new password</label>
                          <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
                        </li>



                      <div class="gform_footer top_label text-center">
                        <p class="description text-center">'.wp_get_password_hint().'</p>
                        <p class="resetpass-submit">
                            <input type="submit" name="submit" id="resetpass-button" class="button" value="Reset Password" />
                        </p>
                      </div>
                  </form>
                </div>';
      return $html;
    } else {
      return __( 'Invalid password reset link.', 'forgotten-password' );
    }
  }
}

add_action( 'login_form_rp', __NAMESPACE__ . '\\do_password_reset' );
add_action( 'login_form_resetpass', __NAMESPACE__ . '\\do_password_reset' );
/**
 * Resets the user's password if the password reset form was submitted.
 */
function do_password_reset() {
  if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
    $rp_key = $_REQUEST['rp_key'];
    $rp_login = $_REQUEST['rp_login'];

    $user = check_password_reset_key( $rp_key, $rp_login );

    if ( ! $user || is_wp_error( $user ) ) {
        if ( $user && $user->get_error_code() === 'expired_key' ) {
            wp_redirect( home_url( 'member-login?login=expiredkey' ) );
        } else {
            wp_redirect( home_url( 'member-login?login=invalidkey' ) );
        }
        exit;
    }

    if ( isset( $_POST['pass1'] ) ) {
        if ( $_POST['pass1'] != $_POST['pass2'] ) {
            // Passwords don't match
            $redirect_url = home_url( 'member-password-reset' );

            $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
            $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
            $redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );

            wp_redirect( $redirect_url );
            exit;
        }

        if ( empty( $_POST['pass1'] ) ) {
            // Password is empty
            $redirect_url = home_url( 'member-password-reset' );

            $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
            $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
            $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

            wp_redirect( $redirect_url );
            exit;
        }

        // Parameter checks OK, reset password
        reset_password( $user, $_POST['pass1'] );
        wp_redirect( home_url( 'member-login?password=changed' ) );
    } else {
        echo "Invalid request.";
    }

    exit;
  }
}
