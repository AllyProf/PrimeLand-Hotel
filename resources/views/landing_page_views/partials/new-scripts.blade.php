<!-- Review Modal functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.gt-testi-read-more-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var author = this.getAttribute('data-author');
                var flag = this.getAttribute('data-flag');
                var location = this.getAttribute('data-location');
                var text = this.getAttribute('data-full-text');

                document.getElementById('gtModalAuthor').textContent = author;
                document.getElementById('gtModalFlag').src = flag;
                document.getElementById('gtModalFlag').alt = author;
                document.getElementById('gtModalLocation').textContent = location;
                document.getElementById('gtModalText').textContent = text;

                $('#gtReviewModal').modal('show');
            });
        });
    });
</script>

<!--<< All JS Plugins >>-->
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<!--<< Viewport Js >>-->
<script src="{{ asset('assets/js/viewport.jquery.js') }}"></script>
<!--<< Bootstrap Js >>-->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<!--<< nice-selec Js >>-->
<script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
<!--<< Waypoints Js >>-->
<script src="{{ asset('assets/js/jquery.waypoints.js') }}"></script>
<!--<< Counterup Js >>-->
<script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
<!--<< Swiper Slider Js >>-->
<script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
<!--<< MeanMenu Js >>-->
<script src="{{ asset('assets/js/jquery.meanmenu.min.js') }}"></script>
<!--<< Parallaxie Js >>-->
<script src="{{ asset('assets/js/parallaxie.js') }}"></script>
<!--<< Magnific Popup Js >>-->
<script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
<!--<< Wow Animation Js >>-->
<script src="{{ asset('assets/js/wow.min.js') }}"></script>
<!--<< Main.js >>-->
<script src="{{ asset('assets/js/main.js') }}"></script>
