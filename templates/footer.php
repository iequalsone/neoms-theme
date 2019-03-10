<?php if (!is_front_page() && !is_page(38) && !is_page(76) && !is_singular('user_profile') && is_page_template('poll-vote')): ?>
<section class="pre-footer-wrap">
  <div class="container text-center member-cta">
    <h1>BECOME A MEMBER TODAY!</h1>
    <p>IT ONLY TAKES A FEW MORE MINUTES AND YOU'LL BE SET.</p>

    <div class="vc_btn3-container  font-montserrat vc_btn3-center">
      <a href="/become-a-member" class="vc_general vc_btn3 vc_btn3-size-lg vc_btn3-shape-rounded vc_btn3-style-flat vc_btn3-color-grey">SIGN ME UP NOW!</a>
    </div>
  </div>
</section>
<?php endif;?>

<?php if (!is_front_page() && !is_singular('user_profile') && (!is_page(6259))): ?>
<section class="rrssb-share-wrap">
  <div class="container">
    <div class="row">
      <div class="col-sm-6 first">
        <p class="font-montserrat">TELL YOUR MUSICIAN FRIENDS</p>
      </div>
      <div class="col-sm-6 last">
        <?=do_shortcode('[rrssb_share]');?>
      </div>
    </div>
  </div>
</section>
<?php endif;?>

<footer class="content-info">
  <div id="embed-player-wrap">
    <?=get_option('company_info_spotify_playlist');?>
  </div>
  <div class="row">
    <div class="container">
      <div class="copy-right hidden-xs col-sm-6 pull-left">&copy; <?=date("Y") . " " . get_bloginfo('name');?>. All Rights Reserverd.</div>
      <div class="col-sm-6 footer-nav-wrap">
        <?php
if (has_nav_menu('footer_navigation')):
    wp_nav_menu(['theme_location' => 'footer_navigation', 'walker' => new wp_bootstrap_navwalker(), 'menu_class' => 'nav navbar-nav']);
endif;
?>
      </div>
    </div>
  </div>
</footer>
<!-- Modal Dialog for Member Sign-In -->
<div id="member-log-in-modal" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h2 class="modal-title">Member Log-In</h2>
      </div>
      <div class="modal-body">
        <?=do_shortcode('[get_member_login_form]');?>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$cookie_name_enews = "enews_popup";
if (!isset($_COOKIE[$cookie_name_enews])): ?>
    <!-- Modal Dialog for Newsletter Signup -->
    <div id="newsletter-signup-modal" data-backdrop="static" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content container">
          <div class="modal-body">
          <div class="col-sm-6 left">
            <h1>Even more instrumental connections, right in your inbox.</h1>
          </div>
            <div class="col-sm-6 right">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <p>Join our newsletter for the latest events, News, and member exclusives. And hey - tell your friends</p>
              <div id='mc_embed_signup'>
                <form action='' method='post' id='mc-embedded-subscribe-form' name='mc-embedded-subscribe-form' class='validate' target='_blank'>
                  <div class='row'>
                    <div class='col-xs-12'>
                      <input type='email' value='' name='EMAIL' class='required email font-montserrat' id='mce-EMAIL' placeholder='Enter email address here' required='required'>
                    </div>
                    <div class='col-xs-12'>
                      <input type='submit' value='Subscribe' name='subscribe' id='mc-embedded-subscribe' class='button font-montserrat'>
                    </div>
                  </div>
                  <div id='mce-responses' class='clear'>
                    <div class='response' id='mce-error-response' style='display:none'></div>
                    <div class='response' id='mce-success-response' style='display:none'></div>
                  </div>
                  <div style='position: absolute; left: -5000px;' aria-hidden='true'><input type='text' name='b_8b1a29192e67a8ea3c2d4d7b9_509ec5f61c' tabindex='-1' value=''></div>
                </form>
              </div>
            </div>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif;?>