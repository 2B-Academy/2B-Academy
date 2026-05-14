<div class="col-lg-3">
    <button class="btn btn-main rounded filter-btn w-100" id="filter">التصفيه
        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.99952 4.82561H10.6048C10.834 5.86894 11.7657 6.65221 12.8769 6.65221C13.9881 6.65221 14.9198 5.86897 15.149 4.82561H17.985C18.2609 4.82561 18.4845 4.60195 18.4845 4.32609C18.4845 4.05023 18.2609 3.82657 17.985 3.82657H15.1488C14.9191 2.78377 13.9861 2 12.8769 2C11.7671 2 10.8346 2.78365 10.605 3.82657H2.99952C2.72366 3.82657 2.5 4.05023 2.5 4.32609C2.5 4.60195 2.72366 4.82561 2.99952 4.82561ZM11.5499 4.3274C11.5499 4.32562 11.5499 4.32381 11.5499 4.32203C11.5521 3.59252 12.1474 2.99906 12.8769 2.99906C13.6054 2.99906 14.2007 3.59171 14.2039 4.32088L14.204 4.32821C14.2028 5.05897 13.608 5.65321 12.8769 5.65321C12.1462 5.65321 11.5515 5.05957 11.5499 4.32924L11.5499 4.3274ZM17.985 15.1744H15.1488C14.9191 14.1316 13.9861 13.3478 12.8769 13.3478C11.7671 13.3478 10.8346 14.1315 10.605 15.1744H2.99952C2.72366 15.1744 2.5 15.398 2.5 15.6739C2.5 15.9498 2.72366 16.1734 2.99952 16.1734H10.6048C10.834 17.2167 11.7657 18 12.8769 18C13.9881 18 14.9198 17.2167 15.149 16.1734H17.985C18.2609 16.1734 18.4845 15.9498 18.4845 15.6739C18.4845 15.398 18.2609 15.1744 17.985 15.1744ZM12.8769 17.001C12.1462 17.001 11.5515 16.4073 11.5499 15.677L11.5499 15.6752C11.5499 15.6734 11.5499 15.6716 11.5499 15.6698C11.5521 14.9403 12.1474 14.3468 12.8769 14.3468C13.6054 14.3468 14.2007 14.9395 14.2039 15.6686L14.204 15.6759C14.2029 16.4068 13.608 17.001 12.8769 17.001ZM17.985 9.50048H10.3797C10.1505 8.45715 9.21877 7.67391 8.1076 7.67391C6.99643 7.67391 6.0647 8.45715 5.83549 9.50048H2.99952C2.72366 9.50048 2.5 9.72414 2.5 10C2.5 10.2759 2.72366 10.4995 2.99952 10.4995H5.83574C6.06542 11.5423 6.99839 12.3261 8.1076 12.3261C9.2174 12.3261 10.15 11.5424 10.3795 10.4995H17.985C18.2609 10.4995 18.4845 10.2759 18.4845 10C18.4845 9.72414 18.2609 9.50048 17.985 9.50048ZM9.43463 9.99869C9.43463 10.0005 9.43459 10.0023 9.43459 10.0041C9.43241 10.7336 8.83714 11.327 8.1076 11.327C7.37909 11.327 6.78379 10.7344 6.78063 10.0052L6.78054 9.99794C6.78163 9.26709 7.37653 8.67294 8.1076 8.67294C8.83833 8.67294 9.433 9.26655 9.43466 9.99691L9.43463 9.99869Z" fill="#ffffff"/>
        </svg>
    </button>
    <div class="filter-sidebar d-small-none rounded-12 bg-main-25 p-32 border border-neutral-30">
        <form action="{{route('front.courses')}}" method="GET">
            <div class="flex-between mb-24">
                <h4 class="mb-0">بحث</h4>
            </div>

            <span class="d-block border border-neutral-30 border-dashed my-24"></span>

            <h6 class="text-lg mb-24 fw-medium">الأقسام</h6>
            <div class="d-flex flex-column gap-16">
                @foreach($front_categories as $category)
                    <div class="flex-between gap-16">
                        <div class="form-check common-check mb-0">
                            <input class="form-check-input" @checked(is_array(request('category')) && in_array($category->id, request('category'))) type="checkbox" name="category[]" value="{{$category->id}}">
                            <label class="form-check-label fw-normal flex-grow-1" for="{{$category->id}}">{{$category->name}}</label>
                        </div>
                        <span class="text-neutral-500">{{$category->courses_count}}</span>
                    </div>
                @endforeach
            </div>
            <span class="d-block border border-neutral-30 border-dashed my-24"></span>

            <h6 class="text-lg mb-24 fw-medium">المستوي</h6>
            <div class="d-flex flex-column gap-16">
                <div class="form-check common-check mb-0">
                    <input class="form-check-input" value="beginner" type="checkbox" @checked(is_array(request('level')) && in_array('beginner', request('level'))) name="level[]" id="beginner">
                    <label class="form-check-label fw-normal flex-grow-1" for="beginner">مبتدئ</label>
                </div>
                <div class="form-check common-check mb-0">
                    <input class="form-check-input" value="medium" type="checkbox" @checked(is_array(request('level')) && in_array('medium', request('level'))) name="level[]" id="medium">
                    <label class="form-check-label fw-normal flex-grow-1" for="medium">متوسط</label>
                </div>
                <div class="form-check common-check mb-0">
                    <input class="form-check-input" value="professional" type="checkbox" @checked(is_array(request('level')) && in_array('professional', request('level'))) name="level[]" id="professional">
                    <label class="form-check-label fw-normal flex-grow-1" for="professional">محترف</label>
                </div>
            </div>
            <span class="d-block border border-neutral-30 border-dashed my-24"></span>

            <button type="submit"  class="btn btn-main rounded-pill flex-center gap-16 mb-5 fw-semibold w-100">
                بحث
                <i class="ph-bold ph-arrow-right d-flex text-lg"></i>
            </button>
            <button type="button" onclick="location.href='{{route('front.courses')}}'" class="btn btn-outline-main rounded-pill flex-center gap-16 fw-semibold w-100">
                <i class="ph-bold ph-arrow-clockwise d-flex text-lg"></i>
                إلغاء التصفيه
            </button>
        </form>
    </div>
</div>
