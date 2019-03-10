/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function ($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function () {
        // JavaScript to be fired on all pages
        // $('a[href*=#]').click(function() {
        //   if (location.pathname.replace(/^\//,'') === this.pathname.replace(/^\//,'') && location.hostname === this.hostname) {
        //     var $target = $(this.hash);
        //     $target = $target.length && $target || $('[name=' + this.hash.slice(1) +']');
        //     if ($target.length) {
        //       var targetOffset = $target.offset().top - 150;
        //       $('html,body')
        //       .animate({scrollTop: targetOffset}, 1000);
        //     return false;
        //     }
        //   }

        //   console.log("here");
        // });

        //add copy to login form username label
        $('#member-log-in-modal #field_1 label').html('Username / Email');
        // console.log(label);
        // label.innerHTML = label.innerHTML + ' / Email';

        var memberLoginLink = $("#menu-footer-navigation .member-login a");
        var memberPollVoteLoginLink = $(".poll-vote-page-login");

        memberLoginLink.on("click", function (event) {
          event.preventDefault();
          $("#member-log-in-modal").modal();
        });

        memberPollVoteLoginLink.on("click", function (event) {
          event.preventDefault();
          $("#member-log-in-modal").modal();
        });

        // Member Seach functionality
        $('#member-search-str').typeWatch({
          captureLength: 2,
          callback: function (value) {
            var that = $(this);
            var target = that;
            var results = $(that.data("results"));
            var submit = $(that.data("submit"));
            var searchStr = target.val();

            $.ajax({
              type: "POST",
              url: ajaxURL.ajaxurl,
              data: {
                action: "get_member_search_results",
                search_str: searchStr
              },
              beforeSend: function () {
                results.hide();
              },
              success: function (r) {
                results.html(r);
                // $(".autocomplete-result-item a").on("click", function(event){
                //   event.preventDefault();
                //   console.log($(this));
                // });

                results.find("li").each(function () {
                  $(this).find("a").on("click", function (event) {
                    event.preventDefault();
                    target.val($(this).html());
                    results.slideToggle();
                    submit.trigger("click");
                  });
                });

                // console.log(results.find("li"));
                results.slideToggle();
                // console.log(r);
                // clicked_approve_button.prop('disabled', true).removeClass('approve_wait_list_entry').text('APPROVED');
              }
            });
          }
        });

        $("#filter-alphabetically").on("change", function () {
          var form = $(this).data("form");
          $(form).submit();
        });

        $("#filter-genre").on("change", function () {
          var form = $(this).data("form");
          $(form).submit();
        });

        $("#filter-category").on("change", function () {
          var form = $(this).data("form");
          $(form).submit();
        });

        $(".section-trigger").on("click", function (event) {
          event.preventDefault();
          $(this).toggleClass("active").next(".section-collapsable").slideToggle();
        });

        $("#menu-footer-navigation .fa-music a").on("click", function (event) {
          event.preventDefault();
          $("#embed-player-wrap").slideToggle(function () {
            $(this).toggleClass('expanded');
          });
        });

        //close playlist on click outside when it's open
        $(document).click(function (e) {
          var target = e.target;
          if (!$(target).is('#embed-player-wrap') && !$(target).is('#menu-footer-navigation .fa-music a') && !$(target).parents().is('#embed-player-wrap')) {
            $('#embed-player-wrap.expanded').slideToggle(function () {
              $(this).toggleClass('expanded');
            });
          }
        });


        if ($('#gform_0 .gfield_description.validation_message').length > 0) {
          $("#member-log-in-modal").modal();
        }

        $(".video").fancybox({
          'padding': 0,
          'autoScale': false,
          'title': this.title,
          'width': 640,
          'height': 385
        });

        $(".video").trigger("click");
      },
      finalize: function () {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function () {
        // JavaScript to be fired on the home page
        var memberSpotlightSlider = $(".member-spotlight-wrap");
        memberSpotlightSlider.slick({
          arrows: false,
          adaptiveHeight: true
        });
        $(".desktop-prev").on("click", function (event) {
          event.preventDefault();
          memberSpotlightSlider.slick('slickPrev');
        });
        $(".desktop-next").on("click", function (event) {
          event.preventDefault();
          memberSpotlightSlider.slick('slickNext');
        });
        $(".mobile-prev").on("click", function (event) {
          event.preventDefault();
          memberSpotlightSlider.slick('slickPrev');
        });
        $(".mobile-next").on("click", function (event) {
          event.preventDefault();
          memberSpotlightSlider.slick('slickNext');
        });

        var industryPartnerSlider = $(".industry-partner-slider");
        industryPartnerSlider.slick({
          infinite: true,
          slidesToShow: 2,
          slidesToScroll: 1,
          arrows: false,
          autoplay: true,
          responsive: [
            {
              breakpoint: 480,
              settings: {
                slidesToShow: 1
              }
            }
          ]
        });
      },
      finalize: function () {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    // Become a Member Application Form page
    'page_id_76': {
      init: function () {
        // $(".main .gform_wrapper form").prop("target", "_blank");
        $(".gform_button").after("<img class='paypal-logo' src='/sage/assets/images/logo-paypal.png' alt='PayPal'>");
      }
    },
    // Events Test - React App
    'page_id_6259': {
      init: function () {
        // $(".main .gform_wrapper form").prop("target", "_blank");
        setTimeout(function () {
          $("#newsletter-signup-modal").modal();
        }, 5000);
      }
    },
    // Single Member Profile
    'single_user_profile': {
      init: function () {
        var profileImage = $(".member-profile-image .profile-image");
        $(".additional-images li img").each(function () {
          var that = $(this);
          $(this).on("click", function (event) {
            profileImage.fadeToggle(function () {
              profileImage.prop("src", that.data("large-image"));
            }).fadeToggle();
          });
        });

        $(".member-content-wrap").on("click", function () {
          $(this).toggleClass("expanded");
          console.log("here");
        });
      }
    },
    // Archive Members page
    'post_type_archive_user_profile': {
      init: function () {

      }
    },
    // Profile Editor
    'profile_editor': {
      init: function () {
        var userProfileId = $("#user-profile-id").val();

        // Profile Image Upload
        var profileImageUploadForm = $("#profile-image-upload-form");
        var profileImageUpload = $("#profile-image-upload");
        $("#update-profile-image").on("click", function () {
          profileImageUpload.trigger("click");
        });
        profileImageUpload.on("change", function () {
          profileImageUploadForm.submit();
        });
        profileImageUploadForm.on("submit", function (event) {
          that = $(this);
          event.preventDefault();
          var fd = new FormData();
          fd.append("user_profile_id", userProfileId);
          fd.append("action", "profile_editor_update_profile_image");
          fd.append("profile_image", profileImageUpload[0].files[0]);

          $.ajax({
            type: "POST",
            url: ajaxURL.ajaxurl,
            data: fd,
            processData: false,
            contentType: false,

            beforeSend: function () {
              that.parent().find("i.fa").removeClass("fa-upload").addClass("fa-circle-o-notch fa-spin fa-1x fa-fw");
            },
            success: function (r) {
              console.log(r);
              that.parent().find("i.fa").removeClass("fa-circle-o-notch fa-spin fa-1x fa-fw").addClass("fa-upload");
              that.parent().find("img").prop("src", r);
            }
          });
        });

        // Additional Image Upload/Delete
        var additionalImageUploadBtn = $(".additional-image-upload");
        var additionalImageDeleteBtn = ".additional-image-delete";
        var additionImageForm = $(".additional-image-upload-form");
        var additionalImageUploadInput = additionalImageUploadBtn.next("input");

        additionalImageUploadBtn.each(function () {
          $(this).on("click", function (event) {
            event.preventDefault();
            $(this).next(".additional-image-upload-form").find("input").trigger("click");
          });
        });

        additionImageForm.each(function () {
          var that = $(this);
          that.find("input").on("change", function () {
            that.submit();
          });

          that.on("submit", function (event) {
            event.preventDefault();
            var fd = new FormData();
            fd.append("user_profile_id", userProfileId);
            fd.append("action", "profile_editor_update_additional_images");
            fd.append("additional_image", that.find("input")[0].files[0]);
            fd.append("additional_image_handler", that.find("input").data("slug"));

            $.ajax({
              type: "POST",
              url: ajaxURL.ajaxurl,
              data: fd,
              processData: false,
              contentType: false,
              dataType: "json",
              beforeSend: function () {
                that.parent().find("i.fa").removeClass("fa-upload").addClass("fa-circle-o-notch fa-spin fa-1x fa-fw");
              },
              success: function (r) {
                console.log(r);
                that.parent().find("i.fa").removeClass("fa-circle-o-notch fa-spin fa-1x fa-fw").addClass("fa-upload");
                that.parent().find("img").prop("src", r.src);
                that.parent().find(".image-container").append(r.remove_button);
              },
              error: function (error) {
                console.log(error);
              }
            });
          });
        });

        //document .on trigger used so dynamically added remove buttons are picked up by the click trigger!
        $(document).on("click", additionalImageDeleteBtn, function (event) {
          event.preventDefault();
          var el_ = $(this),
            fd = new FormData();
          fd.append("user_profile_id", userProfileId);
          fd.append("action", "profile_editor_delete_additional_images");
          fd.append("additional_image_id", el_.data('additional-image-id'));
          fd.append("additional_image_handler", el_.data("slug"));

          $.ajax({
            type: "POST",
            url: ajaxURL.ajaxurl,
            data: fd,
            processData: false,
            contentType: false,
            beforeSend: function () {
              el_.find("i.fa").removeClass("fa-remove").addClass("fa-circle-o-notch fa-spin fa-1x fa-fw");
              el_.parent().parent().find(".additional-image-upload i.fa").removeClass("fa-upload").addClass("fa-circle-o-notch fa-spin fa-1x fa-fw");
            },
            success: function (r) {
              console.log(r);
              el_.parent().find("img").prop("src", r);
              el_.parent().parent().find(".additional-image-upload i.fa").removeClass("fa-circle-o-notch fa-spin fa-1x fa-fw").addClass("fa-upload");
              el_.remove();
            },
            error: function (error) {
              console.log(error);
            }
          });
        });

        // Simple Form update (non-images)
        var simpleForm = $(".simple-form");
        simpleForm.on("submit", function (event) {
          that = $(this);
          event.preventDefault();
          var formData = $(this).serializeArray();
          console.log(formData);

          $.ajax({
            type: "POST",
            url: ajaxURL.ajaxurl,
            data: {
              action: "profile_editor_simple_form_update",
              form_data: formData,
              user_profile_id: userProfileId,
            },
            beforeSend: function () {
              // console.log("here");
              that.find(".submit").append("<i class='fa fa-circle-o-notch fa-spin fa-1x fa-fw' aria-hidden='true'></i>");
            },
            success: function (r) {
              console.log(r);
              that.find(".submit .fa-circle-o-notch").remove();
            }
          });
        });
      }
    },
    'page_template_template_member_dashboard': {
      init: function () {
        $(".add-event").on("click", function (event) {
          event.preventDefault();

          $(this).toggleClass("active").parent().next(".event-collapsible").slideToggle();
        });

        $(".datepicker").datepicker();
        $('.timepicker').timepicker();

        $("#renew-membership-btn").on("click", function (event) {
          event.preventDefault();
          $("#renew-membership-modal").modal();
        });
      }
    },
    // About Us Page
    'about': {
      init: function () {
        var subNavHolder = $(".sub-nav-holder");
        var dcAncors = $(".dc-anchor");

        $.each(dcAncors, function (index, element) {
          var anchorText = element.innerHTML;
          var anchorId = element.innerHTML.toLowerCase().replace(/ /g, '-');
          $(this).prop("id", anchorId);
          subNavHolder.append("<li><a href='#" + anchorId + "'>" + anchorText + "</a></li>");
          // console.log(anchorId);
        });
      }
    },
    // Pages with Sidebar Primary
    'sidebar_primary': {
      init: function () {
        var showFilters = $(".show-filters");
        showFilters.on("click", function (event) {
          $(this).toggleClass('active').parent().find('.filter-list').toggleClass("active");
          // console.log();
        });

        var filterToggle = $("li.toggle span");
        filterToggle.on("click", function (event) {
          $(this).parent().toggleClass("active");
          // console.log();
        });

        $(".sidebar.visible-xs h2").on("click", function () {
          console.log("here");
          $(this).toggleClass("active").parent().find(".nav-opportunity").slideToggle();
        });
      }
    },
    //Template Poll Vote
    'page_template_template_poll_vote': {
      init: function () {
        // Mark nominee selected on click
        $('.nominee-container .inner-wrap img').on('click', function (e) {
          e.stopPropagation();
          var el_ = $(this).parents('.nominee-container'),
            nominee_id = el_.data('nominee-id'),
            nominee_name = el_.data('nominee-name'),
            award_id = el_.data('award-id');

          if (!el_.hasClass('selected')) {
            el_.addClass('selected');                           // set selected class to current element
            el_.siblings().removeClass('selected');             // remove selected class from all current element's siblings
            $('#award-' + award_id + '-nominee-id').val(nominee_id);// update award's hidden nominee id field value 
            $('#award-' + award_id + '-nominee-name').val(nominee_name);// update award's hidden nominee name field value 
          }
        });

        // Validate nominee selection on submit
        $('#submit-award-poll-vote').on('click', function (e) {
          e.preventDefault();
          var el_ = $(this),
            validation_successful = true;

          el_.prop('disabled', true); // disable the submit input to prevent users from submitting the form multiple times

          $('.award-section').each(function (i, obj) { // loop award sections
            // if a nominee is selected, and the hidden input fields have values, set validation variable to true, else set to false
            validation_successful = ($(this).find('.nominee-container.selected').length && $(this).find('.hidden-award-nominee-id-field').val()) ? true : false;
            validation_successful = ($(this).find('.nominee-container.selected').length && $(this).find('.hidden-award-nominee-name-field').val()) ? true : false;
          });

          if (validation_successful) { // on successful validation, submit form
            el_.parents('#award-nominees-voting-form').submit();
          } else { // else, show error message
            $('.message-container .message').removeClass('success').removeClass('error').addClass('error');
            $('html, body').animate({
              scrollTop: $('.message-container .message').offset().top
            }, 1000);
            el_.prop('disabled', false); // enable the submit input
          }
        });

        // Smooth scroll to awards
        $('.awards-nav a').on('click', function (e) {
          e.preventDefault();
          console.log('link clicked!!');
          $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top
          }, 1000);
        });

        // Fixed scroll within content block
        /*
        * Fix sidebar at some point and remove
        * fixed position at content bottom
        * Src: https://codepen.io/jovanivezic/pen/ZQNdag
        */
        $(window).scroll(function () {
          var fixSidebar = $('header.banner').innerHeight();
          var contentHeight = $('.wrap.container').innerHeight();
          var sidebarHeight = $('.awards-nav').height();
          var sidebarBottomPos = contentHeight - sidebarHeight;
          var trigger = $(window).scrollTop() - fixSidebar;

          if ($(window).scrollTop() >= fixSidebar) {
            $('.awards-nav').addClass('fixed');
          } else {
            $('.awards-nav').removeClass('fixed');
          }

          if (trigger >= sidebarBottomPos) {
            $('.awards-nav').addClass('bottom');
          } else {
            $('.awards-nav').removeClass('bottom');
          }
        });
      }
    },
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function (func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function () {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function (i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.