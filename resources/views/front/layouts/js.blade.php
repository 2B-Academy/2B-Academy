
<!-- Jquery js -->
<script src="{{asset('front/assets/js/jquery-3.6.0.min.js')}}"></script>
<!-- Bootstrap Bundle Js -->
<script src="{{asset('front/assets/js/boostrap.bundle.min.js')}}"></script>
<!-- select2 Js -->
<script src="{{asset('front/assets/js/select2.min.js')}}"></script>
<!-- Phosphor Icon Js -->
<script src="{{asset('front/assets/js/phosphor-icon.js')}}"></script>
<!-- Slick js -->
<script src="{{asset('front/assets/js/slick.min.js')}}"></script>
<!-- Slick js -->
<script src="{{asset('front/assets/js/counter.min.js')}}"></script>
<!-- magnific popup -->
<script src="{{asset('front/assets/js/magnific-popup.min.js')}}"></script>
<!-- Jquery Ui js -->
<script src="{{asset('front/assets/js/jquery-ui.js')}}"></script>
<!-- marquee js -->
<script src="{{asset('front/assets/js/marquee.min.js')}}"></script>
<!-- react charts-->
<script src="{{asset('front/assets/js/apexcharts.js')}}"></script>
<!-- plyr Js -->
<script src="{{asset('front/assets/js/plyr.js')}}"></script>
<!-- vanilla Tilt -->
<!-- Editor js Toolbar Start -->
<script src="{{asset('front/assets/js/editor-quill.js')}}"></script>
<!-- dataTables -->
<script src="{{asset('front/assets/js/dataTables.min.js')}}"></script>
<!-- Tilt -->
<script src="{{asset('front/assets/js/vanilla-tilt.min.js')}}"></script>
<!-- wow -->
<script src="{{asset('front/assets/js/wow.min.js')}}"></script>

<script src="{{asset('front/assets/js/aos.js')}}"></script>

<!-- main js -->
<script src="{{asset('front/assets/js/main.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

<script>
    $(document).ready(function(){
        $('img.animate__swing').each(function() {
            var currentSrc = $(this).attr("src");
            $(this).attr("src", "front/" + currentSrc);
        });
        $('.pagination .page-link').addClass('text-neutral-700 fw-semibold w-40 h-40 bg-main-25 rounded-circle hover-bg-main-600 border-neutral-30 hover-border-main-600 hover-text-white flex-center p-0');
    });
    $(document).on('change','.select_category', function (e){
        e.preventDefault();
        location.href = '{{route('front.courses')}}?category[]='+$(this).val();
    })
</script>

@stack('js')
