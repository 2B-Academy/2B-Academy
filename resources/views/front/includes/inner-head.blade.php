<!-- ==================== Breadcrumb Start Here ==================== -->
<section class="breadcrumb py-60 inner-banner position-relative z-1 overflow-hidden mb-0">
    <div class="overlay-banner"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb__wrapper">
                    <h1 class="breadcrumb__title display-4 fw-semibold text-white text-center"> {{$title}}</h1>
                    <ul class="breadcrumb__list d-flex align-items-center justify-content-center gap-4">
                        <li class="breadcrumb__item">
                            <a href="{{route('front.home')}}" class="breadcrumb__link text-white hover-text-main-600 fw-medium">
                                <i class="text-lg d-inline-flex ph-bold ph-house"></i> الرئيسية</a>
                        </li>
                        <li class="breadcrumb__item">
                            <i class="text-white d-flex ph-bold ph-caret-right"></i>
                        </li>
                        <li class="breadcrumb__item">
                            <span class="text-main-two-600"> {{$title}} </span>
                        </li>
                        @if(isset($single))
                            <li class="breadcrumb__item">
                                <i class="text-white d-flex ph-bold ph-caret-right"></i>
                            </li>
                            <li class="breadcrumb__item">
                                <span class="text-main-two-600"> {{$single}} </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ==================== Breadcrumb End Here ==================== -->
