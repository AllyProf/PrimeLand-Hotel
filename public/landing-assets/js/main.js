(function($) {
    "use strict";
  
    const $documentOn = $(document);
    const $windowOn = $(window);
  
    $documentOn.ready( function() {
  
      /* ================================
       Mobile Menu Js Start
    ================================ */
    
    $('#mobile-menu').meanmenu({
        meanMenuContainer: '.mobile-menu',
        meanScreenWidth: "1199",
        meanExpand: ['<i class="far fa-plus"></i>'],
    });
    
     /* ================================
       Sidebar Toggle Js Start
    ================================ */

      $(".offcanvas__close,.offcanvas__overlay").on("click", function () {
        $(".offcanvas__info").removeClass("info-open");
        $(".offcanvas__overlay").removeClass("overlay-open");
      });
      $(".sidebar__toggle").on("click", function () {
        $(".offcanvas__info").addClass("info-open");
        $(".offcanvas__overlay").addClass("overlay-open");
      });
      
       /* ================================
       Body Overlay Js Start
    ================================ */

      $(".body-overlay").on("click", function () {
        $(".offcanvas__area").removeClass("offcanvas-opened");
        $(".df-search-area").removeClass("opened");
        $(".body-overlay").removeClass("opened");
      });
  
      /* ================================
       Sticky Header Js Start
    ================================ */

      $windowOn.on("scroll", function () {
        if ($(this).scrollTop() > 250) {
          $("#header-sticky").addClass("sticky");
        } else {
          $("#header-sticky").removeClass("sticky");
        }
      });      
      
       /* ================================
       Video & Image Popup Js Start
    ================================ */

      $(".img-popup").magnificPopup({
        type: "image",
        gallery: {
          enabled: true,
        },
      });

      $(".video-popup").magnificPopup({
        type: "iframe",
        callbacks: {},
      });
  
      /* ================================
       Counterup Js Start
    ================================ */

      $(".gt-count").counterUp({
        delay: 15,
        time: 4000,
      });
  
      /* ================================
       Wow Animation Js Start
    ================================ */

      new WOW().init();
  
      /* ================================
       Nice Select Js Start
    ================================ */

    if ($('.single-select').length) {
        $('.single-select').niceSelect();
    }

    //>> Hero Slider Start <<//
      if($('.hero-slider').length > 0) {
        const HeroSlider = new Swiper(".hero-slider", {
            spaceBetween: 20,
            speed: 1300,
            loop: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
             navigation: {
                nextEl: ".array-prev1",
                prevEl: ".array-next1",
            },
           pagination: {
                el: ".dot",
                clickable: true,
            },
            breakpoints: {
                1199: {
                    slidesPerView: 3,
                },
                991: {
                    slidesPerView: 3,
                },
                767: {
                    slidesPerView: 3,
                },
                575: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
      }

       if($('.hero-slider1').length > 0) {
        const HeroSlider1 = new Swiper(".hero-slider1", {
            spaceBetween: 20,
            speed: 1300,
            loop: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
             
           pagination: {
                el: ".dot2",
                clickable: true,
            },
        });
      }


     /* ================================
       GT Room Slider Js Start
    ================================ */

    if($('.gt-room-slider').length > 0) {
        const gtRoomSlider = new Swiper(".gt-room-slider", {
            spaceBetween: 30,
            speed: 1300,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
            },
            breakpoints: {
                1699: {
                    slidesPerView: 5,
                },
                1399: {
                    slidesPerView: 4.5,
                },
                1199: {
                    slidesPerView: 4,
                },
                991: {
                    slidesPerView: 3,
                },
                767: {
                    slidesPerView: 2,
                },
                575: {
                    slidesPerView: 1.5,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
    }

     if($('.gt-room-explore-slider').length > 0) {
        const gtRoomExploreSlider = new Swiper(".gt-room-explore-slider", {
            spaceBetween: 30,
            speed: 1300,
            loop: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
            },
            breakpoints: {
                1199: {
                    slidesPerView: 2,
                },
                991: {
                    slidesPerView: 1,
                },
                767: {
                    slidesPerView: 1,
                },
                575: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
    }

    if($('.room-slider-3').length > 0) {
        const gtRoomSlider3 = new Swiper(".room-slider-3", {
            spaceBetween: 30,
            speed: 1300,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
            },
            breakpoints: {
                1199: {
                    slidesPerView: 1.8,
                },
                991: {
                    slidesPerView: 1,
                },
                767: {
                    slidesPerView: 1,
                },
                575: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
    }

    /* ================================
        Instagram Slider Js Start
    ================================ */

    if($('.gt-instagram-slider').length > 0) {
        const gtInstagramSlider = new Swiper(".gt-instagram-slider", {
            spaceBetween: 30,
            speed: 1300,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
            },
            breakpoints: {
                1399: {
                    slidesPerView: 6,
                },
                1199: {
                    slidesPerView: 4,
                },
                991: {
                    slidesPerView: 3.3,
                },
                767: {
                    slidesPerView: 3,
                },
                575: {
                    slidesPerView: 2,
                },
                0: {
                    slidesPerView: 1.5,
                },
            },
        });
    }

    if($('.gt-instagram-slider-2').length > 0) {
        const gtInstagramSlider2 = new Swiper(".gt-instagram-slider-2", {
            spaceBetween: 0,
            speed: 1300,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
            },
            breakpoints: {
                1399: {
                    slidesPerView: 6,
                },
                1199: {
                    slidesPerView: 4,
                },
                991: {
                    slidesPerView: 3,
                },
                767: {
                    slidesPerView: 2,
                },
                575: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
    }

    /* ================================
       Service Slider Js Start
    ================================ */

     if($('.service-image-slider').length > 0) {
        const serviceImageSlider = new Swiper(".service-image-slider", {
            spaceBetween: 0,
            speed: 1300,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
            },
             pagination: {
                el: ".dot",
                clickable: true,
            },
        });
    }
      
     /* ================================
       GT Special Offer Slider Js Start
    ================================ */

    if($('.gt-special-offer-slider').length > 0) {
        const gtSpecialOfferSlider = new Swiper(".gt-special-offer-slider", {
            spaceBetween: 30,
            speed: 1300,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".dot21",
                clickable: true,
            },
            
            breakpoints: {
                1199: {
                    slidesPerView: 3,
                },
                991: {
                    slidesPerView: 2,
                },
                767: {
                    slidesPerView: 2,
                },
                575: {
                    slidesPerView: 1,
                },
                0: {
                    slidesPerView: 1,
                },
            },
        });
    }

    /* ================================
       GT Testimonial Slider Js Start
    ================================ */

    if($('.gt-testimonial-slider').length > 0) {
        const gtTestimonialSlider = new Swiper(".gt-testimonial-slider", {
            spaceBetween: 30,
            speed: 1300,
            loop: true,
            centeredSlides: true,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".array-prev",
                prevEl: ".array-next",
            },
            pagination: {
                el: ".dot",
                clickable: true,
            },
        });
    }

   /* ================================
       Parallaxie Js Start
    ================================ */

    if ($('.parallaxie').length && $(window).width() > 991) {
        if ($(window).width() > 768) {
            $('.parallaxie').parallaxie({
                speed: 0.55,
                offset: 0,
            });
        }
    }

    /* ================================
      Faq Custom Js Start
    ================================ */

    document.addEventListener("DOMContentLoaded", function () {
        const items = document.querySelectorAll('.faq-item');

        items.forEach(item => {
            item.addEventListener('mouseenter', function () {
            document.querySelector('.faq-item.active')?.classList.remove('active');
            this.classList.add('active');
            });
        });
    });

     /* ================================
       Search Popup Toggle Js Start
    ================================ */

    if ($(".search-toggler").length) {
        $(".search-toggler").on("click", function(e) {
            e.preventDefault();
            $(".search-popup").toggleClass("active");
            $("body").toggleClass("locked");
        });
    }


     /* ================================
       Mouse Cursor Animation Js Start
    ================================ */

    if ($(".mouseCursor").length > 0) {
        function itCursor() {
            var myCursor = jQuery(".mouseCursor");
            if (myCursor.length) {
                if ($("body")) {
                    const e = document.querySelector(".cursor-inner"),
                        t = document.querySelector(".cursor-outer");
                    let n,
                        i = 0,
                        o = !1;
                    (window.onmousemove = function(s) {
                        o ||
                            (t.style.transform =
                                "translate(" + s.clientX + "px, " + s.clientY + "px)"),
                            (e.style.transform =
                                "translate(" + s.clientX + "px, " + s.clientY + "px)"),
                            (n = s.clientY),
                            (i = s.clientX);
                    }),
                    $("body").on(
                            "mouseenter",
                            "button, a, .cursor-pointer",
                            function() {
                                e.classList.add("cursor-hover"),
                                    t.classList.add("cursor-hover");
                            }
                        ),
                        $("body").on(
                            "mouseleave",
                            "button, a, .cursor-pointer",
                            function() {
                                ($(this).is("a", "button") &&
                                    $(this).closest(".cursor-pointer").length) ||
                                (e.classList.remove("cursor-hover"),
                                    t.classList.remove("cursor-hover"));
                            }
                        ),
                        (e.style.visibility = "visible"),
                        (t.style.visibility = "visible");
                }
            }
        }
        itCursor();
      }
  

   /* ================================
       Back To Top Button Js Start
    ================================ */

    $windowOn.on('scroll', function() {
        if ($(this).scrollTop() > 20) {
            $("#gt-back-top").addClass("show");
        } else {
            $("#gt-back-top").removeClass("show");
        }
    });
    
    $documentOn.on('click', '#gt-back-top', function() {
        $('html, body').animate({ scrollTop: 0 }, 800);
        return false;
    });

    
    }); // End Document Ready Function

     function loader() {
            setTimeout(function() {
                var $preloader = $(".preloader");
                if ($preloader.length && !$preloader.hasClass('loaded')) {
                    $preloader.addClass('loaded');
                    $preloader.fadeOut(400);
                }
            }, 1000);
        }
        
        loader();


  
  })(jQuery); // End jQuery