@if(isset($settings['certificate']) || isset($settings['certificate_content']))
    <!-- ============================== Certificate Two Section Start ===================================== -->
    <section class="certificate-two py-60 position-relative z-1 mash-bg-main mash-bg-main-two mash-reverse">
        <div class="section-heading text-center">
            <div class="flex-align d-inline-flex gap-8 mb-16 wow bounceInDown">
                <span class="text-main-600 text-2xl d-flex"><i class="ph-bold ph-certificate"></i></span>
                <h5 class="text-main-600 mb-0">الشهادات</h5>
            </div>
            <h2 class="mb-24 wow bounceIn">شهادة المهارات من توبي</h2>
        </div>
        <div class="position-relative">
            <div class="container">
                <div class="row align-items-center gy-4">
                    <div class="col-lg-6 pe-lg-5">
                        {!! $settings['certificate_content'] ?? '' !!}
                    </div>
                    <div class="col-lg-6">
                        <div class="certificate-two__thumb">
                            <img src="{{ getFullPath($settings['certificate'] ?? '') ?: '' }}" alt="" data-tilt data-tilt-max="10" data-tilt-speed="500" data-tilt-perspective="5000" data-tilt-full-page-listening>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ============================== Certificate Two Section End ===================================== -->
@endif
